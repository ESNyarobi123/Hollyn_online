@extends('layouts.admin')
@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="modern-card p-8 gradient-bg-1 text-white overflow-hidden relative animate-fade-in-up">
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Welcome back, {{ Auth::user()->name }}! 👋</h1>
                    <p class="text-white/80 text-lg">Here's what's happening with your platform today.</p>
                </div>
                <div class="hidden lg:block">
                    <div class="w-24 h-24 rounded-full bg-white/10 flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-chart-line text-5xl"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-white/5 rounded-full -ml-48 -mb-48"></div>
    </div>
    
    <!-- KPI Cards with Unique Colors -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Orders - Purple/Indigo -->
        <div class="modern-card p-6 stats-card animate-fade-in-up bg-gradient-to-br from-indigo-500 to-purple-600 text-white border-0" style="animation-delay: 0.1s">
            <div class="flex items-center justify-between mb-4">
                <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-2xl text-white"></i>
                </div>
                <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-xs font-bold">
                    <i class="fas fa-arrow-up mr-1"></i> +12%
                </span>
            </div>
            <h3 class="text-white/80 text-sm font-medium mb-1">Total Orders</h3>
            <p class="text-4xl font-extrabold text-white mb-2">{{ number_format($stats['orders_total'] ?? 0) }}</p>
            <div class="flex items-center text-sm text-white/90">
                <i class="fas fa-check-circle mr-2"></i>
                <span>Paid: <strong>{{ number_format($stats['orders_paid'] ?? 0) }}</strong></span>
            </div>
    </div>

        <!-- Active Services - Green/Emerald -->
        <div class="modern-card p-6 stats-card animate-fade-in-up bg-gradient-to-br from-emerald-500 to-teal-600 text-white border-0" style="animation-delay: 0.2s">
            <div class="flex items-center justify-between mb-4">
                <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                    <i class="fas fa-server text-2xl text-white"></i>
                </div>
                <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-xs font-bold">
                    <i class="fas fa-check mr-1"></i> Active
                </span>
            </div>
            <h3 class="text-white/80 text-sm font-medium mb-1">Active Services</h3>
            <p class="text-4xl font-extrabold text-white mb-2">{{ number_format($stats['active'] ?? 0) }}</p>
            <div class="flex items-center text-sm text-white/90">
                <i class="fas fa-clock mr-2"></i>
                <span>Pending: <strong>{{ number_format($stats['pending'] ?? 0) }}</strong></span>
    </div>
  </div>

        <!-- Total Revenue - Orange/Red -->
        <div class="modern-card p-6 stats-card animate-fade-in-up bg-gradient-to-br from-orange-500 to-red-600 text-white border-0" style="animation-delay: 0.3s">
            <div class="flex items-center justify-between mb-4">
                <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                    <i class="fas fa-coins text-2xl text-white"></i>
                </div>
                <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-xs font-bold">
                    <i class="fas fa-fire mr-1"></i> Hot
                </span>
      </div>
            <h3 class="text-white/80 text-sm font-medium mb-1">Total Revenue</h3>
            <p class="text-4xl font-extrabold text-white mb-2">TZS {{ number_format($stats['revenue_tzs'] ?? 0) }}</p>
            <div class="flex items-center text-sm text-white/90">
                <i class="fas fa-chart-line mr-2"></i>
                <span>MRR: <strong>TZS {{ number_format($stats['mrr_tzs'] ?? 0) }}</strong></span>
      </div>
    </div>

        <!-- Total Users - Blue/Cyan -->
        <div class="modern-card p-6 stats-card animate-fade-in-up bg-gradient-to-br from-blue-500 to-cyan-600 text-white border-0" style="animation-delay: 0.4s">
            <div class="flex items-center justify-between mb-4">
                <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                    <i class="fas fa-users text-2xl text-white"></i>
                </div>
                <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-xs font-bold">
                    <i class="fas fa-user-plus mr-1"></i> +{{ count($recentUsers ?? []) }}
                </span>
            </div>
            <h3 class="text-white/80 text-sm font-medium mb-1">Total Users</h3>
            <p class="text-4xl font-extrabold text-white mb-2">{{ number_format($stats['users'] ?? 0) }}</p>
            <div class="flex items-center text-sm text-white/90">
                <i class="fas fa-box mr-2"></i>
                <span>Plans: <strong>{{ number_format($stats['plans'] ?? 0) }}</strong></span>
      </div>
      </div>
    </div>

    <!-- Compact Charts Row with FIXED HEIGHT - No Stretching! -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Revenue Trend - Fixed Height -->
        <div class="modern-card p-4 animate-fade-in-up bg-gradient-to-br from-pink-50 to-white border-2 border-pink-200 h-48 flex flex-col" style="animation-delay: 0.5s">
            <div class="flex items-center justify-between mb-2">
                <div class="w-8 h-8 rounded-lg gradient-bg-5 flex items-center justify-center">
                    <i class="fas fa-chart-line text-white text-xs"></i>
                </div>
                <span class="text-xs font-bold text-pink-600">14 Days</span>
            </div>
            <h3 class="text-sm font-bold text-gray-700 mb-1">Revenue Trend</h3>
            <p class="text-xl font-extrabold gradient-text-2 mb-3">TZS {{ number_format(array_sum($chart['revenue'] ?? [0])) }}</p>
            <!-- Fixed height chart container -->
            <div class="flex-1 flex items-center justify-center">
                <canvas id="revenueChart" class="max-h-16"></canvas>
      </div>
        </div>
        
        <!-- Orders Trend - Fixed Height -->
        <div class="modern-card p-4 animate-fade-in-up bg-gradient-to-br from-purple-50 to-white border-2 border-purple-200 h-48 flex flex-col" style="animation-delay: 0.6s">
            <div class="flex items-center justify-between mb-2">
                <div class="w-8 h-8 rounded-lg gradient-bg-1 flex items-center justify-center">
                    <i class="fas fa-chart-bar text-white text-xs"></i>
                </div>
                <span class="text-xs font-bold text-purple-600">14 Days</span>
            </div>
            <h3 class="text-sm font-bold text-gray-700 mb-1">Orders Trend</h3>
            <p class="text-xl font-extrabold gradient-text mb-3">{{ array_sum($chart['orders'] ?? [0]) }} Orders</p>
            <!-- Fixed height chart container -->
            <div class="flex-1 flex items-center justify-center">
                <canvas id="ordersChart" class="max-h-16"></canvas>
      </div>
    </div>

        <!-- Services Growth - Fixed Height -->
        <div class="modern-card p-4 animate-fade-in-up bg-gradient-to-br from-green-50 to-white border-2 border-green-200 h-48 flex flex-col" style="animation-delay: 0.7s">
            <div class="flex items-center justify-between mb-2">
                <div class="w-8 h-8 rounded-lg gradient-bg-4 flex items-center justify-center">
                    <i class="fas fa-server text-white text-xs"></i>
                </div>
                <span class="badge badge-success text-xs">
                    <i class="fas fa-arrow-up mr-1"></i> +{{ $stats['active'] ?? 0 }}
                </span>
            </div>
            <h3 class="text-sm font-bold text-gray-700 mb-1">Services Growth</h3>
            <p class="text-xl font-extrabold gradient-text-4 mb-3">{{ number_format($stats['active'] ?? 0) }}</p>
            <!-- Fixed stats list -->
            <div class="flex-1 flex flex-col justify-center space-y-1">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-600">Active</span>
                    <span class="font-bold text-green-600">{{ $stats['active'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-600">Provisioning</span>
                    <span class="font-bold text-yellow-600">{{ $stats['services_provisioning'] ?? 0 }}</span>
                </div>
      </div>
        </div>
        
        <!-- User Growth - Fixed Height -->
        <div class="modern-card p-4 animate-fade-in-up bg-gradient-to-br from-blue-50 to-white border-2 border-blue-200 h-48 flex flex-col" style="animation-delay: 0.8s">
            <div class="flex items-center justify-between mb-2">
                <div class="w-8 h-8 rounded-lg gradient-bg-3 flex items-center justify-center">
                    <i class="fas fa-users text-white text-xs"></i>
                </div>
                <span class="badge badge-info text-xs">
                    <i class="fas fa-plus mr-1"></i> New
                </span>
            </div>
            <h3 class="text-sm font-bold text-gray-700 mb-1">User Growth</h3>
            <p class="text-xl font-extrabold gradient-text-3 mb-3">{{ number_format($stats['users'] ?? 0) }}</p>
            <!-- Fixed stats list -->
            <div class="flex-1 flex flex-col justify-center space-y-1">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-600">Total Users</span>
                    <span class="font-bold text-blue-600">{{ $stats['users'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-600">Recent</span>
                    <span class="font-bold text-cyan-600">+{{ count($recentUsers ?? []) }}</span>
                </div>
      </div>
    </div>
  </div>

    <!-- Recent Activity Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Orders -->
        <div class="modern-card p-6 animate-fade-in-up" style="animation-delay: 0.9s">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-800">Recent Orders</h3>
                @if(Route::has('admin.orders.index'))
                    <a href="{{ route('admin.orders.index') }}" class="text-sm font-semibold text-purple-600 hover:text-purple-700">
                        View all <i class="fas fa-arrow-right ml-1"></i>
                    </a>
        @endif
            </div>
            
            <div class="space-y-4">
                @forelse($recentOrders ?? [] as $order)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-xl gradient-bg-{{ ($loop->index % 6) + 1 }} flex items-center justify-center text-white font-bold">
                                #{{ $order->id }}
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">{{ $order->plan->name ?? 'N/A' }}</h4>
                                <p class="text-sm text-gray-500">{{ optional($order->user)->name ?? 'Guest' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-gray-800">TZS {{ number_format($order->price_tzs ?? 0) }}</p>
                            <span class="badge badge-{{ in_array($order->status, ['paid', 'active', 'complete']) ? 'success' : 'warning' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-3"></i>
                        <p>No recent orders</p>
                    </div>
                @endforelse
            </div>
        </div>
        
        <!-- Recent Services -->
        <div class="modern-card p-6 animate-fade-in-up" style="animation-delay: 1s">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-800">Recent Services</h3>
                @if(Route::has('admin.services.index'))
                    <a href="{{ route('admin.services.index') }}" class="text-sm font-semibold text-purple-600 hover:text-purple-700">
                        View all <i class="fas fa-arrow-right ml-1"></i>
                    </a>
        @endif
            </div>
            
            <div class="space-y-4">
                @forelse($recentServices ?? [] as $service)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-xl gradient-bg-{{ (($loop->index + 3) % 6) + 1 }} flex items-center justify-center text-white">
                                <i class="fas fa-server"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">{{ $service->domain ?? 'N/A' }}</h4>
                                <p class="text-sm text-gray-500">{{ optional($service->order->user)->name ?? 'Guest' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="badge badge-{{ $service->status === 'active' ? 'success' : ($service->status === 'provisioning' ? 'warning' : 'danger') }}">
                                {{ ucfirst($service->status ?? 'pending') }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-3"></i>
                        <p>No recent services</p>
                    </div>
                @endforelse
            </div>
      </div>
    </div>

    <!-- Top Plans & Recent Users -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Plans -->
        <div class="modern-card p-6 animate-fade-in-up" style="animation-delay: 1.1s">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-800">Top Performing Plans</h3>
                @if(Route::has('admin.plans.index'))
                    <a href="{{ route('admin.plans.index') }}" class="text-sm font-semibold text-purple-600 hover:text-purple-700">
                        View all <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                @endif
            </div>
            
            <div class="space-y-3">
                @forelse($topPlans ?? [] as $plan)
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-purple-50 to-transparent rounded-xl border border-purple-100">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-lg gradient-bg-{{ ($loop->index % 6) + 1 }} flex items-center justify-center text-white font-bold">
                                {{ $loop->iteration }}
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">{{ $plan->name }}</h4>
                                <p class="text-sm text-gray-500">TZS {{ number_format($plan->price_tzs ?? 0) }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="badge badge-success">
                                <i class="fas fa-shopping-cart mr-1"></i>
                                {{ $plan->paid_orders_count ?? 0 }} orders
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-box-open text-4xl mb-3"></i>
                        <p>No plans available</p>
                </div>
                @endforelse
    </div>
  </div>

        <!-- Recent Users -->
        <div class="modern-card p-6 animate-fade-in-up" style="animation-delay: 1.2s">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-800">Recent Users</h3>
                @if(Route::has('admin.users.index'))
                    <a href="{{ route('admin.users.index') }}" class="text-sm font-semibold text-purple-600 hover:text-purple-700">
                        View all <i class="fas fa-arrow-right ml-1"></i>
                    </a>
      @endif
    </div>
            
            <div class="space-y-3">
                @forelse($recentUsers ?? [] as $user)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full gradient-bg-{{ ($loop->index % 6) + 1 }} flex items-center justify-center text-white font-semibold">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">{{ $user->name }}</h4>
                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="badge badge-info text-xs">
                                {{ ucfirst($user->role ?? 'user') }}
                            </span>
                            <p class="text-xs text-gray-500 mt-1">{{ $user->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-user-slash text-4xl mb-3"></i>
                        <p>No users yet</p>
                    </div>
                @endforelse
            </div>
                  </div>
    </div>
  </div>

@push('scripts')
<script>
    // Fixed Height Revenue Chart - No Stretching!
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        const revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: @json($chart['labels'] ?? []),
                datasets: [{
                    label: 'Revenue',
                    data: @json($chart['revenue'] ?? []),
                    borderColor: 'rgb(236, 72, 153)',
                    backgroundColor: function(context) {
                        const chart = context.chart;
                        const {ctx, chartArea} = chart;
                        if (!chartArea) return;
                        const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                        gradient.addColorStop(0, 'rgba(236, 72, 153, 0.05)');
                        gradient.addColorStop(1, 'rgba(236, 72, 153, 0.3)');
                        return gradient;
                    },
                    tension: 0.4,
                    fill: true,
                    borderWidth: 2,
                    pointRadius: 0,
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: 'rgb(236, 72, 153)',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                aspectRatio: 4,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 6,
                        borderRadius: 4,
                        bodyFont: { size: 10 },
                        displayColors: false,
                        callbacks: {
                            title: () => '',
                            label: (context) => 'TZS ' + new Intl.NumberFormat().format(context.parsed.y)
                        }
                    }
                },
                scales: {
                    y: {
                        display: false,
                        beginAtZero: true
                    },
                    x: {
                        display: false
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                elements: {
                    point: {
                        radius: 0
                    }
                }
            }
        });
    }
    
    // Fixed Height Orders Chart - No Stretching!
    const ordersCtx = document.getElementById('ordersChart');
    if (ordersCtx) {
        const ordersChart = new Chart(ordersCtx, {
            type: 'bar',
            data: {
                labels: @json($chart['labels'] ?? []),
                datasets: [{
                    label: 'Orders',
                    data: @json($chart['orders'] ?? []),
                    backgroundColor: function(context) {
                        const chart = context.chart;
                        const {ctx, chartArea} = chart;
                        if (!chartArea) return;
                        const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                        gradient.addColorStop(0, 'rgba(139, 92, 246, 0.5)');
                        gradient.addColorStop(1, 'rgba(139, 92, 246, 1)');
                        return gradient;
                    },
                    borderRadius: 2,
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                aspectRatio: 4,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 6,
                        borderRadius: 4,
                        bodyFont: { size: 10 },
                        displayColors: false,
                        callbacks: {
                            title: () => '',
                            label: (context) => context.parsed.y + ' orders'
                        }
                    }
                },
                scales: {
                    y: {
                        display: false,
                        beginAtZero: true
                    },
                    x: {
                        display: false
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }
</script>
@endpush
@endsection
