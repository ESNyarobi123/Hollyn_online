@extends('layouts.app')
@section('title', 'Checkout - Select Your Duration')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
  {{-- Alerts --}}
  @if (session('status'))
    <div class="mb-4 rounded-2xl border border-green-200 bg-green-50 text-green-800 px-4 py-3">
      {{ session('status') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 text-red-800 px-4 py-3">
      <strong>Angalia makosa yafuatayo:</strong>
      <ul class="list-disc list-inside mt-2 text-sm">
        @foreach ($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- Progress Steps --}}
  <div class="mb-6 flex items-center justify-center gap-3 text-sm">
    <div class="flex items-center gap-2">
      <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500 text-white font-bold">✓</span>
      <span class="font-medium">Plan</span>
    </div>
    <div class="h-0.5 w-12 bg-emerald-300"></div>
    <div class="flex items-center gap-2">
      <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500 text-white font-bold">2</span>
      <span class="font-medium">Duration</span>
    </div>
    <div class="h-0.5 w-12 bg-gray-300"></div>
    <div class="flex items-center gap-2 opacity-60">
      <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-gray-200 text-gray-600 font-bold">3</span>
      <span>Payment</span>
    </div>
  </div>

  <div class="grid md:grid-cols-3 gap-6">
    {{-- Left: Form --}}
    <div class="md:col-span-2 space-y-6">
      {{-- Plan Summary Card --}}
      <div class="rounded-2xl border-2 border-indigo-100 bg-gradient-to-br from-indigo-50 to-purple-50 p-5">
        <div class="flex items-start justify-between">
          <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $plan->name ?? 'Plan' }}</h2>
            <p class="text-sm text-gray-600 mt-1">Base Price: TZS {{ number_format((int)($plan->price_tzs ?? 0)) }}/month</p>
          </div>
          <div class="bg-white/80 rounded-xl px-3 py-1.5 border border-indigo-200">
            <span class="text-xs font-medium text-indigo-600">{{ $plan->slug }}</span>
          </div>
        </div>
      </div>

      <form method="POST" action="{{ route('checkout.create') }}" id="checkoutForm" class="space-y-6">
        @csrf
        <input type="hidden" name="plan_id" value="{{ $plan->id ?? '' }}">
        <input type="hidden" name="duration_months" id="duration_months" value="1">

        {{-- Duration Selector --}}
        <div class="rounded-2xl border-2 border-gray-200 bg-white p-6">
          <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Choose Subscription Duration
          </h3>

          {{-- Quick Options --}}
          <div class="grid grid-cols-2 gap-3 mb-4">
            @php
              $presets = [
                1 => ['label' => '1 Month', 'badge' => ''],
                3 => ['label' => '3 Months', 'badge' => 'Save 10%', 'popular' => false],
                6 => ['label' => '6 Months', 'badge' => 'Save 15%', 'popular' => false],
                12 => ['label' => '1 Year', 'badge' => 'Save 20%', 'popular' => true],
              ];
            @endphp
            @foreach($presets as $months => $info)
              <button type="button" 
                      class="duration-btn relative rounded-xl border-2 p-4 text-left transition-all hover:border-indigo-400 hover:shadow-md"
                      data-months="{{ $months }}"
                      onclick="selectDuration({{ $months }})">
                <div class="flex items-start justify-between">
                  <div>
                    <div class="font-bold text-gray-900">{{ $info['label'] }}</div>
                    @if($info['badge'])
                      <div class="mt-1 inline-block px-2 py-0.5 rounded-full text-xs font-semibold
                                  {{ ($info['popular'] ?? false) ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                        {{ $info['badge'] }}
                      </div>
                    @endif
                  </div>
                  <div class="radio-circle"></div>
                </div>
                @if($info['popular'] ?? false)
                  <div class="absolute -top-2 -right-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">
                    BEST VALUE
                  </div>
                @endif
              </button>
            @endforeach
          </div>

          {{-- Custom Duration Input --}}
          <div class="mt-4 p-4 rounded-xl bg-gray-50 border border-gray-200">
            <label class="block text-sm font-medium text-gray-700 mb-2">Or choose custom duration:</label>
            <div class="flex items-center gap-3">
              <input type="number" 
                     id="custom_months" 
                     min="1" 
                     max="36" 
                     placeholder="Enter months"
                     class="flex-1 rounded-xl border-gray-300 px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
                     oninput="selectCustomDuration(this.value)">
              <span class="text-sm font-medium text-gray-600">months</span>
            </div>
            <p class="mt-2 text-xs text-gray-500">Maximum: 36 months (3 years)</p>
          </div>
        </div>

        {{-- Customer Details --}}
        <div class="rounded-2xl border-2 border-gray-200 bg-white p-6">
          <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Your Information
          </h3>

          <div class="space-y-4">
            {{-- Full Name --}}
            <div>
              <label for="customer_name" class="block text-sm font-medium mb-1.5">Full Name *</label>
              <input id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required autocomplete="name"
                     class="w-full rounded-xl border-gray-300 px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
              @error('customer_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
              {{-- Email --}}
              <div>
                <label for="customer_email" class="block text-sm font-medium mb-1.5">Email *</label>
                <input id="customer_email" type="email" name="customer_email" value="{{ old('customer_email') }}" 
                       required autocomplete="email"
                       class="w-full rounded-xl border-gray-300 px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                @error('customer_email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
              </div>

              {{-- Phone --}}
              <div>
                <label for="customer_phone" class="block text-sm font-medium mb-1.5">Phone (TZ) *</label>
                <div class="relative">
                  <input id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" 
                         required inputmode="tel"
                         pattern="^(?:0[67]\d{8}|(?:\+?255|00255)[67]\d{8})$"
                         placeholder="07xxxxxxxx"
                         class="w-full rounded-xl border-gray-300 px-4 py-2.5 pr-24 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                  <span id="providerPill" class="absolute right-2 top-1/2 -translate-y-1/2 hidden text-xs font-medium px-2 py-1 rounded-full"></span>
                </div>
                @error('customer_phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
              </div>
            </div>

            {{-- Domain --}}
            <div>
              <label for="domain" class="block text-sm font-medium mb-1.5">Domain (Optional)</label>
              <input id="domain" name="domain" value="{{ old('domain') }}" placeholder="example.com"
                     class="w-full rounded-xl border-gray-300 px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
              @error('domain') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Notes --}}
            <div>
              <label for="notes" class="block text-sm font-medium mb-1.5">Special Requirements (Optional)</label>
              <textarea id="notes" name="notes" rows="2" placeholder="Any special requirements or notes..."
                        class="w-full rounded-xl border-gray-300 px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">{{ old('notes') }}</textarea>
              @error('notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
          </div>
        </div>

        {{-- Hidden gateway fields --}}
        <input type="hidden" name="buyer_name" id="buyer_name">
        <input type="hidden" name="buyer_email" id="buyer_email">
        <input type="hidden" name="buyer_phone" id="buyer_phone">
        <input type="hidden" name="provider" id="provider_hint">

        {{-- Submit Button --}}
        <button type="submit" id="payBtn"
                class="w-full rounded-2xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold py-4 shadow-lg hover:shadow-xl active:scale-[.99] transition-all">
          <span class="inline-flex items-center gap-2 text-lg">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
              <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Proceed to Payment
          </span>
        </button>

        <p class="text-xs text-center text-gray-500">
          🔒 Secure payment powered by ZenoPay. Your data is encrypted and safe.
        </p>
      </form>
    </div>

    {{-- Right: Price Summary (Sticky) --}}
    <div class="md:col-span-1">
      <div class="sticky top-6 rounded-2xl border-2 border-gray-200 bg-white p-6 shadow-lg">
        <h3 class="text-lg font-bold mb-4">Order Summary</h3>
        
        <div class="space-y-3 border-b pb-4 mb-4">
          <div class="flex justify-between text-sm">
            <span class="text-gray-600">Plan:</span>
            <span class="font-semibold">{{ $plan->name }}</span>
          </div>
          <div class="flex justify-between text-sm">
            <span class="text-gray-600">Duration:</span>
            <span class="font-semibold" id="summary-duration">1 month</span>
          </div>
          <div class="flex justify-between text-sm">
            <span class="text-gray-600">Base Price:</span>
            <span>TZS <span id="summary-base">{{ number_format($plan->price_tzs) }}</span></span>
          </div>
          <div class="flex justify-between text-sm" id="discount-row" style="display: none;">
            <span class="text-emerald-600 font-medium">Discount:</span>
            <span class="text-emerald-600 font-semibold">-<span id="summary-discount-pct">0</span>%</span>
          </div>
        </div>

        <div class="mb-4">
          <div class="flex justify-between items-baseline">
            <span class="text-gray-900 font-bold">Total:</span>
            <div class="text-right">
              <div class="text-3xl font-bold text-indigo-600">
                TZS <span id="summary-total">{{ number_format($plan->price_tzs) }}</span>
              </div>
              <div class="text-xs text-gray-500 mt-1" id="savings-text" style="display: none;">
                You save: TZS <span id="summary-savings">0</span>
              </div>
            </div>
          </div>
        </div>

        {{-- Features Preview --}}
        <div class="pt-4 border-t">
          <p class="text-xs font-semibold text-gray-700 mb-2">What's Included:</p>
          <ul class="space-y-1.5 text-xs text-gray-600">
            <li class="flex items-center gap-2">
              <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
              Instant activation
            </li>
            <li class="flex items-center gap-2">
              <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
              Free SSL certificate
            </li>
            <li class="flex items-center gap-2">
              <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
              24/7 Support
            </li>
            <li class="flex items-center gap-2">
              <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
              Money-back guarantee
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .duration-btn {
    border-color: #e5e7eb;
    background: white;
  }
  .duration-btn.selected {
    border-color: #6366f1;
    background: #eef2ff;
  }
  .radio-circle {
    width: 20px;
    height: 20px;
    border: 2px solid #d1d5db;
    border-radius: 50%;
    position: relative;
  }
  .duration-btn.selected .radio-circle {
    border-color: #6366f1;
  }
  .duration-btn.selected .radio-circle::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 10px;
    height: 10px;
    background: #6366f1;
    border-radius: 50%;
  }
</style>

<script>
(function() {
  const form = document.getElementById('checkoutForm');
  const durationInput = document.getElementById('duration_months');
  const customInput = document.getElementById('custom_months');
  const basePriceMonthly = {{ $plan->price_tzs }};
  
  // Phone provider detection
  const phnI = document.getElementById('customer_phone');
  const pill = document.getElementById('providerPill');
  
  function detectProvider(msisdn255) {
    const p3 = msisdn255?.slice(3, 6);
    if (['074','075','076'].includes(p3)) return 'M-PESA';
    if (['078','079'].includes(p3)) return 'AIRTEL-MONEY';
    if (['062','063','065','066','067','068','069','071','073','077'].includes(p3)) return 'TIGO-PESA';
    return '';
  }
  
  function toMsisdn255(raw) {
    if (!raw) return '';
    raw = raw.trim();
    let m = raw.match(/^\+255([6-7]\d{8})$/);
    if (m) return '255' + m[1];
    const digits = raw.replace(/\D+/g, '');
    m = digits.match(/^0([6-7]\d{8})$/);     if (m) return '255' + m[1];
    m = digits.match(/^255([6-7]\d{8})$/);   if (m) return '255' + m[1];
    m = digits.match(/^00255([6-7]\d{8})$/); if (m) return '255' + m[1];
    return '';
  }
  
  function stylePill(provider) {
    if (!pill) return;
    if (!provider) { pill.classList.add('hidden'); return; }
    pill.classList.remove('hidden');
    pill.textContent = provider.replace('-', ' ');
    pill.className = 'absolute right-2 top-1/2 -translate-y-1/2 inline-flex items-center gap-1 text-xs font-medium px-2 py-1 rounded-full';
    if (provider === 'M-PESA') pill.classList.add('bg-emerald-100','text-emerald-700');
    else if (provider === 'TIGO-PESA') pill.classList.add('bg-sky-100','text-sky-700');
    else if (provider === 'AIRTEL-MONEY') pill.classList.add('bg-rose-100','text-rose-700');
  }
  
  phnI?.addEventListener('input', function() {
    const msisdn = toMsisdn255(this.value);
    const prov = msisdn ? detectProvider(msisdn) : '';
    stylePill(prov);
  });

  // Duration selection & price calculation
  function calculatePrice(months) {
    let discount = 0;
    if (months >= 12) discount = 20;
    else if (months >= 6) discount = 15;
    else if (months >= 3) discount = 10;
    
    const subtotal = basePriceMonthly * months;
    const discountAmount = subtotal * (discount / 100);
    const total = subtotal - discountAmount;
    
    return {
      months,
      discount,
      subtotal,
      discountAmount,
      total: Math.round(total),
      savings: Math.round(discountAmount)
    };
  }
  
  function updateSummary(months) {
    const calc = calculatePrice(months);
    
    // Update hidden input
    durationInput.value = months;
    
    // Update summary
    document.getElementById('summary-duration').textContent = 
      months === 1 ? '1 month' : 
      months === 12 ? '1 year' :
      months + ' months';
    
    document.getElementById('summary-base').textContent = 
      (basePriceMonthly * months).toLocaleString();
    
    document.getElementById('summary-total').textContent = calc.total.toLocaleString();
    
    // Show/hide discount
    const discountRow = document.getElementById('discount-row');
    const savingsText = document.getElementById('savings-text');
    
    if (calc.discount > 0) {
      discountRow.style.display = 'flex';
      savingsText.style.display = 'block';
      document.getElementById('summary-discount-pct').textContent = calc.discount;
      document.getElementById('summary-savings').textContent = calc.savings.toLocaleString();
    } else {
      discountRow.style.display = 'none';
      savingsText.style.display = 'none';
    }
  }
  
  window.selectDuration = function(months) {
    // Update UI
    document.querySelectorAll('.duration-btn').forEach(btn => {
      btn.classList.remove('selected');
    });
    document.querySelector(`[data-months="${months}"]`)?.classList.add('selected');
    
    // Clear custom input
    customInput.value = '';
    
    // Update summary
    updateSummary(months);
  };
  
  window.selectCustomDuration = function(value) {
    const months = parseInt(value) || 1;
    if (months < 1 || months > 36) return;
    
    // Clear preset selection
    document.querySelectorAll('.duration-btn').forEach(btn => {
      btn.classList.remove('selected');
    });
    
    // Update summary
    updateSummary(months);
  };
  
  // Form submission
  form.addEventListener('submit', function() {
    const nameI = document.getElementById('customer_name');
    const mailI = document.getElementById('customer_email');
    
    document.getElementById('buyer_name').value = nameI?.value || '';
    document.getElementById('buyer_email').value = mailI?.value || '';
    document.getElementById('buyer_phone').value = toMsisdn255(phnI?.value || '');
    document.getElementById('provider_hint').value = detectProvider(toMsisdn255(phnI?.value || '')) || '';
    
    const btn = document.getElementById('payBtn');
    if (btn) {
      btn.disabled = true;
      btn.style.opacity = .7;
      btn.innerHTML = '<span class="inline-flex items-center gap-2"><svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...</span>';
    }
  }, { once: true });
  
  // Initialize with 1 month
  selectDuration(1);
})();
</script>
@endsection

