@extends('layouts.admin')
@section('title', 'Users Management')
@section('breadcrumb', 'Users')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 animate-fade-in-up">
        <div>
            <h1 class="text-3xl font-bold gradient-text">Users Management</h1>
            <p class="text-gray-500 mt-1">Manage all users, view credentials, and access accounts</p>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-fade-in-up" style="animation-delay: 0.1s">
        <div class="modern-card p-6 stats-card">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl gradient-bg-1 flex items-center justify-center">
                    <i class="fas fa-users text-2xl text-white"></i>
                </div>
                <span class="badge badge-info">All</span>
            </div>
            <h3 class="text-gray-500 text-sm font-medium mb-1">Total Users</h3>
            <p class="text-3xl font-bold gradient-text">{{ number_format($stats['total'] ?? 0) }}</p>
        </div>
        
        <div class="modern-card p-6 stats-card">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl gradient-bg-2 flex items-center justify-center">
                    <i class="fas fa-crown text-2xl text-white"></i>
                </div>
                <span class="badge badge-warning">Admins</span>
            </div>
            <h3 class="text-gray-500 text-sm font-medium mb-1">Admin Users</h3>
            <p class="text-3xl font-bold gradient-text-2">{{ number_format($stats['admins'] ?? 0) }}</p>
        </div>
        
        <div class="modern-card p-6 stats-card">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl gradient-bg-4 flex items-center justify-center">
                    <i class="fas fa-user text-2xl text-white"></i>
                </div>
                <span class="badge badge-success">Regular</span>
            </div>
            <h3 class="text-gray-500 text-sm font-medium mb-1">Regular Users</h3>
            <p class="text-3xl font-bold gradient-text-4">{{ number_format($stats['users'] ?? 0) }}</p>
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
                        placeholder="Search by name, email, or phone..." 
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    >
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            
            <select 
                name="role" 
                class="px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            >
                <option value="">All Roles</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
            </select>
            
            <button type="submit" class="px-8 py-3 gradient-bg-1 text-white rounded-xl font-semibold hover:shadow-lg transition btn-modern">
                <i class="fas fa-filter mr-2"></i> Filter
            </button>
            
            @if(request('search') || request('role'))
                <a href="{{ route('admin.users.index') }}" class="px-8 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition">
                    <i class="fas fa-times mr-2"></i> Clear
                </a>
            @endif
        </form>
    </div>
    
    <!-- Users Table -->
    <div class="modern-card overflow-hidden animate-fade-in-up" style="animation-delay: 0.3s">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-purple-50 to-pink-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">User</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Stats</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Joined</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 rounded-full gradient-bg-{{ ($loop->index % 6) + 1 }} flex items-center justify-center text-white font-bold text-lg">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $user->name }}</h4>
                                        <p class="text-sm text-gray-500">ID: #{{ $user->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <p class="text-sm text-gray-900">
                                        <i class="fas fa-envelope text-gray-400 mr-2"></i>
                                        {{ $user->email }}
                                    </p>
                                    @if($user->phone)
                                        <p class="text-sm text-gray-500">
                                            <i class="fas fa-phone text-gray-400 mr-2"></i>
                                            {{ $user->phone }}
                                        </p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->role === 'admin')
                                    <span class="badge badge-warning">
                                        <i class="fas fa-crown mr-1"></i> Admin
                                    </span>
                                @else
                                    <span class="badge badge-info">
                                        <i class="fas fa-user mr-1"></i> User
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <span class="badge badge-success">
                                        <i class="fas fa-shopping-cart mr-1"></i>
                                        {{ $user->orders_count ?? 0 }}
                                    </span>
                                    <span class="badge badge-purple">
                                        <i class="fas fa-server mr-1"></i>
                                        {{ $user->services_count ?? 0 }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <p class="text-gray-900 font-medium">{{ $user->created_at->format('M d, Y') }}</p>
                                    <p class="text-gray-500">{{ $user->created_at->diffForHumans() }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end space-x-2">
                                    <!-- View Details -->
                                    <a 
                                        href="{{ route('admin.users.show', $user) }}" 
                                        class="w-9 h-9 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center hover:bg-blue-200 transition"
                                        title="View Details"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <!-- View Credentials -->
                                    @if(Route::has('admin.users.credentials'))
                                        <a 
                                            href="{{ route('admin.users.credentials', $user) }}" 
                                            class="w-9 h-9 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center hover:bg-purple-200 transition"
                                            title="View Credentials"
                                        >
                                            <i class="fas fa-key"></i>
                                        </a>
                                    @endif
                                    
                                    <!-- Impersonate (Login As) -->
                                    @if(!$user->isAdmin())
                                        <form method="POST" action="{{ route('admin.users.impersonate', $user) }}" class="inline">
                                            @csrf
                                            <button 
                                                type="submit" 
                                                class="w-9 h-9 rounded-lg bg-green-100 text-green-600 flex items-center justify-center hover:bg-green-200 transition"
                                                title="Login as {{ $user->name }}"
                                                onclick="return confirm('Login as {{ $user->name }}?')"
                                            >
                                                <i class="fas fa-sign-in-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <!-- Delete -->
                                    @if(!$user->isAdmin() && $user->id !== Auth::id())
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button 
                                                type="submit" 
                                                class="w-9 h-9 rounded-lg bg-red-100 text-red-600 flex items-center justify-center hover:bg-red-200 transition"
                                                title="Delete User"
                                                onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-500">
                                    <i class="fas fa-user-slash text-6xl mb-4 text-gray-300"></i>
                                    <p class="text-lg font-semibold mb-2">No users found</p>
                                    <p class="text-sm">Try adjusting your search or filters</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
