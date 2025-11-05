<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? config('app.name') }}</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen">
  <header class="topbar">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
      <a href="{{ url('/') }}" class="flex items-center gap-2">
        <span class="rounded-xl w-8 h-8 bg-brand-gold inline-block"></span>
        <span class="font-bold text-brand-ocean">{{ config('app.name','Hollyn Hosting') }}</span>
      </a>
      <nav class="flex items-center gap-4">
        <a href="{{ route('plans') }}" class="hover:underline">Plans</a>
        @auth
          <a href="{{ route('dashboard') }}" class="hover:underline">Dashboard</a>
          @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.index') }}" class="hover:underline">Admin</a>
          @endif
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-gold">Logout</button>
          </form>
        @else
          <a href="{{ route('login') }}" class="btn-primary">Login</a>
          <a href="{{ route('register') }}" class="btn-gold">Register</a>
        @endauth
      </nav>
    </div>
  </header>

  <main class="max-w-7xl mx-auto px-4 py-6">
    {{ $slot ?? '' }}
    @yield('content')
  </main>

  <footer class="mt-10 border-t border-brand-cream/70">
    <div class="max-w-7xl mx-auto px-4 py-6 text-sm text-brand-slate">
      © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </div>
  </footer>
</body>
</html>
