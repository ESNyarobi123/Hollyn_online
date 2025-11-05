@extends('layouts.app')
@section('content')
<div class="max-w-2xl mx-auto card">
  <h2 class="heading mb-2">Order Summary</h2>
  <p>Status: <span class="badge">{{ $order->status }}</span></p>
  <p class="mt-2">Plan: <b>{{ $order->plan->name }}</b> — TZS {{ number_format($order->price_tzs) }}</p>
  <p>Domain: {{ $order->domain ?: '—' }}</p>

  @if($order->service)
    <div class="mt-4">
      <h3 class="font-semibold mb-1">Service</h3>
      <p>State: <span class="badge">{{ $order->service->status }}</span></p>
      @if($order->service->status==='active' && $order->service->enduser_url)
        <a class="btn-primary mt-3" target="_blank" href="{{ $order->service->enduser_url }}">Go to cPanel</a>
      @endif
    </div>
  @endif
</div>
@endsection
