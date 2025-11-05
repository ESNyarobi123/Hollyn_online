@extends('layouts.admin')
@section('title', 'User Details - ' . $user->name)
@section('breadcrumb', 'Users / ' . $user->name)

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div class="animate-fade-in-up">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 font-medium">
            <i class="fas fa-arrow-left mr-2"></i> Back to Users
        </a>
    </div>
    
    <!-- User Profile Header -->
    <div class="modern-card p-8 gradient-bg-1 text-white overflow-hidden relative animate-fade-in-up" style="animation-delay: 0.1s">
        <div class="relative z-10">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex items-center space-x-6">
                    <div class="w-24 h-24 rounded-2xl bg-white/20 flex items-center justify-center text-4xl font-bold backdrop-blur-sm">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold mb-2">{{ $user->name }}</h1>
                        <p class="text-white/80 text-lg mb-3">{{ $user->email }}</p>
                        <div class="flex items-center space-x-3">
                            @if($user->isAdmin())
                                <span class="badge badge-warning">
                                    <i class="fas fa-crown mr-1"></i> Admin
                                </span>
                            @else
                                <span class="badge badge-info">
                                    <i class="fas fa-user mr-1"></i> User
                                </span>
                            @endif
                            <span class="text-white/70 text-sm">
                                <i class="fas fa-calendar mr-1"></i> Joined {{ $user->created_at->format('M d, Y') }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-3">
                    @if(Route::has('admin.users.credentials'))
                        <a href="{{ route('admin.users.credentials', $user) }}" class="px-6 py-3 bg-white/20 backdrop-blur-sm text-white rounded-xl font-semibold hover:bg-white/30 transition btn-modern">
                            <i class="fas fa-key mr-2"></i> View Credentials
                        </a>
                    @endif
                    
                    @if(!$user->isAdmin())
                        <form method="POST" action="{{ route('admin.users.impersonate', $user) }}" class="inline">
                            @csrf
                            <button type="submit" class="px-6 py-3 bg-white text-purple-600 rounded-xl font-semibold hover:shadow-lg transition btn-modern" onclick="return confirm('Login as {{ $user->name }}?')">
                                <i class="fas fa-sign-in-alt mr-2"></i> Login As User
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-white/5 rounded-full -ml-48 -mb-48"></div>
    </div>
    
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="modern-card p-6 stats-card animate-fade-in-up" style="animation-delay: 0.2s">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl gradient-bg-1 flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-xl text-white"></i>
                </div>
            </div>
            <h3 class="text-gray-500 text-sm font-medium mb-1">Total Orders</h3>
            <p class="text-3xl font-bold gradient-text">{{ $user->orders_count ?? 0 }}</p>
        </div>
        
        <div class="modern-card p-6 stats-card animate-fade-in-up" style="animation-delay: 0.3s">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl gradient-bg-4 flex items-center justify-center">
                    <i class="fas fa-server text-xl text-white"></i>
                </div>
            </div>
            <h3 class="text-gray-500 text-sm font-medium mb-1">Active Services</h3>
            <p class="text-3xl font-bold gradient-text-4">{{ $services->where('status', 'active')->count() }}</p>
        </div>
        
        <div class="modern-card p-6 stats-card animate-fade-in-up" style="animation-delay: 0.4s">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl gradient-bg-5 flex items-center justify-center">
                    <i class="fas fa-coins text-xl text-white"></i>
                </div>
            </div>
            <h3 class="text-gray-500 text-sm font-medium mb-1">Total Spent</h3>
            <p class="text-3xl font-bold gradient-text-2">TZS {{ number_format($user->orders->sum('price_tzs') ?? 0) }}</p>
        </div>
        
        <div class="modern-card p-6 stats-card animate-fade-in-up" style="animation-delay: 0.5s">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl gradient-bg-3 flex items-center justify-center">
                    <i class="fas fa-clock text-xl text-white"></i>
                </div>
            </div>
            <h3 class="text-gray-500 text-sm font-medium mb-1">Member Since</h3>
            <p class="text-lg font-bold gradient-text-3">{{ $user->created_at->diffForHumans() }}</p>
        </div>
    </div>
    
    <!-- User Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Contact Information -->
        <div class="modern-card p-6 animate-fade-in-up" style="animation-delay: 0.6s">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-address-card text-purple-600 mr-2"></i>
                Contact Information
            </h3>
            <div class="space-y-4">
                <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-xl">
                    <div class="w-10 h-10 rounded-lg gradient-bg-1 flex items-center justify-center text-white">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Full Name</p>
                        <p class="text-gray-900 font-semibold">{{ $user->name }}</p>
                    </div>
                </div>
                
                <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-xl">
                    <div class="w-10 h-10 rounded-lg gradient-bg-3 flex items-center justify-center text-white">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Email Address</p>
                        <p class="text-gray-900 font-semibold">{{ $user->email }}</p>
                    </div>
                </div>
                
                @if($user->phone)
                    <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-xl">
                        <div class="w-10 h-10 rounded-lg gradient-bg-4 flex items-center justify-center text-white">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Phone Number</p>
                            <p class="text-gray-900 font-semibold">{{ $user->phone }}</p>
                        </div>
                    </div>
                @endif
                
                <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-xl">
                    <div class="w-10 h-10 rounded-lg gradient-bg-2 flex items-center justify-center text-white">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Role</p>
                        <p class="text-gray-900 font-semibold">{{ ucfirst($user->role ?? 'user') }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Account Activity -->
        <div class="modern-card p-6 animate-fade-in-up" style="animation-delay: 0.7s">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-chart-line text-purple-600 mr-2"></i>
                Account Activity
            </h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg bg-green-100 text-green-600 flex items-center justify-center">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Completed Orders</p>
                            <p class="text-xs text-gray-500">Successfully paid</p>
                        </div>
                    </div>
                    <span class="text-2xl font-bold gradient-text-4">{{ $user->orders->whereIn('status', ['paid', 'active', 'complete'])->count() }}</span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg bg-yellow-100 text-yellow-600 flex items-center justify-center">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Pending Orders</p>
                            <p class="text-xs text-gray-500">Awaiting payment</p>
                        </div>
                    </div>
                    <span class="text-2xl font-bold gradient-text-2">{{ $user->orders->where('status', 'pending')->count() }}</span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                            <i class="fas fa-server"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Total Services</p>
                            <p class="text-xs text-gray-500">All hosting services</p>
                        </div>
                    </div>
                    <span class="text-2xl font-bold gradient-text-3">{{ $services->count() }}</span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Last Login</p>
                            <p class="text-xs text-gray-500">Recent activity</p>
                        </div>
                    </div>
                    <span class="text-sm font-semibold text-gray-600">{{ $user->updated_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Orders & Services -->
    <div class="grid grid-cols-1 gap-6">
        <!-- Orders List -->
        <div class="modern-card p-6 animate-fade-in-up" style="animation-delay: 0.8s">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-shopping-cart text-purple-600 mr-2"></i>
                    Orders History
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-purple-50 to-pink-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Order ID</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Plan</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Date</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($user->orders as $order)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 font-semibold text-gray-900">#{{ $order->id }}</td>
                                <td class="px-4 py-3 text-gray-900">{{ $order->plan->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 font-semibold text-gray-900">TZS {{ number_format($order->price_tzs ?? 0) }}</td>
                                <td class="px-4 py-3">
                                    <span class="badge badge-{{ in_array($order->status, ['paid', 'active', 'complete']) ? 'success' : 'warning' }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $order->created_at->format('M d, Y') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-purple-600 hover:text-purple-800 font-semibold text-sm">
                                        View <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-2"></i>
                                    <p>No orders yet</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Services List -->
        <div class="modern-card p-6 animate-fade-in-up" style="animation-delay: 0.9s">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-server text-purple-600 mr-2"></i>
                    Hosting Services
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-purple-50 to-pink-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Service ID</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Domain</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Plan</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Created</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($services as $service)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 font-semibold text-gray-900">#{{ $service->id }}</td>
                                <td class="px-4 py-3 text-gray-900">{{ $service->domain ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-gray-900">{{ $service->plan->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3">
                                    <span class="badge badge-{{ $service->status === 'active' ? 'success' : ($service->status === 'provisioning' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($service->status ?? 'pending') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $service->created_at->format('M d, Y') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.services.show', $service) }}" class="text-purple-600 hover:text-purple-800 font-semibold text-sm">
                                        View <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-2"></i>
                                    <p>No services yet</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

