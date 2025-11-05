@extends('layouts.app')
@section('content')
<div class="max-w-xl mx-auto card text-center">
  <h2 class="heading mb-2">Complete payment on your phone</h2>
  <p class="text-brand-slate">Order: <b>{{ $order->order_uuid }}</b></p>
  <p class="mt-2">Amount: <span class="font-bold text-brand-emerald">TZS {{ number_format($order->price_tzs) }}</span></p>
  <p class="mt-4">Tutakutengenezea akaunti ya Webuzo moja kwa moja tukipokea <b>COMPLETED</b>.</p>
</div>
@endsection
