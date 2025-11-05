<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Service;
use App\Support\WebuzoPackageResolver;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

class ProvisionWebuzoAccount implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $orderId;

    /** Retry attempts */
    public int $tries = 3;

    /** Max seconds per run */
    public int $timeout = 120;

    /** Uniqueness window (sec) while waiting to run */
    public int $uniqueFor = 900; // 15 min

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
        $this->onQueue('provisioning');
    }

    public function uniqueId(): string
    {
        return 'provision:order:' . $this->orderId;
    }

    /** Ensure ShouldBeUnique uses a deterministic cache store */
    public function uniqueVia(): CacheRepository
    {
        $store = config('queue.unique_cache');
        return $store ? Cache::store($store) : Cache::store(config('cache.default'));
    }

    public function backoff(): array
    {
        return [15, 60, 180];
    }

    public function middleware(): array
    {
        return [
            (new WithoutOverlapping('prov-webuzo-order-' . $this->orderId))
                ->releaseAfter(20)
                ->expireAfter(180),
        ];
    }

    public function handle(): void
    {
        $order = Order::with(['user', 'plan', 'service'])->find($this->orderId);
        if (!$order) {
            Log::warning("ProvisionWebuzoAccount: order {$this->orderId} not found");
            return;
        }

        // Only proceed when order is paid/complete-like
        $paidStates = ['paid','succeeded','complete','completed','success','active'];
        if (!in_array(Str::lower((string)$order->status), $paidStates, true)) {
            Log::info('ProvisionWebuzoAccount: order not paid-like, skipping', ['order' => $order->id, 'status' => $order->status]);
            return;
        }

        // Skip if we already have provisioning/active service
        if ($order->service && in_array(Str::lower((string)$order->service->status), ['provisioning','active'], true)) {
            Log::info('ProvisionWebuzoAccount: service already provisioning/active, skipping', ['order' => $order->id]);
            return;
        }

        $user = $order->user;
        $plan = $order->plan;
        if (!$user || !$plan) {
            Log::warning("ProvisionWebuzoAccount: missing user/plan for order {$order->id}");
            return;
        }

        /* ---------------- Package (plan → Webuzo package) ---------------- */
        $package = null;
        try {
            $package = (string) (WebuzoPackageResolver::resolve($plan)['package'] ?? null);
        } catch (Throwable $__) {
            // ignore – fallback below
        }
        if (!$package) {
            $map     = (array) (config('services.webuzo.plan_map') ?? []);
            $package = $map[$plan->slug] ?? (config('services.webuzo.default_package', 'Hollyn Lite'));
        }

        /* ---------------- Credentials + domain ---------------- */
        $baseUsername = $this->makeBaseUsername($user->name ?? $user->email ?? 'user'); // ≤8 chars, starts with letter
        // top up with random suffix to ensure uniqueness but keep ≤8
        $username = $this->topUpUsername($baseUsername);
        $password = $this->makeStrongPassword();

        $domain = $this->normalizeDomain(
            $order->domain
            ?: ($order->service->domain ?? null)
            ?: (Str::slug($user->name ?? 'site') . '.hollyn.site')
        );

        /* ---------------- Persist/ensure local Service row (provisioning) ---------------- */
        $service = Service::updateOrCreate(
            ['order_id' => $order->id],
            [
                'user_id'                  => $order->user_id,
                'plan_id'                  => $plan->id ?? null,
                'plan_slug'                => $plan->slug ?? null,
                'domain'                   => $domain,
                'enduser_url'              => (string) config('services.webuzo.enduser_url'),
                'webuzo_username'          => $username,
                'webuzo_temp_password_enc' => Crypt::encryptString($password),
                'status'                   => 'provisioning',
            ]
        );

        /* ---------------- Build Webuzo request ---------------- */
        $cfg = (array) config('services.webuzo');

        // Validate + normalize base URL and path (MUST be absolute base + '/index.php?api=...&act=add_user')
        $apiBase = trim((string) ($cfg['api_url'] ?? ''));
        if ($apiBase === '' || !preg_match('#^https?://#i', $apiBase)) {
            throw new \RuntimeException('Webuzo base URL is missing/invalid. Set SERVICES_WEBUZO_API_URL e.g. https://109.123.240.147:2005');
        }
        $apiBase = rtrim($apiBase, '/');

        // Accept both create_user_path and create_path (backwards compat); default to correct endpoint 'add_user'
        $createPath = trim((string) ($cfg['create_user_path'] ?? $cfg['create_path'] ?? '/index.php?api=json&act=add_user'));
        if ($createPath === '') $createPath = '/index.php?api=json&act=add_user';
        if ($createPath[0] !== '/') $createPath = '/'.$createPath;

        $url       = $apiBase . $createPath;
        $timeout   = (int) ($cfg['timeout'] ?? 90);
        $cto       = (int) ($cfg['connect_timeout'] ?? 10);
        $verifySsl = (bool) ($cfg['verify_ssl'] ?? true);

        // Prefer 'auth' then legacy 'use_basic_auth'
        $authMode = (string) ($cfg['auth'] ?? (($cfg['use_basic_auth'] ?? false) ? 'basic' : 'key'));

        // Payload aligned with working legacy scripts
        $payload = [
            'create_user'      => 1,
            // username (aliases)
            'user'             => $username,
            'username'         => $username,
            // password
            'user_passwd'      => $password,
            'cnf_user_passwd'  => $password,
            // extras
            'email'            => $user->email,
            'domain'           => $domain,
            // package/plan (aliases)
            'plan'             => $package,
            'package'          => $package,
            // harmless aliases for broader compatibility
            'password'         => $password,
            'pass'             => $password,
        ];

        try {
            Log::info('ProvisionWebuzoAccount: calling add_user', [
                'url'     => $url,
                'order'   => $order->id,
                'plan'    => $plan->slug,
                'package' => $package,
                'domain'  => $domain,
                'user'    => $username,
            ]);

            $req = Http::timeout($timeout)
                ->connectTimeout($cto)
                ->asForm()
                ->withHeaders(['Accept' => 'application/json']);

            if ($verifySsl === false) {
                $req = $req->withOptions(['verify' => false]);
            }

            if ($authMode === 'key' && !empty($cfg['api_key'])) {
                $header = $cfg['key_header'] ?? 'Authorization';
                $prefix = trim((string) ($cfg['key_prefix'] ?? 'Bearer'));
                $value  = $prefix !== '' ? ($prefix.' '.$cfg['api_key']) : $cfg['api_key'];
                $req    = $req->withHeaders([$header => $value]);
            } else {
                // default to basic if creds are present — this avoids user:pass@host URL issues with @/: etc
                if (!empty($cfg['admin_user']) && !empty($cfg['admin_pass'])) {
                    $req = $req->withBasicAuth((string)$cfg['admin_user'], (string)$cfg['admin_pass']);
                }
            }

            // First attempt
            $resp = $req->post($url, $payload);

            // Username conflict → try a couple more usernames
            if (($resp->status() === 409) || $this->hasUsernameError($resp)) {
                for ($i = 0; $i < 2; $i++) {
                    $username = $this->topUpUsername($baseUsername); // regenerate short user
                    $payload['user']            = $username;
                    $payload['username']        = $username;

                    $service->webuzo_username = $username;
                    $service->save();

                    $resp = $req->post($url, $payload);
                    if ($resp->successful() && !$this->hasError($resp)) break;
                }
            }

            if (!$resp->successful()) {
                throw new \RuntimeException("Webuzo HTTP error: {$resp->status()} - " . Str::limit((string)$resp->body(), 600));
            }

            $data = $this->safeJson($resp);
            if ($this->hasError($resp, $data) || !$this->isSuccess($data, $username)) {
                // Improve credential hint if Webuzo reported Invalid Password
                $rawErr = isset($data['error'])
                    ? (is_array($data['error']) ? json_encode($data['error'], JSON_UNESCAPED_SLASHES) : (string)$data['error'])
                    : 'unknown error / unexpected response';

                if (stripos($rawErr, 'invalid password') !== false || stripos($rawErr, 'incorrect username') !== false) {
                    throw new \RuntimeException('Webuzo add_user failed: Invalid credentials (check .env admin user/pass and avoid putting credentials in URL).');
                }

                throw new \RuntimeException("Webuzo add_user failed: {$rawErr}");
            }

            // Success → flip states
            $service->status      = 'active';
            $service->enduser_url = (string) ($cfg['enduser_url'] ?? $service->enduser_url);

            if (Schema::hasColumn('services', 'panel_url')) {
                // Keep same as enduser_url unless you store a distinct per-service panel URL
                $service->panel_url = $service->enduser_url;
            }

            $service->save();

            $order->status = 'active';
            $order->save();

            Log::info("Provisioned Webuzo OK", [
                'order'   => $order->id,
                'user'    => $service->webuzo_username,
                'package' => $package,
                'domain'  => $domain,
            ]);
        } catch (Throwable $e) {
            // If already active, swallow
            $service->refresh();
            if (Str::lower((string)$service->status) === 'active') {
                Log::warning("ProvisionWebuzoAccount: exception but service active; swallow. msg=".$e->getMessage());
                return;
            }

            Log::error("ProvisionWebuzoAccount failed for order {$order->id}: ".$e->getMessage(), [
                'url'     => $url ?? null,
                'payload' => $this->mask($payload ?? []),
            ]);

            // small delay then let retry happen
            $this->release(30);
            throw $e;
        }
    }

    public function failed(Throwable $e): void
    {
        try {
            $order = Order::with('service')->find($this->orderId);
            if ($order?->service && Str::lower((string)$order->service->status) !== 'active') {
                $order->service->status = 'failed';
                $order->service->save();
            }
        } catch (Throwable) {
            // ignore
        }
        Log::error("ProvisionWebuzoAccount permanently failed for order {$this->orderId}: {$e->getMessage()}");
    }

    /* ----------------- Helpers ----------------- */

    protected function makeBaseUsername(string $seed): string
    {
        $base = Str::of($seed)
            ->ascii()
            ->lower()
            ->replace(' ', '')
            ->replaceMatches('/[^a-z0-9]/', '')
            ->value();
        $base = $base ?: 'user';
        if (!ctype_alpha($base[0] ?? '')) $base = 'u'.$base;
        return Str::limit($base, 6, ''); // leave room for 2-char suffix → ≤8 total
    }

    protected function topUpUsername(string $base): string
    {
        $need = max(0, 8 - strlen($base));
        $suffix = Str::lower(Str::random($need > 0 ? $need : 1));
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

    protected function safeJson($resp): array
    {
        try {
            // $resp can be Response|mixed; Laravel Http\Response has ->json()
            if (is_object($resp) && method_exists($resp, 'json')) {
                return $resp->json() ?? [];
            }
            if (is_string($resp)) {
                $arr = json_decode($resp, true);
                return is_array($arr) ? $arr : [];
            }
            return [];
        } catch (Throwable) {
            return [];
        }
    }

    protected function valToString(mixed $v): string
    {
        if (is_string($v)) return $v;
        if (is_scalar($v)) return (string) $v;

        if (is_array($v)) {
            foreach (['user','username','name','login','value'] as $k) {
                if (isset($v[$k]) && is_scalar($v[$k])) return (string)$v[$k];
            }
            $first = reset($v);
            if (is_scalar($first)) return (string)$first;
            return '';
        }

        if (is_object($v)) {
            foreach (['user','username','name','login','value'] as $prop) {
                if (isset($v->$prop) && is_scalar($v->$prop)) return (string)$v->$prop;
            }
            return '';
        }

        return '';
    }

    protected function hasError($resp, ?array $data = null): bool
    {
        $data ??= $this->safeJson($resp) ?: [];
        if (isset($data['error']) && !empty($data['error'])) return true;

        // Webuzo sometimes returns done:0 or done:{status:0}
        if (isset($data['done']) && !is_array($data['done']) && (int)$data['done'] === 0) return true;
        if (isset($data['done']) && is_array($data['done']) && isset($data['done']['status']) && (int)$data['done']['status'] === 0) return true;

        if (isset($data['success']) && ($data['success'] === false || (int)$data['success'] === 0)) return true;

        return false;
    }

    protected function isSuccess(array $data, string $expectUser): bool
    {
        // Accept {done:1} or {success:1}
        if (!empty($data['done']) && !is_array($data['done']) && (int)$data['done'] === 1) return true;
        if (!empty($data['success']) && (int)$data['success'] === 1) return true;

        // Accept {user:"..."} or shapes like {user:{name:"..."}} etc.
        if (!empty($data['user'])) {
            $u = $this->valToString($data['user']);
            if ($u !== '' && strcasecmp($u, $expectUser) === 0) return true;
        }

        // Accept {done:{status:1}} and/or {done:{user:"..."/{...}}}
        if (!empty($data['done']) && is_array($data['done'])) {
            if (isset($data['done']['status']) && (int)$data['done']['status'] === 1) return true;

            if (!empty($data['done']['user'])) {
                $u = $this->valToString($data['done']['user']);
                if ($u !== '' && strcasecmp($u, $expectUser) === 0) return true;
            }
        }

        return false;
    }

    protected function hasUsernameError($resp): bool
    {
        $data = $this->safeJson($resp);
        if (!isset($data['error'])) return false;
        $err = is_array($data['error']) ? json_encode($data['error']) : (string) $data['error'];
        return stripos($err, 'username') !== false
            || stripos($err, 'user already exists') !== false
            || stripos($err, 'exists') !== false;
    }

    protected function mask(array $payload): array
    {
        $masked = $payload;
        foreach (['pass','password','user_passwd','cnf_user_passwd'] as $k) {
            if (isset($masked[$k])) $masked[$k] = '***';
        }
        return $masked;
    }
}
