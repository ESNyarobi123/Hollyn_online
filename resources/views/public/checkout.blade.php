@extends('layouts.app')
@section('title', 'Checkout')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6">
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

  {{-- Step header --}}
  <div class="mb-5 flex items-center gap-3 text-sm">
    <div class="flex items-center gap-2">
      <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 font-semibold">1</span>
      <span>Maelezo yako</span>
    </div>
    <span class="text-brand-slate/50">—</span>
    <div class="flex items-center gap-2 opacity-70">
      <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-gray-100 text-gray-700 font-semibold">2</span>
      <span>Malipo</span>
    </div>
  </div>

  {{-- Plan summary --}}
  <div class="mb-5 rounded-2xl border border-brand-cream bg-white/70 backdrop-blur px-4 py-3">
    <div class="flex items-start justify-between gap-4">
      <div>
        <h2 class="text-lg font-semibold">{{ $plan->name ?? 'Plan' }}</h2>
        <p class="text-xs text-brand-slate/80 mt-0.5">Utalipiwa kwa njia ya simu (STK/USSD push).</p>
      </div>
      <div class="text-right">
        <div class="text-sm text-brand-slate">Pay</div>
        <div class="text-2xl font-bold">
          TZS {{ number_format((int)($plan->price_tzs ?? 0)) }}
        </div>
      </div>
    </div>
  </div>

  {{-- Smart checkout form --}}
  <form method="POST" action="{{ route('checkout.create') }}" id="checkoutForm" class="space-y-5">
    @csrf
    <input type="hidden" name="plan_id" value="{{ $plan->id ?? '' }}">

    {{-- Full Name --}}
    <div>
      <label for="customer_name" class="block text-sm mb-1">Full Name</label>
      <div class="relative">
        <input
          id="customer_name"
          name="customer_name"
          value="{{ old('customer_name') }}"
          required
          autocomplete="name"
          class="peer w-full rounded-2xl border border-brand-cream px-3 py-2 focus:border-brand-emerald focus:ring-2 focus:ring-brand-emerald/20"
        >
        <span class="pointer-events-none absolute right-3 top-2.5 hidden text-emerald-600 peer-[&:not(:placeholder-shown)]:block">✓</span>
      </div>
      @error('customer_name')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
      @enderror
    </div>

    <div class="grid sm:grid-cols-2 gap-4">
      {{-- Email --}}
      <div>
        <label for="customer_email" class="block text-sm mb-1">Email</label>
        <div class="relative">
          <input
            id="customer_email"
            type="email"
            name="customer_email"
            value="{{ old('customer_email') }}"
            required
            autocomplete="email"
            class="peer w-full rounded-2xl border border-brand-cream px-3 py-2 focus:border-brand-emerald focus:ring-2 focus:ring-brand-emerald/20"
          >
          <span class="pointer-events-none absolute right-3 top-2.5 hidden text-emerald-600 peer-[&:not(:placeholder-shown)]:block">✓</span>
        </div>
        @error('customer_email')
          <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
      </div>

      {{-- Phone --}}
      <div>
        <label for="customer_phone" class="block text-sm mb-1">Simu (TZ)</label>
        <div class="relative">
          <input
            id="customer_phone"
            name="customer_phone"
            value="{{ old('customer_phone') }}"
            required
            inputmode="tel"
            pattern="^(?:0[67]\d{8}|(?:\+?255|00255)[67]\d{8})$"
            title="Tumia 07xxxxxxxx / 06xxxxxxxx au +2557xxxxxxxx / 2557xxxxxxxx / 002557xxxxxxxx"
            placeholder="07xxxxxxxx au +2557xxxxxxxx"
            class="peer w-full rounded-2xl border border-brand-cream px-3 py-2 pr-28 focus:border-brand-emerald focus:ring-2 focus:ring-brand-emerald/20"
          >
          {{-- Provider pill (auto detect) --}}
          <span id="providerPill" class="absolute right-2 top-1/2 -translate-y-1/2 hidden items-center gap-1 rounded-full border px-2 py-0.5 text-xs"></span>
        </div>
        @error('customer_phone')
          <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
        <p id="phoneHint" class="mt-1 text-xs text-brand-slate">
          Inakubali: 07/06…, +255…, 255…, 00255… (mfano: 0765XXXXXX au +255712XXXXXX).
        </p>
      </div>
    </div>

    {{-- Domain (optional) --}}
    <div>
      <label for="domain" class="block text-sm mb-1">Domain (hiari)</label>
      <input
        id="domain"
        name="domain"
        value="{{ old('domain') }}"
        placeholder="example.tz"
        class="w-full rounded-2xl border border-brand-cream px-3 py-2 focus:border-brand-emerald focus:ring-2 focus:ring-brand-emerald/20"
      >
      @error('domain')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
      @enderror
    </div>

    {{-- Hidden fields expected by gateway/client --}}
    <input type="hidden" name="buyer_name"  id="buyer_name">
    <input type="hidden" name="buyer_email" id="buyer_email">
    <input type="hidden" name="buyer_phone" id="buyer_phone">
    {{-- Optional provider hint to backend --}}
    <input type="hidden" name="provider" id="provider_hint">

    {{-- Help bar --}}
    <div class="flex items-center justify-between rounded-2xl border border-brand-cream bg-white/70 px-4 py-3">
      <div class="text-xs text-brand-slate">
        <span class="inline-flex items-center gap-1">
          {{-- lock icon --}}
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" class="-mt-0.5">
            <path d="M7 10V7a5 5 0 1110 0v3M5 10h14v9a2 2 0 01-2 2H7a2 2 0 01-2-2v-9z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
          Malipo salama: utapokea STK/USSD push kwenye simu yako.
        </span>
      </div>
      <a href="https://youtu.be/O8B2KxNAAGo" target="_blank" class="text-xs underline">Tazama jinsi inavyofanya kazi</a>
    </div>

    {{-- Submit --}}
    <button type="submit" id="payBtn"
      class="btn w-full rounded-2xl bg-gradient-to-r from-amber-300 to-yellow-400 text-black font-semibold py-3 shadow-md hover:shadow-lg active:scale-[.99] transition">
      <span class="inline-flex items-center gap-2">
        {{-- wand icon --}}
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M4 20l8-8M14 6l4 4M14 6l2-4m0 0l2 4m-2-4v6" stroke="#111827" stroke-width="1.6" stroke-linecap="round"/>
        </svg>
        Lipa Sasa na ZenoPay
      </span>
    </button>

    <p class="text-xs text-center text-brand-slate">
      Kwa kubofya malipo unakubali <a href="#" class="underline">Terms</a> na <a href="#" class="underline">Privacy</a>.
    </p>
  </form>
</div>

{{-- Smart helpers (UX): phone hint, auto provider, buyer_* mirror, 255 msisdn, prevent double submit --}}
<script>
(function () {
  const form  = document.getElementById('checkoutForm');
  const btn   = document.getElementById('payBtn');
  const nameI = document.getElementById('customer_name');
  const mailI = document.getElementById('customer_email');
  const phnI  = document.getElementById('customer_phone');
  const hint  = document.getElementById('phoneHint');

  const buyerName  = document.getElementById('buyer_name');
  const buyerEmail = document.getElementById('buyer_email');
  const buyerPhone = document.getElementById('buyer_phone');
  const providerEl = document.getElementById('provider_hint');
  const pill       = document.getElementById('providerPill');

  if (!form) return;

  // Detect provider from msisdn (2557/2556…) or local 07/06
  function detectProvider(msisdn255) {
    // msisdn255 expected "2557xxxxxxxx"
    const p3 = msisdn255?.slice(3, 6);
    const vod = ['074','075','076']; // Vodacom (M-PESA)
    const air = ['078','079'];       // Airtel Money
    const tgl = ['062','063','065','066','067','068','069','071','073','077']; // Tigo-like
    if (vod.includes(p3)) return 'M-PESA';
    if (air.includes(p3)) return 'AIRTEL-MONEY';
    if (tgl.includes(p3)) return 'TIGO-PESA';
    // fallback by p2
    const p2 = msisdn255?.slice(3,5);
    if (['71','74','75','76'].includes(p2)) return 'M-PESA';
    if (['78','79'].includes(p2))           return 'AIRTEL-MONEY';
    if (['65','66','67','68','69'].includes(p2)) return 'TIGO-PESA';
    return '';
  }

  // Convert local formats → 255…
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
    pill.className = 'absolute right-2 top-1/2 -translate-y-1/2 inline-flex items-center gap-1 rounded-full border px-2 py-0.5 text-xs';
    if (provider === 'M-PESA')       pill.classList.add('border-emerald-200','bg-emerald-50','text-emerald-700');
    else if (provider === 'TIGO-PESA') pill.classList.add('border-sky-200','bg-sky-50','text-sky-700');
    else if (provider === 'AIRTEL-MONEY') pill.classList.add('border-rose-200','bg-rose-50','text-rose-700');
  }

  // Live hint + provider
  function refreshPhoneUX() {
    const raw = phnI?.value || '';
    const ok  = /^(?:0[67]\d{8}|(?:\+?255|00255)[67]\d{8})$/.test(raw);
    if (ok) {
      hint.textContent = 'Utapokea STK/USSD push kwenye hii namba.';
      hint.className = 'mt-1 text-xs text-emerald-700';
    } else {
      const digits = raw.replace(/\D+/g, '');
      if (/^0[67]\d{0,9}$/.test(digits)) {
        hint.textContent = `Umeandika tarakimu ${digits.length}. Namba ya local inahitaji 10 (mfano 07xxxxxxxx).`;
      } else {
        hint.textContent = 'Tumia 07/06xxxxxxxx au +255/255/00255 ikifuatiwa na 7/6 na tarakimu 8.';
      }
      hint.className = 'mt-1 text-xs text-red-600';
    }

    const msisdn = toMsisdn255(raw);
    const prov   = msisdn ? detectProvider(msisdn) : '';
    stylePill(prov);
  }

  phnI?.addEventListener('input', refreshPhoneUX);
  refreshPhoneUX();

  // Mirror buyer_* and provider on submit
  form.addEventListener('submit', function () {
    if (buyerName)  buyerName.value  = (nameI?.value || '').trim();
    if (buyerEmail) buyerEmail.value = (mailI?.value || '').trim();
    if (buyerPhone) buyerPhone.value = toMsisdn255(phnI?.value || '');
    if (providerEl) providerEl.value = detectProvider(buyerPhone.value) || '';

    // Loading state
    if (btn) {
      btn.disabled = true;
      btn.style.opacity = .85;
      btn.innerHTML = '<span class="inline-flex items-center gap-2">'+
        '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" class="animate-spin" aria-hidden="true">'+
          '<circle cx="12" cy="12" r="9" stroke="#111827" stroke-width="2" stroke-dasharray="56" stroke-linecap="round"></circle>'+
        '</svg>'+
        'Tunaandaa malipo…'+
      '</span>';
    }
  }, { once: true });
})();
</script>
@endsection
