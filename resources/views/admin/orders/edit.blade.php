@extends('admin.layout')
@section('title','Edit Order #'.$order->id)
@section('content')
<h1 class="text-2xl font-semibold mb-6">Edit Order #{{ $order->id }}</h1>

<form action="{{ route('admin.orders.update',$order) }}" method="post" class="bg-white border rounded-xl p-6 space-y-4 max-w-2xl">
  @csrf @method('PUT')

  <div>
    <label class="block text-sm mb-1">User</label>
    <select name="user_id" class="w-full border rounded-lg px-3 py-2">
      @foreach($users as $u)
        <option value="{{ $u->id }}" @selected($order->user_id==$u->id)>{{ $u->name }}</option>
      @endforeach
    </select>
  </div>

  <div>
    <label class="block text-sm mb-1">Plan</label>
    <select name="plan_id" class="w-full border rounded-lg px-3 py-2">
      @foreach($plans as $p)
        <option value="{{ $p->id }}" @selected($order->plan_id==$p->id)>{{ $p->name }}</option>
      @endforeach
    </select>
  </div>

  <div class="grid md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm mb-1">Customer Name</label>
      <input type="text" name="customer_name" value="{{ old('customer_name',$order->customer_name) }}" class="w-full border rounded-lg px-3 py-2" required>
    </div>
    <div>
      <label class="block text-sm mb-1">Customer Email</label>
      <input type="email" name="customer_email" value="{{ old('customer_email',$order->customer_email) }}" class="w-full border rounded-lg px-3 py-2" required>
    </div>
  </div>

  <div class="grid md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm mb-1">Customer Phone</label>
      <input type="text" name="customer_phone" value="{{ old('customer_phone',$order->customer_phone) }}" class="w-full border rounded-lg px-3 py-2" required>
    </div>
    <div>
      <label class="block text-sm mb-1">Domain</label>
      <input type="text" name="domain" value="{{ old('domain',$order->domain) }}" class="w-full border rounded-lg px-3 py-2">
    </div>
  </div>

  <div class="grid md:grid-cols-3 gap-4">
    <div>
      <label class="block text-sm mb-1">Price (TZS)</label>
      <input type="number" name="price_tzs" value="{{ old('price_tzs',$order->price_tzs) }}" class="w-full border rounded-lg px-3 py-2" min="0" required>
    </div>
    <div>
      <label class="block text-sm mb-1">Currency</label>
      <input type="text" name="currency" value="{{ old('currency',$order->currency) }}" class="w-full border rounded-lg px-3 py-2">
    </div>
    <div>
      <label class="block text-sm mb-1">Status</label>
      <select name="status" class="w-full border rounded-lg px-3 py-2">
        @foreach(['pending','paid','failed','active','cancelled','complete','succeeded'] as $st)
          <option value="{{ $st }}" @selected($order->status===$st)>{{ ucfirst($st) }}</option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="grid md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm mb-1">Payment Reference</label>
      <input type="text" name="payment_ref" value="{{ old('payment_ref',$order->payment_ref) }}" class="w-full border rounded-lg px-3 py-2">
    </div>
    <div>
      <label class="block text-sm mb-1">Gateway Provider</label>
      <select name="gateway_provider" class="w-full border rounded-lg px-3 py-2">
        <option value="">-- Select Provider --</option>
        @foreach(['M-PESA','TIGO-PESA','AIRTEL-MONEY'] as $gw)
          <option value="{{ $gw }}" @selected($order->gateway_provider===$gw)>{{ $gw }}</option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="flex items-center gap-3">
    <button class="px-5 py-2.5 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Save Changes</button>
    <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 rounded-lg border">Back</a>
  </div>
</form>
@endsection
