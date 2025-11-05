@extends('layouts.app')
@section('title','Order #'.$order->id)

@section('content')
  <h1 class="text-2xl md:text-3xl font-extrabold text-brand-ocean mb-3">Order #{{ $order->id }}</h1>
  <p class="text-brand-slate mb-6">
    Plan: <strong>{{ optional($order->plan)->name ?? '—' }}</strong> &middot;
    Status: <strong>{{ ucfirst($order->status) }}</strong>
  </p>

  @php
    $service = $order->service ?? null;
    $panelUrl = $service->enduser_url ?? config('services.webuzo.enduser_url');
    $uname = $service->panel_username ?? null;
  @endphp

  <div class="card mb-6">
    <h2 class="text-lg font-semibold text-brand-ocean mb-2">Hosting Status</h2>

    @if($service && $service->status === 'active')
      <div class="text-emerald-700 mb-3">Your hosting is active ✅</div>
      <div class="text-sm text-brand-slate mb-4">
        Domain: <strong>{{ $service->domain ?? '—' }}</strong><br>
        Username: <strong>{{ $uname ?? '—' }}</strong>
      </div>
      @if($panelUrl)
        <a href="{{ $panelUrl }}" target="_blank" class="btn-primary">Go to Control Panel</a>
      @endif
    @elseif($service && $service->status === 'provisioning')
      <div class="text-amber-700">Provisioning… your hosting account is being created. Please check back shortly.</div>
    @elseif(in_array($order->status, ['paid','complete']))
      <div class="text-amber-700">Queued — provisioning will start shortly.</div>
    @else
      <div class="text-brand-slate">Pending payment. Once paid, your hosting will be created automatically.</div>
    @endif
  </div>

  <div class="card">
    <h2 class="text-lg font-semibold text-brand-ocean mb-2">Order Details</h2>
    <div class="text-sm text-brand-slate">
      <div>Plan: <strong>{{ optional($order->plan)->name ?? '—' }}</strong></div>
      <div>Price: <strong>TZS {{ number_format((int)($order->price_tzs ?? 0)) }}</strong></div>
      <div>Created: <strong>{{ $order->created_at }}</strong></div>
    </div>
  </div>
@endsection
