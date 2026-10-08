@php
    $storePhone = \App\Models\Setting::get('store_phone');
    $storeEmail = \App\Models\Setting::get('store_email');
@endphp

<div class="rounded-2xl p-5 border border-brand-200 dark:border-brand-500/20 bg-gradient-to-br from-brand-50 to-white dark:from-brand-500/10 dark:to-transparent">
    <div class="flex items-center gap-3 mb-3">
        <div class="w-9 h-9 rounded-xl bg-brand-600 text-white flex items-center justify-center shrink-0 shadow-glow">
            <i data-lucide="headphones" class="w-4 h-4"></i>
        </div>
        <div class="leading-tight">
            <h4 class="text-sm font-bold text-slate-900 dark:text-white">Need help?</h4>
            <p class="text-xs text-slate-500 dark:text-slate-400">9am – 9pm, every day</p>
        </div>
    </div>
    <div class="space-y-1.5">
        @if($storePhone)
        <a href="tel:{{ preg_replace('/[^\d+]/', '', $storePhone) }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 hover:border-brand-400 transition-colors">
            <i data-lucide="phone" class="w-4 h-4 text-brand-600 dark:text-brand-400 shrink-0"></i>
            <span class="text-sm font-semibold text-slate-900 dark:text-white truncate">{{ $storePhone }}</span>
        </a>
        @endif
        @if($storeEmail)
        <a href="mailto:{{ $storeEmail }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 hover:border-brand-400 transition-colors">
            <i data-lucide="mail" class="w-4 h-4 text-brand-600 dark:text-brand-400 shrink-0"></i>
            <span class="text-sm text-slate-700 dark:text-slate-200 truncate">{{ $storeEmail }}</span>
        </a>
        @endif
    </div>
</div>
