<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WebuzoClient
{
    // -----------------------------
    // Core helpers
    // -----------------------------
    protected function base(): string
    {
        return rtrim((string) config('services.webuzo.api_url'), '/');
    }

    protected function verifySsl(): bool
    {
        return (bool) config('services.webuzo.verify_ssl', true);
    }

    protected function timeout(): int
    {
        return (int) config('services.webuzo.timeout', 90);
    }

    protected function connectTimeout(): int
    {
        return (int) config('services.webuzo.connect_timeout', 10);
    }

    protected function attachAuth($req)
    {
        $mode = (string) config('services.webuzo.auth', 'key'); // 'key' | 'basic'

        if ($mode === 'key') {
            $key   = (string) config('services.webuzo.api_key');
            $hdr   = (string) config('services.webuzo.key_header', 'Authorization');
            $pref  = (string) config('services.webuzo.key_prefix', 'Bearer');
            $value = trim($pref) !== '' ? ($pref.' '.$key) : $key;

            return $req->withHeaders([$hdr => $value]);
        }

        // basic (also used by some Webuzo installs)
        return $req->withBasicAuth(
            (string) config('services.webuzo.admin_user'),
            (string) config('services.webuzo.admin_pass')
        );
    }

    protected function http()
    {
        $req = Http::asForm()
            ->timeout($this->timeout())
            ->connectTimeout($this->connectTimeout());

        if (!$this->verifySsl()) {
            $req = $req->withoutVerifying();
        }

        return $this->attachAuth($req);
    }

    protected function join(string $path): string
    {
        return $this->base().'/'.ltrim($path, '/');
    }

    /**
     * Call Webuzo Admin API action via index.php?api=json&act=...
     * (Useful when you know the `act` exactly)
     */
    public function callAct(string $act, array $params = [], string $method = 'POST'): array
    {
        $path = 'index.php?api=json&act='.$act;
        $url  = $this->join($path);

        $req = $this->http();
        $res = strtoupper($method) === 'GET'
            ? $req->get($url, $params)
            : $req->post($url, $params);

        return $res->throw()->json();
    }

    // -----------------------------
    // High-level helpers (paths from config)
    // -----------------------------

    /**
     * Create end-user account.
     * Uses config('services.webuzo.create_path') if provided,
     * otherwise falls back to act=add_user.
     *
     * Common params (adjust to your Webuzo version):
     * - username, email, password, domain, package
     * - ip, ns1, ns2 (optional)
     */
    public function createUser(array $payload): array
    {
        $payload = $this->normalizeCreatePayload($payload);

        $createPath = config('services.webuzo.create_path'); // e.g. /index.php?api=json&act=add_user
        if ($createPath) {
            $url = $this->join($createPath);
            return $this->http()->post($url, $payload)->throw()->json();
        }

        // default via act
        return $this->callAct('add_user', $payload, 'POST');
    }

    /**
     * Assign a package/plan to an existing username.
     * If your Webuzo uses a different action name, change `assign_plan_path` or the `act`.
     *
     * Expected $payload keys:
     * - username, package
     */
    public function assignPlan(array $payload): array
    {
        $username = (string) ($payload['username'] ?? '');
        $package  = (string) ($payload['package'] ?? $payload['plan'] ?? '');

        if ($username === '' || $package === '') {
            throw new \InvalidArgumentException('assignPlan requires username and package.');
        }

        $assignPath = config('services.webuzo.assign_plan_path'); // optional
        if ($assignPath) {
            $url = $this->join($assignPath);
            return $this->http()->post($url, [
                'username' => $username,
                'package'  => $package,
            ])->throw()->json();
        }

        // fallback act name (adjust if your Webuzo differs)
        return $this->callAct('assign_plan', [
            'username' => $username,
            'package'  => $package,
        ], 'POST');
    }

    /**
     * Add a domain to an existing user.
     * Expected $payload keys:
     * - username, domain
     */
    public function addDomain(array $payload): array
    {
        $username = (string) ($payload['username'] ?? '');
        $domain   = $this->normalizeDomain($payload['domain'] ?? '');

        if ($username === '' || $domain === '') {
            throw new \InvalidArgumentException('addDomain requires username and domain.');
        }

        $addDomainPath = config('services.webuzo.add_domain_path'); // optional
        if ($addDomainPath) {
            $url = $this->join($addDomainPath);
            return $this->http()->post($url, [
                'username' => $username,
                'domain'   => $domain,
            ])->throw()->json();
        }

        // fallback act name (adjust if your Webuzo differs)
        return $this->callAct('add_domain', [
            'username' => $username,
            'domain'   => $domain,
        ], 'POST');
    }

    // -----------------------------
    // Utilities
    // -----------------------------
    protected function normalizeCreatePayload(array $in): array
    {
        // Map common aliases → canonical names
        $u  = (string) ($in['username'] ?? $in['user'] ?? '');
        $em = (string) ($in['email'] ?? '');
        $pw = (string) ($in['password'] ?? $in['pass'] ?? '');
        $dm = $this->normalizeDomain($in['domain'] ?? ($in['primary_domain'] ?? ''));
        $pkg= (string) ($in['package'] ?? $in['plan'] ?? '');

        if ($u === '' || $em === '' || $pw === '' || $dm === '') {
            throw new \InvalidArgumentException('createUser requires username, email, password and domain.');
        }

        // Defaults from config
        $ip  = (string) config('services.webuzo.default_ip', '');
        $ns1 = (string) config('services.webuzo.ns1', '');
        $ns2 = (string) config('services.webuzo.ns2', '');

        // If no package provided, use default package from config
        if ($pkg === '') {
            $pkg = (string) config('services.webuzo.default_package', 'starter');
        }

        return array_filter([
            'username' => $u,
            'email'    => $em,
            'password' => $pw,
            'domain'   => $dm,
            'package'  => $pkg,
            'ip'       => $ip ?: null,
            'ns1'      => $ns1 ?: null,
            'ns2'      => $ns2 ?: null,
        ], fn($v) => !is_null($v));
    }

    protected function normalizeDomain(string $d): string
    {
        $d = strtolower(trim($d));
        $d = preg_replace('#^https?://#', '', $d);
        return rtrim($d, '/');
    }

    // -----------------------------
    // Optional: resolve package from Plan using config/webuzo_packages.php
    // -----------------------------
    public function resolvePackageForPlan(?\App\Models\Plan $plan): array
    {
        $map = config('webuzo_packages', []);

        if (!$plan) {
            return $map['_default'] ?? ['package' => 'starter', 'limits' => []];
        }

        $keys = [
            'id:'.$plan->id,
            'slug:'.$plan->slug,
            'name:'.$plan->name,
        ];

        foreach ($keys as $k) {
            if (isset($map[$k])) {
                return $map[$k];
            }
        }

        return $map['_default'] ?? ['package' => 'starter', 'limits' => []];
    }
}
