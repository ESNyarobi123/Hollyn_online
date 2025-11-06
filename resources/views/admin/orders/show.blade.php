@extends('layouts.admin')
@section('title', 'Order Details #' . $order->id)
@section('breadcrumb', 'Orders / #' . $order->id)

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div class="animate-fade-in-up">
        <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 font-medium">
            <i class="fas fa-arrow-left mr-2"></i> Back to Orders
        </a>
    </div>
    
    @php
        $statusColors = [
            'paid' => ['bg' => 'gradient-bg-4', 'badge' => 'badge-success'],
            'active' => ['bg' => 'gradient-bg-4', 'badge' => 'badge-success'],
            'complete' => ['bg' => 'gradient-bg-4', 'badge' => 'badge-success'],
            'pending' => ['bg' => 'gradient-bg-5', 'badge' => 'badge-warning'],
            'failed' => ['bg' => 'gradient-bg-2', 'badge' => 'badge-danger'],
            'cancelled' => ['bg' => 'gradient-bg-6', 'badge' => 'badge-danger'],
        ];
        $colors = $statusColors[$order->status] ?? ['bg' => 'gradient-bg-1', 'badge' => 'badge-info'];
    @endphp
    
    <!-- Order Header -->
    <div class="modern-card p-8 {{ $colors['bg'] }} text-white overflow-hidden relative animate-fade-in-up" style="animation-delay: 0.1s">
        <div class="relative z-10">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex items-center space-x-6">
                    <div class="w-24 h-24 rounded-2xl bg-white/20 flex items-center justify-center text-4xl backdrop-blur-sm">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <h1 class="text-3xl font-bold">Order #{{ $order->id }}</h1>
                            <span class="badge {{ $colors['badge'] }}">
                                <i class="fas fa-circle mr-1 text-xs"></i>
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                        <p class="text-white/80 text-2xl mb-2 font-bold">TZS {{ number_format($order->price_tzs ?? 0) }}</p>
                        <div class="flex items-center space-x-4 text-sm text-white/70">
                            <span><i class="fas fa-user mr-1"></i> {{ optional($order->user)->name ?? 'Guest' }}</span>
                            <span><i class="fas fa-calendar mr-1"></i> {{ $order->created_at->format('M d, Y') }}</span>
                            @if($order->order_uuid)
                                <span><i class="fas fa-hashtag mr-1"></i> {{ $order->order_uuid }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.orders.edit', $order) }}" class="px-6 py-3 bg-white text-purple-600 rounded-xl font-semibold hover:shadow-lg transition btn-modern">
                        <i class="fas fa-edit mr-2"></i> Edit Order
                    </a>
                    
                    @if($order->status === 'pending' && $order->gateway_order_id)
                        <form action="{{ route('admin.orders.check-status', $order) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="px-6 py-3 bg-blue-500 text-white rounded-xl font-semibold hover:bg-blue-600 hover:shadow-lg transition btn-modern">
                                <i class="fas fa-sync mr-2"></i> Check Payment Status
                            </button>
                        </form>
                        
                        <form action="{{ route('admin.orders.mark-paid', $order) }}" method="POST" style="display: inline;" onsubmit="return confirm('Mark this order as PAID? This action will update the order status.');">
                            @csrf
                            <button type="submit" class="px-6 py-3 bg-green-500 text-white rounded-xl font-semibold hover:bg-green-600 hover:shadow-lg transition btn-modern">
                                <i class="fas fa-check-circle mr-2"></i> Mark as Paid
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-white/5 rounded-full -ml-48 -mb-48"></div>
    </div>
    
    <!-- Order Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Customer Information -->
        <div class="modern-card p-6 animate-fade-in-up" style="animation-delay: 0.2s">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-user text-purple-600 mr-2"></i>
                Customer Information
            </h3>
            <div class="space-y-3">
                @if($order->user)
                    <div class="flex items-center space-x-4 p-4 bg-gradient-to-r from-purple-50 to-transparent rounded-xl">
                        <div class="w-12 h-12 rounded-full gradient-bg-1 flex items-center justify-center text-white font-bold text-lg">
                            {{ substr($order->user->name, 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">{{ $order->user->name }}</p>
                            <p class="text-sm text-gray-600">{{ $order->user->email }}</p>
                        </div>
                        <a href="{{ route('admin.users.show', $order->user) }}" class="text-purple-600 hover:text-purple-800">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                @endif
                
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <span class="text-gray-600">Customer Name</span>
                    <span class="font-semibold text-gray-900">{{ $order->customer_name ?? optional($order->user)->name ?? 'N/A' }}</span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <span class="text-gray-600">Email</span>
                    <span class="font-semibold text-gray-900">{{ $order->customer_email ?? optional($order->user)->email ?? 'N/A' }}</span>
                </div>
                
                @if($order->customer_phone)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <span class="text-gray-600">Phone</span>
                        <span class="font-semibold text-gray-900">{{ $order->customer_phone }}</span>
                    </div>
                @endif
                
                @if($order->payer_phone && $order->payer_phone !== $order->customer_phone)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <span class="text-gray-600">Payer Phone</span>
                        <span class="font-semibold text-gray-900">{{ $order->payer_phone }}</span>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Order Information -->
        <div class="modern-card p-6 animate-fade-in-up" style="animation-delay: 0.3s">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-shopping-cart text-purple-600 mr-2"></i>
                Order Information
            </h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <span class="text-gray-600">Order ID</span>
                    <span class="font-bold text-gray-900">#{{ $order->id }}</span>
                </div>
                
                @if($order->order_uuid)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <span class="text-gray-600">Order UUID</span>
                        <span class="font-mono text-sm text-gray-900">{{ $order->order_uuid }}</span>
                    </div>
                @endif
                
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <span class="text-gray-600">Plan</span>
                    <span class="font-semibold text-gray-900">{{ optional($order->plan)->name ?? 'N/A' }}</span>
                </div>
                
                @if($order->domain)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <span class="text-gray-600">Domain</span>
                        <span class="font-semibold text-gray-900">{{ $order->domain }}</span>
                    </div>
                @endif
                
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <span class="text-gray-600">Amount</span>
                    <span class="text-2xl font-bold gradient-text">TZS {{ number_format($order->price_tzs ?? 0) }}</span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <span class="text-gray-600">Currency</span>
                    <span class="font-semibold text-gray-900">{{ $order->currency ?? 'TZS' }}</span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <span class="text-gray-600">Status</span>
                    <span class="badge {{ $colors['badge'] }}">{{ ucfirst($order->status) }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Payment Information -->
    <div class="modern-card p-6 animate-fade-in-up" style="animation-delay: 0.4s">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-credit-card text-purple-600 mr-2"></i>
            Payment Information
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="p-4 bg-gradient-to-br from-blue-50 to-white rounded-xl border border-blue-200">
                <p class="text-xs text-gray-500 mb-2">Payment Reference</p>
                <p class="text-lg font-bold text-gray-900">{{ $order->payment_ref ?? '—' }}</p>
            </div>
            
            <div class="p-4 bg-gradient-to-br from-green-50 to-white rounded-xl border border-green-200">
                <p class="text-xs text-gray-500 mb-2">Gateway Order ID</p>
                <p class="text-lg font-bold text-gray-900">{{ $order->gateway_order_id ?? '—' }}</p>
            </div>
            
            <div class="p-4 bg-gradient-to-br from-purple-50 to-white rounded-xl border border-purple-200">
                <p class="text-xs text-gray-500 mb-2">Payment Provider</p>
                <p class="text-lg font-bold text-gray-900">{{ $order->gateway_provider ?? '—' }}</p>
            </div>
        </div>
        
        @if($order->gateway_meta && is_array($order->gateway_meta))
            <div class="mt-4">
                <h4 class="font-semibold text-gray-700 mb-2">Gateway Metadata</h4>
                <pre class="bg-gray-50 p-4 rounded-xl text-xs overflow-x-auto">{{ json_encode($order->gateway_meta, JSON_PRETTY_PRINT) }}</pre>
            </div>
        @endif
    </div>
    
    <!-- Timeline -->
    <div class="modern-card p-6 animate-fade-in-up" style="animation-delay: 0.5s">
        <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
            <i class="fas fa-history text-purple-600 mr-2"></i>
            Order Timeline
        </h3>
        
        <div class="space-y-4">
            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 rounded-full bg-green-100 text-green-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-plus"></i>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-900">Order Created</p>
                    <p class="text-sm text-gray-600">{{ $order->created_at->format('M d, Y \a\t H:i') }}</p>
                    <p class="text-xs text-gray-500">{{ $order->created_at->diffForHumans() }}</p>
                </div>
            </div>
            
            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-sync"></i>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-900">Last Updated</p>
                    <p class="text-sm text-gray-600">{{ $order->updated_at->format('M d, Y \a\t H:i') }}</p>
                    <p class="text-xs text-gray-500">{{ $order->updated_at->diffForHumans() }}</p>
                </div>
            </div>
            
            @if($order->service)
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-server"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-900">Service Provisioned</p>
                        <p class="text-sm text-gray-600">Service #{{ $order->service->id }} created</p>
                        <a href="{{ route('admin.services.show', $order->service) }}" class="text-sm text-purple-600 hover:text-purple-800">
                            View Service <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Associated Service -->
    @if($order->service)
        <div class="modern-card p-6 border-l-4 border-purple-500 animate-fade-in-up" style="animation-delay: 0.6s">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-server text-purple-600 mr-2"></i>
                Associated Service
            </h3>
            
            <div class="p-6 bg-gradient-to-r from-purple-50 to-transparent rounded-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Service #{{ $order->service->id }}</p>
                        <p class="text-xl font-bold text-gray-900 mb-2">{{ $order->service->domain ?? 'No Domain' }}</p>
                        <div class="flex items-center space-x-3">
                            <span class="badge badge-{{ $order->service->status === 'active' ? 'success' : ($order->service->status === 'provisioning' ? 'warning' : 'danger') }}">
                                {{ ucfirst($order->service->status ?? 'pending') }}
                            </span>
                            <span class="text-sm text-gray-600">
                                Created {{ $order->service->created_at->format('M d, Y') }}
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('admin.services.show', $order->service) }}" class="px-6 py-3 gradient-bg-1 text-white rounded-xl font-semibold hover:shadow-lg transition btn-modern">
                        <i class="fas fa-eye mr-2"></i> View Service
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
