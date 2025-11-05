@extends('admin.layout')
@section('title','Edit Plan: '.$plan->name)
@section('content')
<h1 class="text-2xl font-semibold mb-6">Edit Plan</h1>

<form action="{{ route('admin.plans.update',$plan) }}" method="post" class="bg-white border rounded-xl p-6 space-y-4 max-w-2xl">
  @csrf @method('PUT')
  <div>
    <label class="block text-sm mb-1">Name</label>
    <input type="text" name="name" value="{{ old('name',$plan->name) }}" class="w-full border rounded-lg px-3 py-2" required>
  </div>
  <div>
    <label class="block text-sm mb-1">Description</label>
    <textarea name="description" rows="4" class="w-full border rounded-lg px-3 py-2">{{ old('description',$plan->description) }}</textarea>
  </div>
  <div class="grid md:grid-cols-3 gap-4">
    <div>
      <label class="block text-sm mb-1">Price (TZS)</label>
      <input type="number" name="price_tzs" min="0" value="{{ old('price_tzs',$plan->price_tzs) }}" class="w-full border rounded-lg px-3 py-2" required>
    </div>
    <div>
      <label class="block text-sm mb-1">Period (months)</label>
      <input type="number" name="period_months" min="1" value="{{ old('period_months',$plan->period_months) }}" class="w-full border rounded-lg px-3 py-2" required>
    </div>
    <div>
      <label class="block text-sm mb-1">Active?</label>
      <select name="is_active" class="w-full border rounded-lg px-3 py-2">
        <option value="1" @selected($plan->is_active)>Yes</option>
        <option value="0" @selected(!$plan->is_active)>No</option>
      </select>
    </div>
  </div>

  <div class="flex items-center gap-3">
    <button class="px-5 py-2.5 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Save</button>
    <a href="{{ route('admin.plans.index') }}" class="px-4 py-2 rounded-lg border">Back</a>
  </div>
</form>
@endsection
