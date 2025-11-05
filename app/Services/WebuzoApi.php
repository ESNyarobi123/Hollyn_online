<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WebuzoApi
{
    protected function base(): string
    {
        return rtrim((string) config('services.webuzo.api_url'), '/');
    }

    protected function client()
    {
        $req = Http::timeout((int) config('services.webuzo.timeout', 90))
            ->connectTimeout((int) config('services.webuzo.connect_timeout', 10))
            ->when(!config('services.webuzo.verify_ssl', false), fn($h) => $h->withoutVerifying());

        // Basic auth (root + pass) kama ilivyo kwenye .env yako
        if (config('services.webuzo.use_basic_auth', true)) {
            $req = $req->withBasicAuth(
                (string) config('services.webuzo.admin_user'),
                (string) config('services.webuzo.admin_pass')
            );
        }

        // Bearer key kama ipo
        if ($key = config('services.webuzo.api_key')) {
            $req = $req->withHeaders(['Authorization' => 'Bearer '.$key]);
        }

        return $req;
    }

    public function createUser(string $email, string $username, string $password, string $package, array $limits = []): array
    {
        // Webuzo add_user endpoint
        $url = $this->base().config('services.webuzo.create_path', '/index.php?api=json&act=add_user');

        $payload = array_filter([
            'email'    => $email,
            'user'     => $username,
            'pass'     => $password,
            'package'  => $package,
            'ip'       => config('services.webuzo.default_ip'),
            'dns1'     => config('services.webuzo.ns1'),
            'dns2'     => config('services.webuzo.ns2'),
            // limits (ukizihitaji)
            'disk'     => $limits['disk_mb']     ?? null,
            'bandwidth'=> $limits['bandwidth_mb']?? null,
            'addons'   => $limits['addons']      ?? null,
            'emails'   => $limits['emails']      ?? null,
            'dbs'      => $limits['dbs']         ?? null,
            'ftp'      => $limits['ftp']         ?? null,
        ], fn($v) => !is_null($v));

        $resp = $this->client()->asForm()->post($url, $payload);

        return [
            'ok'      => $resp->successful() && !data_get($resp->json(),'error'),
            'status'  => $resp->status(),
            'body'    => $resp->json(),
        ];
    }

    public function addDomain(string $username, string $domain): array
    {
        // mfano endpoint; weka act sahihi ya Webuzo (adddomain / adddns nk)
        $url = $this->base().'/index.php?api=json&act=adddomain';
        $resp = $this->client()->asForm()->post($url, [
            'user'   => $username,
            'domain' => $domain,
        ]);

        return [
            'ok'     => $resp->successful() && !data_get($resp->json(),'error'),
            'status' => $resp->status(),
            'body'   => $resp->json(),
        ];
    }
}
