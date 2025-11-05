<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $uid = Auth::id();
        if (!$uid) return redirect()->route('login');

        // ---- Normalized statuses ----
        $PAID   = ['paid','active','complete','succeeded'];
        $ACTIVE = ['active','running'];
        $PROV   = ['provisioning','pending_provision','pending','queued'];
        $FAILED = ['failed','cancelled','canceled'];

        // -------- Orders base --------
        $ordersUser = Order::query()->where('user_id', $uid);

        // -------- Services base (robust: user_id OR order->user_id) --------
        $servicesBase = Service::query();
        $servicesBase->where(function ($q) use ($uid) {
            if (Schema::hasColumn('services','user_id')) {
                $q->where('user_id', $uid);
            }
            $q->orWhereHas('order', fn($qq) => $qq->where('user_id', $uid));
        });

        // -------- Aggregates --------
        $ordersTotal  = (clone $ordersUser)->count();
        $ordersPaid   = (clone $ordersUser)->whereIn('status', $PAID)->count();
        $ordersFailed = (clone $ordersUser)->whereIn('status', $FAILED)->count();

        $servicesActive = (clone $servicesBase)
            ->whereIn(DB::raw("LOWER(TRIM(status))"), $ACTIVE)
            ->count();

        $servicesProvisioning = (clone $servicesBase)
            ->whereIn(DB::raw("LOWER(TRIM(status))"), $PROV)
            ->count();

        $revenueTzs = (int) (clone $ordersUser)
            ->whereIn('status', $PAID)
            ->sum(DB::raw('COALESCE(price_tzs,0)'));

        $lastPaidOrder = (clone $ordersUser)
            ->whereIn('status', $PAID)
            ->latest('id')
            ->select('id','price_tzs','created_at','plan_id')
            ->first();

        // -------- Lists --------
        // Panga active kwanza kisha nyingine, kisha latest
        $services = (clone $servicesBase)
            ->with(['plan:id,name,slug','order:id,user_id,plan_id,status'])
            ->orderByRaw("CASE WHEN LOWER(TRIM(status)) IN ('active','running') THEN 0 ELSE 1 END")
            ->latest('id')
            ->get();

        $recentOrders = (clone $ordersUser)
            ->with(['plan:id,name'])
            ->latest('id')
            ->take(6)
            ->get();

        // -------- Provisionable: latest PAID order without service --------
        $provisionableOrder = null;
        if (Schema::hasTable('services')) {
            $provisionableOrder = (clone $ordersUser)
                ->with(['service','plan:id,name'])
                ->whereIn('status', $PAID)
                ->whereDoesntHave('service')
                ->latest('id')
                ->first();
        }

        // -------- Panel URL (prefer service enduser/panel URL; fallback SSO if enabled) --------
        $ssoEnabled = (bool) (config('services.webuzo.sso_enabled') ?? false);
        $ssoRoute   = ($ssoEnabled && \Illuminate\Support\Facades\Route::has('me.panel'))
                        ? route('me.panel')
                        : null;

        $latestActive = $services
            ->first(fn($s) => in_array(strtolower(trim((string)$s->status)), $ACTIVE));

        $panelUrl = $latestActive
            ? ($latestActive->enduser_url ?: ($latestActive->panel_url ?? null))
            : null;

        if (!$panelUrl) {
            $panelUrl = $ssoRoute; // tumia SSO tu kama hakuna URL ya service
        }

        // -------- Stats --------
        $stats = [
            'orders_total'          => $ordersTotal,
            'orders_paid'           => $ordersPaid,
            'orders_failed'         => $ordersFailed,
            'services_active'       => $servicesActive,
            'services_provisioning' => $servicesProvisioning,
            'revenue_tzs'           => $revenueTzs,
            'last_payment_at'       => $lastPaidOrder?->created_at,
            'last_payment_amount'   => (int)($lastPaidOrder->price_tzs ?? 0),
        ];

        // -------- CTA helpers --------
        $cta = [
            'show_finish_setup' => (bool) $provisionableOrder,
            'show_upgrade'      => !$provisionableOrder && $servicesActive === 0,
            'panel_url'         => $panelUrl,
            'sso_enabled'       => $ssoEnabled,
        ];

        return view('user.dashboard', [
            'stats'               => $stats,
            'services'            => $services,
            'recentOrders'        => $recentOrders,
            'hasActiveService'    => $servicesActive > 0,
            'provisionableOrder'  => $provisionableOrder,
            'cta'                 => $cta,
        ]);
    }
}
