<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Service;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // -------- Quick stats (QUALIFIED COLUMNS) --------
        $stats = [
            'users'        => User::count(),
            'plans'        => Plan::count(),
            'orders_total' => Order::count(),

            'active'       => Service::where('services.status', 'active')->count(),
            'pending'      => Order::where('orders.status', 'pending')->count(),
            'failed'       => Order::where('orders.status', 'failed')->count(),

            // Revenue ya orders zilizolipwa/active
            'revenue_tzs'  => (int) Order::whereIn('orders.status', ['paid', 'active'])
                                ->sum('orders.price_tzs'),

            // MRR makisio: services active × price ya order yake
            'mrr_tzs'      => (int) Service::query()
                                ->where('services.status', 'active')
                                ->join('orders', 'services.order_id', '=', 'orders.id')
                                ->sum('orders.price_tzs'),
        ];

        // -------- Top plans (kwa orders zilizolipwa/active) --------
        $topPlans = Plan::select('id', 'name', 'slug', 'price_tzs')
            ->withCount([
                'orders as paid_orders_count' => function ($q) {
                    $q->whereIn('orders.status', ['paid', 'active']); // qualify
                }
            ])
            ->orderByDesc('paid_orders_count')
            ->take(5)
            ->get();

        // -------- Recent users --------
        $recentUsers = User::select('id', 'name', 'email', 'role', 'created_at')
            ->latest('id')
            ->take(6)
            ->get();

        // -------- Recent items (orders & services) --------
        $recentOrders = Order::with([
                'plan:id,name,slug',
                'user:id,name,email',
            ])
            ->latest('id')
            ->take(8)
            ->get();

        $recentServices = Service::with([
                'order:id,plan_id,user_id,domain,status',
                'order.plan:id,name,slug',
                'order.user:id,name,email',
            ])
            ->latest('id')
            ->take(8)
            ->get();

        // -------- Charts: siku 14 zilizopita --------
        $from = Carbon::now()->subDays(13)->startOfDay(); // 14 points inc. leo
        $to   = Carbon::now()->endOfDay();

        // Orders/day
        $ordersPerDay = Order::whereBetween('orders.created_at', [$from, $to])
            ->selectRaw('DATE(orders.created_at) as d, COUNT(*) as c')
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('c', 'd')
            ->all();

        // Revenue/day (paid/active)
        $revenuePerDay = Order::whereBetween('orders.created_at', [$from, $to])
            ->whereIn('orders.status', ['paid', 'active'])
            ->selectRaw('DATE(orders.created_at) as d, SUM(orders.price_tzs) as s')
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('s', 'd')
            ->all();

        // Normalize to full date range
        $dates = [];
        $cursor = $from->copy();
        while ($cursor->lte($to)) {
            $dates[] = $cursor->toDateString();
            $cursor->addDay();
        }
        $chart = [
            'labels'   => $dates,
            'orders'   => array_map(fn($d) => (int)($ordersPerDay[$d] ?? 0), $dates),
            'revenue'  => array_map(fn($d) => (int)($revenuePerDay[$d] ?? 0), $dates),
        ];

        return view('admin.dashboard', [
            'stats'          => $stats,
            'recentOrders'   => $recentOrders,
            'recentServices' => $recentServices,
            'topPlans'       => $topPlans,
            'recentUsers'    => $recentUsers,
            'chart'          => $chart,
        ]);
    }
}
