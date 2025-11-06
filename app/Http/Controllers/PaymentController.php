<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\ZenoPayClient;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Client\RequestException;

class PaymentController extends Controller
{
    public function start(Order $order, ZenoPayClient $zeno, Request $req)
    {
        // Kama si pending/failed, mpeleke summary
        if (!in_array($order->status, ['pending','failed'], true)) {
            return redirect()->route('order.summary', $order->id)
                ->with('status', 'Order is already '.$order->status.'.');
        }

        // 1) gateway_order_id ya kudumu
        if (empty($order->gateway_order_id)) {
            $order->gateway_order_id = 'ORD-'.Str::ulid();
            $order->save();
        }

        // 2) Phone normalization -> 2556/2557xxxxxxxx (E.164 bila +)
        //    Fallback: request('phone') -> payer_phone -> customer_phone
        $rawPhone = $req->input('phone') ?: ($order->payer_phone ?: $order->customer_phone);
        $phone    = $this->normalizeMsisdn($rawPhone);
        if (!$phone) {
            return back()->withErrors([
                'phone' => 'Invalid phone number. Tafadhali tumia 07xxxxxxxx / 06xxxxxxxx au 2557xxxxxxxx / 2556xxxxxxxx (pia 00255/ +255 zinakubalika).',
            ])->withInput();
        }
        $order->payer_phone = $phone; // persist for next time

        // 3) Provider
        $provider = $this->detectProvider($phone, $req->input('provider'));
        $order->gateway_provider = $provider;
        $order->save();

        // 4) Build payload (ONYESHA buyer_* kutoka Order ili ZenoPayClient asilalamike)
        $payload = [
            'order_id'     => $order->gateway_order_id,
            'amount'       => (int) $order->price_tzs,
            'currency'     => 'TZS',
            'description'  => 'Order #'.$order->id,
            'callback_url' => route('webhooks.zeno'),

            // muhimu kwa client/gateway
            'buyer_name'   => $order->customer_name ?: 'Customer '.$order->id,
            'buyer_email'  => $order->customer_email ?: 'no-email@example.com',
            'buyer_phone'  => $phone,            // 2557/2556xxxxxxxx
            'provider'     => $provider,         // 'M-PESA'|'TIGO-PESA'|'AIRTEL-MONEY'
        ];

        try {
            $resp = $zeno->start($payload); // throws on 4xx/5xx
            $order->gateway_meta = $this->mergeMeta($order->gateway_meta, ['start'=>$resp]);
            $order->status = 'pending';
            $order->save();

            return redirect()->route('order.summary', $order->id)
                ->with('status', 'STK push imetumwa. Thibitisha kwenye simu.');

        } catch (RequestException $e) {
            $body = $e->response?->json() ?? [];

            // Duplicate order_id → check status
            $orderIdErrors = Arr::get($body, 'errors.order_id', []);
            $blob = strtolower(json_encode($orderIdErrors).(Arr::get($body,'message','')));
            if (str_contains($blob, 'exist') || str_contains($blob, 'already')) {
                try {
                    $st = $zeno->status($order->gateway_order_id);
                    $order->gateway_meta = $this->mergeMeta($order->gateway_meta, ['status'=>$st]);
                    $state = strtolower((string) (Arr::get($st,'state') ?? Arr::get($st,'status','pending')));

                    if (in_array($state, ['paid','success','completed','active'], true)) {
                        $order->status = 'paid';
                    } elseif (in_array($state, ['failed','cancelled'], true)) {
                        $order->status = 'failed';
                    } else {
                        $order->status = 'pending';
                    }
                    $order->save();

                    return redirect()->route('order.summary', $order->id)
                        ->with('status', 'Gateway status refreshed (duplicate prevented).');
                } catch (\Throwable $ex) {
                    return back()->withErrors([
                        'gateway' => 'Gateway status check failed: '.$ex->getMessage()
                    ]);
                }
            }

            // Vinginevyo, rudisha validation details vizuri
            $human = (Arr::get($body,'message','Payment failed'))
                     .' — '.json_encode(Arr::get($body,'errors',[]));
            return back()->withErrors(['gateway' => $human]);
        }
    }

    // -------- helpers (bila mabadiliko makubwa) --------
    private function normalizeMsisdn(?string $raw): ?string
    {
        if (!$raw) return null;
        $trimmed = trim($raw);
        if (preg_match('/^\+255([6-7]\d{8})$/', $trimmed, $m)) return '255'.$m[1];
        $digits = preg_replace('/\D+/', '', $trimmed);
        if (!$digits) return null;
        if (preg_match('/^0([6-7]\d{8})$/', $digits, $m))    return '255'.$m[1];
        if (preg_match('/^255([6-7]\d{8})$/', $digits, $m))  return '255'.$m[1];
        if (preg_match('/^00255([6-7]\d{8})$/', $digits, $m))return '255'.$m[1];
        return null;
    }

    private function detectProvider(string $msisdn, ?string $hint): string
    {
        if ($hint) return strtoupper($hint);
        $p2 = substr($msisdn, 3, 2);
        $p3 = substr($msisdn, 3, 3);
        $vodacom  = ['074','075','076'];
        $airtel   = ['078','079'];
        $tigoLike = ['062','063','065','066','067','068','069','071','073','077'];
        if (in_array($p3, $vodacom, true)) return 'M-PESA';
        if (in_array($p3, $airtel, true))  return 'AIRTEL-MONEY';
        if (in_array($p3, $tigoLike, true))return 'TIGO-PESA';
        return match ($p2) {
            '71','74','75','76'       => 'M-PESA',
            '65','66','67','68','69'  => 'TIGO-PESA',
            '78','79'                 => 'AIRTEL-MONEY',
            default                   => 'M-PESA',
        };
    }

    /**
     * Poll payment status (for frontend polling)
     */
    public function pollStatus(Order $order, ZenoPayClient $zeno)
    {
        // Return current order status if already terminal
        if (in_array($order->status, ['paid', 'active', 'complete', 'succeeded', 'failed', 'cancelled'], true)) {
            return response()->json([
                'status' => $order->status,
                'is_paid' => in_array($order->status, ['paid', 'active', 'complete', 'succeeded'], true),
                'is_terminal' => true,
                'message' => 'Payment ' . $order->status,
            ]);
        }

        // Check with ZenoPay if we have gateway_order_id
        if (!empty($order->gateway_order_id)) {
            try {
                $statusResp = $zeno->status($order->gateway_order_id);
                
                // Extract status from ZenoPay response
                $zenoStatus = strtolower((string) (Arr::get($statusResp, 'state') ?? Arr::get($statusResp, 'status', 'pending')));
                
                // Update order based on ZenoPay status
                if (in_array($zenoStatus, ['paid', 'success', 'completed', 'active'], true)) {
                    $order->status = 'paid';
                    $order->payment_ref = Arr::get($statusResp, 'transaction_id') ?? Arr::get($statusResp, 'reference');
                    $order->gateway_meta = $this->mergeMeta($order->gateway_meta, ['status_check' => $statusResp]);
                    $order->save();
                    
                    return response()->json([
                        'status' => 'paid',
                        'is_paid' => true,
                        'is_terminal' => true,
                        'message' => 'Payment confirmed!',
                        'payment_ref' => $order->payment_ref,
                    ]);
                    
                } elseif (in_array($zenoStatus, ['failed', 'cancelled', 'expired'], true)) {
                    $order->status = 'failed';
                    $order->gateway_meta = $this->mergeMeta($order->gateway_meta, ['status_check' => $statusResp]);
                    $order->save();
                    
                    return response()->json([
                        'status' => 'failed',
                        'is_paid' => false,
                        'is_terminal' => true,
                        'message' => 'Payment failed',
                    ]);
                }
                
            } catch (\Throwable $e) {
                // Continue with current status if API call fails
            }
        }

        // Return pending status
        return response()->json([
            'status' => $order->status,
            'is_paid' => false,
            'is_terminal' => false,
            'message' => 'Checking payment status...',
        ]);
    }

    private function mergeMeta($current, array $new): array
    {
        $curr = is_array($current) ? $current : (is_string($current) ? json_decode($current, true) : []);
        if (!is_array($curr)) $curr = [];
        return array_merge($curr, $new);
    }
}
