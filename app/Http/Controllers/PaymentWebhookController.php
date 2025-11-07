<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    /**
     * Handle incoming ZenoPay webhook notification
     */
    public function handle(Request $request)
    {
        // Log the webhook for debugging
        Log::info('ZenoPay webhook received', [
            'ip' => $request->ip(),
            'body' => $request->all(),
            'headers' => $request->headers->all(),
        ]);

        // Extract payload
        $payload = $request->all();
        
        // Get order_id from various possible keys
        $orderId = Arr::get($payload, 'order_id') 
                ?? Arr::get($payload, 'data.order_id') 
                ?? Arr::get($payload, 'merchant_order_id')
                ?? Arr::get($payload, 'reference');

        if (!$orderId) {
            Log::warning('Webhook missing order_id', ['payload' => $payload]);
            return response()->json(['error' => 'Missing order_id'], 400);
        }

        // Find order by gateway_order_id
        $order = Order::where('gateway_order_id', $orderId)->first();

        if (!$order) {
            Log::warning('Order not found for webhook', ['order_id' => $orderId]);
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Extract payment status from various possible keys
        $rawStatus = strtolower((string) (
            Arr::get($payload, 'status') 
            ?? Arr::get($payload, 'data.status')
            ?? Arr::get($payload, 'state')
            ?? Arr::get($payload, 'data.state')
            ?? Arr::get($payload, 'payment_status')
            ?? 'unknown'
        ));

        // Extract transaction reference
        $transactionRef = Arr::get($payload, 'transaction_id')
                       ?? Arr::get($payload, 'data.transaction_id')
                       ?? Arr::get($payload, 'reference')
                       ?? Arr::get($payload, 'data.reference')
                       ?? Arr::get($payload, 'transaction_reference');

        // Map ZenoPay status to our order status
        $newStatus = $this->mapPaymentStatus($rawStatus);

        Log::info('Webhook processing', [
            'order_id' => $order->id,
            'current_status' => $order->status,
            'webhook_status' => $rawStatus,
            'mapped_status' => $newStatus,
            'transaction_ref' => $transactionRef,
        ]);

        // Only update if status changed
        if ($newStatus !== $order->status) {
            $oldStatus = $order->status;
            $order->status = $newStatus;
            
            if ($transactionRef) {
                $order->payment_ref = $transactionRef;
            }
            
            // Merge webhook data into gateway_meta
            $order->gateway_meta = $this->mergeMeta($order->gateway_meta, [
                'webhook' => $payload,
                'webhook_received_at' => now()->toIso8601String(),
                'status_updated_from' => $oldStatus,
            ]);
            
            $order->save();

            Log::info('Order status updated via webhook', [
                'order_id' => $order->id,
                'from' => $oldStatus,
                'to' => $newStatus,
            ]);

            // Record payment event
            $this->recordPaymentEvent($order, $payload, $rawStatus);

            // Trigger provisioning if paid
            if ($newStatus === 'paid' && !$order->service) {
                $this->triggerProvisioning($order);
            }
        }

        return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'status' => $order->status,
        ]);
    }

    /**
     * Map ZenoPay status variations to our order status
     */
    protected function mapPaymentStatus(string $status): string
    {
        $status = strtolower(trim($status));

        // Success states → paid
        $successStates = [
            'paid', 'success', 'successful', 'completed', 'complete', 
            'active', 'approved', 'confirmed'
        ];
        
        if (in_array($status, $successStates, true)) {
            return 'paid';
        }

        // Failure states → failed
        $failureStates = [
            'failed', 'cancelled', 'canceled', 'expired', 
            'rejected', 'declined', 'error'
        ];
        
        if (in_array($status, $failureStates, true)) {
            return 'failed';
        }

        // Pending states → pending
        $pendingStates = [
            'pending', 'processing', 'initiated', 'created', 'requested'
        ];
        
        if (in_array($status, $pendingStates, true)) {
            return 'pending';
        }

        // Unknown status - keep current
        return 'pending';
    }

    /**
     * Record payment event for audit trail
     */
    protected function recordPaymentEvent(Order $order, array $payload, string $rawStatus): void
    {
        try {
            PaymentEvent::create([
                'order_id' => $order->id,
                'event_type' => 'webhook',
                'status' => $rawStatus,
                'amount' => $order->price_tzs,
                'currency' => $order->currency,
                'provider' => $order->gateway_provider ?? 'unknown',
                'transaction_id' => $order->payment_ref,
                'raw_response' => $payload,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to create payment event', [
                'error' => $e->getMessage(),
                'order_id' => $order->id,
            ]);
        }
    }

    /**
     * Trigger provisioning job for paid order
     */
    protected function triggerProvisioning(Order $order): void
    {
        try {
            // Dispatch provisioning job (if job exists)
            if (class_exists(\App\Jobs\ProvisionServiceJob::class)) {
                \App\Jobs\ProvisionServiceJob::dispatch($order)
                    ->onQueue('provisioning');
                
                Log::info('Provisioning job dispatched', ['order_id' => $order->id]);
            }
        } catch (\Throwable $e) {
            Log::error('Failed to dispatch provisioning job', [
                'error' => $e->getMessage(),
                'order_id' => $order->id,
            ]);
        }
    }

    /**
     * Merge metadata arrays
     */
    protected function mergeMeta($current, array $new): array
    {
        $curr = is_array($current) ? $current : (is_string($current) ? json_decode($current, true) : []);
        if (!is_array($curr)) $curr = [];
        return array_merge($curr, $new);
    }
}
