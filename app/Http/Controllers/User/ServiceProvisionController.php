<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Jobs\ProvisionWebuzoAccount;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ServiceProvisionController extends Controller
{
    public function provision(int $order)
    {
        $uid = Auth::id();
        if (!$uid) return back()->withErrors('You must be logged in.');

        $ord = Order::query()->where('id', $order)->where('user_id', $uid)->first();
        if (!$ord) return back()->withErrors('Order not found.');

        if (!$this->isEligible($ord->status)) {
            return back()->withErrors('This order is not paid yet.');
        }

        if ($this->serviceExistsForOrder($ord->id, $ord)) {
            return back()->with('status', 'Service already exists and will move to Active soon if still provisioning.');
        }

        // Throttle duplicate clicks (10 min) — file cache, no Redis
        if (!$this->throttleFile("prov:dispatch:order:{$ord->id}", 10)) {
            return back()->with('status', 'Provisioning already started. Please wait for status to update.');
        }

        // **LOCAL/DEV → run sync; PROD → queue**
        $this->dispatchProvisionSmart($ord->id);
        Log::info('Provisioning dispatched', ['order_id' => $ord->id, 'user_id' => $uid]);

        return back()->with('status', 'Provisioning started. This will update to “Provisioning…” then “Active”.');
    }

    public function provisionLatest()
    {
        $uid = Auth::id();
        if (!$uid) return back()->withErrors('You must be logged in.');

        $eligible = Order::query()
            ->where('user_id', $uid)
            ->whereIn('status', ['paid','succeeded','success','complete','completed','active']);

        if (method_exists(Order::class, 'service')) {
            $ord = (clone $eligible)->whereDoesntHave('service')->latest('id')->first();
        } else {
            if (Schema::hasTable('services') && Schema::hasColumn('services','order_id')) {
                $ord = (clone $eligible)
                    ->whereNotExists(function ($q) {
                        $q->from('services')->whereColumn('services.order_id','orders.id');
                    })
                    ->latest('id')->first();
            } else {
                $ord = (clone $eligible)->latest('id')->first();
            }
        }

        if (!$ord) return back()->withErrors('No paid order found to provision.');
        if ($this->serviceExistsForOrder($ord->id, $ord)) {
            return back()->with('status', 'Service already exists for your latest paid order.');
        }

        if (!$this->throttleFile("prov:dispatch:order:{$ord->id}", 10)) {
            return back()->with('status', 'Provisioning already started for your latest order. Please wait.');
        }

        $this->dispatchProvisionSmart($ord->id);
        Log::info('Provisioning dispatched (latest)', ['order_id' => $ord->id, 'user_id' => $uid]);

        return back()->with('status', 'Provisioning started. This will update to “Provisioning…” then “Active”.');
    }

    public function status()
    {
        $uid = Auth::id();
        if (!$uid) return response()->json(['error' => 'unauthorized'], 401);

        $q = Service::query()->where(function ($qq) use ($uid) {
            if (Schema::hasColumn('services', 'user_id')) {
                $qq->where('user_id', $uid);
            } else {
                $qq->whereHas('order', fn ($o) => $o->where('user_id', $uid));
            }
        });

        return response()->json([
            'active'       => (clone $q)->where('status', 'active')->count(),
            'provisioning' => (clone $q)->where('status', 'provisioning')->count(),
            'requested'    => (Schema::hasColumn('services','status') ? (clone $q)->where('status', 'requested')->count() : 0),
            'failed'       => (clone $q)->where('status', 'failed')->count(),
        ]);
    }

    /* ======================== Helpers ======================== */

    private function isEligible(string $status): bool
    {
        $s = strtolower($status);
        return in_array($s, ['paid','succeeded','success','complete','completed','active'], true);
    }

    private function serviceExistsForOrder(int $orderId, ?Order $ord = null): bool
    {
        if ($ord && method_exists($ord, 'service')) {
            $rel = $ord->service();
            return method_exists($rel, 'exists')
                ? (bool) $rel->exists()
                : (bool) ($ord->service?->id ?? false);
        }
        if (Schema::hasTable('services') && Schema::hasColumn('services','order_id')) {
            return Service::query()->where('order_id', $orderId)->exists();
        }
        return false;
    }

    private function throttleFile(string $key, int $minutes): bool
    {
        try {
            return Cache::store('file')->add($key, 1, now()->addMinutes($minutes));
        } catch (\Throwable $e) {
            Log::warning('Throttle(file) unavailable; allowing dispatch', ['key'=>$key,'msg'=>$e->getMessage()]);
            return true;
        }
    }

    /**
     * LOCAL/DEV: dispatchSync (immediate)
     * PROD: use queue connection (redis/database) if available.
     */
    private function dispatchProvisionSmart(int $orderId): void
    {
        $isLocal = app()->environment(['local', 'development', 'testing']);

        if ($isLocal) {
            // hakuna worker → run now
            ProvisionWebuzoAccount::dispatchSync($orderId);
            return;
        }

        // Production: chagua connection salama
        $defaultConn = config('queue.default', 'sync');
        $hasRedis    = class_exists('Redis') || class_exists('Predis\\Client');

        $useConn = $defaultConn;
        if ($defaultConn === 'redis' && !$hasRedis) {
            if (config('queue.connections.database') && Schema::hasTable('jobs')) {
                $useConn = 'database';
            } else {
                $useConn = 'sync';
            }
        }

        if ($useConn === 'sync') {
            ProvisionWebuzoAccount::dispatchSync($orderId);
        } else {
            ProvisionWebuzoAccount::dispatch($orderId)
                ->onConnection($useConn)
                ->onQueue('provisioning');
        }
    }
}
