@extends('layouts.app')
@section('content')
<div class="max-w-2xl mx-auto">
  <div class="card">
    <h2 class="heading mb-4">Order Summary #{{ $order->id }}</h2>
    
    <!-- Status Display -->
    <div class="mb-4 p-4 rounded-lg" id="status-container"
         @if($order->status === 'pending')
           style="background-color: #FEF3C7; border: 2px solid #F59E0B;"
         @elseif(in_array($order->status, ['paid','active','complete','succeeded']))
           style="background-color: #D1FAE5; border: 2px solid #10B981;"
         @elseif(in_array($order->status, ['failed','cancelled']))
           style="background-color: #FEE2E2; border: 2px solid #EF4444;"
         @else
           style="background-color: #F3F4F6; border: 2px solid #9CA3AF;"
         @endif
    >
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm text-gray-600">Payment Status</p>
          <p class="text-xl font-bold" id="order-status">
            {{ ucfirst($order->status) }}
          </p>
        </div>
        <div id="status-icon">
          @if($order->status === 'pending')
            <svg class="animate-spin h-8 w-8 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          @elseif(in_array($order->status, ['paid','active','complete','succeeded']))
            <svg class="h-8 w-8 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          @elseif(in_array($order->status, ['failed','cancelled']))
            <svg class="h-8 w-8 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          @endif
        </div>
      </div>
      <p class="text-sm mt-2" id="status-message">
        @if($order->status === 'pending')
          <span class="text-yellow-800">⏳ Tunasubiri uthibitisho wa malipo...</span>
        @elseif(in_array($order->status, ['paid','active','complete','succeeded']))
          <span class="text-green-800">✅ Malipo yamefanikiwa!</span>
        @elseif(in_array($order->status, ['failed','cancelled']))
          <span class="text-red-800">❌ Malipo yameshindikana</span>
        @endif
      </p>
    </div>

    <!-- Order Details -->
    <div class="space-y-3 border-t pt-4">
      <div class="flex justify-between">
        <span class="text-gray-600">Plan:</span>
        <span class="font-semibold">{{ $order->plan->name }}</span>
      </div>
      @if($order->duration_months)
      <div class="flex justify-between">
        <span class="text-gray-600">Duration:</span>
        <span class="font-semibold">{{ $order->getDurationFormatted() }}</span>
      </div>
      @endif
      <div class="flex justify-between">
        <span class="text-gray-600">Amount:</span>
        <span class="font-semibold">TZS {{ number_format($order->price_tzs) }}</span>
      </div>
      @if($order->discount_percentage > 0)
      <div class="flex justify-between text-emerald-600">
        <span class="font-medium">Discount Applied:</span>
        <span class="font-semibold">{{ $order->discount_percentage }}% (Save TZS {{ number_format($order->getSavingsAmount()) }})</span>
      </div>
      @endif
      @if($order->domain)
      <div class="flex justify-between">
        <span class="text-gray-600">Domain:</span>
        <span class="font-semibold">{{ $order->domain }}</span>
      </div>
      @endif
      @if($order->payment_ref)
      <div class="flex justify-between">
        <span class="text-gray-600">Payment Reference:</span>
        <span class="font-mono text-sm">{{ $order->payment_ref }}</span>
      </div>
      @endif
    </div>

    <!-- Service Info -->
    @if($order->service)
      <div class="mt-6 p-4 bg-blue-50 rounded-lg border-2 border-blue-200">
        <h3 class="font-semibold mb-2 text-blue-900">🖥️ Huduma Yako</h3>
        <p class="text-sm text-blue-800 mb-2">Status: <span class="font-bold">{{ ucfirst($order->service->status) }}</span></p>
        @if($order->service->status === 'active' && $order->service->enduser_url)
          <a class="btn-primary mt-3 inline-block" target="_blank" href="{{ $order->service->enduser_url }}">
            🚀 Fungua Control Panel
          </a>
        @elseif($order->service->status === 'provisioning')
          <p class="text-sm text-blue-700">⚙️ Tunatengeneza akaunti yako, subiri kidogo...</p>
        @endif
      </div>
    @endif

    <!-- Actions -->
    <div class="mt-6 flex gap-3">
      @if($order->status === 'pending')
        <button onclick="checkStatusNow()" id="check-btn" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
          🔄 Angalia Status Ya Malipo
        </button>
      @endif
      @auth
        <a href="{{ route('dashboard') }}" class="btn-primary">
          📊 Dashboard Yangu
        </a>
      @endauth
      <a href="{{ route('home') }}" class="btn-gold">
        🏠 Rudi Home
      </a>
    </div>
  </div>
</div>

<!-- Auto-Polling JavaScript -->
@if($order->status === 'pending')
<script>
  (function() {
    let pollInterval;
    let pollCount = 0;
    const maxPolls = 60; // Poll for 5 minutes (60 × 5s = 5min)
    const pollDelay = 5000; // Check every 5 seconds
    
    function updateUI(data) {
      const statusElement = document.getElementById('order-status');
      const messageElement = document.getElementById('status-message');
      const containerElement = document.getElementById('status-container');
      const iconElement = document.getElementById('status-icon');
      
      if (data.is_paid) {
        // Payment successful!
        statusElement.textContent = 'PAID ✅';
        messageElement.innerHTML = '<span class="text-green-800">✅ Malipo yamefanikiwa! Tunapanga huduma yako...</span>';
        containerElement.style.backgroundColor = '#D1FAE5';
        containerElement.style.borderColor = '#10B981';
        iconElement.innerHTML = '<svg class="h-8 w-8 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
        
        if (pollInterval) clearInterval(pollInterval);
        
        // Reload page after 2 seconds to show service
        setTimeout(() => {
          window.location.reload();
        }, 2000);
        
      } else if (data.is_terminal && !data.is_paid) {
        // Payment failed
        statusElement.textContent = 'FAILED ❌';
        messageElement.innerHTML = '<span class="text-red-800">❌ Malipo yameshindikana. Jaribu tena.</span>';
        containerElement.style.backgroundColor = '#FEE2E2';
        containerElement.style.borderColor = '#EF4444';
        iconElement.innerHTML = '<svg class="h-8 w-8 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
        
        if (pollInterval) clearInterval(pollInterval);
      } else {
        // Still pending
        statusElement.textContent = 'PENDING ⏳';
        messageElement.innerHTML = '<span class="text-yellow-800">⏳ Tunasubiri uthibitisho wa malipo...</span>';
      }
    }
    
    function checkPaymentStatus() {
      pollCount++;
      
      // Disable check button while checking
      const btn = document.getElementById('check-btn');
      if (btn) {
        btn.disabled = true;
        btn.textContent = '⏳ Inaangalia...';
      }
      
      fetch('{{ route("pay.status", $order->id) }}', {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(data => {
        console.log('Payment status check #' + pollCount + ':', data);
        updateUI(data);
        
        // Re-enable button
        if (btn) {
          btn.disabled = false;
          btn.textContent = '🔄 Angalia Status Ya Malipo';
        }
        
        // Stop polling after max attempts
        if (pollCount >= maxPolls && !data.is_terminal) {
          if (pollInterval) clearInterval(pollInterval);
          document.getElementById('status-message').innerHTML = 
            '<span class="text-yellow-800">⏰ Tumechelewa kupata response. Bofya button ya "Angalia Status" ukagundua.</span>';
        }
      })
      .catch(error => {
        console.error('Error checking status:', error);
        
        // Re-enable button
        if (btn) {
          btn.disabled = false;
          btn.textContent = '🔄 Angalia Status Ya Malipo';
        }
        
        // Don't stop polling on error, just log it
        if (pollCount >= maxPolls && pollInterval) {
          clearInterval(pollInterval);
        }
      });
    }
    
    // Manual check function (for button)
    window.checkStatusNow = function() {
      console.log('Manual status check triggered');
      checkPaymentStatus();
    };
    
    // Start polling immediately, then every 5 seconds
    checkPaymentStatus();
    pollInterval = setInterval(checkPaymentStatus, pollDelay);
    
    // Cleanup on page unload
    window.addEventListener('beforeunload', () => {
      if (pollInterval) clearInterval(pollInterval);
    });
  })();
</script>
@endif
@endsection
