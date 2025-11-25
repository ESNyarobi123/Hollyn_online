@extends('layouts.app')
@section('title', 'My Dashboard')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-950 font-sans" x-data="{ sidebarOpen: false }">
    
    <!-- Mobile Header -->
    <div class="lg:hidden bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-4 py-3 flex items-center justify-between sticky top-0 z-30">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-600 to-indigo-600 flex items-center justify-center text-white font-bold shadow-lg shadow-violet-500/20">H</div>
            <span class="font-bold text-slate-900 dark:text-white">Hollyn</span>
        </div>
        <button @click="sidebarOpen = !sidebarOpen" class="p-2 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
        </button>
    </div>

    <div class="flex">
        <!-- Sidebar Overlay -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity class="fixed inset-0 bg-slate-900/50 z-40 lg:hidden backdrop-blur-sm"></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed lg:static inset-y-0 left-0 z-50 w-72 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 transform lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col h-screen">
            <!-- Logo -->
            <div class="p-6 border-b border-slate-100 dark:border-slate-800 hidden lg:flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-600 to-indigo-600 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-violet-500/20">H</div>
                <div>
                    <h1 class="font-bold text-lg text-slate-900 dark:text-white leading-tight">Hollyn<span class="text-violet-600">Online</span></h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Client Portal</p>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-300 font-medium transition-all group">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                    Dashboard
                </a>
                
                <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white font-medium transition-all group">
                    <svg class="w-5 h-5 group-hover:text-violet-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 01-2 2v4a2 2 0 012 2h14a2 2 0 012-2v-4a2 2 0 01-2-2m-2-4h.01M17 16h.01" /></svg>
                    My Services
                </a>

                <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white font-medium transition-all group">
                    <svg class="w-5 h-5 group-hover:text-violet-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                    Orders & Invoices
                </a>

                <div class="pt-6 pb-2 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Account</div>
                
                <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white font-medium transition-all group">
                    <svg class="w-5 h-5 group-hover:text-violet-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    Profile Settings
                </a>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 dark:text-slate-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 font-medium transition-all group">
                        <svg class="w-5 h-5 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                        Sign Out
                    </button>
                </form>
            </nav>

            <!-- User Profile Snippet -->
            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700">
                    <div class="w-10 h-10 rounded-full bg-violet-100 dark:bg-violet-900/50 flex items-center justify-center text-violet-600 dark:text-violet-400 font-bold">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-900 dark:text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ Auth::user()->email }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 min-w-0 overflow-y-auto h-screen">
            <!-- Header -->
            <header class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 sticky top-0 z-20">
                <div class="px-8 py-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Dashboard</h1>
                        <p class="text-slate-500 dark:text-slate-400 mt-1">Welcome back! Here's an overview of your account.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('plans') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-semibold hover:opacity-90 transition-all shadow-lg shadow-slate-500/20">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                            New Service
                        </a>
                    </div>
                </div>
            </header>

            <div class="p-8 max-w-7xl mx-auto space-y-8">
                
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Active Services -->
                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-all group">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-3 rounded-xl bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 01-2 2v4a2 2 0 012 2h14a2 2 0 012-2v-4a2 2 0 01-2-2m-2-4h.01M17 16h.01" /></svg>
                            </div>
                            <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">Active</span>
                        </div>
                        <h3 class="text-3xl font-bold text-slate-900 dark:text-white mb-1">{{ $stats['services_active'] ?? 0 }}</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Running Services</p>
                    </div>

                    <!-- Pending Orders -->
                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-all group">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            @if(($stats['services_provisioning'] ?? 0) > 0)
                                <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300">Action Needed</span>
                            @endif
                        </div>
                        <h3 class="text-3xl font-bold text-slate-900 dark:text-white mb-1">{{ $stats['services_provisioning'] ?? 0 }}</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Pending Setup</p>
                    </div>

                    <!-- Total Orders -->
                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-all group">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-3 rounded-xl bg-violet-50 dark:bg-violet-900/20 text-violet-600 dark:text-violet-400 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                            </div>
                        </div>
                        <h3 class="text-3xl font-bold text-slate-900 dark:text-white mb-1">{{ $stats['orders_total'] ?? 0 }}</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Total Orders</p>
                    </div>

                    <!-- Total Spent -->
                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-all group">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-3 rounded-xl bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                        </div>
                        <h3 class="text-3xl font-bold text-slate-900 dark:text-white mb-1">{{ number_format($stats['revenue_tzs'] ?? 0) }}</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">TZS Spent</p>
                    </div>
                </div>

                <!-- Action Banner (if needed) -->
                @if($provisionableOrder)
                <div class="bg-gradient-to-r from-violet-600 to-indigo-600 rounded-2xl p-6 md:p-8 text-white shadow-xl shadow-violet-500/20 relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                    <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                        <div>
                            <h2 class="text-2xl font-bold mb-2">Setup Your Hosting</h2>
                            <p class="text-violet-100 max-w-xl">You have a paid order for <strong>{{ $provisionableOrder->plan->name }}</strong> that is ready to be set up. Complete the process now to get started.</p>
                        </div>
                        <form method="POST" action="{{ route('me.services.provisionLatest') }}">
                            @csrf
                            <button type="submit" class="px-6 py-3 bg-white text-violet-600 font-bold rounded-xl hover:bg-violet-50 transition-colors shadow-lg">
                                Finish Setup Now
                            </button>
                        </form>
                    </div>
                </div>
                @endif

                <!-- Active Services List -->
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white">Your Services</h2>
                        @if($services->isNotEmpty())
                            <a href="#" class="text-sm font-medium text-violet-600 hover:text-violet-700 dark:text-violet-400 hover:underline">View All</a>
                        @endif
                    </div>

                    @if($services->isEmpty())
                        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-dashed border-slate-300 dark:border-slate-700 p-12 text-center">
                            <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 01-2 2v4a2 2 0 012 2h14a2 2 0 012-2v-4a2 2 0 01-2-2m-2-4h.01M17 16h.01" /></svg>
                            </div>
                            <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-1">No Services Yet</h3>
                            <p class="text-slate-500 dark:text-slate-400 mb-6">Get started by purchasing a hosting plan.</p>
                            <a href="{{ route('plans') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-violet-600 text-white font-semibold hover:bg-violet-700 transition-colors">
                                Browse Plans
                            </a>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                            @foreach($services as $svc)
                                @php
                                    $status = strtolower($svc->status ?? 'pending');
                                    $isActive = $status === 'active';
                                    $panelUrl = $svc->panel_url ?? $svc->enduser_url;
                                    
                                    // Decrypt password safely
                                    $password = '********';
                                    try {
                                        if ($svc->webuzo_temp_password_enc) {
                                            $password = \Illuminate\Support\Facades\Crypt::decryptString($svc->webuzo_temp_password_enc);
                                        } elseif ($svc->webuzo_password || $svc->panel_password) {
                                            $password = $svc->webuzo_password ?? $svc->panel_password;
                                        }
                                    } catch (\Throwable $e) {}
                                @endphp
                                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm hover:shadow-md transition-all group relative overflow-hidden">
                                    @if($isActive)
                                        <div class="absolute top-0 right-0 w-16 h-16 bg-gradient-to-br from-green-400/20 to-emerald-400/20 rounded-bl-full -mr-8 -mt-8"></div>
                                    @endif

                                    <div class="flex justify-between items-start mb-6 relative z-10">
                                        <div>
                                            <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-1">{{ $svc->plan->name ?? 'Hosting Plan' }}</h3>
                                            <a href="http://{{ $svc->domain }}" target="_blank" class="text-sm text-violet-600 hover:underline flex items-center gap-1">
                                                {{ $svc->domain }}
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                            </a>
                                        </div>
                                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide
                                            {{ $isActive ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300' }}">
                                            {{ $status }}
                                        </span>
                                    </div>

                                    <div class="space-y-3 mb-6 bg-slate-50 dark:bg-slate-800/50 rounded-xl p-4 border border-slate-100 dark:border-slate-700">
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-slate-500 dark:text-slate-400">Username</span>
                                            <span class="font-mono font-medium text-slate-700 dark:text-slate-200">{{ $svc->webuzo_username ?? $svc->panel_username ?? '—' }}</span>
                                        </div>
                                        <div class="flex justify-between items-center text-sm" x-data="{ show: false }">
                                            <span class="text-slate-500 dark:text-slate-400">Password</span>
                                            <div class="flex items-center gap-2">
                                                <span class="font-mono font-medium text-slate-700 dark:text-slate-200" x-text="show ? '{{ $password }}' : '••••••••'"></span>
                                                <button @click="show = !show" class="text-slate-400 hover:text-violet-600 transition-colors">
                                                    <svg x-show="!show" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                                    <svg x-show="show" x-cloak class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    @if($isActive && $panelUrl)
                                        <a href="{{ $panelUrl }}" target="_blank" class="block w-full py-3 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold text-center hover:opacity-90 transition-opacity">
                                            Login to Control Panel
                                        </a>
                                    @else
                                        <button disabled class="block w-full py-3 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-400 font-bold text-center cursor-not-allowed">
                                            {{ $isActive ? 'Panel Unavailable' : 'Setting Up...' }}
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Recent Orders -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center">
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Recent Orders</h3>
                        <a href="#" class="text-sm font-medium text-violet-600 hover:underline">View All</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 uppercase tracking-wider font-semibold text-xs">
                                <tr>
                                    <th class="px-6 py-4">Order ID</th>
                                    <th class="px-6 py-4">Service</th>
                                    <th class="px-6 py-4">Date</th>
                                    <th class="px-6 py-4">Amount</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                @forelse($recentOrders as $order)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                        <td class="px-6 py-4 font-mono text-slate-600 dark:text-slate-300">#{{ $order->id }}</td>
                                        <td class="px-6 py-4 font-medium text-slate-900 dark:text-white">{{ $order->plan->name }}</td>
                                        <td class="px-6 py-4 text-slate-500 dark:text-slate-400">{{ $order->created_at->format('M d, Y') }}</td>
                                        <td class="px-6 py-4 font-medium text-slate-900 dark:text-white">TZS {{ number_format($order->price_tzs) }}</td>
                                        <td class="px-6 py-4">
                                            @if(in_array($order->status, ['paid', 'active', 'complete']))
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Paid
                                                </span>
                                            @elseif($order->status === 'pending')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Pending
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> {{ ucfirst($order->status) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            @if($order->status === 'pending')
                                                <a href="{{ route('pay.start', $order->id) }}" class="text-violet-600 hover:text-violet-700 font-medium hover:underline">Pay Now</a>
                                            @else
                                                <a href="{{ route('order.summary', $order->id) }}" class="text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 font-medium">View</a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">
                                            No recent orders found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>
</div>
@endsection