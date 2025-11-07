<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? config('app.name') }}</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
  <style>
    /* Modern Header Styles */
    .modern-header {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid rgba(0, 0, 0, 0.05);
      position: sticky;
      top: 0;
      z-index: 1000;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
    }

    .modern-header.scrolled {
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .header-container {
      max-width: 1400px;
      margin: 0 auto;
      padding: 1rem 2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .logo-wrapper {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      text-decoration: none;
      transition: transform 0.2s ease;
    }

    .logo-wrapper:hover {
      transform: translateY(-2px);
    }

    .logo-icon {
      width: 40px;
      height: 40px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 700;
      font-size: 1.25rem;
      box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);
    }

    .logo-text {
      font-weight: 800;
      font-size: 1.25rem;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .nav-wrapper {
      display: flex;
      align-items: center;
      gap: 2rem;
    }

    .nav-links {
      display: flex;
      align-items: center;
      gap: 2rem;
    }

    .nav-link {
      text-decoration: none;
      color: #1f2937;
      font-weight: 600;
      font-size: 0.95rem;
      transition: all 0.2s ease;
      position: relative;
      padding: 0.5rem 0;
    }

    .nav-link::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 0;
      height: 2px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      transition: width 0.3s ease;
    }

    .nav-link:hover::after {
      width: 100%;
    }

    .nav-link:hover {
      color: #667eea;
    }

    .nav-actions {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .header-btn {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.625rem 1.25rem;
      border-radius: 10px;
      font-weight: 600;
      font-size: 0.875rem;
      text-decoration: none;
      border: none;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .header-btn-primary {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .header-btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }

    .header-btn-secondary {
      background: transparent;
      color: #667eea;
      border: 2px solid #667eea;
    }

    .header-btn-secondary:hover {
      background: #667eea;
      color: white;
      transform: translateY(-2px);
    }

    .header-btn-ghost {
      background: rgba(102, 126, 234, 0.1);
      color: #667eea;
    }

    .header-btn-ghost:hover {
      background: rgba(102, 126, 234, 0.2);
    }

    .mobile-menu-toggle {
      display: none;
      background: transparent;
      border: none;
      cursor: pointer;
      padding: 0.5rem;
    }

    .mobile-menu {
      display: none;
      position: fixed;
      top: 80px;
      left: 0;
      right: 0;
      background: white;
      border-bottom: 1px solid rgba(0, 0, 0, 0.05);
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      padding: 1.5rem;
      animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .mobile-menu.active {
      display: block;
    }

    .mobile-nav-links {
      display: flex;
      flex-direction: column;
      gap: 1rem;
      margin-bottom: 1.5rem;
    }

    .mobile-nav-actions {
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .header-container {
        padding: 1rem;
      }

      .nav-wrapper {
        display: none;
      }

      .mobile-menu-toggle {
        display: block;
      }

      .logo-text {
        font-size: 1.1rem;
      }

      .logo-icon {
        width: 36px;
        height: 36px;
        font-size: 1.1rem;
      }
    }
  </style>
</head>
<body class="min-h-screen">
  <!-- Modern Header -->
  <header class="modern-header" id="mainHeader">
    <div class="header-container">
      <!-- Logo -->
      <a href="{{ url('/') }}" class="logo-wrapper">
        <div class="logo-icon">H</div>
        <span class="logo-text">{{ config('app.name','Hollyn Hosting') }}</span>
      </a>

      <!-- Desktop Navigation -->
      <div class="nav-wrapper">
        <nav class="nav-links">
          <a href="{{ route('home') }}" class="nav-link">Home</a>
          <a href="{{ route('plans') }}" class="nav-link">Plans</a>
          @auth
            <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
            @if(auth()->user()->role === 'admin')
              <a href="{{ route('admin.index') }}" class="nav-link">Admin</a>
            @endif
          @endauth
        </nav>

        <div class="nav-actions">
          @auth
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
              @csrf
              <button type="submit" class="header-btn header-btn-ghost">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/>
                </svg>
                Logout
              </button>
            </form>
          @else
            <a href="{{ route('login') }}" class="header-btn header-btn-secondary">Login</a>
            <a href="{{ route('register') }}" class="header-btn header-btn-primary">
              Get Started
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M5 12h14M12 5l7 7-7 7"/>
              </svg>
            </a>
          @endauth
        </div>
      </div>

      <!-- Mobile Menu Toggle -->
      <button class="mobile-menu-toggle" id="mobileMenuToggle">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#667eea" stroke-width="2">
          <path d="M3 12h18M3 6h18M3 18h18"/>
        </svg>
      </button>
    </div>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
      <nav class="mobile-nav-links">
        <a href="{{ route('home') }}" class="nav-link">Home</a>
        <a href="{{ route('plans') }}" class="nav-link">Plans</a>
        @auth
          <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
          @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.index') }}" class="nav-link">Admin</a>
          @endif
        @endauth
      </nav>

      <div class="mobile-nav-actions">
        @auth
          <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
            @csrf
            <button type="submit" class="header-btn header-btn-ghost" style="width: 100%; justify-content: center;">
              Logout
            </button>
          </form>
        @else
          <a href="{{ route('login') }}" class="header-btn header-btn-secondary" style="justify-content: center;">Login</a>
          <a href="{{ route('register') }}" class="header-btn header-btn-primary" style="justify-content: center;">Get Started</a>
        @endauth
      </div>
    </div>
  </header>

  <script>
    // Mobile menu toggle
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mobileMenu = document.getElementById('mobileMenu');
    
    if (mobileMenuToggle) {
      mobileMenuToggle.addEventListener('click', function() {
        mobileMenu.classList.toggle('active');
      });
    }

    // Header scroll effect
    const header = document.getElementById('mainHeader');
    let lastScroll = 0;
    
    window.addEventListener('scroll', function() {
      const currentScroll = window.pageYOffset;
      
      if (currentScroll > 50) {
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }
      
      lastScroll = currentScroll;
    });

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
      if (mobileMenu && mobileMenuToggle) {
        if (!mobileMenu.contains(event.target) && !mobileMenuToggle.contains(event.target)) {
          mobileMenu.classList.remove('active');
        }
      }
    });
  </script>

  <main>
    {{ $slot ?? '' }}
    @yield('content')
  </main>

  <footer style="background: #1f2937; color: white; padding: 3rem 0;">
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 2rem;">
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-bottom: 2rem;">
        <!-- Company Info -->
        <div>
          <h4 style="font-weight: 700; margin-bottom: 1rem; font-size: 1.125rem;">{{ config('app.name') }}</h4>
          <p style="color: rgba(255,255,255,0.7); font-size: 0.875rem; line-height: 1.6;">
            Professional web hosting with auto-provision, cPanel access, and 24/7 support for businesses in Tanzania.
          </p>
        </div>
        
        <!-- Quick Links -->
        <div>
          <h4 style="font-weight: 700; margin-bottom: 1rem; font-size: 1.125rem;">Quick Links</h4>
          <div style="display: flex; flex-direction: column; gap: 0.5rem;">
            <a href="{{ route('home') }}" style="color: rgba(255,255,255,0.7); text-decoration: none; font-size: 0.875rem; transition: color 0.2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,0.7)'">Home</a>
            <a href="{{ route('plans') }}" style="color: rgba(255,255,255,0.7); text-decoration: none; font-size: 0.875rem; transition: color 0.2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,0.7)'">Hosting Plans</a>
            @auth
              <a href="{{ route('dashboard') }}" style="color: rgba(255,255,255,0.7); text-decoration: none; font-size: 0.875rem; transition: color 0.2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,0.7)'">Dashboard</a>
            @else
              <a href="{{ route('register') }}" style="color: rgba(255,255,255,0.7); text-decoration: none; font-size: 0.875rem; transition: color 0.2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,0.7)'">Get Started</a>
            @endauth
          </div>
        </div>
        
        <!-- Contact -->
        <div>
          <h4 style="font-weight: 700; margin-bottom: 1rem; font-size: 1.125rem;">Support</h4>
          <div style="display: flex; flex-direction: column; gap: 0.5rem;">
            <p style="color: rgba(255,255,255,0.7); font-size: 0.875rem;">📧 support@hollynhosting.co.tz</p>
            <p style="color: rgba(255,255,255,0.7); font-size: 0.875rem;">📱 24/7 Customer Support</p>
            <p style="color: rgba(255,255,255,0.7); font-size: 0.875rem;">🇹🇿 Made in Tanzania</p>
          </div>
        </div>
      </div>
      
      <!-- Copyright -->
      <div style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 2rem; text-align: center;">
        <p style="color: rgba(255,255,255,0.6); font-size: 0.875rem;">
          © {{ date('Y') }} {{ config('app.name') }}. All rights reserved. | Powered by Auto-Provision Technology
        </p>
      </div>
    </div>
  </footer>
</body>
</html>
