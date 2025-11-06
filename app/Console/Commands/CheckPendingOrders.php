<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\ZenoPayClient;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class CheckPendingOrders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'orders:check-pending 
                            {--all : Check all pending orders, not just recent}
                            {--order= : Check specific order ID}';

    /**
     * The console command description.
     */
    protected $description = 'Check payment status of pending orders and update them';

    /**
     * Execute the console command.
     */
    public function handle(ZenoPayClient $zeno)
    {
        $this->info('🔍 Checking pending orders...');
        $this->newLine();

        // Get orders to check
        $query = Order::where('status', 'pending')
            ->whereNotNull('gateway_order_id');

        // Specific order
        if ($orderId = $this->option('order')) {
            $query->where('id', $orderId);
        } 
        // Recent orders only (last 24 hours)
        elseif (!$this->option('all')) {
            $query->where('created_at', '>=', now()->subDay());
        }

        $orders = $query->get();

        if ($orders->isEmpty()) {
            $this->warn('No pending orders found.');
            return 0;
        }

        $this->info("Found {$orders->count()} pending order(s) to check.");
        $this->newLine();

        $updated = 0;
        $failed = 0;
        $unchanged = 0;

        foreach ($orders as $order) {
            $this->line("Checking Order #{$order->id} (Gateway: {$order->gateway_order_id})...");

            try {
                // Call ZenoPay API
                $statusResp = $zeno->status($order->gateway_order_id);
                
                // Extract status
                $zenoStatus = strtolower((string) (Arr::get($statusResp, 'state') ?? Arr::get($statusResp, 'status', 'pending')));
                
                $this->line("  ZenoPay Status: {$zenoStatus}");

                // Update based on status
                if (in_array($zenoStatus, ['paid', 'success', 'completed', 'active'], true)) {
                    $order->status = 'paid';
                    $order->payment_ref = Arr::get($statusResp, 'transaction_id') ?? Arr::get($statusResp, 'reference') ?? $order->payment_ref;
                    $order->gateway_meta = $this->mergeMeta($order->gateway_meta, ['status_check' => $statusResp, 'checked_at' => now()]);
                    $order->save();
                    
                    $this->info("  ✅ Updated to PAID (Ref: {$order->payment_ref})");
                    $updated++;
                    
                } elseif (in_array($zenoStatus, ['failed', 'cancelled', 'expired'], true)) {
                    $order->status = 'failed';
                    $order->gateway_meta = $this->mergeMeta($order->gateway_meta, ['status_check' => $statusResp, 'checked_at' => now()]);
                    $order->save();
                    
                    $this->warn("  ❌ Updated to FAILED");
                    $failed++;
                    
                } else {
                    $this->line("  ⏳ Still PENDING");
                    $unchanged++;
                }
                
            } catch (\Throwable $e) {
                $this->error("  ❌ Error: " . $e->getMessage());
                $failed++;
            }

            $this->newLine();
        }

        // Summary
        $this->newLine();
        $this->info('=== Summary ===');
        $this->line("✅ Updated to PAID: {$updated}");
        $this->line("❌ Updated to FAILED: {$failed}");
        $this->line("⏳ Still PENDING: {$unchanged}");

        return 0;
    }

    protected function mergeMeta($current, array $new): array
    {
        $curr = is_array($current) ? $current : (is_string($current) ? json_decode($current, true) : []);
        if (!is_array($curr)) $curr = [];
        return array_merge($curr, $new);
    }
}
