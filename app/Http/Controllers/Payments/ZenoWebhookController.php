<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Jobs\ProvisionWebuzoAccount;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ZenoWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // 1) Basic signature check (generic HMAC; rekebisha ikibidi kulingana na Zeno)
        $secret   = (string) config('services.zeno.webhook_secret');
        $payload  = $request->getContent();
        $provided = $request->header('X-Zeno-Signature'); // angalia docs zao
        $valid    = $secret ? hash_equals(hash_hmac('sha256', $payload, $secret), (string) $provided) : true;
        if (!$valid) {
            Log::warning('Zeno webhook invalid signature');
            return response()->json(['ok'=>false,'error'=>'invalid signature'], 400);
        }

        // 2) Soma event
        $data       = $request->json()->all();
        $orderUuid  = (string) data_get($data,'order_id') ?: (string) data_get($data,'order_uuid');
        $status     = Str::lower((string) data_get($data,'status'));        // e.g. "paid" / "succeeded"
        $amount     = (int) data_get($data,'amount');
        $payRef     = (string) data_get($data,'txn_ref') ?: (string) data_get($data,'payment_ref');

        if (!$orderUuid) {
            return response()->json(['ok'=>false,'error'=>'missing order id'], 422);
        }

        // 3) Tafuta order yetu
        $order = Order::query()
            ->where('order_uuid', $orderUuid)
            ->orWhere('id', (int) $orderUuid)
            ->first();

        if (!$order) {
            Log::warning('Zeno webhook: order not found', ['order_uuid'=>$orderUuid]);
            return response()->json(['ok'=>false], 200); // isirudishe retry bila mpango
        }

        // 4) Mark paid → dispatch auto-provision (idempotent)
        $PAID = ['paid','succeeded','complete','completed','success'];
        if (in_array($status, $PAID, true)) {
            $order->status      = 'paid';
            if ($amount > 0 && empty($order->price_tzs)) $order->price_tzs = $amount;
            if ($payRef) $order->payment_ref = $payRef;
            $order->save();

            // Dispatch only if haina service bado
            if (!$order->service()->exists()) {
                ProvisionWebuzoAccount::dispatch($order->id)->onQueue('provisioning');
                Log::info('Auto-provision dispatched from webhook', ['order_id'=>$order->id]);
            }
        }

        return response()->json(['ok'=>true]);
    }
}
