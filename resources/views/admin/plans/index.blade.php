@extends('layouts.admin')
@section('title', 'Plans Management')
@section('breadcrumb', 'Plans')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 animate-fade-in-up">
        <div>
            <h1 class="text-3xl font-bold gradient-text">Plans Management</h1>
            <p class="text-gray-500 mt-1">Create and manage hosting plans</p>
        </div>
        @if(Route::has('admin.plans.create'))
            <a href="{{ route('admin.plans.create') }}" class="px-6 py-3 gradient-bg-1 text-white rounded-xl font-semibold hover:shadow-lg transition btn-modern">
                <i class="fas fa-plus mr-2"></i> Create New Plan
            </a>
        @endif
    </div>
    
    <!-- Stats Cards -->
    @php
        $totalPlans = $plans->total();
        $activePlans = \App\Models\Plan::where('is_active', true)->count();
        $totalOrders = \App\Models\Order::count();
    @endphp
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-fade-in-up" style="animation-delay: 0.1s">
        <div class="modern-card p-6 stats-card">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl gradient-bg-1 flex items-center justify-center">
                    <i class="fas fa-box text-2xl text-white"></i>
                </div>
                <span class="badge badge-info">Total</span>
            </div>
            <h3 class="text-gray-500 text-sm font-medium mb-1">Total Plans</h3>
            <p class="text-3xl font-bold gradient-text">{{ number_format($totalPlans) }}</p>
        </div>
        
        <div class="modern-card p-6 stats-card">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl gradient-bg-4 flex items-center justify-center">
                    <i class="fas fa-check-circle text-2xl text-white"></i>
                </div>
                <span class="badge badge-success">Active</span>
            </div>
            <h3 class="text-gray-500 text-sm font-medium mb-1">Active Plans</h3>
            <p class="text-3xl font-bold gradient-text-4">{{ number_format($activePlans) }}</p>
        </div>
        
        <div class="modern-card p-6 stats-card">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl gradient-bg-3 flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-2xl text-white"></i>
                </div>
                <span class="badge badge-purple">Orders</span>
            </div>
            <h3 class="text-gray-500 text-sm font-medium mb-1">Total Orders</h3>
            <p class="text-3xl font-bold gradient-text-3">{{ number_format($totalOrders) }}</p>
        </div>
    </div>
    
    <!-- Plans Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-fade-in-up" style="animation-delay: 0.2s">
        @forelse($plans as $plan)
            <div class="modern-card p-6 hover:shadow-2xl transition group relative overflow-hidden">
                <!-- Background Decoration -->
                <div class="absolute top-0 right-0 w-32 h-32 gradient-bg-{{ ($loop->index % 6) + 1 }} opacity-5 rounded-full transform translate-x-16 -translate-y-16 group-hover:scale-150 transition duration-500"></div>
                
                <div class="relative z-10">
                    <!-- Plan Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-14 h-14 rounded-2xl gradient-bg-{{ ($loop->index % 6) + 1 }} flex items-center justify-center text-white text-2xl">
                            <i class="fas fa-box"></i>
                        </div>
                        @if($plan->is_active)
                            <span class="badge badge-success">
                                <i class="fas fa-check mr-1"></i> Active
                            </span>
                        @else
                            <span class="badge badge-danger">
                                <i class="fas fa-times mr-1"></i> Inactive
                            </span>
                        @endif
                    </div>
                    
                    <!-- Plan Name -->
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $plan->name }}</h3>
                    
                    <!-- Plan Description -->
                    @if($plan->description)
                        <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $plan->description }}</p>
                    @else
                        <p class="text-sm text-gray-400 italic mb-4">No description</p>
                    @endif
                    
                    <!-- Pricing -->
                    <div class="p-4 bg-gradient-to-br from-purple-50 to-transparent rounded-xl border border-purple-100 mb-4">
                        <div class="flex items-baseline">
                            <span class="text-3xl font-bold gradient-text">TZS {{ number_format($plan->price_tzs ?? 0) }}</span>
                            <span class="text-gray-500 ml-2">/ {{ $plan->period_months }} {{ $plan->period_months == 1 ? 'month' : 'months' }}</span>
                        </div>
                    </div>
                    
                    <!-- Plan Details -->
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Plan ID</span>
                            <span class="font-semibold text-gray-900">#{{ $plan->id }}</span>
                        </div>
                        
                        @if($plan->slug)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Slug</span>
                                <span class="font-mono text-xs text-gray-900">{{ $plan->slug }}</span>
                            </div>
                        @endif
                        
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Orders</span>
                            <span class="font-semibold text-gray-900">{{ $plan->orders_count ?? 0 }}</span>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex flex-wrap gap-2 pt-4 border-t border-gray-200">
                        <a href="{{ route('admin.plans.show', $plan) }}" class="flex-1 px-4 py-2 bg-purple-100 text-purple-700 rounded-lg font-semibold hover:bg-purple-200 transition text-center text-sm">
                            <i class="fas fa-eye mr-1"></i> View
                        </a>
                        
                        <a href="{{ route('admin.plans.edit', $plan) }}" class="flex-1 px-4 py-2 bg-blue-100 text-blue-700 rounded-lg font-semibold hover:bg-blue-200 transition text-center text-sm">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </a>
                        
                        <form method="POST" action="{{ route('admin.plans.destroy', $plan) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this plan? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-100 text-red-700 rounded-lg font-semibold hover:bg-red-200 transition text-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full modern-card p-12 text-center">
                <div class="w-24 h-24 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-box text-5xl text-gray-300"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-700 mb-2">No Plans Yet</h3>
                <p class="text-gray-500 mb-4">Create your first hosting plan to get started</p>
                @if(Route::has('admin.plans.create'))
                    <a href="{{ route('admin.plans.create') }}" class="inline-flex items-center px-6 py-3 gradient-bg-1 text-white rounded-xl font-semibold hover:shadow-lg transition btn-modern">
                        <i class="fas fa-plus mr-2"></i> Create Your First Plan
                    </a>
                @endif
            </div>
        @endforelse
    </div>
    
    <!-- Pagination -->
    @if($plans->hasPages())
        <div class="modern-card p-4 animate-fade-in-up">
            {{ $plans->links() }}
        </div>
    @endif
</div>
@endsection
