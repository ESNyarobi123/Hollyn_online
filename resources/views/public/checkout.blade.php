@extends('layouts.app')
@section('title', 'Checkout - ' . ($plan->name ?? 'Hosting Plan'))

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-950 py-12" x-data="checkoutCalculator({{ $plan->price_tzs ?? 0 }})">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-10 text-center">
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Secure Checkout</h1>
            <p class="mt-2 text-slate-600 dark:text-slate-400">Complete your order to get started instantly.</p>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">
            
            <!-- Main Form Column -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Alerts -->
                @if (session('status'))
                    <div class="rounded-2xl border border-green-200 bg-green-50 text-green-800 px-4 py-3 flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="rounded-2xl border border-red-200 bg-red-50 text-red-800 px-4 py-3">
                        <div class="flex items-center gap-3 mb-2">
                            <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <strong>Please correct the following errors:</strong>
                        </div>
                        <ul class="list-disc list-inside text-sm ml-8">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('checkout.create') }}" id="checkoutForm" class="space-y-8">
                    @csrf
                    <input type="hidden" name="plan_id" value="{{ $plan->id ?? '' }}">
                    <input type="hidden" name="duration_months" :value="duration">

                    <!-- Billing Cycle Selector -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                            <span class="flex items-center justify-center w-6 h-6 rounded-full bg-violet-100 text-violet-600 text-xs font-bold">1</span>
                            Choose Billing Cycle
                        </h2>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Monthly -->
                            <label class="relative flex cursor-pointer rounded-2xl border p-4 shadow-sm focus:outline-none transition-all"
                                :class="duration === 1 ? 'border-violet-600 ring-1 ring-violet-600 bg-violet-50/50 dark:bg-violet-900/20' : 'border-slate-200 dark:border-slate-700 hover:border-violet-300 dark:hover:border-violet-700'">
                                <input type="radio" name="billing_cycle" value="1" class="sr-only" @click="setDuration(1)">
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="block text-sm font-medium text-slate-900 dark:text-white">Monthly</span>
                                        <span class="mt-1 flex items-center text-sm text-slate-500 dark:text-slate-400">Pay monthly, cancel anytime</span>
                                        <span class="mt-6 text-sm font-semibold text-slate-900 dark:text-white" x-text="formatMoney(basePrice)"></span>
                                    </span>
                                </span>
                                <svg class="h-5 w-5 text-violet-600" viewBox="0 0 20 20" fill="currentColor" x-show="duration === 1">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </label>

                            <!-- Quarterly -->
                            <label class="relative flex cursor-pointer rounded-2xl border p-4 shadow-sm focus:outline-none transition-all"
                                :class="duration === 3 ? 'border-violet-600 ring-1 ring-violet-600 bg-violet-50/50 dark:bg-violet-900/20' : 'border-slate-200 dark:border-slate-700 hover:border-violet-300 dark:hover:border-violet-700'">
                                <input type="radio" name="billing_cycle" value="3" class="sr-only" @click="setDuration(3)">
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="block text-sm font-medium text-slate-900 dark:text-white">Quarterly</span>
                                        <span class="mt-1 flex items-center text-sm text-slate-500 dark:text-slate-400">
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">Save 10%</span>
                                        </span>
                                        <span class="mt-6 text-sm font-semibold text-slate-900 dark:text-white" x-text="formatMoney(calculateTotal(3))"></span>
                                    </span>
                                </span>
                                <svg class="h-5 w-5 text-violet-600" viewBox="0 0 20 20" fill="currentColor" x-show="duration === 3">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </label>

                            <!-- Semi-Annual -->
                            <label class="relative flex cursor-pointer rounded-2xl border p-4 shadow-sm focus:outline-none transition-all"
                                :class="duration === 6 ? 'border-violet-600 ring-1 ring-violet-600 bg-violet-50/50 dark:bg-violet-900/20' : 'border-slate-200 dark:border-slate-700 hover:border-violet-300 dark:hover:border-violet-700'">
                                <input type="radio" name="billing_cycle" value="6" class="sr-only" @click="setDuration(6)">
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="block text-sm font-medium text-slate-900 dark:text-white">Semi-Annual</span>
                                        <span class="mt-1 flex items-center text-sm text-slate-500 dark:text-slate-400">
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">Save 15%</span>
                                        </span>
                                        <span class="mt-6 text-sm font-semibold text-slate-900 dark:text-white" x-text="formatMoney(calculateTotal(6))"></span>
                                    </span>
                                </span>
                                <svg class="h-5 w-5 text-violet-600" viewBox="0 0 20 20" fill="currentColor" x-show="duration === 6">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </label>

                            <!-- Annual -->
                            <label class="relative flex cursor-pointer rounded-2xl border p-4 shadow-sm focus:outline-none transition-all"
                                :class="duration === 12 ? 'border-violet-600 ring-1 ring-violet-600 bg-violet-50/50 dark:bg-violet-900/20' : 'border-slate-200 dark:border-slate-700 hover:border-violet-300 dark:hover:border-violet-700'">
                                <input type="radio" name="billing_cycle" value="12" class="sr-only" @click="setDuration(12)">
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="block text-sm font-medium text-slate-900 dark:text-white">Annual</span>
                                        <span class="mt-1 flex items-center text-sm text-slate-500 dark:text-slate-400">
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">Best Value (20% Off)</span>
                                        </span>
                                        <span class="mt-6 text-sm font-semibold text-slate-900 dark:text-white" x-text="formatMoney(calculateTotal(12))"></span>
                                    </span>
                                </span>
                                <svg class="h-5 w-5 text-violet-600" viewBox="0 0 20 20" fill="currentColor" x-show="duration === 12">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </label>
                        </div>
                    </div>

                    <!-- Personal Details -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                            <span class="flex items-center justify-center w-6 h-6 rounded-full bg-violet-100 text-violet-600 text-xs font-bold">2</span>
                            Account Details
                        </h2>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="customer_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Full Name</label>
                                <input id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required autocomplete="name"
                                    class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:border-violet-500 focus:ring-violet-500 transition-colors"
                                    placeholder="John Doe">
                            </div>

                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="customer_email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email Address</label>
                                    <input id="customer_email" type="email" name="customer_email" value="{{ old('customer_email') }}" required autocomplete="email"
                                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:border-violet-500 focus:ring-violet-500 transition-colors"
                                        placeholder="john@example.com">
                                </div>
                                <div>
                                    <label for="customer_phone" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Phone Number (TZ)</label>
                                    <div class="relative">
                                        <input id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" required inputmode="tel"
                                            class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:border-violet-500 focus:ring-violet-500 transition-colors pr-24"
                                            placeholder="07xxxxxxxx">
                                        <div id="providerPill" class="absolute right-3 top-1/2 -translate-y-1/2 hidden text-xs font-medium px-2 py-0.5 rounded-full"></div>
                                    </div>
                                    <p class="mt-1 text-xs text-slate-500">Used for mobile money payment (M-Pesa, Tigo, Airtel)</p>
                                </div>
                            </div>

                            <div>
                                <label for="domain" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Domain Name (Optional)</label>
                                <input id="domain" name="domain" value="{{ old('domain') }}"
                                    class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:border-violet-500 focus:ring-violet-500 transition-colors"
                                    placeholder="example.com">
                            </div>
                        </div>
                    </div>

                    <!-- Hidden fields for JS logic -->
                    <input type="hidden" name="buyer_name" id="buyer_name">
                    <input type="hidden" name="buyer_email" id="buyer_email">
                    <input type="hidden" name="buyer_phone" id="buyer_phone">

                    <button type="submit" id="payBtn"
                        class="w-full rounded-2xl bg-gradient-to-r from-violet-600 to-indigo-600 text-white font-bold text-lg py-4 shadow-lg shadow-violet-500/30 hover:shadow-violet-500/50 hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center justify-center gap-2">
                        <span>Complete Order</span>
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </button>
                    
                    <p class="text-center text-xs text-slate-500">
                        By continuing, you agree to our Terms of Service and Privacy Policy.
                    </p>
                </form>
            </div>

            <!-- Order Summary Sidebar -->
            <div class="lg:col-span-1">
                <div class="sticky top-24">
                    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-lg border border-slate-200 dark:border-slate-800 p-6 overflow-hidden relative">
                        <!-- Decorative background blob -->
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-violet-500/10 rounded-full blur-2xl"></div>

                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Order Summary</h3>
                        
                        <div class="flex items-center gap-3 mb-6 pb-6 border-b border-slate-100 dark:border-slate-800">
                            <div class="w-12 h-12 rounded-xl bg-violet-100 dark:bg-violet-900/50 flex items-center justify-center text-violet-600 dark:text-violet-400">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 01-2 2v4a2 2 0 012 2h14a2 2 0 012-2v-4a2 2 0 01-2-2m-2-4h.01M17 16h.01"/></svg>
                            </div>
                            <div>
                                <div class="font-semibold text-slate-900 dark:text-white">{{ $plan->name }}</div>
                                <div class="text-sm text-slate-500">Web Hosting</div>
                            </div>
                        </div>

                        <div class="space-y-3 text-sm mb-6">
                            <div class="flex justify-between text-slate-600 dark:text-slate-400">
                                <span>Duration</span>
                                <span class="font-medium text-slate-900 dark:text-white" x-text="duration + ' Month(s)'"></span>
                            </div>
                            <div class="flex justify-between text-slate-600 dark:text-slate-400">
                                <span>Base Price</span>
                                <span class="font-medium text-slate-900 dark:text-white" x-text="formatMoney(basePrice) + '/mo'"></span>
                            </div>
                            <div class="flex justify-between text-green-600 dark:text-green-400" x-show="discountAmount > 0">
                                <span>Discount</span>
                                <span class="font-medium" x-text="'-' + formatMoney(discountAmount)"></span>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                            <div class="flex justify-between items-end">
                                <span class="text-slate-600 dark:text-slate-400 font-medium">Total</span>
                                <span class="text-2xl font-bold text-slate-900 dark:text-white" x-text="formatMoney(totalPrice)"></span>
                            </div>
                            <p class="text-xs text-slate-500 mt-2 text-right">Includes all taxes & fees</p>
                        </div>
                    </div>

                    <!-- Trust Badges -->
                    <div class="mt-6 grid grid-cols-3 gap-4 text-center">
                        <div class="flex flex-col items-center gap-1">
                            <svg class="w-6 h-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <span class="text-[10px] text-slate-500 uppercase tracking-wider font-medium">Secure</span>
                        </div>
                        <div class="flex flex-col items-center gap-1">
                            <svg class="w-6 h-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <span class="text-[10px] text-slate-500 uppercase tracking-wider font-medium">Instant</span>
                        </div>
                        <div class="flex flex-col items-center gap-1">
                            <svg class="w-6 h-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <span class="text-[10px] text-slate-500 uppercase tracking-wider font-medium">Support</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('checkoutCalculator', (initialBasePrice) => ({
        duration: 1,
        basePrice: initialBasePrice,
        
        get totalPrice() {
            return this.calculateTotal(this.duration);
        },

        get discountAmount() {
            const subtotal = this.basePrice * this.duration;
            return subtotal - this.totalPrice;
        },

        setDuration(months) {
            this.duration = months;
        },

        calculateTotal(months) {
            let discount = 0;
            if (months >= 3 && months < 6) discount = 0.10;
            else if (months >= 6 && months < 12) discount = 0.15;
            else if (months >= 12) discount = 0.20;

            const subtotal = this.basePrice * months;
            return subtotal * (1 - discount);
        },

        formatMoney(amount) {
            return new Intl.NumberFormat('en-TZ', {
                style: 'currency',
                currency: 'TZS',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        }
    }));
});

// Phone number logic (Vanilla JS for simplicity with existing script)
(function () {
  const phnI  = document.getElementById('customer_phone');
  const pill  = document.getElementById('providerPill');
  const form  = document.getElementById('checkoutForm');
  const btn   = document.getElementById('payBtn');
  
  // Mirror fields
  const buyerName  = document.getElementById('buyer_name');
  const buyerEmail = document.getElementById('buyer_email');
  const buyerPhone = document.getElementById('buyer_phone');
  const nameI = document.getElementById('customer_name');
  const mailI = document.getElementById('customer_email');

  function detectProvider(msisdn255) {
    const p3 = msisdn255?.slice(3, 6);
    const vod = ['074','075','076']; 
    const air = ['078','079'];       
    const tgl = ['062','063','065','066','067','068','069','071','073','077']; 
    if (vod.includes(p3)) return 'M-PESA';
    if (air.includes(p3)) return 'AIRTEL-MONEY';
    if (tgl.includes(p3)) return 'TIGO-PESA';
    
    const p2 = msisdn255?.slice(3,5);
    if (['71','74','75','76'].includes(p2)) return 'M-PESA';
    if (['78','79'].includes(p2))           return 'AIRTEL-MONEY';
    if (['65','66','67','68','69'].includes(p2)) return 'TIGO-PESA';
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

  function updateProvider() {
      if (!phnI) return;
      const raw = phnI.value;
      const msisdn = toMsisdn255(raw);
      const prov = msisdn ? detectProvider(msisdn) : '';
      
      if (prov) {
          pill.classList.remove('hidden');
          pill.textContent = prov.replace('-', ' ');
          pill.className = 'absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold px-2 py-0.5 rounded-full transition-colors';
          
          if (prov === 'M-PESA') pill.classList.add('bg-green-100', 'text-green-700');
          else if (prov === 'TIGO-PESA') pill.classList.add('bg-blue-100', 'text-blue-700');
          else if (prov === 'AIRTEL-MONEY') pill.classList.add('bg-red-100', 'text-red-700');
      } else {
          pill.classList.add('hidden');
      }
  }

  phnI?.addEventListener('input', updateProvider);

  form?.addEventListener('submit', function() {
      if (buyerName)  buyerName.value  = (nameI?.value || '').trim();
      if (buyerEmail) buyerEmail.value = (mailI?.value || '').trim();
      if (buyerPhone) buyerPhone.value = toMsisdn255(phnI?.value || '');

      if (btn) {
          btn.disabled = true;
          btn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...';
      }
  });

})();
</script>
@endsection
