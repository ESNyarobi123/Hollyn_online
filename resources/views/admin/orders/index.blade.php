@extends('layouts.admin')
@section('title', 'Orders Management')
@section('breadcrumb', 'Orders')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 animate-fade-in-up">
        <div>
            <h1 class="text-3xl font-bold gradient-text">Orders Management</h1>
            <p class="text-gray-500 mt-1">View and manage all customer orders</p>
        </div>
        @if(Route::has('admin.orders.create'))
            <a href="{{ route('admin.orders.create') }}" class="px-6 py-3 gradient-bg-1 text-white rounded-xl font-semibold hover:shadow-lg transition btn-modern">
                <i class="fas fa-plus mr-2"></i> Create Order
            </a>
        @endif
    </div>
    
    <!-- Stats Cards -->
    @php
        $totalOrders = $orders->total();
        $paidOrders = \App\Models\Order::whereIn('status', ['paid', 'active', 'complete'])->count();
        $pendingOrders = \App\Models\Order::where('status', 'pending')->count();
        $totalRevenue = \App\Models\Order::whereIn('status', ['paid', 'active', 'complete'])->sum('price_tzs');
    @endphp
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 animate-fade-in-up" style="animation-delay: 0.1s">
        <div class="modern-card p-6 stats-card">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl gradient-bg-1 flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-2xl text-white"></i>
                </div>
                <span class="badge badge-info">All</span>
            </div>
            <h3 class="text-gray-500 text-sm font-medium mb-1">Total Orders</h3>
            <p class="text-3xl font-bold gradient-text">{{ number_format($totalOrders) }}</p>
        </div>
        
        <div class="modern-card p-6 stats-card">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl gradient-bg-4 flex items-center justify-center">
                    <i class="fas fa-check-circle text-2xl text-white"></i>
                </div>
                <span class="badge badge-success">Paid</span>
            </div>
            <h3 class="text-gray-500 text-sm font-medium mb-1">Completed Orders</h3>
            <p class="text-3xl font-bold gradient-text-4">{{ number_format($paidOrders) }}</p>
        </div>
        
        <div class="modern-card p-6 stats-card">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl gradient-bg-5 flex items-center justify-center">
                    <i class="fas fa-clock text-2xl text-white"></i>
                </div>
                <span class="badge badge-warning">Waiting</span>
            </div>
            <h3 class="text-gray-500 text-sm font-medium mb-1">Pending Orders</h3>
            <p class="text-3xl font-bold gradient-text-2">{{ number_format($pendingOrders) }}</p>
        </div>
        
        <div class="modern-card p-6 stats-card">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl gradient-bg-3 flex items-center justify-center">
                    <i class="fas fa-coins text-2xl text-white"></i>
                </div>
                <span class="badge badge-success">Revenue</span>
            </div>
            <h3 class="text-gray-500 text-sm font-medium mb-1">Total Revenue</h3>
            <p class="text-2xl font-bold gradient-text-3">TZS {{ number_format($totalRevenue) }}</p>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="modern-card p-6 animate-fade-in-up" style="animation-delay: 0.2s">
        <form method="GET" class="flex flex-col lg:flex-row gap-4">
            <select 
                name="status" 
                class="px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                onchange="this.form.submit()"
            >
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="complete" {{ request('status') === 'complete' ? 'selected' : '' }}>Complete</option>
                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            
            @if(request('status'))
                <a href="{{ route('admin.orders.index') }}" class="px-8 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition">
                    <i class="fas fa-times mr-2"></i> Clear Filter
                </a>
            @endif
        </form>
    </div>
    
    <!-- Orders Grid/Table -->
    <div class="modern-card overflow-hidden animate-fade-in-up" style="animation-delay: 0.3s">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-purple-50 to-pink-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Order</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Plan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($orders as $order)
                        @php
                            $statusColors = [
                                'paid' => 'success',
                                'active' => 'success',
                                'complete' => 'success',
                                'pending' => 'warning',
                                'failed' => 'danger',
                                'cancelled' => 'danger',
                            ];
                            $badgeClass = 'badge-' . ($statusColors[$order->status] ?? 'info');
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-xl gradient-bg-{{ ($loop->index % 6) + 1 }} flex items-center justify-center text-white font-bold">
                                        {{ $loop->iteration }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900">#{{ $order->id }}</p>
                                        <p class="text-xs text-gray-500">{{ $order->order_uuid ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full gradient-bg-{{ (($loop->index + 2) % 6) + 1 }} flex items-center justify-center text-white font-semibold">
                                        {{ substr(optional($order->user)->name ?? 'G', 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ optional($order->user)->name ?? 'Guest' }}</p>
                                        <p class="text-sm text-gray-500">{{ $order->customer_email ?? optional($order->user)->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-semibold text-gray-900">{{ optional($order->plan)->name ?? 'N/A' }}</p>
                                @if($order->domain)
                                    <p class="text-sm text-gray-500">{{ $order->domain }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-lg font-bold gradient-text">TZS {{ number_format($order->price_tzs ?? 0) }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="badge {{ $badgeClass }}">
                                    <i class="fas fa-circle mr-1 text-xs"></i>
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900">{{ $order->created_at->format('M d, Y') }}</p>
                                <p class="text-sm text-gray-500">{{ $order->created_at->format('H:i') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end space-x-2">
                                    <a 
                                        href="{{ route('admin.orders.show', $order) }}" 
                                        class="w-9 h-9 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center hover:bg-purple-200 transition"
                                        title="View Details"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <a 
                                        href="{{ route('admin.orders.edit', $order) }}" 
                                        class="w-9 h-9 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center hover:bg-blue-200 transition"
                                        title="Edit Order"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="submit" 
                                            class="w-9 h-9 rounded-lg bg-red-100 text-red-600 flex items-center justify-center hover:bg-red-200 transition"
                                            title="Delete Order"
                                            onclick="return confirm('Are you sure you want to delete this order?')"
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-500">
                                    <i class="fas fa-shopping-cart text-6xl mb-4 text-gray-300"></i>
                                    <p class="text-lg font-semibold mb-2">No orders found</p>
                                    <p class="text-sm">Try adjusting your filters or create a new order</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
