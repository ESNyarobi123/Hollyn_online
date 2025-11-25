@extends('layouts.app')
@section('title', 'Order Status #' . $order->id)

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-950 py-12 flex items-center justify-center" 
     x-data="orderStatus({{ $order->id }}, '{{ $order->status }}')">
    
    <div class="max-w-xl w-full mx-auto px-4 sm:px-6">
        
        <!-- Main Card -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-xl border border-slate-200 dark:border-slate-800 overflow-hidden relative">
            
            <!-- Top Decorative Line -->
            <div class="h-2 w-full bg-gradient-to-r from-violet-500 via-fuchsia-500 to-indigo-500"></div>

            <div class="p-8 text-center">
                
                <!-- Status Icons & Animation -->
                <div class="mb-6 flex justify-center">
                    
                    <!-- Pending / Waiting -->
                    <template x-if="status === 'pending'">
                        <div class="relative">
                            <div class="w-24 h-24 rounded-full bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center relative z-10">
                                <svg class="w-12 h-12 text-amber-500 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="absolute inset-0 bg-amber-400/20 rounded-full animate-ping"></div>
                        </div>
                    </template>

                    <!-- Paid / Success -->
                    <template x-if="isPaid">
                        <div class="w-24 h-24 rounded-full bg-green-50 dark:bg-green-900/20 flex items-center justify-center animate-[bounce_1s_ease-in-out_1]">
                            <svg class="w-12 h-12 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </template>

                    <!-- Failed -->
                    <template x-if="isFailed">
                        <div class="w-24 h-24 rounded-full bg-red-50 dark:bg-red-900/20 flex items-center justify-center">
                            <svg class="w-12 h-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    </template>
                </div>

                <!-- Status Text -->
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-2" x-text="statusTitle"></h1>
                <p class="text-slate-600 dark:text-slate-400 mb-8" x-html="statusMessage"></p>

                <!-- Order Info Box -->
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-6 mb-8 text-left border border-slate-100 dark:border-slate-700">
                    <div class="flex justify-between items-center mb-4 pb-4 border-b border-slate-200 dark:border-slate-700">
                        <span class="text-sm text-slate-500 dark:text-slate-400">Order ID</span>
                        <span class="font-mono font-medium text-slate-900 dark:text-white">#{{ $order->id }}</span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-slate-500 dark:text-slate-400">Plan</span>
                        <span class="font-medium text-slate-900 dark:text-white">{{ $order->plan->name }}</span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-slate-500 dark:text-slate-400">Amount</span>
                        <span class="font-bold text-slate-900 dark:text-white">TZS {{ number_format($order->price_tzs) }}</span>
                    </div>
                    @if($order->domain)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500 dark:text-slate-400">Domain</span>
                        <span class="font-medium text-slate-900 dark:text-white">{{ $order->domain }}</span>
                    </div>
                    @endif
                </div>

                <!-- Actions -->
                <div class="space-y-3">
                    <!-- Pending Actions -->
                    <template x-if="status === 'pending'">
                        <div class="space-y-3">
                            <button @click="checkStatus()" :disabled="loading" 
                                class="w-full py-3.5 px-6 rounded-xl bg-violet-600 hover:bg-violet-700 text-white font-semibold shadow-lg shadow-violet-500/30 transition-all flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed">
                                <svg x-show="loading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span x-text="loading ? 'Checking...' : 'I Have Paid'"></span>
                            </button>
                            <p class="text-xs text-slate-500">
                                Check your phone for the payment prompt.
                            </p>
                        </div>
                    </template>

                    <!-- Success Actions -->
                    <template x-if="isPaid">
                        <div>
                            <a href="{{ route('dashboard') }}" class="block w-full py-3.5 px-6 rounded-xl bg-green-600 hover:bg-green-700 text-white font-semibold shadow-lg shadow-green-500/30 transition-all">
                                Go to Dashboard
                            </a>
                        </div>
                    </template>

                    <!-- Failed Actions -->
                    <template x-if="isFailed">
                        <div class="grid grid-cols-2 gap-3">
                            <a href="{{ route('pay.start', $order->id) }}" class="py-3 px-4 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-semibold hover:opacity-90 transition-all">
                                Try Again
                            </a>
                            <a href="{{ route('home') }}" class="py-3 px-4 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-medium hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                                Cancel
                            </a>
                        </div>
                    </template>
                </div>

            </div>
            
            <!-- Progress Bar (for polling) -->
            <div x-show="status === 'pending'" class="absolute bottom-0 left-0 h-1 bg-violet-500 transition-all duration-[5000ms] ease-linear" :style="'width: ' + progress + '%'"></div>
        </div>

        <!-- Help Text -->
        <div class="mt-8 text-center">
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Need help? <a href="#" class="text-violet-600 hover:underline">Contact Support</a>
            </p>
        </div>

    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('orderStatus', (orderId, initialStatus) => ({
        status: initialStatus,
        loading: false,
        pollInterval: null,
        progress: 0,
        pollCount: 0,
        maxPolls: 60, // 5 minutes

        init() {
            if (this.status === 'pending') {
                this.startPolling();
            }
        },

        get isPaid() {
            return ['paid', 'active', 'complete', 'succeeded'].includes(this.status);
        },

        get isFailed() {
            return ['failed', 'cancelled'].includes(this.status);
        },

        get statusTitle() {
            if (this.isPaid) return 'Payment Successful!';
            if (this.isFailed) return 'Payment Failed';
            return 'Waiting for Payment';
        },

        get statusMessage() {
            if (this.isPaid) return 'Thank you! Your transaction has been completed successfully.<br>We are setting up your service now.';
            if (this.isFailed) return 'We could not confirm your payment.<br>Please try again or contact support.';
            return 'Please check your phone and enter your PIN to complete the transaction.<br>This page will update automatically.';
        },

        startPolling() {
            this.progress = 0;
            // Animate progress bar to 100% over 5 seconds
            setTimeout(() => { this.progress = 100; }, 100);

            this.pollInterval = setInterval(() => {
                this.checkStatus();
                
                // Reset progress bar
                this.progress = 0;
                setTimeout(() => { this.progress = 100; }, 100);

                this.pollCount++;
                if (this.pollCount >= this.maxPolls) {
                    this.stopPolling();
                }
            }, 5000);
        },

        stopPolling() {
            if (this.pollInterval) clearInterval(this.pollInterval);
        },

        async checkStatus() {
            this.loading = true;
            try {
                const res = await fetch(`/pay/${orderId}/status`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                
                console.log('Status check:', data);

                if (data.status !== this.status) {
                    this.status = data.status;
                    
                    if (data.is_terminal) {
                        this.stopPolling();
                        if (data.is_paid) {
                            // Optional: Redirect after a delay
                            setTimeout(() => {
                                window.location.reload();
                            }, 3000);
                        }
                    }
                }
            } catch (e) {
                console.error('Polling error:', e);
            } finally {
                this.loading = false;
            }
        }
    }));
});
</script>
@endsection
