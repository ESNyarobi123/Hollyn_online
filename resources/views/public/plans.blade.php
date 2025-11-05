@extends('layouts.app')

@section('content')
<style>
  /* Modern Color Palette */
  :root {
    --primary: #6366f1;
    --primary-dark: #4f46e5;
    --secondary: #8b5cf6;
    --accent: #06b6d4;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --dark: #1f2937;
    --light: #f8fafc;
    --gray: #6b7280;
    --gray-light: #e5e7eb;
    --white: #ffffff;
    --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    --shadow-xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --gradient-success: linear-gradient(135deg, #10b981 0%, #059669 100%);
    --gradient-warning: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    --gradient-info: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
  }

  /* Global Styles */
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    line-height: 1.6;
    color: var(--dark);
    background: var(--light);
  }

  /* Modern Animations */
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

  @keyframes fadeInLeft {
    from {
      opacity: 0;
      transform: translateX(-30px);
    }
    to {
      opacity: 1;
      transform: translateX(0);
    }
  }

  @keyframes fadeInRight {
    from {
      opacity: 0;
      transform: translateX(30px);
    }
    to {
      opacity: 1;
      transform: translateX(0);
    }
  }

  @keyframes float {
    0%, 100% {
      transform: translateY(0px);
    }
    50% {
      transform: translateY(-10px);
    }
  }

  @keyframes pulse {
    0%, 100% {
      opacity: 1;
    }
    50% {
      opacity: 0.5;
    }
  }

  @keyframes gradient {
    0% {
      background-position: 0% 50%;
    }
    50% {
      background-position: 100% 50%;
    }
    100% {
      background-position: 0% 50%;
    }
  }

  .animate-fade-in-up {
    animation: fadeInUp 0.8s ease-out;
  }

  .animate-fade-in-left {
    animation: fadeInLeft 0.8s ease-out;
  }

  .animate-fade-in-right {
    animation: fadeInRight 0.8s ease-out;
  }

  .animate-float {
    animation: float 6s ease-in-out infinite;
  }

  .animate-pulse {
    animation: pulse 2s ease-in-out infinite;
  }

  .animate-gradient {
    background-size: 200% 200%;
    animation: gradient 3s ease infinite;
  }

  /* Hero Section */
  .hero-section {
    background: var(--gradient-primary);
    color: white;
    padding: 4rem 0;
    position: relative;
    overflow: hidden;
  }

  .hero-bg {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
      radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
      radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.3) 0%, transparent 50%);
  }

  .hero-content {
    position: relative;
    z-index: 10;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
    text-align: center;
  }

  .hero-title {
    font-size: 3rem;
    font-weight: 800;
    margin-bottom: 1rem;
    animation: fadeInUp 0.8s ease-out;
  }

  .hero-subtitle {
    font-size: 1.25rem;
    opacity: 0.9;
    max-width: 600px;
    margin: 0 auto 2rem;
    animation: fadeInUp 0.8s ease-out 0.2s both;
  }

  .hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 0.75rem 1.5rem;
    border-radius: 50px;
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 2rem;
    animation: fadeInUp 0.8s ease-out 0.4s both;
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
    position: relative;
    overflow: hidden;
  }

  .btn-primary {
    background: var(--gradient-primary);
    color: white;
    box-shadow: var(--shadow-lg);
  }

  .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-xl);
  }

  .btn-secondary {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(10px);
  }

  .btn-secondary:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
  }

  .btn-success {
    background: var(--gradient-success);
    color: white;
    box-shadow: var(--shadow-lg);
  }

  .btn-success:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-xl);
  }

  .btn-ghost {
    background: transparent;
    color: var(--gray);
    border: 1px solid var(--gray-light);
  }

  .btn-ghost:hover {
    background: var(--light);
    color: var(--dark);
  }

  /* Content Container */
  .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
  }

  /* Plans Grid */
  .plans-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin: 4rem 0;
  }

  /* Modern Plan Cards */
  .plan-card {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: var(--shadow);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    border: 2px solid transparent;
  }

  .plan-card:hover {
    transform: translateY(-10px);
    box-shadow: var(--shadow-xl);
  }

  .plan-card.popular {
    border-color: var(--primary);
    transform: scale(1.05);
  }

  .plan-card.popular::before {
    content: 'Most Popular';
    position: absolute;
    top: -10px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--gradient-primary);
    color: white;
    padding: 0.5rem 1.5rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
    z-index: 10;
  }

  .plan-card::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--gradient-primary);
  }

  .plan-card.popular::after {
    background: var(--gradient-success);
  }

  .plan-header {
    text-align: center;
    margin-bottom: 2rem;
  }

  .plan-slug {
    font-size: 0.875rem;
    color: var(--gray);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.5rem;
  }

  .plan-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 1rem;
  }

  .plan-status {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  .plan-status.active {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
  }

  .plan-status.inactive {
    background: rgba(107, 114, 128, 0.1);
    color: var(--gray);
  }

  .plan-price {
    text-align: center;
    margin-bottom: 2rem;
  }

  .price-amount {
    font-size: 3rem;
    font-weight: 800;
    color: var(--primary);
    line-height: 1;
    margin-bottom: 0.5rem;
  }

  .price-period {
    font-size: 0.875rem;
    color: var(--gray);
  }

  /* Features */
  .plan-features {
    margin-bottom: 2rem;
  }

  .feature-list {
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
  }

  .feature-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: var(--light);
    border-radius: 12px;
    transition: all 0.2s ease;
  }

  .feature-item:hover {
    background: rgba(99, 102, 241, 0.05);
  }

  .feature-icon {
    width: 20px;
    height: 20px;
    background: var(--gradient-success);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.75rem;
    flex-shrink: 0;
  }

  .feature-text {
    flex: 1;
    font-size: 0.875rem;
    color: var(--dark);
  }

  .feature-label {
    font-weight: 600;
    color: var(--dark);
  }

  .feature-value {
    color: var(--gray);
    margin-left: 0.25rem;
  }

  /* Plan Actions */
  .plan-actions {
    display: flex;
    gap: 1rem;
  }

  .btn-order {
    flex: 1;
    justify-content: center;
    padding: 1rem 1.5rem;
    font-weight: 700;
  }

  .btn-features {
    padding: 1rem;
    background: transparent;
    border: 1px solid var(--gray-light);
    color: var(--gray);
    border-radius: 12px;
    transition: all 0.2s ease;
  }

  .btn-features:hover {
    background: var(--light);
    color: var(--dark);
    border-color: var(--primary);
  }

  /* Modern Modal */
  .modal {
    background: white;
    border-radius: 20px;
    box-shadow: var(--shadow-xl);
    border: none;
    padding: 0;
    max-width: 800px;
    width: 90vw;
    max-height: 90vh;
    overflow: hidden;
  }

  .modal::backdrop {
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(5px);
  }

  .modal-header {
    padding: 2rem 2rem 1rem;
    border-bottom: 1px solid var(--gray-light);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .modal-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--dark);
  }

  .modal-price {
    font-size: 2rem;
    font-weight: 800;
    color: var(--primary);
  }

  .modal-close {
    background: var(--gray-light);
    border: none;
    border-radius: 8px;
    padding: 0.5rem 1rem;
    color: var(--dark);
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .modal-close:hover {
    background: var(--gray);
    color: white;
  }

  .modal-content {
    padding: 2rem;
    max-height: 60vh;
    overflow-y: auto;
  }

  .modal-features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
  }

  .modal-feature {
    padding: 1rem;
    background: var(--light);
    border-radius: 12px;
    border-left: 4px solid var(--primary);
  }

  .modal-feature-label {
    font-size: 0.75rem;
    color: var(--gray);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.25rem;
  }

  .modal-feature-value {
    font-weight: 600;
    color: var(--dark);
  }

  .modal-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
  }

  /* Comparison Table */
  .comparison-section {
    margin: 4rem 0;
  }

  .comparison-header {
    text-align: center;
    margin-bottom: 3rem;
  }

  .comparison-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--dark);
    margin-bottom: 1rem;
  }

  .comparison-subtitle {
    font-size: 1.125rem;
    color: var(--gray);
    max-width: 600px;
    margin: 0 auto;
  }

  .comparison-table-container {
    background: white;
    border-radius: 20px;
    box-shadow: var(--shadow);
    overflow: hidden;
  }

  .comparison-table {
    width: 100%;
    border-collapse: collapse;
  }

  .comparison-table th {
    background: var(--light);
    padding: 1.5rem 1rem;
    text-align: left;
    font-weight: 600;
    color: var(--dark);
    border-bottom: 1px solid var(--gray-light);
  }

  .comparison-table td {
    padding: 1rem;
    border-bottom: 1px solid var(--gray-light);
    color: var(--dark);
  }

  .comparison-table tr:hover {
    background: rgba(99, 102, 241, 0.02);
  }

  .plan-column {
    text-align: center;
    font-weight: 600;
  }

  .plan-column-name {
    font-size: 1.125rem;
    color: var(--dark);
    margin-bottom: 0.25rem;
  }

  .plan-column-price {
    color: var(--primary);
    font-weight: 700;
  }

  .unlimited {
    color: var(--success);
    font-weight: 700;
  }

  /* Responsive Design */
  @media (max-width: 768px) {
    .hero-title {
      font-size: 2rem;
    }

    .plans-grid {
      grid-template-columns: 1fr;
      gap: 1.5rem;
    }

    .plan-card.popular {
      transform: none;
    }

    .plan-actions {
      flex-direction: column;
    }

    .modal {
      width: 95vw;
      margin: 1rem;
    }

    .modal-header {
      padding: 1.5rem 1.5rem 1rem;
    }

    .modal-content {
      padding: 1.5rem;
    }

    .comparison-table-container {
      overflow-x: auto;
    }
  }

  /* Scroll reveal animations */
  .scroll-reveal {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.6s ease;
  }

  .scroll-reveal.revealed {
    opacity: 1;
    transform: translateY(0);
  }
</style>

<!-- Hero Section -->
<section class="hero-section">
  <div class="hero-bg"></div>
  
  <div class="hero-content">
    <div class="hero-badge">
      <span>🚀</span>
      <span>Choose Your Perfect Plan</span>
    </div>
    
    <h1 class="hero-title">Hosting Plans</h1>
    <p class="hero-subtitle">
      Select the perfect hosting plan for your needs. All plans include auto-provision, 
      cPanel access, and instant setup with ZenoPay payment integration.
    </p>
    
    <a href="{{ route('home') }}" class="btn btn-secondary">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
        <path d="M19 12H5M12 19l-7-7 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      Back to Home
    </a>
  </div>
</section>

<!-- Plans Section -->
<div class="container">
  <div class="plans-grid">
    @php
      use Illuminate\Support\Str;

      /**
       * Parse features from various formats to a normalized collection of pairs
       */
      $GLOBALS['__parseFeaturePairs'] = null;
      $parseFeaturePairs = $GLOBALS['__parseFeaturePairs'] = function($raw){
        if (is_array($raw)) {
          $isAssoc = array_keys($raw)!==range(0, count($raw)-1);
          if ($isAssoc) {
            return collect($raw)->map(function($v,$k){
              return ['k'=>(string)$k, 'v'=>(string)$v];
            })->values();
          }
          return collect($raw)->filter()->map(function($line){
            $line = trim((string)$line);
            if ($line==='') return null;
            if (Str::contains($line, ':')) {
              [$k,$v] = array_map('trim', explode(':',$line,2));
              return ['k'=>$k ?: 'Feature', 'v'=>$v ?: 'Yes'];
            }
            return ['k'=>'Feature', 'v'=>$line];
          })->filter()->values();
        }

        if (is_string($raw) && Str::startsWith(trim($raw), ['{','['])) {
          try {
            $arr = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
            return ($GLOBALS['__parseFeaturePairs'] ?? null)
              ? ($GLOBALS['__parseFeaturePairs'])($arr)
              : null;
          } catch (\Throwable $__) { /* fallthrough to lines */ }
        }

        $text = trim((string)$raw);
        if ($text==='') return collect();

        $lines = preg_split('/\r\n|\r|\n/', $text);
        return collect($lines)->map(function($line){
          $line = trim($line);
          if ($line==='') return null;
          if (Str::contains($line, ':')) {
            [$k,$v] = array_map('trim', explode(':',$line,2));
            return ['k'=>$k ?: 'Feature', 'v'=>$v ?: 'Yes'];
          }
          return ['k'=>'Feature', 'v'=>$line];
        })->filter()->values();
      };

      $getFeatureValue = function($plan, string $name) use ($parseFeaturePairs){
        $pairs = $parseFeaturePairs($plan->features ?? []);
        $needle = Str::lower($name);
        $found = $pairs->first(fn($p)=> Str::lower((string)$p['k']) === $needle);
        return $found['v'] ?? null;
      };
    @endphp

    @foreach($plans as $index => $p)
      @php
        $isPopular = $p->slug === 'hollyn-boost';
        $isMax = $p->slug === 'hollyn-max';
        $pairs = $parseFeaturePairs($p->features ?? []);
        $preview = $pairs->take(4);
        $perLabel = ($p->period_months ?? 1) > 1 ? '/ '.((int)$p->period_months).' months' : 'per month';
      @endphp

      <div class="plan-card scroll-reveal {{ $isPopular ? 'popular' : '' }}" style="animation-delay: {{ $index * 0.1 }}s;">
        <div class="plan-header">
          <div class="plan-slug">{{ $p->slug }}</div>
          <h3 class="plan-name">{{ $p->name }}</h3>
          <div class="plan-status {{ $p->is_active ? 'active' : 'inactive' }}">
            <span>{{ $p->is_active ? 'Active' : 'Inactive' }}</span>
          </div>
        </div>

        <div class="plan-price">
          <div class="price-amount">TZS {{ number_format((int)$p->price_tzs) }}</div>
          <div class="price-period">{{ $perLabel }}</div>
        </div>

        <div class="plan-features">
          @if($preview->isNotEmpty())
            <ul class="feature-list">
              @foreach($preview as $it)
                @php
                  $label = trim((string)($it['k'] ?? 'Feature'));
                  $value = trim((string)($it['v'] ?? 'Yes'));
                  $display = $isMax && Str::lower($value) === 'unlimited' ? '∞ Unlimited' : $value;
                @endphp
                <li class="feature-item">
                  <div class="feature-icon">✓</div>
                  <div class="feature-text">
                    <span class="feature-label">{{ $label }}:</span>
                    <span class="feature-value">{{ $display }}</span>
                  </div>
                </li>
              @endforeach
            </ul>
          @else
            <ul class="feature-list">
              <li class="feature-item">
                <div class="feature-icon">✓</div>
                <div class="feature-text">
                  <span class="feature-label">Optimized PHP</span>
                </div>
              </li>
              <li class="feature-item">
                <div class="feature-icon">✓</div>
                <div class="feature-text">
                  <span class="feature-label">SSD Storage</span>
                </div>
              </li>
              <li class="feature-item">
                <div class="feature-icon">✓</div>
                <div class="feature-text">
                  <span class="feature-label">Free SSL</span>
                </div>
              </li>
            </ul>
          @endif
        </div>

        <div class="plan-actions">
          <a href="{{ route('checkout.show', $p) }}" class="btn btn-primary btn-order">
            <span>Order {{ $p->name }}</span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
              <path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </a>
          <button type="button" onclick="document.getElementById('modal-{{ $p->id }}')?.showModal()" class="btn-features">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
              <path d="M3 7h18M3 12h14M3 17h10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Modern Modal -->
      <dialog id="modal-{{ $p->id }}" class="modal">
        <div class="modal-header">
          <div>
            <div class="modal-title">{{ $p->name }}</div>
            <div class="modal-price">TZS {{ number_format((int)$p->price_tzs) }}</div>
          </div>
          <button class="modal-close" onclick="document.getElementById('modal-{{ $p->id }}')?.close()">
            Close
          </button>
        </div>
        
        <div class="modal-content">
          @php $all = $pairs; @endphp
          @if($all->isEmpty())
            <p style="text-align: center; color: var(--gray); padding: 2rem;">
              No features provided for this plan.
            </p>
          @else
            <div class="modal-features">
              @foreach($all as $it)
                @php
                  $label = trim((string)($it['k'] ?? 'Feature'));
                  $value = trim((string)($it['v'] ?? 'Yes'));
                  $display = ($p->slug === 'hollyn-max' && Str::lower($value) === 'unlimited') ? '∞ Unlimited' : $value;
                @endphp
                <div class="modal-feature">
                  <div class="modal-feature-label">{{ $label }}</div>
                  <div class="modal-feature-value">{{ $display }}</div>
                </div>
              @endforeach
            </div>
          @endif
          
          <div class="modal-actions">
            <a href="{{ route('checkout.show', $p) }}" class="btn btn-success">
              <span>Continue with {{ $p->name }}</span>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                <path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </a>
          </div>
        </div>
      </dialog>
    @endforeach
  </div>

  <!-- Comparison Section -->
  <section class="comparison-section">
    <div class="comparison-header">
      <h2 class="comparison-title">Compare All Plans</h2>
      <p class="comparison-subtitle">
        See all features side by side to make the best choice for your needs
      </p>
    </div>

    <div class="comparison-table-container">
      @php
        $featureOrder = [
          'Disk Space Quota',
          'Max Inodes',
          'Monthly Bandwidth',
          'Max FTP Accounts',
          'Max Email Accounts',
          'Max Quota per Mailbox',
          'Max MySQL Databases',
          'Max Subdomains',
          'Max Parked (Aliases)',
          'Max Addon Domains',
          'Hourly Email Limit (domain)',
          'Max % Failed/Deferred / hr',
        ];

        $bySlug = $plans->keyBy('slug');
        $cols = ['hollyn-lite','hollyn-grow','hollyn-boost','hollyn-max'];

        $val = function($slug, $feat) use ($bySlug, $getFeatureValue){
          $pl = $bySlug->get($slug);
          if (!$pl) return '—';
          $v = $getFeatureValue($pl, $feat) ?? '—';
          if ($slug === 'hollyn-max' && Str::lower((string)$v) === 'unlimited') return '∞ Unlimited';
          return $v;
        };
      @endphp

      <table class="comparison-table">
        <thead>
          <tr>
            <th>Feature</th>
            @foreach($cols as $slug)
              @php $pl = $bySlug->get($slug); @endphp
              <th class="plan-column">
                @if($pl)
                  <div class="plan-column-name">{{ $pl->name }}</div>
                  <div class="plan-column-price">
                    TZS {{ number_format((int)$pl->price_tzs) }}
                    <span style="font-size: 0.75rem; color: var(--gray);">
                      {{ (($pl->period_months ?? 1) > 1) ? '/ '.((int)$pl->period_months).' mo' : 'per month' }}
                    </span>
                  </div>
                @else
                  <div style="color: var(--gray);">—</div>
                @endif
              </th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach($featureOrder as $feat)
            <tr>
              <td style="font-weight: 600;">{{ $feat }}</td>
              @foreach($cols as $slug)
                @php $v = $val($slug, $feat); @endphp
                <td class="{{ ($slug==='hollyn-max' && Str::startsWith($v,'∞')) ? 'unlimited' : '' }}">
                  {{ $v }}
                </td>
              @endforeach
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </section>
</div>

<script>
// Scroll reveal animation
function scrollReveal() {
  const elements = document.querySelectorAll('.scroll-reveal');
  const windowHeight = window.innerHeight;
  
  elements.forEach(element => {
    const elementTop = element.getBoundingClientRect().top;
    const elementVisible = 150;
    
    if (elementTop < windowHeight - elementVisible) {
      element.classList.add('revealed');
    }
  });
}

// Initialize scroll reveal
window.addEventListener('scroll', scrollReveal);
scrollReveal(); // Run on load

// Modal close on backdrop click
document.querySelectorAll('dialog').forEach(dialog => {
  dialog.addEventListener('click', function(e) {
    if (e.target === this) {
      this.close();
    }
  });
});

// Add loading animation
window.addEventListener('load', function() {
  document.body.classList.add('loaded');
});
</script>

@endsection