@extends('admin.layout')
@section('title','Create Order')
@section('content')
<h1 class="text-2xl font-semibold mb-6">Create Order</h1>

<form action="{{ route('admin.orders.store') }}" method="post" class="bg-white border rounded-xl p-6 space-y-4 max-w-2xl">
  @csrf
  <div>
    <label class="block text-sm mb-1">User</label>
    <select name="user_id" class="w-full border rounded-lg px-3 py-2" required>
      <option value="">-- Choose User --</option>
      @foreach($users as $u)
        <option value="{{ $u->id }}">{{ $u->name }}</option>
      @endforeach
    </select>
  </div>

  <div>
    <label class="block text-sm mb-1">Plan</label>
    <select name="plan_id" class="w-full border rounded-lg px-3 py-2" required>
      <option value="">-- Choose Plan --</option>
      @foreach($plans as $p)
        <option value="{{ $p->id }}">{{ $p->name }}</option>
      @endforeach
    </select>
  </div>

  <div class="grid md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm mb-1">Price (TZS)</label>
      <input type="number" name="price_tzs" class="w-full border rounded-lg px-3 py-2" min="0" required>
    </div>
    <div>
      <label class="block text-sm mb-1">Status</label>
      <select name="status" class="w-full border rounded-lg px-3 py-2" required>
        @foreach(['pending','paid','failed','active','cancelled'] as $st)
          <option value="{{ $st }}">{{ ucfirst($st) }}</option>
        @endforeach
      </select>
    </div>
  </div>

  <div>
    <label class="block text-sm mb-1">Notes</label>
    <textarea name="notes" rows="4" class="w-full border rounded-lg px-3 py-2" placeholder="Optional admin notes..."></textarea>
  </div>

  <div class="flex items-center gap-3">
    <button class="px-5 py-2.5 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Create</button>
    <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 rounded-lg border">Cancel</a>
  </div>
</form>
@endsection
