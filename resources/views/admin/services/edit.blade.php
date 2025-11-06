@extends('layouts.admin')
@section('title','Edit Service #'.$service->id)
@section('content')
<div class="p-6">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-3xl font-bold">Edit Service #{{ $service->id }}</h1>
        <a href="{{ route('admin.services.show', $service) }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg">
            <i class="fas fa-arrow-left mr-2"></i>Back to Service
        </a>
    </div>

    @if($errors->any())
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <ul class="list-disc list-inside text-red-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('ok'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
            <i class="fas fa-check-circle mr-2"></i>{{ session('ok') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm p-6 max-w-2xl">
        <form action="{{ route('admin.services.update', $service) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <!-- Service Info -->
                <div class="pb-3 border-b">
                    <p class="text-sm text-gray-600">
                        <strong>Order:</strong> #{{ $service->order_id }} 
                        @if($service->order)
                            | {{ $service->order->user->name ?? 'No User' }}
                        @endif
                    </p>
                    <p class="text-sm text-gray-600">
                        <strong>Plan:</strong> {{ $service->plan->name ?? $service->plan_slug }}
                    </p>
                </div>

                <!-- Domain -->
                <div>
                    <label for="domain" class="block text-sm font-medium text-gray-700 mb-2">
                        Domain
                    </label>
                    <input type="text" 
                           id="domain" 
                           name="domain" 
                           value="{{ old('domain', $service->domain) }}" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Panel Username -->
                <div>
                    <label for="panel_username" class="block text-sm font-medium text-gray-700 mb-2">
                        Panel Username
                    </label>
                    <input type="text" 
                           id="panel_username" 
                           name="panel_username" 
                           value="{{ old('panel_username', $service->webuzo_username ?? $service->panel_username) }}" 
                           maxlength="60"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Enduser URL -->
                <div>
                    <label for="enduser_url" class="block text-sm font-medium text-gray-700 mb-2">
                        Control Panel URL
                    </label>
                    <input type="url" 
                           id="enduser_url" 
                           name="enduser_url" 
                           value="{{ old('enduser_url', $service->enduser_url) }}" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select id="status" 
                            name="status" 
                            required 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @foreach(['provisioning','active','suspended','cancelled'] as $st)
                            <option value="{{ $st }}" {{ old('status', $service->status) === $st ? 'selected' : '' }}>
                                {{ ucfirst($st) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center gap-3 pt-4">
                    <button type="submit" 
                            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
                        <i class="fas fa-save mr-2"></i>Save Changes
                    </button>
                    <a href="{{ route('admin.services.show', $service) }}" 
                       class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

