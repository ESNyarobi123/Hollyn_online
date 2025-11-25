<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Hollyn Online') }} - Premium Web Hosting</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700|inter:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        .glass-nav {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }
        .dark .glass-nav {
            background: rgba(17, 24, 39, 0.7);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .nav-scrolled {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }
        .hero-gradient {
            background: radial-gradient(circle at 50% 0%, rgba(124, 58, 237, 0.15) 0%, rgba(255, 255, 255, 0) 60%);
        }
        .dark .hero-gradient {
            background: radial-gradient(circle at 50% 0%, rgba(124, 58, 237, 0.2) 0%, rgba(17, 24, 39, 0) 60%);
        }
    </style>
</head>
<body class="font-sans antialiased text-slate-800 bg-slate-50 dark:bg-gray-950 dark:text-slate-200 selection:bg-violet-500 selection:text-white" x-data="{ mobileMenuOpen: false, scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 20)">

    <!-- Navigation -->
    <nav class="fixed w-full z-50 transition-all duration-300" 
         :class="{ 'glass-nav nav-scrolled py-2': scrolled, 'bg-transparent py-4': !scrolled }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center gap-3 cursor-pointer" onclick="window.location.href='/'">
                    <div class="relative group">
                        <div class="absolute -inset-1 bg-gradient-to-r from-violet-600 to-indigo-600 rounded-xl blur opacity-25 group-hover:opacity-50 transition duration-200"></div>
                        <div class="relative w-10 h-10 rounded-xl bg-gradient-to-br from-violet-600 to-indigo-600 flex items-center justify-center text-white font-bold text-xl shadow-lg">
                            H
                        </div>
                    </div>
                    <span class="font-display font-bold text-xl tracking-tight text-slate-900 dark:text-white group-hover:text-violet-600 transition-colors">
                        Hollyn<span class="text-violet-600">Online</span>
                    </span>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex space-x-1 items-center">
                    <div class="relative group px-3 py-2">
                        <button class="text-sm font-medium text-slate-600 hover:text-violet-600 dark:text-slate-300 dark:hover:text-violet-400 transition-colors flex items-center gap-1">
                            Products
                            <svg class="w-4 h-4 opacity-50 group-hover:rotate-180 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <!-- Dropdown -->
                        <div class="absolute top-full left-1/2 -translate-x-1/2 mt-2 w-64 bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-800 p-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top scale-95 group-hover:scale-100">
                            <a href="{{ route('plans') }}" class="block p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                <div class="font-semibold text-slate-900 dark:text-white">Web Hosting</div>
                                <div class="text-xs text-slate-500">Fast & secure shared hosting</div>
                            </a>
                            <a href="#" class="block p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                <div class="font-semibold text-slate-900 dark:text-white">VPS Servers</div>
                                <div class="text-xs text-slate-500">Scalable cloud performance</div>
                            </a>
                            <a href="#" class="block p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                <div class="font-semibold text-slate-900 dark:text-white">Domains</div>
                                <div class="text-xs text-slate-500">Find your perfect name</div>
                            </a>
                        </div>
                    </div>
                    <a href="#features" class="px-3 py-2 text-sm font-medium text-slate-600 hover:text-violet-600 dark:text-slate-300 dark:hover:text-violet-400 transition-colors">Features</a>
                    <a href="{{ route('plans') }}" class="px-3 py-2 text-sm font-medium text-slate-600 hover:text-violet-600 dark:text-slate-300 dark:hover:text-violet-400 transition-colors">Pricing</a>
                    <a href="#support" class="px-3 py-2 text-sm font-medium text-slate-600 hover:text-violet-600 dark:text-slate-300 dark:hover:text-violet-400 transition-colors">Support</a>
                </div>

                <!-- CTA Buttons -->
                <div class="hidden md:flex items-center space-x-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-sm font-medium text-slate-600 hover:text-violet-600 dark:text-slate-300 transition-colors">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-violet-600 dark:text-slate-300 transition-colors">Log in</a>
                        <a href="{{ route('plans') }}" class="group relative px-5 py-2.5 rounded-full bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-sm font-semibold shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all overflow-hidden">
                            <span class="relative z-10">Get Started</span>
                            <div class="absolute inset-0 bg-gradient-to-r from-violet-600 to-indigo-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300 dark:hidden"></div>
                        </a>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path x-show="mobileMenuOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Overlay -->
        <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="absolute top-full left-0 w-full bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800 shadow-2xl md:hidden" x-cloak>
            <div class="px-4 py-6 space-y-4">
                <a href="{{ route('plans') }}" class="block px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-800 font-medium text-slate-900 dark:text-white">Web Hosting</a>
                <a href="#features" class="block px-4 py-2 font-medium text-slate-600 dark:text-slate-300 hover:text-violet-600">Features</a>
                <a href="{{ route('plans') }}" class="block px-4 py-2 font-medium text-slate-600 dark:text-slate-300 hover:text-violet-600">Pricing</a>
                <a href="#support" class="block px-4 py-2 font-medium text-slate-600 dark:text-slate-300 hover:text-violet-600">Support</a>
                <div class="border-t border-slate-100 dark:border-slate-800 pt-4 mt-4 flex flex-col gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="w-full text-center px-5 py-3 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-semibold">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="block px-4 py-2 font-medium text-slate-600 dark:text-slate-300 text-center">Log in</a>
                        <a href="{{ route('plans') }}" class="w-full text-center px-5 py-3 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-semibold">Get Started</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden hero-gradient">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300 text-xs font-semibold mb-8 border border-violet-200 dark:border-violet-800 animate-fade-in-up">
                <span class="relative flex h-2 w-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-violet-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-violet-500"></span>
                </span>
                New: Annual Plans now 20% OFF
            </div>
            
            <h1 class="text-5xl md:text-7xl font-bold tracking-tight text-slate-900 dark:text-white mb-6 leading-tight">
                Web Hosting that <br class="hidden md:block" />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-violet-600 to-indigo-600 dark:from-violet-400 dark:to-indigo-400">Simply Works.</span>
            </h1>
            
            <p class="mt-4 text-xl text-slate-600 dark:text-slate-400 max-w-2xl mx-auto mb-10 leading-relaxed">
                Deploy your website in seconds with our high-performance, secure, and reliable hosting platform. Automated provisioning, instant SSL, and 24/7 support.
            </p>
            
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('plans') }}" class="px-8 py-4 rounded-full bg-gradient-to-r from-violet-600 to-indigo-600 text-white font-semibold text-lg hover:from-violet-700 hover:to-indigo-700 transition-all shadow-lg shadow-violet-500/25 hover:shadow-violet-500/40 hover:-translate-y-1">
                    View Plans & Pricing
                </a>
                <a href="#features" class="px-8 py-4 rounded-full bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-semibold text-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all hover:-translate-y-1">
                    Learn More
                </a>
            </div>

            <!-- Stats/Trust -->
            <div class="mt-16 pt-8 border-t border-slate-200 dark:border-slate-800 grid grid-cols-2 md:grid-cols-4 gap-8 max-w-4xl mx-auto">
                <div>
                    <div class="text-3xl font-bold text-slate-900 dark:text-white">99.9%</div>
                    <div class="text-sm text-slate-500 dark:text-slate-400">Uptime Guarantee</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-slate-900 dark:text-white">24/7</div>
                    <div class="text-sm text-slate-500 dark:text-slate-400">Expert Support</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-slate-900 dark:text-white">NVMe</div>
                    <div class="text-sm text-slate-500 dark:text-slate-400">SSD Storage</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-slate-900 dark:text-white">SSL</div>
                    <div class="text-sm text-slate-500 dark:text-slate-400">Free Certificates</div>
                </div>
            </div>
        </div>
        
        <!-- Abstract Background Elements -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
            <div class="absolute top-1/4 left-0 w-96 h-96 bg-violet-500/10 rounded-full blur-3xl -translate-x-1/2"></div>
            <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-indigo-500/10 rounded-full blur-3xl translate-x-1/3 translate-y-1/3"></div>
        </div>
    </div>

    <!-- Features Grid -->
    <section id="features" class="py-24 bg-white dark:bg-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-base font-semibold text-violet-600 dark:text-violet-400 tracking-wide uppercase">Why Choose Us</h2>
                <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-4xl">Everything you need to scale</p>
                <p class="mt-4 text-lg text-slate-500 dark:text-slate-400">We've built a platform that handles the technical details so you can focus on building your business.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="p-8 rounded-3xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700 hover:border-violet-200 dark:hover:border-violet-800 transition-all group">
                    <div class="w-12 h-12 rounded-2xl bg-violet-100 dark:bg-violet-900/50 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-violet-600 dark:text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">Blazing Fast Speed</h3>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed">Powered by NVMe SSDs and LiteSpeed web servers, ensuring your website loads instantly for visitors worldwide.</p>
                </div>

                <!-- Feature 2 -->
                <div class="p-8 rounded-3xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700 hover:border-violet-200 dark:hover:border-violet-800 transition-all group">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">Bank-Grade Security</h3>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed">Free SSL certificates, automated daily backups, and advanced DDoS protection keep your data safe.</p>
                </div>

                <!-- Feature 3 -->
                <div class="p-8 rounded-3xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700 hover:border-violet-200 dark:hover:border-violet-800 transition-all group">
                    <div class="w-12 h-12 rounded-2xl bg-pink-100 dark:bg-pink-900/50 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-pink-600 dark:text-pink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">24/7 Expert Support</h3>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed">Our team of hosting experts is always available to help you resolve any issues, day or night.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative rounded-3xl overflow-hidden bg-slate-900 dark:bg-indigo-950 px-6 py-16 shadow-2xl sm:px-16 sm:py-24 lg:flex lg:items-center lg:justify-between">
                <div class="absolute inset-0 overflow-hidden">
                    <div class="absolute -top-24 -right-24 w-96 h-96 bg-violet-500/20 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 w-full h-full bg-gradient-to-r from-slate-900/50 to-transparent"></div>
                </div>
                <div class="relative">
                    <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">
                        Ready to get started?
                        <br />
                        <span class="text-violet-400">Start your free trial today.</span>
                    </h2>
                    <p class="mt-4 text-lg text-slate-300 max-w-xl">
                        Join thousands of developers and businesses who trust Hollyn Online for their web hosting needs.
                    </p>
                </div>
                <div class="mt-10 flex items-center gap-x-6 lg:mt-0 lg:shrink-0 relative">
                    <a href="{{ route('plans') }}" class="rounded-full bg-white px-8 py-3.5 text-base font-semibold text-slate-900 shadow-sm hover:bg-slate-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white transition-all hover:scale-105">
                        Get Started
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white dark:bg-slate-950 border-t border-slate-200 dark:border-slate-800 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-12">
                <div class="col-span-2 md:col-span-1">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-600 to-indigo-600 flex items-center justify-center text-white font-bold text-sm">H</div>
                        <span class="font-bold text-lg text-slate-900 dark:text-white">Hollyn<span class="text-violet-600">Online</span></span>
                    </div>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
                        Premium web hosting solutions for modern businesses and developers.
                    </p>
                </div>
                <div>
                    <h4 class="font-semibold text-slate-900 dark:text-white mb-4">Product</h4>
                    <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-400">
                        <li><a href="#" class="hover:text-violet-600 dark:hover:text-violet-400">Shared Hosting</a></li>
                        <li><a href="#" class="hover:text-violet-600 dark:hover:text-violet-400">VPS Servers</a></li>
                        <li><a href="#" class="hover:text-violet-600 dark:hover:text-violet-400">Domain Names</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-slate-900 dark:text-white mb-4">Company</h4>
                    <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-400">
                        <li><a href="#" class="hover:text-violet-600 dark:hover:text-violet-400">About Us</a></li>
                        <li><a href="#" class="hover:text-violet-600 dark:hover:text-violet-400">Contact</a></li>
                        <li><a href="#" class="hover:text-violet-600 dark:hover:text-violet-400">Privacy Policy</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-slate-900 dark:text-white mb-4">Support</h4>
                    <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-400">
                        <li><a href="#" class="hover:text-violet-600 dark:hover:text-violet-400">Help Center</a></li>
                        <li><a href="#" class="hover:text-violet-600 dark:hover:text-violet-400">Status</a></li>
                        <li><a href="#" class="hover:text-violet-600 dark:hover:text-violet-400">Report Abuse</a></li>
                    </ul>
                </div>
            </div>
            <div class="pt-8 border-t border-slate-200 dark:border-slate-800 text-center text-sm text-slate-500 dark:text-slate-400">
                &copy; {{ date('Y') }} Hollyn Online. All rights reserved.
            </div>
        </div>
    </footer>

</body>
</html>
