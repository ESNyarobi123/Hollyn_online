<?php
namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class ZenoPayClient
{
    protected function base(): string { return rtrim((string) config('services.zeno.base'), '/'); }
    protected function key(): string  { return (string) config('services.zeno.key'); }

    public function start(array $payload): array
    {
        $p = $this->normalizePayload($payload);

        // Basic sanity checks (helpful error before hitting gateway)
        foreach (['order_id','amount','currency','buyer_name','buyer_email','buyer_phone'] as $k) {
            if (!isset($p[$k]) || $p[$k] === '') {
                throw new \InvalidArgumentException("Missing required field: {$k}");
            }
        }

        $headers = [
            'x-api-key'        => $this->key(),
            'Idempotency-Key'  => $p['order_id'], // prevent duplicates on retries
            'User-Agent'       => 'Unida/Checkout (Laravel HTTP Client)',
            'Accept'           => 'application/json',
        ];

        return Http::withHeaders($headers)
            ->asJson()
            ->timeout(20)            // total timeout
            ->connectTimeout(10)     // TCP connect timeout
            ->retry(3, 200, function ($exception, $request) {
                // retry on 429 or 5xx or timeouts
                if (method_exists($exception, 'response') && $exception->response()) {
                    $code = $exception->response()->status();
                    return $code === 429 || ($code >= 500 && $code <= 599);
                }
                // network/timeouts -> retry
                return true;
            }, throw:false)
            ->post($this->base().'/payments/mobile_money_tanzania', $p)
            ->throw() // if still failing after retries
            ->json();
    }

    public function status(string $gatewayOrderId): array
    {
        $headers = [
            'x-api-key'   => $this->key(),
            'User-Agent'  => 'Unida/Checkout (Laravel HTTP Client)',
            'Accept'      => 'application/json',
        ];

        return Http::withHeaders($headers)
            ->timeout(15)
            ->connectTimeout(8)
            ->retry(3, 200, fn($e) => true, throw:false)
            ->get($this->base().'/order-status', ['order_id'=>$gatewayOrderId])
            ->throw()
            ->json();
    }

    /* --------------------------- helpers --------------------------- */

    /**
     * Accepts various input keys, outputs the exact shape the gateway expects.
     * - Maps customer_* / payer_phone -> buyer_*
     * - Ensures buyer_phone is 2556/2557xxxxxxxx (no '+')
     * - Normalizes provider label
     * - Drops null/empty values
     */
    protected function normalizePayload(array $in): array
    {
        $out = [
            'order_id'     => Arr::get($in, 'order_id') ?? Arr::get($in, 'gateway_order_id'),
            'amount'       => (int) Arr::get($in, 'amount', 0),
            'currency'     => strtoupper((string) Arr::get($in, 'currency', 'TZS')),
            'description'  => Arr::get($in, 'description'),
            'callback_url' => Arr::get($in, 'callback_url'),
        ];

        // Buyer name/email
        $out['buyer_name']  = Arr::get($in, 'buyer_name')
            ?? Arr::get($in, 'customer_name')
            ?? Arr::get($in, 'name');

        $out['buyer_email'] = Arr::get($in, 'buyer_email')
            ?? Arr::get($in, 'customer_email')
            ?? Arr::get($in, 'email');

        // Phone: prefer buyer_phone then payer_phone then customer_phone
        $rawPhone = Arr::get($in, 'buyer_phone')
            ?? Arr::get($in, 'payer_phone')
            ?? Arr::get($in, 'customer_phone');

        $msisdn = $this->toMsisdn255($rawPhone);
        if ($msisdn) {
            $out['buyer_phone'] = $msisdn;  // 2557/2556xxxxxxxx
        }

        // Provider
        $prov = Arr::get($in, 'provider');
        if ($prov) {
            $out['provider'] = $this->normalizeProvider($prov);
        }

        // Optional passthrough (if present)
        if ($meta = Arr::get($in, 'metadata')) {
            $out['metadata'] = $meta;
        }

        // Remove null/empty strings
        foreach ($out as $k => $v) {
            if ($v === null || $v === '') unset($out[$k]);
        }

        return $out;
    }

    /**
     * Convert many TZ formats to 2556/2557xxxxxxxx (E.164 without '+').
     */
    protected function toMsisdn255(?string $raw): ?string
    {
        if (!$raw) return null;
        $raw = trim($raw);

        // +2557/6xxxxxxxx
        if (preg_match('/^\+255([6-7]\d{8})$/', $raw, $m)) {
            return '255'.$m[1];
        }

        // remove non-digits
        $digits = preg_replace('/\D+/', '', $raw);
        if (!$digits) return null;

        // 07/06xxxxxxxx
        if (preg_match('/^0([6-7]\d{8})$/', $digits, $m)) {
            return '255'.$m[1];
        }
        // 2557/6xxxxxxxx
        if (preg_match('/^255([6-7]\d{8})$/', $digits, $m)) {
            return '255'.$m[1];
        }
        // 002557/6xxxxxxxx
        if (preg_match('/^00255([6-7]\d{8})$/', $digits, $m)) {
            return '255'.$m[1];
        }
        return null;
    }

    protected function normalizeProvider(string $p): string
    {
        $p = strtoupper(trim($p));
        // Accept aliases
        if (in_array($p, ['M-PESA','MPESA','VODACOM','VODA'], true)) return 'M-PESA';
        if (in_array($p, ['TIGO-PESA','TIGOPESA','TIGO','HALOTEL','TTCL'], true)) return 'TIGO-PESA';
        if (in_array($p, ['AIRTEL-MONEY','AIRTELMONEY','AIRTEL'], true)) return 'AIRTEL-MONEY';
        return 'M-PESA'; // sane default
    }
}
