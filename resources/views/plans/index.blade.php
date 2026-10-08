@extends('layouts.app')

@section('title', 'NovaMart VIP & Prime Membership Plans | NovaMart')
@section('meta_description', 'Unlock free express delivery, exclusive member discounts, priority customer support, and early access to sales with NovaMart VIP.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-500/10 text-brand-600 dark:text-brand-400 text-xs font-bold font-mono">
            NOVAMART &bull; VIP PRIVILEGE CLUB
        </div>
        <h1 class="text-3xl sm:text-5xl font-black text-slate-900 dark:text-white tracking-tight">
            Elevate Your Shopping Experience.<br>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-500 to-teal-400">Exclusive VIP Benefits & Free Express Delivery.</span>
        </h1>
        <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 leading-relaxed">
            Get unlimited zero-fee fast shipping, 5% cashback on every order, early access to lightning sales, and 24/7 dedicated concierge assistance.
        </p>
    </div>

    <!-- Plans Matrix -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-20">
        @foreach($plans as $plan)
        <div class="relative bg-white dark:bg-[#0f1723] rounded-3xl p-7 border {{ $plan->is_popular ? 'border-brand-500 ring-4 ring-brand-500/15 shadow-lift' : 'border-slate-200 dark:border-white/10 shadow-soft' }} flex flex-col justify-between">
            @if($plan->badge)
            <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 px-4 py-1 rounded-full text-[10px] font-bold font-mono uppercase tracking-wider {{ $plan->is_popular ? 'bg-brand-500 text-white shadow-glow' : 'bg-slate-200 dark:bg-white/10 text-slate-800 dark:text-slate-200' }}">
                {{ $plan->badge }}
            </div>
            @endif

            <div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ $plan->name }}</h3>
                <div class="mt-4 flex items-baseline gap-1 font-mono">
                    <span class="text-4xl sm:text-5xl font-black text-brand-600 dark:text-brand-400">{{ $plan->formatted_price }}</span>
                    <span class="text-xs text-slate-500">/ {{ $plan->interval }}</span>
                </div>
                <p class="text-xs text-slate-500 mt-1 font-mono">Validity: {{ $plan->duration_days }} Day(s)</p>

                <div class="mt-8 pt-6 border-t border-slate-100 dark:border-white/10 space-y-3">
                    @foreach($plan->features as $feature)
                    <div class="flex items-start gap-2.5 text-xs text-slate-700 dark:text-slate-300">
                        <i data-lucide="check" class="w-4 h-4 text-brand-500 shrink-0 mt-0.5"></i>
                        <span>{{ $feature }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-8 pt-4">
                <a href="{{ route('plans.subscribe', $plan->slug) }}" class="w-full py-3.5 rounded-2xl font-bold text-xs text-center block transition-all {{ $plan->is_popular || $plan->is_trial ? 'bg-brand-600 hover:bg-brand-500 text-white shadow-glow' : 'bg-slate-100 dark:bg-white/10 hover:bg-slate-200 dark:hover:bg-white/15 text-slate-900 dark:text-white' }}">
                    {{ $plan->is_trial ? 'Activate Free Trial' : 'Join Membership' }}
                </a>
            </div>
        </div>
        @endforeach
    </div>

    <!-- FAQ Section -->
    <div class="max-w-3xl mx-auto border-t border-slate-200 dark:border-white/10 pt-16">
        <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white text-center mb-8">Frequently Asked Questions</h2>
        <div class="space-y-4">
            <div class="p-5 rounded-2xl bg-white dark:bg-[#0f1723] border border-slate-200 dark:border-white/10">
                <h4 class="text-sm font-bold text-slate-900 dark:text-white">How does VIP Free Shipping work?</h4>
                <p class="text-xs text-slate-500 mt-1">All orders placed with an active VIP membership automatically receive zero delivery fees with priority dispatch within 24 hours.</p>
            </div>
            <div class="p-5 rounded-2xl bg-white dark:bg-[#0f1723] border border-slate-200 dark:border-white/10">
                <h4 class="text-sm font-bold text-slate-900 dark:text-white">Can I cancel or change my plan anytime?</h4>
                <p class="text-xs text-slate-500 mt-1">Yes! There are no lock-in periods or automatic hidden debits. You can easily upgrade or renew anytime from your account dashboard.</p>
            </div>
        </div>
    </div>
</div>
@endsection
