<!DOCTYPE html>
<html lang="en" x-data="{ sidebarOpen: false, darkMode: false }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - {{ config('app.name', 'Hollyn') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
        }
        
        [x-cloak] { display: none !important; }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #8b5cf6 0%, #6366f1 100%);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #7c3aed 0%, #4f46e5 100%);
        }
        
        /* Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .gradient-text-2 {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .gradient-text-3 {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .gradient-text-4 {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Modern Card Styles */
        .modern-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            border: 1px solid rgba(226, 232, 240, 0.8);
        }
        
        .modern-card:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            transform: translateY(-2px);
        }
        
        /* Gradient Backgrounds */
        .gradient-bg-1 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .gradient-bg-2 {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .gradient-bg-3 {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .gradient-bg-4 {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }
        
        .gradient-bg-5 {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }
        
        .gradient-bg-6 {
            background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in-up {
            animation: fadeInUp 0.5s ease-out;
        }
        
        @keyframes pulse-glow {
            0%, 100% {
                box-shadow: 0 0 20px rgba(139, 92, 246, 0.4);
            }
            50% {
                box-shadow: 0 0 30px rgba(139, 92, 246, 0.6);
            }
        }
        
        .pulse-glow {
            animation: pulse-glow 2s infinite;
        }
        
        /* Sidebar Active Link */
        .sidebar-link {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .sidebar-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }
        
        .sidebar-link.active::before,
        .sidebar-link:hover::before {
            transform: translateX(0);
        }
        
        .sidebar-link.active {
            background: linear-gradient(90deg, rgba(102, 126, 234, 0.1) 0%, transparent 100%);
            color: #667eea;
        }
        
        /* Modern Button */
        .btn-modern {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .btn-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.2);
            transition: left 0.3s ease;
        }
        
        .btn-modern:hover::before {
            left: 100%;
        }
        
        /* Badge Styles */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.025em;
        }
        
        .badge-success {
            background: linear-gradient(135deg, #d4fc79 0%, #96e6a1 100%);
            color: #065f46;
        }
        
        .badge-warning {
            background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
            color: #92400e;
        }
        
        .badge-danger {
            background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
            color: #991b1b;
        }
        
        .badge-info {
            background: linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%);
            color: #1e40af;
        }
        
        .badge-purple {
            background: linear-gradient(135deg, #e0c3fc 0%, #8ec5fc 100%);
            color: #5b21b6;
        }
        
        /* Stats Card Animation */
        .stats-card {
            position: relative;
            overflow: hidden;
        }
        
        .stats-card::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            transform: scale(0);
            transition: transform 0.6s ease;
        }
        
        .stats-card:hover::after {
            transform: scale(1);
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50 antialiased">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside 
            x-show="sidebarOpen" 
            @click.away="sidebarOpen = false"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-2xl lg:static lg:translate-x-0 lg:z-auto"
            x-cloak
        >
            <!-- Sidebar Header -->
            <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl gradient-bg-1 flex items-center justify-center pulse-glow">
                        <i class="fas fa-crown text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold gradient-text">Hollyn</h1>
                        <p class="text-xs text-gray-500">Admin Panel</p>
                    </div>
                </div>
                <button @click="sidebarOpen = false" class="lg:hidden text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                @php
                    $navItems = [
                        ['route' => 'admin.index', 'icon' => 'fa-chart-line', 'label' => 'Dashboard', 'pattern' => 'admin.index'],
                        ['route' => 'admin.users.index', 'icon' => 'fa-users', 'label' => 'Users', 'pattern' => 'admin.users.*'],
                        ['route' => 'admin.services.index', 'icon' => 'fa-server', 'label' => 'Services', 'pattern' => 'admin.services.*'],
                        ['route' => 'admin.orders.index', 'icon' => 'fa-shopping-cart', 'label' => 'Orders', 'pattern' => 'admin.orders.*'],
                        ['route' => 'admin.plans.index', 'icon' => 'fa-box', 'label' => 'Plans', 'pattern' => 'admin.plans.*'],
                    ];
                @endphp
                
                @foreach($navItems as $item)
                    @php
                        $isActive = request()->routeIs($item['pattern']);
                        $routeExists = Route::has($item['route']);
                    @endphp
                    @if($routeExists)
                        <a 
                            href="{{ route($item['route']) }}" 
                            class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-xl {{ $isActive ? 'active font-semibold' : 'text-gray-600 hover:bg-gray-50' }}"
                        >
                            <i class="fas {{ $item['icon'] }} w-5 text-center"></i>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endif
                @endforeach
                
                <div class="pt-6 mt-6 border-t border-gray-200">
                    <h3 class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Quick Actions</h3>
                    
                    @if(Route::has('dashboard'))
                        <a href="{{ route('dashboard') }}" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50">
                            <i class="fas fa-home w-5 text-center"></i>
                            <span>User Dashboard</span>
                        </a>
                    @endif
                    
                    <a href="{{ route('home') }}" target="_blank" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50">
                        <i class="fas fa-globe w-5 text-center"></i>
                        <span>Visit Site</span>
                    </a>
                </div>
            </nav>
            
            <!-- Sidebar Footer -->
            <div class="p-4 border-t border-gray-200">
                <div class="modern-card p-4 gradient-bg-1 text-white">
                    <h4 class="font-semibold mb-1">Need Help?</h4>
                    <p class="text-xs text-white/80 mb-3">Contact support for assistance</p>
                    <a href="mailto:support@hollyn.com" class="text-xs font-semibold text-white/90 hover:text-white underline">
                        support@hollyn.com
                    </a>
                </div>
            </div>
        </aside>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 shadow-sm">
                <div class="flex items-center space-x-4">
                    <!-- Mobile Menu Button -->
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-500 hover:text-gray-700">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    
                    <!-- Breadcrumb -->
                    <div class="hidden md:flex items-center space-x-2 text-sm">
                        <i class="fas fa-home text-gray-400"></i>
                        <span class="text-gray-400">/</span>
                        <span class="font-semibold text-gray-700">@yield('breadcrumb', 'Dashboard')</span>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Search -->
                    <div class="hidden md:block relative">
                        <input 
                            type="text" 
                            placeholder="Search..." 
                            class="pl-10 pr-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent w-64"
                        >
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                    
                    <!-- Notifications -->
                    <button class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-xl transition">
                        <i class="fas fa-bell text-xl"></i>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>
                    
                    <!-- Profile Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center space-x-3 p-2 rounded-xl hover:bg-gray-100 transition">
                            <div class="w-8 h-8 rounded-full gradient-bg-1 flex items-center justify-center text-white font-semibold">
                                {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                            </div>
                            <div class="hidden md:block text-left">
                                <p class="text-sm font-semibold text-gray-700">{{ Auth::user()->name ?? 'Admin' }}</p>
                                <p class="text-xs text-gray-500">{{ ucfirst(Auth::user()->role ?? 'admin') }}</p>
                            </div>
                            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div 
                            x-show="open" 
                            @click.away="open = false"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg py-2 border border-gray-200 z-50"
                            x-cloak
                        >
                            @if(Route::has('profile.edit'))
                                <a href="{{ route('profile.edit') }}" class="flex items-center space-x-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-user-circle w-4"></i>
                                    <span>Profile</span>
                                </a>
                            @endif
                            
                            <a href="#" class="flex items-center space-x-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-cog w-4"></i>
                                <span>Settings</span>
                            </a>
                            
                            <hr class="my-2 border-gray-200">
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center space-x-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 w-full text-left">
                                    <i class="fas fa-sign-out-alt w-4"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Impersonation Banner -->
                @if(session('impersonate_from'))
                    <div class="mb-6 modern-card p-4 border-l-4 border-yellow-500 bg-yellow-50 animate-fade-in-up">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-user-secret text-yellow-600 text-2xl"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-yellow-800">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        You are currently viewing as <strong>{{ Auth::user()->name }}</strong>
                                    </p>
                                    <p class="text-xs text-yellow-700 mt-1">
                                        All actions will be performed as this user
                                    </p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('admin.users.stopImpersonating') }}" class="inline">
                                @csrf
                                <button type="submit" class="px-6 py-2 bg-yellow-600 text-white rounded-lg font-semibold hover:bg-yellow-700 transition">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Return to Admin
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
                
                <!-- Flash Messages -->
                @if(session('success') || session('ok'))
                    <div class="mb-6 modern-card p-4 border-l-4 border-green-500 bg-green-50 animate-fade-in-up">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">
                                    {{ session('success') ?? session('ok') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="mb-6 modern-card p-4 border-l-4 border-red-500 bg-red-50 animate-fade-in-up">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-500 text-2xl"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800">
                                    {{ session('error') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="mb-6 modern-card p-4 border-l-4 border-red-500 bg-red-50 animate-fade-in-up">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-500 text-2xl"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800 mb-2">Please fix the following errors:</h3>
                                <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- Page Content -->
                @yield('content')
            </main>
        </div>
    </div>
    
    @stack('scripts')
</body>
</html>
