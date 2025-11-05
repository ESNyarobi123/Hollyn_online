@extends('layouts.admin')
@section('title', 'User Credentials - ' . $user->name)
@section('breadcrumb', 'Users / Credentials')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div class="animate-fade-in-up">
        <a href="{{ route('admin.users.show', $user) }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 font-medium">
            <i class="fas fa-arrow-left mr-2"></i> Back to User Details
        </a>
    </div>
    
    <!-- Warning Banner -->
    <div class="modern-card p-6 border-l-4 border-red-500 bg-red-50 animate-fade-in-up" style="animation-delay: 0.1s">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-bold text-red-800 mb-2">
                    <i class="fas fa-lock mr-2"></i>Sensitive Information
                </h3>
                <p class="text-sm text-red-700">
                    This page contains sensitive user credentials. Handle with care and never share these details publicly. All access is logged for security purposes.
                </p>
            </div>
        </div>
    </div>
    
    <!-- User Header -->
    <div class="modern-card p-8 gradient-bg-6 text-white overflow-hidden relative animate-fade-in-up" style="animation-delay: 0.2s">
        <div class="relative z-10">
            <div class="flex items-center space-x-6">
                <div class="w-20 h-20 rounded-2xl bg-white/20 flex items-center justify-center text-3xl font-bold backdrop-blur-sm">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div>
                    <h1 class="text-3xl font-bold mb-2">{{ $user->name }}</h1>
                    <p class="text-white/80 text-lg">{{ $user->email }}</p>
                    <div class="mt-3 flex items-center space-x-3">
                        <span class="badge badge-info">
                            <i class="fas fa-key mr-1"></i> Full Access View
                        </span>
                        <span class="text-white/70 text-sm">
                            <i class="fas fa-server mr-1"></i> {{ $services->count() }} Service(s)
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32"></div>
    </div>
    
    <!-- User Account Credentials -->
    <div class="modern-card p-6 animate-fade-in-up" style="animation-delay: 0.3s">
        <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
            <i class="fas fa-user-shield text-purple-600 mr-3"></i>
            User Account Information
        </h3>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="p-6 bg-gradient-to-br from-purple-50 to-white rounded-xl border border-purple-200">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="font-semibold text-gray-700">Account Email</h4>
                    <button onclick="copyToClipboard('{{ $user->email }}')" class="text-purple-600 hover:text-purple-800">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <p class="text-2xl font-bold gradient-text break-all">{{ $user->email }}</p>
            </div>
            
            <div class="p-6 bg-gradient-to-br from-blue-50 to-white rounded-xl border border-blue-200">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="font-semibold text-gray-700">User ID</h4>
                    <button onclick="copyToClipboard('{{ $user->id }}')" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <p class="text-2xl font-bold gradient-text-3">#{{ $user->id }}</p>
            </div>
            
            @if($user->phone)
                <div class="p-6 bg-gradient-to-br from-green-50 to-white rounded-xl border border-green-200">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-semibold text-gray-700">Phone Number</h4>
                        <button onclick="copyToClipboard('{{ $user->phone }}')" class="text-green-600 hover:text-green-800">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <p class="text-2xl font-bold gradient-text-4">{{ $user->phone }}</p>
                </div>
            @endif
            
            <div class="p-6 bg-gradient-to-br from-pink-50 to-white rounded-xl border border-pink-200">
                <h4 class="font-semibold text-gray-700 mb-4">Account Status</h4>
                <div class="flex items-center space-x-2">
                    <span class="badge badge-success">
                        <i class="fas fa-check-circle mr-1"></i> Active
                    </span>
                    <span class="badge badge-info">
                        <i class="fas fa-shield-alt mr-1"></i> Verified
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Service Credentials -->
    <div class="modern-card p-6 animate-fade-in-up" style="animation-delay: 0.4s">
        <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
            <i class="fas fa-server text-purple-600 mr-3"></i>
            Hosting Service Credentials
        </h3>
        
        @forelse($services as $service)
            <div class="mb-6 p-6 bg-gradient-to-br from-gray-50 to-white rounded-2xl border-2 border-gray-200 hover:border-purple-300 transition">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center space-x-4">
                        <div class="w-14 h-14 rounded-xl gradient-bg-{{ ($loop->index % 6) + 1 }} flex items-center justify-center text-white text-xl">
                            <i class="fas fa-server"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-gray-900">Service #{{ $service->id }}</h4>
                            <p class="text-sm text-gray-600">{{ $service->plan->name ?? 'N/A' }} - {{ $service->domain ?? 'No domain' }}</p>
                        </div>
                    </div>
                    <span class="badge badge-{{ $service->status === 'active' ? 'success' : ($service->status === 'provisioning' ? 'warning' : 'danger') }}">
                        <i class="fas fa-circle mr-1 text-xs"></i>
                        {{ ucfirst($service->status ?? 'pending') }}
                    </span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Domain -->
                    <div class="p-4 bg-white rounded-xl border border-gray-200">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center">
                                <i class="fas fa-globe mr-2 text-blue-500"></i> Domain
                            </label>
                            @if($service->domain)
                                <button onclick="copyToClipboard('{{ $service->domain }}')" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-copy"></i>
                                </button>
                            @endif
                        </div>
                        <p class="text-lg font-bold text-gray-900">{{ $service->domain ?? '—' }}</p>
                    </div>
                    
                    <!-- Username -->
                    <div class="p-4 bg-white rounded-xl border border-gray-200">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center">
                                <i class="fas fa-user mr-2 text-green-500"></i> Username
                            </label>
                            @if($service->webuzo_username || $service->panel_username)
                                <button onclick="copyToClipboard('{{ $service->webuzo_username ?? $service->panel_username }}')" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-copy"></i>
                                </button>
                            @endif
                        </div>
                        <p class="text-lg font-bold text-gray-900">{{ $service->webuzo_username ?? $service->panel_username ?? '—' }}</p>
                    </div>
                    
                    <!-- Password (Original/Decrypted) -->
                    <div class="p-4 bg-white rounded-xl border border-red-200" x-data="{ showPassword: false }">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center">
                                <i class="fas fa-lock mr-2 text-red-500"></i> Password
                                <span class="ml-2 px-2 py-0.5 bg-red-100 text-red-600 text-xs rounded-full">Original</span>
                            </label>
                            <div class="flex items-center space-x-2">
                                @if($service->webuzo_temp_password_enc)
                                    <button onclick="copyToClipboard('{{ $service->webuzo_temp_password_enc }}')" class="text-gray-400 hover:text-gray-600" title="Copy password">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                @endif
                                <button @click="showPassword = !showPassword" class="text-gray-400 hover:text-gray-600" title="Toggle visibility">
                                    <i :class="showPassword ? 'fa-eye-slash' : 'fa-eye'" class="fas"></i>
                                </button>
                            </div>
                        </div>
                        <p class="text-lg font-bold text-gray-900 font-mono break-all" x-show="showPassword">
                            {{ $service->webuzo_temp_password_enc ?? '—' }}
                        </p>
                        <p class="text-lg font-bold text-gray-900" x-show="!showPassword">
                            ••••••••••••••
                        </p>
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-info-circle mr-1"></i> This is the original decrypted password
                        </p>
                    </div>
                    
                    <!-- Control Panel URL -->
                    <div class="p-4 bg-white rounded-xl border border-gray-200">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center">
                                <i class="fas fa-link mr-2 text-purple-500"></i> Control Panel
                            </label>
                            @if($service->enduser_url)
                                <button onclick="copyToClipboard('{{ $service->enduser_url }}')" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-copy"></i>
                                </button>
                            @endif
                        </div>
                        @if($service->enduser_url)
                            <a href="{{ $service->enduser_url }}" target="_blank" class="text-lg font-bold text-purple-600 hover:text-purple-800 truncate block">
                                {{ $service->enduser_url }}
                                <i class="fas fa-external-link-alt ml-1 text-xs"></i>
                            </a>
                        @else
                            <p class="text-lg font-bold text-gray-400">—</p>
                        @endif
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="mt-4 flex flex-wrap gap-3">
                    @if($service->status === 'active' && $service->enduser_url)
                        <a href="{{ $service->enduser_url }}" target="_blank" class="px-4 py-2 gradient-bg-1 text-white rounded-lg font-semibold hover:shadow-lg transition btn-modern">
                            <i class="fas fa-external-link-alt mr-2"></i> Open Control Panel
                        </a>
                    @endif
                    
                    <a href="{{ route('admin.services.show', $service) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-semibold hover:bg-gray-200 transition">
                        <i class="fas fa-info-circle mr-2"></i> View Service Details
                    </a>
                    
                    @if(Route::has('admin.services.sendCredentials'))
                        <form method="POST" action="{{ route('admin.services.sendCredentials', $service) }}" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg font-semibold hover:bg-blue-200 transition">
                                <i class="fas fa-envelope mr-2"></i> Email Credentials
                            </button>
                        </form>
                    @endif
                </div>
                
                <!-- Service Info -->
                <div class="mt-4 pt-4 border-t border-gray-200 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500 mb-1">Created</p>
                        <p class="font-semibold text-gray-900">{{ $service->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 mb-1">Updated</p>
                        <p class="font-semibold text-gray-900">{{ $service->updated_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 mb-1">Order ID</p>
                        <p class="font-semibold text-gray-900">#{{ $service->order_id }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 mb-1">Plan</p>
                        <p class="font-semibold text-gray-900">{{ $service->plan->name ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-server text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">No Services Found</h3>
                <p class="text-gray-500">This user doesn't have any hosting services yet.</p>
            </div>
        @endforelse
    </div>
    
    <!-- Quick Actions -->
    <div class="flex flex-wrap gap-3 animate-fade-in-up" style="animation-delay: 0.5s">
        <a href="{{ route('admin.users.show', $user) }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition">
            <i class="fas fa-arrow-left mr-2"></i> Back to User
        </a>
        
        @if(!$user->isAdmin())
            <form method="POST" action="{{ route('admin.users.impersonate', $user) }}" class="inline">
                @csrf
                <button type="submit" class="px-6 py-3 gradient-bg-4 text-white rounded-xl font-semibold hover:shadow-lg transition btn-modern" onclick="return confirm('Login as {{ $user->name }}?')">
                    <i class="fas fa-sign-in-alt mr-2"></i> Login As This User
                </button>
            </form>
        @endif
    </div>
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

