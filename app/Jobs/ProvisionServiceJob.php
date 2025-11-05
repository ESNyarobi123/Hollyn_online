<?php

namespace App\Jobs;

use App\Models\Service;
use App\Models\ProvisioningLog;
use App\Services\WebuzoApi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Throwable;

class ProvisionServiceJob implements ShouldQueue, ShouldBeUniqueUntilProcessing
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;
    public string $queue = 'provisioning';
    public int $uniqueFor = 900; // 15 min

    public function __construct(public int $serviceId) {}

    public function uniqueId(): string
    {
        return 'provision-service-' . $this->serviceId;
    }

    public function uniqueVia(): CacheRepository
    {
        $store = config('queue.unique_cache');
        return $store ? Cache::store($store) : Cache::store(config('cache.default'));
    }

    public function backoff(): array
    {
        return [10, 60, 180];
    }

    public function middleware(): array
    {
        $mw = [
            (new WithoutOverlapping('prov-svc-' . $this->serviceId))
                ->releaseAfter(30)
                ->expireAfter(300),
        ];

        // Optional: add RateLimited only if limiter exists
        if (method_exists(RateLimiter::class, 'limiter') && RateLimiter::limiter('webuzo-provision')) {
            $mw[] = new \Illuminate\Queue\Middleware\RateLimited('webuzo-provision');
        }

        return $mw;
    }

    public function handle(WebuzoApi $webuzo): void
    {
        // Short throttle (burst double-dispatch protection)
        $runKey = "svc:prov:run:{$this->serviceId}";
        if (!Cache::add($runKey, 1, now()->addMinutes(2))) {
            return;
        }

        // Cross-worker lock
        $lock = Cache::lock("svc:prov:lock:{$this->serviceId}", 300);
        if (!$lock->get()) {
            $this->release(15);
            return;
        }

        try {
            // ----- Transaction: fetch + mark provisioning with FOR UPDATE -----
            /** @var Service $service */
            $service = DB::transaction(function () {
                $svc = Service::query()
                    ->with(['order.user', 'order.plan', 'plan'])
                    ->whereKey($this->serviceId)
                    ->lockForUpdate()
                    ->firstOrFail();

                if (strtolower((string)$svc->status) !== 'active') {
                    $svc->status = 'provisioning';
                    if (Schema::hasColumn('services', 'last_provision_attempt_at')) {
                        $svc->last_provision_attempt_at = now();
                    }
                    $svc->save();
                    $this->log($svc, 'status_update', ['status' => 'provisioning'], ['ok' => true]);
                }

                return $svc;
            });

            // If already active after the txn, no need to proceed
            if (strtolower((string)$service->status) === 'active') {
                return;
            }

            // ---------- Derive inputs ----------
            $order = $service->order;
            $user  = $order?->user;

            $email = $order?->customer_email
                ?? $user?->email
                ?? ('user' . $service->id . '@example.local');

            $username = $this->makeUsername($email, (int)$service->id);
            $password = $this->makeStrongPassword();

            $cfg      = (array) config('services.webuzo');
            $planSlug = $service->plan?->slug ?: $order?->plan?->slug ?: null;
            $package  = ($cfg['plan_map'][$planSlug] ?? null)
                     ?: ($cfg['default_package'] ?? 'Hollyn Lite');

            // Prefer service->domain, else order->domain, else fallback
            $domain = $this->normalizeDomain(
                $service->domain ?: ($order?->domain ?? ($username . '.local'))
            );

            // ---------- Call Webuzo (create user) ----------
            $this->log($service, 'create_user', [
                'email'    => $email,
                'username' => $username,
                'package'  => $package,
                'domain'   => $domain,
            ]);

            $resCreate = $webuzo->createUser(
                email:    $email,
                username: $username,
                password: $password,
                package:  (string)$package,
                limits:   [] // optional
            );
            $this->log($service, 'create_user_response', [], $resCreate);

            if (!($resCreate['ok'] ?? false)) {
                $bodyStr = json_encode($resCreate['body'] ?? []);
                // idempotency: treat "exists/already" as okay
                if (stripos($bodyStr, 'exists') === false && stripos($bodyStr, 'already') === false) {
                    throw new \RuntimeException('Webuzo createUser failed: ' . $bodyStr);
                }
            }

            // ---------- Add/ensure domain (best-effort) ----------
            if (!empty($domain)) {
                $this->log($service, 'add_domain', ['username' => $username, 'domain' => $domain]);
                try {
                    $resDomain = $webuzo->addDomain($username, $domain);
                    $this->log($service, 'add_domain_response', [], $resDomain);
                } catch (Throwable $ex) {
                    // best-effort only; continue
                    $this->log($service, 'add_domain_error', ['username' => $username, 'domain' => $domain], ['ok' => false, 'msg' => $ex->getMessage()]);
                }
            }

            // URLs snapshot
            $enduserUrl = (string)($cfg['enduser_url'] ?? '');
            $panelUrl   = $enduserUrl; // same unless you have a distinct admin URL to store per service

            // ---------- Persist success ----------
            DB::transaction(function () use ($service, $order, $username, $password, $enduserUrl, $panelUrl) {
                $service->webuzo_username          = $username;
                $service->webuzo_temp_password_enc = Crypt::encryptString($password);
                $service->enduser_url              = $enduserUrl;

                if (Schema::hasColumn('services', 'panel_url')) {
                    $service->panel_url = $panelUrl;
                }

                $service->status = 'active';

                if (Schema::hasColumn('services', 'last_provisioned_at')) {
                    $service->last_provisioned_at = now();
                }
                $service->save();

                if ($order) {
                    $order->status = 'active';
                    $order->save();
                }
            });

            $this->log($service, 'status_update', ['status' => 'active'], ['ok' => true]);
        } catch (Throwable $e) {
            // Swallow if already active now
            $svc = Service::find($this->serviceId);
            if ($svc && strtolower((string)$svc->status) === 'active') {
                Log::warning("ProvisionServiceJob: exception swallowed; service already active", [
                    'service_id' => $this->serviceId,
                    'msg'        => $e->getMessage(),
                ]);
                return;
            }

            $this->logRaw($this->serviceId, 'error', ['message' => $e->getMessage()]);
            throw $e; // let queue retry
        } finally {
            optional($lock)->release();
        }
    }

    public function failed(Throwable $e): void
    {
        try {
            $service = Service::find($this->serviceId);
            if ($service && strtolower((string)$service->status) !== 'active') {
                $service->status = 'failed';
                if (Schema::hasColumn('services', 'last_failed_at')) {
                    $service->last_failed_at = now();
                }
                $service->save();

                $this->logRaw($service->id, 'final_failed', [
                    'message' => $e->getMessage(),
                ]);
            }
        } catch (Throwable) {
            // ignore
        }
    }

    // ===================== Helpers =====================

    protected function makeUsername(string $email, int $suffixId): string
    {
        $local = strstr($email, '@', true) ?: 'user';
        $clean = strtolower(preg_replace('/[^a-z0-9]/', '', $local));
        if ($clean === '' || !ctype_alpha($clean[0])) {
            $clean = 'u' . $clean;
        }
        // max 8 chars total; add tiny suffix derived from id
        $base = Str::limit($clean, 6, ''); // leave room for 2-char suffix
        $suffix = substr(base_convert((string)$suffixId, 10, 36), -2); // compact base36
        if (strlen($suffix) < 2) $suffix = Str::lower(Str::random(2));
        return Str::limit($base . $suffix, 8, '');
    }

    protected function makeStrongPassword(): string
    {
        return Str::random(10) . 'aA1!';
    }

    protected function normalizeDomain(?string $domain): string
    {
        $d = strtolower(trim((string)$domain));
        $d = preg_replace('#^https?://#', '', $d);
        $d = preg_replace('#/.*$#', '', $d);
        $d = preg_replace('/\s+/', '', $d);
        return $d ?: 'site-'.Str::lower(Str::random(6)).'.hollyn.site';
    }

    protected function log(Service $service, string $step, array $request = [], $response = null): void
    {
        ProvisioningLog::create([
            'service_id' => $service->id,
            'step'       => $step,
            'request'    => json_encode($request, JSON_UNESCAPED_SLASHES),
            'response'   => is_array($response) ? json_encode($response, JSON_UNESCAPED_SLASHES) : (string)($response ?? ''),
            'success'    => true,
        ]);
    }

    protected function logRaw(int $serviceId, string $step, array $payload = []): void
    {
        ProvisioningLog::create([
            'service_id' => $serviceId,
            'step'       => $step,
            'request'    => json_encode(['service_id' => $serviceId], JSON_UNESCAPED_SLASHES),
            'response'   => json_encode($payload, JSON_UNESCAPED_SLASHES),
            'success'    => false,
        ]);
    }
}
