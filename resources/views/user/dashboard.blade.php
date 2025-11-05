@extends('layouts.app')
@section('title','My Dashboard')

@section('content')
<style>
  /* Modern Color Palette */
  :root {
    --primary: #6366f1;
    --primary-dark: #4f46e5;
    --secondary: #8b5cf6;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --info: #06b6d4;
    --dark: #1f2937;
    --light: #f8fafc;
    --gray: #6b7280;
    --gray-light: #e5e7eb;
    --white: #ffffff;
    --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --gradient-success: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    --gradient-warning: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    --gradient-info: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
  }

  /* Modern Layout */
  .modern-dashboard {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  }

  .dashboard-container {
    display: flex;
    min-height: 100vh;
  }

  /* Modern Sidebar */
  .modern-sidebar {
    width: 280px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-right: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: var(--shadow-lg);
    transition: all 0.3s ease;
  }

  .sidebar-header {
    padding: 2rem 1.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  }

  .brand-logo {
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }

  .logo-icon {
    width: 40px;
    height: 40px;
    background: var(--gradient-primary);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 800;
    font-size: 1.2rem;
  }

  .brand-text {
    font-size: 1.25rem;
    font-weight: 800;
    color: var(--dark);
  }

  .sidebar-nav {
    padding: 1.5rem 1rem;
  }

  .nav-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.875rem 1rem;
    margin-bottom: 0.5rem;
    border-radius: 12px;
    color: var(--gray);
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    position: relative;
  }

  .nav-item:hover {
    background: rgba(99, 102, 241, 0.1);
    color: var(--primary);
    transform: translateX(4px);
  }

  .nav-item.active {
    background: var(--gradient-primary);
    color: white;
    box-shadow: var(--shadow);
  }

  .nav-icon {
    width: 20px;
    height: 20px;
  }

  /* Main Content */
  .main-content {
    flex: 1;
    background: var(--light);
    overflow-y: auto;
  }

  .top-bar {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: var(--shadow);
  }

  .top-bar-content {
    display: flex;
    justify-content: between;
    align-items: center;
  }

  .welcome-text {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--dark);
    margin-bottom: 0.25rem;
  }

  .welcome-subtitle {
    color: var(--gray);
    font-size: 0.875rem;
  }

  .quick-actions {
    display: flex;
    gap: 1rem;
  }

  /* Content Area */
  .content-area {
    padding: 2rem;
    max-width: 1200px;
    margin: 0 auto;
  }

  /* Modern KPI Cards */
  .kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
  }

  .kpi-card {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: var(--shadow);
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
  }

  .kpi-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
  }

  .kpi-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--gradient-primary);
  }

  .kpi-card.success::before {
    background: var(--gradient-success);
  }

  .kpi-card.warning::before {
    background: var(--gradient-warning);
  }

  .kpi-card.info::before {
    background: var(--gradient-info);
  }

  .kpi-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
  }

  .kpi-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--gray);
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  .kpi-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--gradient-primary);
    color: white;
  }

  .kpi-value {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--dark);
    margin-bottom: 0.5rem;
    line-height: 1;
  }

  .kpi-subtitle {
    font-size: 0.875rem;
    color: var(--gray);
  }

  /* Modern Buttons */
  .btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.875rem;
  }

  .btn-primary {
    background: var(--gradient-primary);
    color: white;
    box-shadow: var(--shadow);
  }

  .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
  }

  .btn-success {
    background: var(--gradient-success);
    color: white;
  }

  .btn-warning {
    background: var(--gradient-warning);
    color: white;
  }

  .btn-ghost {
    background: rgba(255, 255, 255, 0.5);
    color: var(--gray);
    border: 1px solid var(--gray-light);
  }

  .btn-ghost:hover {
    background: white;
    color: var(--dark);
  }

  /* Service Cards */
  .services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
  }

  .service-card {
    background: white;
    border-radius: 20px;
    padding: 1.5rem;
    box-shadow: var(--shadow);
    transition: all 0.3s ease;
  }

  .service-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
  }

  .service-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
  }

  .service-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--dark);
  }

  .status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  .status-active {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
  }

  .status-provisioning {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
  }

  .status-pending {
    background: rgba(107, 114, 128, 0.1);
    color: var(--gray);
  }

  /* Credentials Display */
  .credentials-grid {
    display: grid;
    gap: 1rem;
    margin-bottom: 1rem;
  }

  .credential-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: var(--light);
    border-radius: 12px;
    border: 1px solid var(--gray-light);
  }

  .credential-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--gray);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    min-width: 80px;
  }

  .credential-value {
    flex: 1;
    font-family: 'Monaco', 'Menlo', monospace;
    font-size: 0.875rem;
    color: var(--dark);
    background: none;
    border: none;
    outline: none;
  }

  .credential-actions {
    display: flex;
    gap: 0.5rem;
  }

  .btn-copy {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .btn-copy:hover {
    background: var(--primary-dark);
  }

  /* Orders Table */
  .orders-section {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: var(--shadow);
  }

  .section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
  }

  .section-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--dark);
  }

  .modern-table {
    width: 100%;
    border-collapse: collapse;
  }

  .modern-table th,
  .modern-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid var(--gray-light);
  }

  .modern-table th {
    font-weight: 600;
    color: var(--gray);
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  .modern-table td {
    color: var(--dark);
  }

  .modern-table tbody tr:hover {
    background: var(--light);
  }

  /* Responsive */
  @media (max-width: 768px) {
    .modern-sidebar {
      transform: translateX(-100%);
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      z-index: 1000;
    }

    .modern-sidebar.open {
      transform: translateX(0);
    }

    .content-area {
      padding: 1rem;
    }

    .kpi-grid {
      grid-template-columns: 1fr;
    }

    .services-grid {
      grid-template-columns: 1fr;
    }

    .quick-actions {
      flex-direction: column;
    }
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
    animation: fadeInUp 0.6s ease-out;
  }

  /* Mobile Menu Button */
  .mobile-menu-btn {
    display: none;
    position: fixed;
    top: 1rem;
    left: 1rem;
    z-index: 1001;
    background: white;
    border: none;
    border-radius: 12px;
    padding: 0.75rem;
    box-shadow: var(--shadow);
  }

  @media (max-width: 768px) {
    .mobile-menu-btn {
      display: block;
    }
  }
</style>

@php
  use Illuminate\Support\Arr;
  use Illuminate\Support\Facades\Auth;
  use Illuminate\Support\Facades\Crypt;

  $services = collect($services ?? []);
  $ordersList = collect($recentOrders ?? ($orders ?? []));
  $s = $stats ?? [];
  $provOrder = $provisionableOrder ?? null;

  if ($services->isEmpty()) {
    try {
      $uid = Auth::id();
      if ($uid) {
        $services = \App\Models\Service::query()
            ->with(['plan','order'])
            ->when(\Schema::hasColumn('services','user_id'),
                fn($q)=>$q->where('user_id',$uid),
                fn($q)=>$q->whereHas('order', fn($qq)=>$qq->where('user_id',$uid))
            )
            ->latest('id')->limit(8)->get();
      }
    } catch (\Throwable $e) {}
  }

  $fmt = fn($n)=> is_numeric($n) ? number_format((float)$n) : ($n ?? '0');
  $get = function($row,$key=null){ 
    if(is_null($row)) return null; 
    if(is_null($key)) return $row; 
    return is_object($row) ? ($row->{$key} ?? null) : (is_array($row) ? ($row[$key] ?? null) : null); 
  };

  $safeRoute = function(string $name, array $params = []) {
    try { return \Illuminate\Support\Facades\Route::has($name) ? route($name, $params) : null; }
    catch (\Throwable $e) { return null; }
  };

  $plansUrl = $safeRoute('plans');
  $servicesIndex = $safeRoute('me.services.index') ?? $safeRoute('admin.services.index');
  $ordersIndex = $safeRoute('me.orders.index') ?? $safeRoute('admin.orders.index');

  $svcActiveCnt = (int)($s['services_active'] ?? $services->filter(fn($x)=>strtolower((string)Arr::get((array)$x,'status', data_get($x,'status',''))) === 'active')->count());
  $svcProvisioningCnt = (int)($s['services_provisioning'] ?? $services->filter(fn($x)=>strtolower((string)Arr::get((array)$x,'status', data_get($x,'status',''))) === 'provisioning')->count());
  $ordersPaidCnt = (int)($s['orders_paid'] ?? $ordersList->filter(fn($o)=>in_array(strtolower((string)$get($o,'status')),['paid','active','complete','succeeded']))->count());
  $ordersTotalCnt = (int)($s['orders_total'] ?? $ordersList->count());
  $revenueTzs = (int)($s['revenue_tzs'] ?? $ordersList->filter(fn($o)=>in_array(strtolower((string)$get($o,'status')),['paid','active','complete','succeeded']))->sum('price_tzs'));

  $activeSvc = $services->first(fn($svc)=>strtolower((string) data_get($svc,'status',''))==='active');
  $finalPanelHref = data_get($activeSvc,'panel_url') ?? data_get($activeSvc,'enduser_url');
  $hasProvisionAction = (bool)($safeRoute('me.services.provision') || $safeRoute('me.services.provisionLatest'));
@endphp

<div class="modern-dashboard">
  <!-- Mobile Menu Button -->
  <button class="mobile-menu-btn" id="mobileMenuBtn">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
      <path d="M3 6h18M3 12h18M3 18h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
    </svg>
  </button>

  <div class="dashboard-container">
    <!-- Modern Sidebar -->
    <aside class="modern-sidebar" id="sidebar">
      <div class="sidebar-header">
        <div class="brand-logo">
          <div class="logo-icon">CP</div>
          <div class="brand-text">Control Panel</div>
        </div>
      </div>
      
      <nav class="sidebar-nav">
        @php
          $nav = [
            ['name'=>'Dashboard','icon'=>'M3 12h18M3 6h18M3 18h18','url'=>$safeRoute('me.dashboard') ?? '#','active'=>\Illuminate\Support\Facades\Request::routeIs('me.dashboard')],
            ['name'=>'My Services','icon'=>'M4 6h16v12H4z','url'=>$servicesIndex ?? '#','active'=>\Illuminate\Support\Facades\Request::is('*/services*')],
            ['name'=>'Orders','icon'=>'M6 7h12M6 12h12M6 17h12','url'=>$ordersIndex ?? '#','active'=>\Illuminate\Support\Facades\Request::is('*/orders*')],
            ['name'=>'Plans','icon'=>'M12 3l9 4-9 4-9-4 9-4z','url'=>$plansUrl ?? '#','active'=>\Illuminate\Support\Facades\Request::routeIs('plans')],
          ];
        @endphp
        
        @foreach($nav as $item)
          <a href="{{ $item['url'] }}" class="nav-item {{ $item['active'] ? 'active' : '' }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none">
              <path d="{{ $item['icon'] }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            {{ $item['name'] }}
          </a>
        @endforeach
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Top Bar -->
      <div class="top-bar">
        <div class="top-bar-content">
          <div>
            <h1 class="welcome-text">Welcome back, {{ Auth::user()->name }}! 👋</h1>
            <p class="welcome-subtitle">Here's what's happening with your services today</p>
          </div>
          
          <div class="quick-actions">
            @if($activeSvc && $finalPanelHref)
              <a href="{{ $finalPanelHref }}" class="btn btn-primary" target="_blank">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                  <path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Go to cPanel
              </a>
            @elseif($hasProvisionAction)
              <form method="POST" action="{{ $safeRoute('me.services.provisionLatest') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-success">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                    <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                  Finish Setup
                </button>
              </form>
            @elseif($plansUrl)
              <a href="{{ $plansUrl }}" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                  <path d="M12 3l9 4-9 4-9-4 9-4z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Choose a Plan
              </a>
            @endif
          </div>
        </div>
      </div>

      <!-- Content Area -->
      <div class="content-area">
        <!-- Flash Messages -->
        @if(session('status'))
          <div class="kpi-card success animate-fade-in-up" style="margin-bottom: 2rem;">
            <div class="kpi-header">
              <div class="kpi-title">Success</div>
              <svg class="kpi-icon" viewBox="0 0 24 24" fill="none">
                <path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <div class="kpi-value" style="font-size: 1rem;">{{ session('status') }}</div>
          </div>
        @endif

        @if($errors->any())
          <div class="kpi-card warning animate-fade-in-up" style="margin-bottom: 2rem;">
            <div class="kpi-header">
              <div class="kpi-title">Error</div>
              <svg class="kpi-icon" viewBox="0 0 24 24" fill="none">
                <path d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <div class="kpi-value" style="font-size: 1rem;">{{ $errors->first() }}</div>
          </div>
        @endif

        <!-- KPI Cards -->
        <div class="kpi-grid">
          <div class="kpi-card success animate-fade-in-up">
            <div class="kpi-header">
              <div class="kpi-title">Active Services</div>
              <div class="kpi-icon">
                <svg viewBox="0 0 24 24" fill="none">
                  <path d="M4 6h16v12H4z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
            </div>
            <div class="kpi-value">{{ $fmt($svcActiveCnt) }}</div>
            <div class="kpi-subtitle">Provisioning: {{ $fmt($svcProvisioningCnt) }}</div>
          </div>

          <div class="kpi-card info animate-fade-in-up" style="animation-delay: 0.1s;">
            <div class="kpi-header">
              <div class="kpi-title">Paid Orders</div>
              <div class="kpi-icon">
                <svg viewBox="0 0 24 24" fill="none">
                  <path d="M6 7h12M6 12h12M6 17h12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
            </div>
            <div class="kpi-value">{{ $fmt($ordersPaidCnt) }}</div>
            <div class="kpi-subtitle">Total: {{ $fmt($ordersTotalCnt) }}</div>
          </div>

          <div class="kpi-card warning animate-fade-in-up" style="animation-delay: 0.2s;">
            <div class="kpi-header">
              <div class="kpi-title">Total Revenue</div>
              <div class="kpi-icon">
                <svg viewBox="0 0 24 24" fill="none">
                  <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
            </div>
            <div class="kpi-value">TZS {{ $fmt($revenueTzs) }}</div>
            <div class="kpi-subtitle">Last payment: {{ $s['last_payment_at'] ?? '—' }}</div>
          </div>

          <div class="kpi-card animate-fade-in-up" style="animation-delay: 0.3s;">
            <div class="kpi-header">
              <div class="kpi-title">Quick Stats</div>
              <div class="kpi-icon">
                <svg viewBox="0 0 24 24" fill="none">
                  <path d="M3 3v18h18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M18.7 8l-5.1 5.2-2.8-2.7L7 14.3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
            </div>
            <div class="kpi-value">{{ $fmt($services->count()) }}</div>
            <div class="kpi-subtitle">Total Services</div>
          </div>
        </div>

        <!-- My Services Section -->
        <div class="orders-section animate-fade-in-up" style="animation-delay: 0.4s;">
          <div class="section-header">
            <h2 class="section-title">My Hosting Services</h2>
            @if($servicesIndex)
              <a href="{{ $servicesIndex }}" class="btn btn-ghost">View All</a>
            @endif
          </div>

          @if($services->isEmpty())
            <div style="text-align: center; padding: 3rem 1rem; color: var(--gray);">
              <svg width="64" height="64" viewBox="0 0 24 24" fill="none" style="margin: 0 auto 1rem; opacity: 0.5;">
                <path d="M4 6h16v12H4z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem;">No Active Services</h3>
              <p style="margin-bottom: 1.5rem;">You don't have any hosting services yet.</p>
              @if($hasProvisionAction)
                <form method="POST" action="{{ $safeRoute('me.services.provisionLatest') }}" style="display: inline;">
                  @csrf
                  <button type="submit" class="btn btn-primary">Finish Setup</button>
                </form>
              @elseif($plansUrl)
                <a href="{{ $plansUrl }}" class="btn btn-primary">Choose a Plan</a>
              @endif
            </div>
          @else
            <div class="services-grid">
              @foreach($services as $svc)
                @php
                  $status = strtolower((string) ($get($svc,'status') ?? 'provisioning'));
                  $active = $status === 'active';
                  $plan = data_get($svc,'plan.name') ?? data_get($svc,'plan_name') ?? data_get($svc,'plan_title') ?? '—';
                  $domain = $get($svc,'domain') ?: '—';
                  $uname = $get($svc,'webuzo_username') ?: $get($svc,'panel_username') ?: '—';

                  // DECRYPT password
                  $pwd = null;
                  try {
                    $enc = data_get($svc,'webuzo_temp_password_enc');
                    if ($enc) {
                      $pwd = Crypt::decryptString($enc);
                    } else {
                      $pwd = data_get($svc,'webuzo_password') ?? data_get($svc,'panel_password') ?? data_get($svc,'cpanel_password');
                    }
                  } catch (\Throwable $__) { $pwd = null; }

                  $orderId = data_get($svc,'order.id');
                  $statusClass = $active ? 'status-active' : ($status==='provisioning' ? 'status-provisioning' : 'status-pending');
                  $svcPanel = data_get($svc,'panel_url') ?? data_get($svc,'enduser_url');
                  $pid = 'pwd_'.($get($svc,'id') ?? uniqid());
                @endphp

                <div class="service-card">
                  <div class="service-header">
                    <h3 class="service-title">{{ $plan }}</h3>
                    <span class="status-badge {{ $statusClass }}">{{ ucfirst($status) }}</span>
                  </div>

                  <div class="credentials-grid">
                    <div class="credential-item">
                      <div class="credential-label">Domain</div>
                      <input type="text" class="credential-value" value="{{ $domain }}" readonly>
                      <div class="credential-actions">
                        <button class="btn-copy" onclick="copyText('{{ $domain }}')">Copy</button>
                      </div>
                    </div>

                    <div class="credential-item">
                      <div class="credential-label">Username</div>
                      <input type="text" class="credential-value" value="{{ $uname }}" readonly>
                      <div class="credential-actions">
                        <button class="btn-copy" onclick="copyText('{{ $uname }}')">Copy</button>
                      </div>
                    </div>

                    <div class="credential-item">
                      <div class="credential-label">Password</div>
                      @if($pwd)
                        <input type="text" id="{{ $pid }}" class="credential-value" value="{{ $pwd }}" readonly>
                        <div class="credential-actions">
                          <button class="btn-copy" onclick="copyText('{{ $pwd }}')">Copy</button>
                          <button class="btn-copy" onclick="togglePassword('{{ $pid }}', this)">Hide</button>
                        </div>
                      @else
                        <input type="text" class="credential-value" value="— (not stored)" readonly>
                      @endif
                    </div>
                  </div>

                  <div style="display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 1rem;">
                    @if($active && $svcPanel)
                      <a href="{{ $svcPanel }}" class="btn btn-primary" target="_blank">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                          <path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Go to cPanel
                      </a>
                    @elseif($status === 'provisioning')
                      <span class="btn btn-ghost">Provisioning…</span>
                    @else
                      @if($hasProvisionAction)
                        <form method="POST" action="{{ $safeRoute('me.services.provisionLatest') }}" style="display: inline;">
                          @csrf
                          <button type="submit" class="btn btn-warning">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                              <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Provision
                          </button>
                        </form>
                      @else
                        <span class="btn btn-ghost">Pending</span>
                      @endif
                    @endif

                    @if($orderId && \Illuminate\Support\Facades\Route::has('order.summary'))
                      <a href="{{ route('order.summary', $orderId) }}" class="btn btn-ghost">Order #{{ $orderId }}</a>
                    @endif
                  </div>
                </div>
              @endforeach
            </div>
          @endif
        </div>

        <!-- Recent Orders Section -->
        <div class="orders-section animate-fade-in-up" style="animation-delay: 0.5s;">
          <div class="section-header">
            <h2 class="section-title">Recent Orders</h2>
            @if($ordersIndex)
              <a href="{{ $ordersIndex }}" class="btn btn-ghost">View All</a>
            @endif
          </div>

          <div style="overflow-x: auto;">
            <table class="modern-table">
              <thead>
                <tr>
                  <th>Order</th>
                  <th>Plan</th>
                  <th>Price</th>
                  <th>Status</th>
                  <th>Created</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                @forelse($ordersList as $o)
                  @php
                    $oid = $get($o,'id');
                    $pname = data_get($o,'plan.name') ?? $get($o,'plan_name') ?? $get($o,'plan_title') ?? '—';
                    $price = (int) ($get($o,'price_tzs') ?? 0);
                    $st = strtolower((string) ($get($o,'status') ?? 'pending'));
                    $created = $get($o,'created_at') ?: '—';
                    $stClass = in_array($st,['paid','active','complete','succeeded']) ? 'status-active' : ($st==='failed'?'status-pending':'status-provisioning');
                    $viewUrl = ($oid && \Illuminate\Support\Facades\Route::has('order.summary')) ? route('order.summary',$oid) : null;
                  @endphp
                  <tr>
                    <td><strong>#{{ $oid ?: '—' }}</strong></td>
                    <td>{{ $pname }}</td>
                    <td><strong>TZS {{ number_format($price) }}</strong></td>
                    <td><span class="status-badge {{ $stClass }}">{{ ucfirst($st) }}</span></td>
                    <td>{{ $created }}</td>
                    <td>
                      @if($viewUrl)
                        <a href="{{ $viewUrl }}" class="btn btn-ghost" style="padding: 0.5rem 1rem; font-size: 0.75rem;">View</a>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" style="text-align: center; padding: 2rem; color: var(--gray);">
                      <svg width="48" height="48" viewBox="0 0 24 24" fill="none" style="margin: 0 auto 1rem; opacity: 0.5;">
                        <path d="M6 7h12M6 12h12M6 17h12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                      <p>No orders yet.</p>
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

<script>
// Mobile menu toggle
document.getElementById('mobileMenuBtn')?.addEventListener('click', function() {
  document.getElementById('sidebar').classList.toggle('open');
});

// Copy to clipboard function
function copyText(text) {
  navigator.clipboard.writeText(text).then(function() {
    // Show success feedback
    const btn = event.target;
    const originalText = btn.textContent;
    btn.textContent = 'Copied!';
    btn.style.background = 'var(--success)';
    setTimeout(() => {
      btn.textContent = originalText;
      btn.style.background = 'var(--primary)';
    }, 2000);
  }).catch(function(err) {
    console.error('Failed to copy text: ', err);
  });
}

// Toggle password visibility
function togglePassword(inputId, button) {
  const input = document.getElementById(inputId);
  if (input.type === 'password') {
    input.type = 'text';
    button.textContent = 'Hide';
  } else {
    input.type = 'password';
    button.textContent = 'Show';
  }
}

// Close mobile menu when clicking outside
document.addEventListener('click', function(event) {
  const sidebar = document.getElementById('sidebar');
  const menuBtn = document.getElementById('mobileMenuBtn');
  
  if (window.innerWidth <= 768 && 
      !sidebar.contains(event.target) && 
      !menuBtn.contains(event.target)) {
    sidebar.classList.remove('open');
  }
});

// Handle window resize
window.addEventListener('resize', function() {
  if (window.innerWidth > 768) {
    document.getElementById('sidebar').classList.remove('open');
  }
});
</script>

@endsection