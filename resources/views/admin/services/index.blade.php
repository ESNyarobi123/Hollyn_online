@extends('layouts.admin')
@section('title', 'Services Management')
@section('breadcrumb', 'Services')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 animate-fade-in-up">
  <div>
            <h1 class="text-3xl font-bold gradient-text">Services Management</h1>
            <p class="text-gray-500 mt-1">Manage all hosting services with full credential access</p>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 animate-fade-in-up" style="animation-delay: 0.1s">
        <div class="modern-card p-6 stats-card">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl gradient-bg-1 flex items-center justify-center">
                    <i class="fas fa-server text-2xl text-white"></i>
                </div>
                <span class="badge badge-info">All</span>
            </div>
            <h3 class="text-gray-500 text-sm font-medium mb-1">Total Services</h3>
            <p class="text-3xl font-bold gradient-text">{{ number_format($stats['total'] ?? $services->total()) }}</p>
        </div>
        
        <div class="modern-card p-6 stats-card">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl gradient-bg-4 flex items-center justify-center">
                    <i class="fas fa-check-circle text-2xl text-white"></i>
                </div>
                <span class="badge badge-success">Live</span>
            </div>
            <h3 class="text-gray-500 text-sm font-medium mb-1">Active Services</h3>
            <p class="text-3xl font-bold gradient-text-4">{{ number_format($stats['active'] ?? 0) }}</p>
        </div>
        
        <div class="modern-card p-6 stats-card">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl gradient-bg-5 flex items-center justify-center">
                    <i class="fas fa-sync-alt text-2xl text-white"></i>
                </div>
                <span class="badge badge-warning">Processing</span>
            </div>
            <h3 class="text-gray-500 text-sm font-medium mb-1">Provisioning</h3>
            <p class="text-3xl font-bold gradient-text-2">{{ number_format($stats['provisioning'] ?? 0) }}</p>
        </div>
        
        <div class="modern-card p-6 stats-card">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl gradient-bg-2 flex items-center justify-center">
                    <i class="fas fa-pause-circle text-2xl text-white"></i>
                </div>
                <span class="badge badge-danger">Inactive</span>
            </div>
            <h3 class="text-gray-500 text-sm font-medium mb-1">Suspended</h3>
            <p class="text-3xl font-bold gradient-text-2">{{ number_format($stats['suspended'] ?? 0) }}</p>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="modern-card p-6 animate-fade-in-up" style="animation-delay: 0.2s">
        <form method="GET" class="flex flex-col lg:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Search by domain, username..." 
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    >
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
  </div>
            
            <select 
                name="status" 
                class="px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            >
                <option value="">All Statuses</option>
                <option value="provisioning" {{ request('status') === 'provisioning' ? 'selected' : '' }}>Provisioning</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
    </select>
            
            <button type="submit" class="px-8 py-3 gradient-bg-1 text-white rounded-xl font-semibold hover:shadow-lg transition btn-modern">
                <i class="fas fa-filter mr-2"></i> Filter
            </button>
            
            @if(request('search') || request('status'))
                <a href="{{ route('admin.services.index') }}" class="px-8 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition">
                    <i class="fas fa-times mr-2"></i> Clear
                </a>
            @endif
  </form>
</div>

    <!-- Services Grid -->
    <div class="grid grid-cols-1 gap-6 animate-fade-in-up" style="animation-delay: 0.3s">
        @forelse($services as $service)
            @php
                $status = $service->status ?? 'provisioning';
                $statusColors = [
                    'active' => ['bg' => 'gradient-bg-4', 'badge' => 'badge-success', 'border' => 'border-green-300'],
                    'provisioning' => ['bg' => 'gradient-bg-5', 'badge' => 'badge-warning', 'border' => 'border-yellow-300'],
                    'suspended' => ['bg' => 'gradient-bg-2', 'badge' => 'badge-danger', 'border' => 'border-red-300'],
                    'cancelled' => ['bg' => 'gradient-bg-6', 'badge' => 'badge-danger', 'border' => 'border-gray-300'],
                ];
                $colors = $statusColors[$status] ?? $statusColors['provisioning'];
      @endphp
            
            <div class="modern-card p-6 border-2 {{ $colors['border'] }} hover:shadow-xl transition">
                <!-- Service Header -->
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 mb-6">
                    <div class="flex items-start space-x-4">
                        <div class="w-16 h-16 rounded-2xl {{ $colors['bg'] }} flex items-center justify-center text-white text-2xl flex-shrink-0">
                            <i class="fas fa-server"></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $service->domain ?? 'No Domain' }}</h3>
                            <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600">
                                <span class="flex items-center">
                                    <i class="fas fa-user mr-1.5 text-gray-400"></i>
                                    {{ optional(optional($service->order)->user)->name ?? 'N/A' }}
                                </span>
                                <span class="flex items-center">
                                    <i class="fas fa-box mr-1.5 text-gray-400"></i>
                                    {{ optional($service->plan)->name ?? 'N/A' }}
                                </span>
                                <span class="flex items-center">
                                    <i class="fas fa-hashtag mr-1.5 text-gray-400"></i>
                                    ID: {{ $service->id }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="{{ $colors['badge'] }} badge text-sm">
                            <i class="fas fa-circle mr-1 text-xs"></i>
            {{ ucfirst($status) }}
          </span>
                    </div>
                </div>
                
                <!-- Credentials Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <!-- Domain -->
                    <div class="p-4 bg-gradient-to-br from-blue-50 to-white rounded-xl border border-blue-200">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center">
                                <i class="fas fa-globe mr-1.5 text-blue-500"></i> Domain
                            </label>
                            @if($service->domain)
                                <button onclick="copyToClipboard('{{ $service->domain }}')" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-copy"></i>
                                </button>
                            @endif
                        </div>
                        <p class="text-sm font-bold text-gray-900 truncate" title="{{ $service->domain ?? '—' }}">
                            {{ $service->domain ?? '—' }}
                        </p>
                    </div>
                    
                    <!-- Username -->
                    <div class="p-4 bg-gradient-to-br from-green-50 to-white rounded-xl border border-green-200">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center">
                                <i class="fas fa-user mr-1.5 text-green-500"></i> Username
                            </label>
                            @if($service->webuzo_username || $service->panel_username)
                                <button onclick="copyToClipboard('{{ $service->webuzo_username ?? $service->panel_username }}')" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-copy"></i>
                                </button>
                            @endif
                        </div>
                        <p class="text-sm font-bold text-gray-900 font-mono truncate" title="{{ $service->webuzo_username ?? $service->panel_username ?? '—' }}">
                            {{ $service->webuzo_username ?? $service->panel_username ?? '—' }}
                        </p>
                    </div>
                    
                    <!-- Password (Original) -->
                    <div class="p-4 bg-gradient-to-br from-red-50 to-white rounded-xl border border-red-200" x-data="{ show: false }">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center">
                                <i class="fas fa-lock mr-1.5 text-red-500"></i> Password
                                <span class="ml-1.5 px-1.5 py-0.5 bg-red-100 text-red-600 text-[10px] rounded-full">Original</span>
                            </label>
                            <div class="flex items-center space-x-2">
                                @if($service->webuzo_temp_password_enc)
                                    <button onclick="copyToClipboard('{{ $service->webuzo_temp_password_enc }}')" class="text-gray-400 hover:text-gray-600" title="Copy original password">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                @endif
                                <button @click="show = !show" class="text-gray-400 hover:text-gray-600" title="Toggle visibility">
                                    <i :class="show ? 'fa-eye-slash' : 'fa-eye'" class="fas"></i>
                                </button>
                            </div>
                        </div>
                        <p class="text-sm font-bold text-gray-900 font-mono truncate" x-show="show" title="{{ $service->webuzo_temp_password_enc ?? '—' }}">
                            {{ $service->webuzo_temp_password_enc ?? '—' }}
                        </p>
                        <p class="text-sm font-bold text-gray-900" x-show="!show">
                            ••••••••••
                        </p>
                    </div>
                    
                    <!-- Panel URL -->
                    <div class="p-4 bg-gradient-to-br from-purple-50 to-white rounded-xl border border-purple-200">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center">
                                <i class="fas fa-link mr-1.5 text-purple-500"></i> Panel
                            </label>
                            @if($service->enduser_url ?? config('services.webuzo.enduser_url'))
                                <button onclick="copyToClipboard('{{ $service->enduser_url ?? config('services.webuzo.enduser_url') }}')" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-copy"></i>
                                </button>
                            @endif
                        </div>
                        @if($service->enduser_url ?? config('services.webuzo.enduser_url'))
                            <a href="{{ $service->enduser_url ?? config('services.webuzo.enduser_url') }}" target="_blank" class="text-sm font-bold text-purple-600 hover:text-purple-800 truncate block" title="{{ $service->enduser_url ?? config('services.webuzo.enduser_url') }}">
                                <i class="fas fa-external-link-alt mr-1"></i> Open
                            </a>
                        @else
                            <p class="text-sm font-bold text-gray-400">—</p>
                        @endif
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex flex-wrap gap-2 pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.services.show', $service) }}" class="px-4 py-2 bg-purple-100 text-purple-700 rounded-lg font-semibold hover:bg-purple-200 transition text-sm">
                        <i class="fas fa-eye mr-1.5"></i> View Details
                    </a>
                    
                    @if($status === 'active' && ($service->enduser_url ?? config('services.webuzo.enduser_url')))
                        <a href="{{ $service->enduser_url ?? config('services.webuzo.enduser_url') }}" target="_blank" class="px-4 py-2 gradient-bg-1 text-white rounded-lg font-semibold hover:shadow-lg transition text-sm btn-modern">
                            <i class="fas fa-external-link-alt mr-1.5"></i> Control Panel
                        </a>
                    @endif
                    
                    @if(Route::has('admin.services.sendCredentials'))
                        <form method="POST" action="{{ route('admin.services.sendCredentials', $service) }}" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg font-semibold hover:bg-blue-200 transition text-sm">
                                <i class="fas fa-envelope mr-1.5"></i> Email Credentials
                            </button>
                        </form>
                    @endif
                    
                    @if($status !== 'active')
                        <form method="POST" action="{{ route('admin.services.activate', $service) }}" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-green-100 text-green-700 rounded-lg font-semibold hover:bg-green-200 transition text-sm">
                                <i class="fas fa-check mr-1.5"></i> Activate
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.services.suspend', $service) }}" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg font-semibold hover:bg-yellow-200 transition text-sm">
                                <i class="fas fa-pause mr-1.5"></i> Suspend
                            </button>
                        </form>
                    @endif
                    
                    <form method="POST" action="{{ route('admin.services.reprovision', $service) }}" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-orange-100 text-orange-700 rounded-lg font-semibold hover:bg-orange-200 transition text-sm">
                            <i class="fas fa-sync-alt mr-1.5"></i> Re-provision
                        </button>
                    </form>
                    
                    <a href="{{ route('admin.users.show', optional($service->order)->user) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-semibold hover:bg-gray-200 transition text-sm">
                        <i class="fas fa-user mr-1.5"></i> View User
                    </a>
                </div>
                
                <!-- Service Info Footer -->
                <div class="mt-4 pt-4 border-t border-gray-200 grid grid-cols-2 md:grid-cols-4 gap-4 text-xs text-gray-600">
                    <div>
                        <span class="text-gray-400">Created:</span>
                        <span class="font-semibold ml-1">{{ $service->created_at->format('M d, Y') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400">Updated:</span>
                        <span class="font-semibold ml-1">{{ $service->updated_at->format('M d, Y') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400">Order:</span>
                        <a href="{{ route('admin.orders.show', $service->order_id) }}" class="font-semibold ml-1 text-purple-600 hover:text-purple-800">
                            #{{ $service->order_id }}
                        </a>
                    </div>
                    <div>
                        <span class="text-gray-400">User:</span>
                        <a href="{{ route('admin.users.show', optional($service->order)->user) }}" class="font-semibold ml-1 text-purple-600 hover:text-purple-800">
                            {{ optional(optional($service->order)->user)->name ?? 'N/A' }}
                        </a>
                    </div>
                </div>
            </div>
      @empty
            <div class="modern-card p-12 text-center">
                <div class="w-24 h-24 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-server text-5xl text-gray-300"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-700 mb-2">No Services Found</h3>
                <p class="text-gray-500 mb-4">No services match your current filters</p>
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.services.index') }}" class="inline-flex items-center px-6 py-3 gradient-bg-1 text-white rounded-xl font-semibold hover:shadow-lg transition btn-modern">
                        <i class="fas fa-times mr-2"></i> Clear Filters
                    </a>
                @endif
            </div>
      @endforelse
    </div>
    
    <!-- Pagination -->
    @if($services->hasPages())
        <div class="modern-card p-4 animate-fade-in-up">
            {{ $services->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            // Show success notification
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in-up';
            notification.innerHTML = '<i class="fas fa-check mr-2"></i> Copied to clipboard!';
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 2000);
        }).catch(function(err) {
            console.error('Failed to copy:', err);
            alert('Failed to copy to clipboard');
        });
    }
</script>
@endpush
@endsection
