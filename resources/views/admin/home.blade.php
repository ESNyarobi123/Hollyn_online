@extends('layouts.app')
@section('content')
<div class="grid md:grid-cols-[16rem_1fr] gap-6">
  <aside class="sidebar rounded-2xl">
    <h3 class="font-semibold mb-3">Admin</h3>
    <a href="{{ route('admin.home') }}">Overview</a>
    <a href="{{ route('admin.plans.index') }}">Plans</a>
    <a href="{{ route('admin.users.index') }}">Users</a>
    <a href="{{ route('admin.orders.index') }}">Orders</a>
    <a href="{{ route('admin.services.index') }}">Services</a>
  </aside>

  <section class="space-y-6">
    <div class="grid md:grid-cols-4 gap-4">
      <div class="card"><div class="text-sm">Active Services</div><div class="text-2xl font-bold">{{ $stats['active'] }}</div></div>
      <div class="card"><div class="text-sm">Pending Provision</div><div class="text-2xl font-bold">{{ $stats['pending'] }}</div></div>
      <div class="card"><div class="text-sm">Paid Today</div><div class="text-2xl font-bold text-brand-emerald">{{ $stats['paid_today'] }}</div></div>
      <div class="card"><div class="text-sm">Failed</div><div class="text-2xl font-bold text-red-600">{{ $stats['failed'] }}</div></div>
    </div>
    <div class="card">
      <h3 class="heading mb-3">Recent Orders</h3>
      {{-- hapa uta-render table yako ya orders --}}
      <p class="text-sm text-brand-slate">Build: admin/orders index (CRUD) in next step.</p>
    </div>
  </section>
</div>
@endsection
