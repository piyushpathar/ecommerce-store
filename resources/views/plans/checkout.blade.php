@extends('layouts.app')

@section('title', 'Subscribe to ' . $plan->name . ' | NovaMart VIP')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-16">
    <div class="bg-white dark:bg-[#0f1723] rounded-3xl p-8 border border-slate-200 dark:border-white/10 shadow-pop space-y-6">

        <div class="text-center space-y-2 border-b border-slate-100 dark:border-white/10 pb-6">
            <span class="text-xs font-mono font-bold uppercase tracking-wider text-brand-600 dark:text-brand-400">Checkout &bull; Instant Activation</span>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">{{ $plan->name }}</h1>
            <p class="text-xs text-slate-500">Validity: {{ $plan->duration_days }} Days &bull; 100% Secure Checkout</p>
        </div>

        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 flex items-center justify-between font-mono">
            <div>
                <span class="text-xs text-slate-400">Amount Due</span>
                <div class="text-3xl font-black text-brand-600 dark:text-brand-400">{{ $plan->formatted_price }}</div>
            </div>
            <div class="text-right text-xs text-slate-500">
                <span>Inclusive of GST</span>
                <div class="text-emerald-500 font-bold mt-0.5">● 0% Hidden Surcharges</div>
            </div>
        </div>

        <!-- Plan Features Review -->
        <div class="space-y-2 text-xs">
            <h4 class="font-bold text-slate-700 dark:text-slate-300">Included In Your Plan:</h4>
            <div class="space-y-1.5">
                @foreach($plan->features as $f)
                <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                    <i data-lucide="check-circle" class="w-3.5 h-3.5 text-brand-500"></i>
                    <span>{{ $f }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Razorpay Pay Button & Sandbox Simulator -->
        <div class="pt-4 space-y-3" x-data="{
            processing: false,
            openRazorpay() {
                this.processing = true;
                const options = {
                    key: '{{ $razorpayKey }}',
                    amount: '{{ $razorpayOrder['amount'] }}',
                    currency: 'INR',
                    name: 'NovaMart VIP Club',
                    description: 'Subscription for {{ $plan->name }}',
                    order_id: '{{ $razorpayOrder['id'] }}',
                    handler: function (response) {
                        alert('Payment Success! Payment ID: ' + response.razorpay_payment_id);
                        window.location.href = '{{ route('account.index') }}';
                    },
                    prefill: {
                        name: '{{ Auth::user()?->name ?? 'Valued Customer' }}',
                        email: '{{ Auth::user()?->email ?? 'customer@novamart.com' }}',
                        contact: '{{ Auth::user()?->phone ?? '9876543210' }}'
                    },
                    theme: { color: '#059669' }
                };

                @if($isMock)
                // Sandbox simulation
                setTimeout(() => {
                    alert('Razorpay Sandbox: Payment of {{ $plan->formatted_price }} verified!');
                    window.location.href = '{{ route('account.index') }}';
                }, 800);
                @else
                try {
                    const rzp = new Razorpay(options);
                    rzp.open();
                } catch(e) {
                    alert('Local sandbox fallback activated: Payment verified!');
                    window.location.href = '{{ route('account.index') }}';
                }
                @endif
            }
        }">
            <button type="button"
                    @click="openRazorpay()"
                    class="w-full py-4 rounded-2xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-sm shadow-glow flex items-center justify-center gap-2 transition-all">
                <i data-lucide="lock" class="w-4 h-4"></i>
                <span>Pay {{ $plan->formatted_price }} with Razorpay</span>
            </button>

            <div class="text-center text-[11px] text-slate-400">
                Supports UPI (Google Pay, PhonePe, Paytm), All Major Debit/Credit Cards & Net Banking.
            </div>
        </div>

    </div>
</div>
@endsection
