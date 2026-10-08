@extends('layouts.app')

@section('title', $page->meta_title ?: $page->title . ' | NovaMart')
@section('meta_description', $page->meta_description ?: 'Read ' . $page->title . ' on NovaMart marketplace.')

@php
    $allPages = \App\Models\Page::where('is_published', true)->orderBy('sort_order', 'asc')->get(['title', 'slug']);
    $pageIcons = [
        'about-us' => 'info',
        'contact-us' => 'headphones',
        'shipping-policy' => 'truck',
        'refund-policy' => 'rotate-ccw',
        'terms-conditions' => 'file-text',
        'privacy-policy' => 'shield-check',
        'faq' => 'circle-help',
    ];
    $iconFor = fn ($slug) => $pageIcons[$slug] ?? 'file-text';
    $readMinutes = max(1, (int) ceil(str_word_count(strip_tags($page->content)) / 200));
    $updatedAt = $page->updated_at ?? now();
    $field = 'nm-input h-11 px-3.5 text-sm';
@endphp

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-5 sm:py-8">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 mb-4 sm:mb-6" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-brand-600 dark:hover:text-brand-400 transition-colors">Home</a>
        <i data-lucide="chevron-right" class="w-3 h-3 text-slate-300 dark:text-slate-600"></i>
        <span>Help & Policies</span>
        <i data-lucide="chevron-right" class="w-3 h-3 text-slate-300 dark:text-slate-600"></i>
        <span class="text-slate-900 dark:text-white font-medium truncate">{{ $page->title }}</span>
    </nav>

    @if(!$page->is_published)
    <div class="mb-6 p-4 rounded-2xl bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 text-amber-700 dark:text-amber-400 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <i data-lucide="eye-off" class="w-4 h-4 shrink-0"></i>
            <span><strong>Draft:</strong> this page isn't public yet. You can see it because you're signed in as an admin.</span>
        </div>
        @auth
            @if(Auth::user()->role === 'admin')
            <a href="{{ route('admin.pages.edit', $page->id) }}" class="shrink-0 px-3 py-1.5 rounded-lg bg-amber-500 text-black font-bold text-xs hover:bg-amber-400 text-center">Edit in Admin</a>
            @endif
        @endauth
    </div>
    @endif

    <!-- Mobile: page switcher chips -->
    <div class="lg:hidden -mx-4 px-4 mb-4 flex gap-2 overflow-x-auto no-scrollbar">
        @foreach($allPages as $p)
        <a href="{{ route('pages.show', $p->slug) }}"
           class="shrink-0 inline-flex items-center gap-1.5 h-9 px-3.5 rounded-full border text-xs font-medium transition-colors {{ $page->slug === $p->slug ? 'bg-brand-600 border-brand-600 text-white' : 'bg-white dark:bg-white/5 border-slate-200 dark:border-white/10 text-slate-700 dark:text-slate-300' }}">
            <i data-lucide="{{ $iconFor($p->slug) }}" class="w-3.5 h-3.5"></i>
            {{ $p->title }}
        </a>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[260px_minmax(0,1fr)] gap-6 lg:gap-8 items-start">

        <!-- Sidebar (desktop) -->
        <aside class="hidden lg:block sticky top-24 space-y-4">
            <nav class="bg-white dark:bg-[#0f1723] rounded-2xl p-2 border border-slate-200 dark:border-white/10 shadow-soft">
                <div class="px-3 pt-2 pb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">Help & Policies</div>
                @foreach($allPages as $p)
                <a href="{{ route('pages.show', $p->slug) }}"
                   @if($page->slug === $p->slug) aria-current="page" @endif
                   class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-[13px] transition-colors {{ $page->slug === $p->slug ? 'bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-300 font-semibold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 hover:text-slate-900 dark:hover:text-white' }}">
                    <i data-lucide="{{ $iconFor($p->slug) }}" class="w-4 h-4 shrink-0 {{ $page->slug === $p->slug ? 'text-brand-600 dark:text-brand-400' : 'text-slate-400' }}"></i>
                    <span class="truncate">{{ $p->title }}</span>
                </a>
                @endforeach
            </nav>

            @include('pages.partials.support-card')
        </aside>

        <!-- Main -->
        <div class="min-w-0 space-y-6">
            <article class="bg-white dark:bg-[#0f1723] rounded-2xl sm:rounded-3xl border border-slate-200 dark:border-white/10 shadow-soft overflow-hidden">
                <!-- Page header -->
                <header class="px-5 sm:px-10 pt-6 sm:pt-10 pb-6 sm:pb-8 bg-gradient-to-b from-brand-50/70 to-transparent dark:from-brand-500/[0.06] border-b border-slate-100 dark:border-white/10">
                    <div class="w-11 h-11 rounded-2xl bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 text-brand-600 dark:text-brand-400 flex items-center justify-center shadow-xs mb-4">
                        <i data-lucide="{{ $iconFor($page->slug) }}" class="w-5 h-5"></i>
                    </div>
                    <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">{{ $page->title }}</h1>
                    @if($page->meta_description)
                    <p class="mt-2 text-sm sm:text-base text-slate-600 dark:text-slate-400 max-w-2xl leading-relaxed">{{ $page->meta_description }}</p>
                    @endif
                    <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs text-slate-500 dark:text-slate-400">
                        <span class="inline-flex items-center gap-1.5"><i data-lucide="calendar" class="w-3.5 h-3.5"></i>Updated {{ $updatedAt->format('d M Y') }}</span>
                        <span class="inline-flex items-center gap-1.5"><i data-lucide="clock" class="w-3.5 h-3.5"></i>{{ $readMinutes }} min read</span>
                    </div>
                </header>

                <!-- Body (admin-authored HTML) -->
                <div class="px-5 sm:px-10 py-6 sm:py-10">
                    <div class="prose prose-slate dark:prose-invert max-w-none
                                prose-headings:font-bold prose-headings:tracking-tight prose-headings:text-slate-900 dark:prose-headings:text-white
                                prose-h2:text-xl sm:prose-h2:text-2xl prose-h2:mt-10 prose-h2:mb-3 [&>:first-child]:mt-0
                                prose-h3:text-lg prose-h3:mt-8 prose-h3:mb-2
                                prose-h4:text-base prose-h4:mt-0
                                prose-p:leading-relaxed prose-p:text-slate-600 dark:prose-p:text-slate-300
                                prose-li:text-slate-600 dark:prose-li:text-slate-300 prose-li:my-1 prose-li:marker:text-brand-500
                                prose-a:text-brand-600 dark:prose-a:text-brand-400 prose-a:font-medium prose-a:no-underline hover:prose-a:underline
                                prose-strong:text-slate-900 dark:prose-strong:text-white
                                prose-lead:text-lg prose-lead:text-slate-700 dark:prose-lead:text-slate-200
                                [&_.grid>div_p]:my-0 [&_.grid>div_h4]:mb-1.5">
                        {!! $page->content !!}
                    </div>
                </div>
            </article>

            @if($page->slug === 'contact-us')
            <!-- Contact form -->
            <section class="bg-white dark:bg-[#0f1723] rounded-2xl sm:rounded-3xl p-5 sm:p-10 border border-slate-200 dark:border-white/10 shadow-soft">
                <div class="flex items-start gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 flex items-center justify-center shrink-0">
                        <i data-lucide="send" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Send us a message</h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400">We usually reply within 2 business hours.</p>
                    </div>
                </div>

                @if($errors->any())
                <div class="mb-5 p-3 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-700 dark:text-rose-300 text-xs">
                    {{ $errors->first() }}
                </div>
                @endif

                <form action="{{ route('pages.contact.submit') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="block">
                            <span class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Full name <span class="text-rose-500">*</span></span>
                            <input type="text" name="name" required autocomplete="name" placeholder="Rahul Sharma" value="{{ old('name', Auth::user()?->name) }}" class="{{ $field }}">
                        </label>
                        <label class="block">
                            <span class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Email <span class="text-rose-500">*</span></span>
                            <input type="email" name="email" required autocomplete="email" placeholder="name@example.com" value="{{ old('email', Auth::user()?->email) }}" class="{{ $field }}">
                        </label>
                        <label class="block">
                            <span class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Phone <span class="text-slate-400 font-normal">(optional)</span></span>
                            <input type="tel" name="phone" autocomplete="tel" inputmode="tel" placeholder="98765 43210" value="{{ old('phone', Auth::user()?->phone) }}" class="{{ $field }}">
                        </label>
                        <label class="block">
                            <span class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Topic <span class="text-rose-500">*</span></span>
                            <span class="relative block">
                                <select name="subject" required class="{{ $field }} appearance-none pr-10 cursor-pointer">
                                    @foreach(['Order Tracking' => 'Order status & tracking', 'Product Return' => 'Return / replacement', 'Payment / Invoice' => 'Payment / invoice', 'Warranty / Service' => 'Warranty & technical support', 'General Feedback' => 'General feedback / other'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('subject') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                            </span>
                        </label>
                    </div>
                    <label class="block">
                        <span class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Message <span class="text-rose-500">*</span></span>
                        <textarea name="message" rows="5" required placeholder="Tell us how we can help. Include your order ID if you have one." class="nm-input h-auto px-3.5 py-3 text-sm resize-y">{{ old('message') }}</textarea>
                    </label>
                    <div class="flex flex-col-reverse sm:flex-row sm:items-center justify-between gap-3 pt-1">
                        <p class="text-xs text-slate-400">By sending, you agree to our <a href="{{ route('pages.show', 'privacy-policy') }}" class="text-brand-600 dark:text-brand-400 hover:underline">privacy policy</a>.</p>
                        <button type="submit" class="h-11 px-6 rounded-xl bg-brand-600 hover:bg-brand-500 active:scale-[0.98] text-white font-semibold text-sm shadow-glow transition inline-flex items-center justify-center gap-2">
                            <i data-lucide="send" class="w-4 h-4"></i>
                            Send message
                        </button>
                    </div>
                </form>
            </section>
            @endif

            <!-- Support card (mobile) -->
            <div class="lg:hidden">
                @include('pages.partials.support-card')
            </div>
        </div>
    </div>
</div>
@endsection
