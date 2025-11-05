@extends('layouts.admin')
@section('title', 'Service Details #' . $service->id)
@section('breadcrumb', 'Services / #' . $service->id)

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div class="animate-fade-in-up">
        <a href="{{ route('admin.services.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 font-medium">
            <i class="fas fa-arrow-left mr-2"></i> Back to Services
        </a>
    </div>
    
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
    
    <!-- Service Header -->
    <div class="modern-card p-8 {{ $colors['bg'] }} text-white overflow-hidden relative animate-fade-in-up" style="animation-delay: 0.1s">
        <div class="relative z-10">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex items-center space-x-6">
                    <div class="w-24 h-24 rounded-2xl bg-white/20 flex items-center justify-center text-4xl backdrop-blur-sm">
                        <i class="fas fa-server"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <h1 class="text-3xl font-bold">{{ $service->domain ?? 'Service #' . $service->id }}</h1>
                            <span class="badge {{ $colors['badge'] }} text-sm">
                                <i class="fas fa-circle mr-1 text-xs"></i>
                                {{ ucfirst($status) }}
                            </span>
                        </div>
                        <p class="text-white/80 text-lg mb-2">{{ optional($service->plan)->name ?? 'No Plan' }}</p>
                        <div class="flex items-center space-x-4 text-sm text-white/70">
                            <span><i class="fas fa-user mr-1"></i> {{ optional(optional($service->order)->user)->name ?? 'N/A' }}</span>
                            <span><i class="fas fa-hashtag mr-1"></i> Service ID: {{ $service->id }}</span>
                            <span><i class="fas fa-calendar mr-1"></i> {{ $service->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-3">
                    @if($status !== 'active')
                        <form method="POST" action="{{ route('admin.services.activate', $service) }}">
                            @csrf
                            <button type="submit" class="px-6 py-3 bg-white text-green-600 rounded-xl font-semibold hover:shadow-lg transition btn-modern">
                                <i class="fas fa-check mr-2"></i> Activate
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.services.suspend', $service) }}">
                            @csrf
                            <button type="submit" class="px-6 py-3 bg-white/20 backdrop-blur-sm text-white rounded-xl font-semibold hover:bg-white/30 transition btn-modern">
                                <i class="fas fa-pause mr-2"></i> Suspend
                            </button>
                        </form>
                    @endif
                    
                    <form method="POST" action="{{ route('admin.services.reprovision', $service) }}">
                        @csrf
                        <button type="submit" class="px-6 py-3 bg-white text-purple-600 rounded-xl font-semibold hover:shadow-lg transition btn-modern">
                            <i class="fas fa-sync-alt mr-2"></i> Re-provision
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-white/5 rounded-full -ml-48 -mb-48"></div>
    </div>
    
    <!-- Credentials Section -->
    <div class="modern-card p-6 border-l-4 border-purple-500 animate-fade-in-up" style="animation-delay: 0.2s">
        <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
            <i class="fas fa-key text-purple-600 mr-3"></i>
            Service Credentials
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Domain -->
            <div class="p-5 bg-gradient-to-br from-blue-50 to-white rounded-xl border border-blue-200">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-semibold text-gray-600 uppercase tracking-wider flex items-center">
                        <i class="fas fa-globe mr-2 text-blue-500"></i> Domain
                    </label>
                    @if($service->domain)
                        <button onclick="copyToClipboard('{{ $service->domain }}')" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-copy"></i>
                        </button>
                    @endif
                </div>
                <p class="text-2xl font-bold gradient-text-3 break-all">{{ $service->domain ?? '—' }}</p>
            </div>
            
            <!-- Username -->
            <div class="p-5 bg-gradient-to-br from-green-50 to-white rounded-xl border border-green-200">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-semibold text-gray-600 uppercase tracking-wider flex items-center">
                        <i class="fas fa-user mr-2 text-green-500"></i> Username
                    </label>
                    @if($service->webuzo_username || $service->panel_username)
                        <button onclick="copyToClipboard('{{ $service->webuzo_username ?? $service->panel_username }}')" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-copy"></i>
                        </button>
                    @endif
                </div>
                <p class="text-2xl font-bold gradient-text-4 font-mono">{{ $service->webuzo_username ?? $service->panel_username ?? '—' }}</p>
            </div>
            
            <!-- Password (Original/Decrypted) -->
            <div class="p-5 bg-gradient-to-br from-red-50 to-white rounded-xl border border-red-200" x-data="{ showPassword: false }">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-semibold text-gray-600 uppercase tracking-wider flex items-center">
                        <i class="fas fa-lock mr-2 text-red-500"></i> Password
                        <span class="ml-2 px-2 py-1 bg-red-100 text-red-600 text-xs rounded-full">Original Password</span>
                    </label>
                    <div class="flex items-center space-x-2">
                        @if($service->webuzo_temp_password_enc)
                            <button onclick="copyToClipboard('{{ $service->webuzo_temp_password_enc }}')" class="text-gray-400 hover:text-gray-600" title="Copy original password">
                                <i class="fas fa-copy"></i>
                            </button>
                        @endif
                        <button @click="showPassword = !showPassword" class="text-gray-400 hover:text-gray-600" title="Toggle visibility">
                            <i :class="showPassword ? 'fa-eye-slash' : 'fa-eye'" class="fas"></i>
                        </button>
                    </div>
                </div>
                <p class="text-2xl font-bold gradient-text-2 font-mono break-all" x-show="showPassword">
                    {{ $service->webuzo_temp_password_enc ?? '—' }}
                </p>
                <p class="text-2xl font-bold gradient-text-2" x-show="!showPassword">
                    ••••••••••••••
                </p>
                <p class="text-xs text-gray-600 mt-3">
                    <i class="fas fa-info-circle mr-1"></i> This is the original decrypted password from the database
                </p>
            </div>
            
            <!-- Control Panel URL -->
            <div class="p-5 bg-gradient-to-br from-purple-50 to-white rounded-xl border border-purple-200">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-semibold text-gray-600 uppercase tracking-wider flex items-center">
                        <i class="fas fa-link mr-2 text-purple-500"></i> Control Panel
                    </label>
                    @if($service->enduser_url ?? config('services.webuzo.enduser_url'))
                        <button onclick="copyToClipboard('{{ $service->enduser_url ?? config('services.webuzo.enduser_url') }}')" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-copy"></i>
                        </button>
                    @endif
                </div>
                @if($service->enduser_url ?? config('services.webuzo.enduser_url'))
                    <a href="{{ $service->enduser_url ?? config('services.webuzo.enduser_url') }}" target="_blank" class="text-lg font-bold text-purple-600 hover:text-purple-800 break-all">
                        {{ $service->enduser_url ?? config('services.webuzo.enduser_url') }}
                        <i class="fas fa-external-link-alt ml-2"></i>
                    </a>
                @else
                    <p class="text-lg font-bold text-gray-400">—</p>
                @endif
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="mt-6 flex flex-wrap gap-3">
            @if($status === 'active' && ($service->enduser_url ?? config('services.webuzo.enduser_url')))
                <a href="{{ $service->enduser_url ?? config('services.webuzo.enduser_url') }}" target="_blank" class="px-6 py-3 gradient-bg-1 text-white rounded-xl font-semibold hover:shadow-lg transition btn-modern">
                    <i class="fas fa-external-link-alt mr-2"></i> Open Control Panel
                </a>
            @endif
            
            @if(Route::has('admin.services.sendCredentials'))
                <form method="POST" action="{{ route('admin.services.sendCredentials', $service) }}" class="inline">
                    @csrf
                    <button type="submit" class="px-6 py-3 bg-blue-100 text-blue-700 rounded-xl font-semibold hover:bg-blue-200 transition">
                        <i class="fas fa-envelope mr-2"></i> Email Credentials to User
                    </button>
                </form>
            @endif
        </div>
    </div>
    
    <!-- Service Details & Order Info -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Service Details -->
        <div class="modern-card p-6 animate-fade-in-up" style="animation-delay: 0.3s">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-info-circle text-purple-600 mr-2"></i>
                Service Details
            </h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <span class="text-gray-600">Service ID</span>
                    <span class="font-bold text-gray-900">#{{ $service->id }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <span class="text-gray-600">Status</span>
                    <span class="{{ $colors['badge'] }} badge">{{ ucfirst($status) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <span class="text-gray-600">Plan</span>
                    <span class="font-bold text-gray-900">{{ optional($service->plan)->name ?? 'N/A' }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <span class="text-gray-600">Domain</span>
                    <span class="font-bold text-gray-900">{{ $service->domain ?? '—' }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <span class="text-gray-600">Created At</span>
                    <span class="font-bold text-gray-900">{{ $service->created_at->format('M d, Y H:i') }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <span class="text-gray-600">Updated At</span>
                    <span class="font-bold text-gray-900">{{ $service->updated_at->format('M d, Y H:i') }}</span>
                </div>
            </div>
        </div>
        
        <!-- Order Information -->
        <div class="modern-card p-6 animate-fade-in-up" style="animation-delay: 0.4s">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-shopping-cart text-purple-600 mr-2"></i>
                Order Information
            </h3>
            @if($service->order)
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <span class="text-gray-600">Order ID</span>
                        <a href="{{ route('admin.orders.show', $service->order) }}" class="font-bold text-purple-600 hover:text-purple-800">
                            #{{ $service->order->id }}
                        </a>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <span class="text-gray-600">Customer</span>
                        <a href="{{ route('admin.users.show', $service->order->user) }}" class="font-bold text-purple-600 hover:text-purple-800">
                            {{ optional($service->order->user)->name ?? 'N/A' }}
                        </a>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <span class="text-gray-600">Email</span>
                        <span class="font-bold text-gray-900">{{ optional($service->order->user)->email ?? 'N/A' }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <span class="text-gray-600">Order Status</span>
                        <span class="badge badge-{{ in_array($service->order->status, ['paid', 'active', 'complete']) ? 'success' : 'warning' }}">
                            {{ ucfirst($service->order->status) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <span class="text-gray-600">Amount</span>
                        <span class="font-bold text-gray-900">TZS {{ number_format($service->order->price_tzs ?? 0) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <span class="text-gray-600">Order Date</span>
                        <span class="font-bold text-gray-900">{{ $service->order->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-2"></i>
                    <p>No order linked</p>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Provisioning Logs -->
    <div class="modern-card p-6 animate-fade-in-up" style="animation-delay: 0.5s">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                <i class="fas fa-history text-purple-600 mr-2"></i>
                Provisioning Logs
            </h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-purple-50 to-pink-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Step</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Success</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Created</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Request</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Response</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 font-semibold">{{ $log->id }}</td>
                            <td class="px-4 py-3">{{ $log->step }}</td>
                            <td class="px-4 py-3">
                                <span class="badge badge-{{ $log->success ? 'success' : 'danger' }}">
                                    <i class="fas fa-{{ $log->success ? 'check' : 'times' }} mr-1"></i>
                                    {{ $log->success ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $log->created_at->format('M d, Y H:i') }}</td>
                            <td class="px-4 py-3">
                                <pre class="text-xs bg-gray-50 p-2 rounded max-w-xs overflow-x-auto">{{ is_array($log->request) ? json_encode($log->request, JSON_PRETTY_PRINT) : $log->request }}</pre>
                            </td>
                            <td class="px-4 py-3">
                                <pre class="text-xs bg-gray-50 p-2 rounded max-w-xs overflow-x-auto">{{ is_array($log->response) ? json_encode($log->response, JSON_PRETTY_PRINT) : $log->response }}</pre>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-2"></i>
                                <p>No logs yet</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($logs->hasPages())
            <div class="mt-4 pt-4 border-t border-gray-200">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
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
