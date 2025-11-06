@extends('layouts.admin')
@section('title','Create Service')
@section('content')
<div class="p-6">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-3xl font-bold">Create New Service</h1>
        <a href="{{ route('admin.services.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg">
            <i class="fas fa-arrow-left mr-2"></i>Back to Services
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

    <div class="bg-white rounded-lg shadow-sm p-6 max-w-2xl">
        <form action="{{ route('admin.services.store') }}" method="POST">
            @csrf

            <div class="space-y-4">
                <!-- Order -->
                <div>
                    <label for="order_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Order <span class="text-red-500">*</span>
                    </label>
                    <select id="order_id" 
                            name="order_id" 
                            required 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Select Order --</option>
                        @foreach($orders as $order)
                            <option value="{{ $order->id }}" {{ old('order_id') == $order->id ? 'selected' : '' }}>
                                Order #{{ $order->id }} - {{ $order->user->name ?? 'No User' }} ({{ ucfirst($order->status) }})
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-sm text-gray-500">Only showing paid/active orders</p>
                </div>

                <!-- Plan -->
                <div>
                    <label for="plan_slug" class="block text-sm font-medium text-gray-700 mb-2">
                        Plan <span class="text-red-500">*</span>
                    </label>
                    <select id="plan_slug" 
                            name="plan_slug" 
                            required 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Select Plan --</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->slug }}" {{ old('plan_slug') == $plan->slug ? 'selected' : '' }}>
                                {{ $plan->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Domain -->
                <div>
                    <label for="domain" class="block text-sm font-medium text-gray-700 mb-2">
                        Domain <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="domain" 
                           name="domain" 
                           value="{{ old('domain') }}" 
                           required 
                           placeholder="example.com"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-sm text-gray-500">Domain name for this hosting service</p>
                </div>

                <!-- Webuzo Username -->
                <div>
                    <label for="webuzo_username" class="block text-sm font-medium text-gray-700 mb-2">
                        Webuzo Username
                    </label>
                    <input type="text" 
                           id="webuzo_username" 
                           name="webuzo_username" 
                           value="{{ old('webuzo_username') }}" 
                           maxlength="60"
                           placeholder="user123"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-sm text-gray-500">Leave blank to auto-generate during provisioning</p>
                </div>

                <!-- Enduser URL -->
                <div>
                    <label for="enduser_url" class="block text-sm font-medium text-gray-700 mb-2">
                        Control Panel URL
                    </label>
                    <input type="url" 
                           id="enduser_url" 
                           name="enduser_url" 
                           value="{{ old('enduser_url') }}" 
                           placeholder="https://panel.example.com:2003"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-sm text-gray-500">Webuzo control panel URL</p>
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
                        @foreach(['requested','provisioning','active','failed','suspended','cancelled'] as $st)
                            <option value="{{ $st }}" {{ old('status', 'requested') == $st ? 'selected' : '' }}>
                                {{ ucfirst($st) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center gap-3 pt-4">
                    <button type="submit" 
                            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
                        <i class="fas fa-plus-circle mr-2"></i>Create Service
                    </button>
                    <a href="{{ route('admin.services.index') }}" 
                       class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg max-w-2xl">
        <h3 class="font-semibold text-blue-900 mb-2">
            <i class="fas fa-info-circle mr-2"></i>Note
        </h3>
        <p class="text-sm text-blue-800">
            After creating the service, you can trigger provisioning from the service details page, 
            or the system will automatically provision it if the order is paid.
        </p>
    </div>
</div>
@endsection

