@extends('layouts.admin')
@section('title','Create Plan')
@section('content')
<h1 class="text-2xl font-semibold mb-6">Create Plan</h1>

<form action="{{ route('admin.plans.store') }}" method="post" class="bg-white border rounded-xl p-6 space-y-4 max-w-2xl">
  @csrf
  <div>
    <label class="block text-sm mb-1">Name</label>
    <input type="text" name="name" class="w-full border rounded-lg px-3 py-2" required>
  </div>
  <div>
    <label class="block text-sm mb-1">Description</label>
    <textarea name="description" rows="4" class="w-full border rounded-lg px-3 py-2" placeholder="Optional"></textarea>
  </div>
  <div class="grid md:grid-cols-3 gap-4">
    <div>
      <label class="block text-sm mb-1">Price (TZS)</label>
      <input type="number" name="price_tzs" min="0" class="w-full border rounded-lg px-3 py-2" required>
    </div>
    <div>
      <label class="block text-sm mb-1">Period (months)</label>
      <input type="number" name="period_months" min="1" class="w-full border rounded-lg px-3 py-2" required>
    </div>
    <div>
      <label class="block text-sm mb-1">Active?</label>
      <select name="is_active" class="w-full border rounded-lg px-3 py-2">
        <option value="1">Yes</option>
        <option value="0">No</option>
      </select>
    </div>
  </div>

  <div class="flex items-center gap-3">
    <button class="px-5 py-2.5 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Create</button>
    <a href="{{ route('admin.plans.index') }}" class="px-4 py-2 rounded-lg border">Cancel</a>
  </div>
</form>
@endsection
