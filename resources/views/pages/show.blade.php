@extends('layouts.app')

@section('title', $page->meta_title ?: $page->title . ' | NovaMart')
@section('meta_description', $page->meta_description ?: 'Read ' . $page->title . ' on NovaMart marketplace.')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-1.5 text-xs text-slate-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-brand-500">Home</a>
        <span>/</span>
        <span class="text-slate-400">Pages</span>
        <span>/</span>
        <span class="text-slate-900 dark:text-white font-semibold">{{ $page->title }}</span>
    </nav>

    @if(!$page->is_published)
    <div class="mb-6 p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-500 text-xs flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i data-lucide="eye-off" class="w-4 h-4"></i>
            <span><strong>Draft Mode:</strong> This page is not published to the public. You are seeing it because you are signed in as an administrator.</span>
        </div>
        @auth
            @if(Auth::user()->role === 'admin')
            <a href="{{ route('admin.pages.edit', $page->id) }}" class="px-2.5 py-1 rounded-lg bg-amber-500 text-black font-bold text-xs hover:bg-amber-400">
                Edit in Admin
            </a>
            @endif
        @endauth
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Main Content Area -->
        <article class="lg:col-span-8 bg-white dark:bg-[#0f1723] rounded-3xl p-6 sm:p-10 border border-slate-200 dark:border-white/10 shadow-soft">
            <header class="pb-6 mb-8 border-b border-slate-100 dark:border-white/10">
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900 dark:text-white mb-2">
                    {{ $page->title }}
                </h1>
                <div class="flex items-center gap-4 text-xs text-slate-400">
                    <span>Last updated: {{ $page->updated_at ? $page->updated_at->format('M d, Y') : now()->format('M d, Y') }}</span>
                    <span>&bull;</span>
                    <span>NovaMart Verified Legal & Support</span>
                </div>
            </header>

            <!-- Rendered HTML Content -->
            <div class="prose prose-sm sm:prose-base dark:prose-invert max-w-none text-slate-700 dark:text-slate-300 leading-relaxed">
                {!! $page->content !!}
            </div>

            <!-- If Contact Page, Display Interactive Contact Form -->
            @if($page->slug === 'contact-us')
            <div class="mt-10 pt-8 border-t border-slate-100 dark:border-white/10">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Send Us an Instant Inquiry</h3>
                <p class="text-xs text-slate-400 mb-6">Fill in the form below and our customer support team will get back to you within 2 business hours.</p>

                <form action="{{ route('pages.contact.submit') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Your Full Name *</label>
                            <input type="text" name="name" required placeholder="e.g. Rahul Sharma" value="{{ Auth::user()?->name }}" class="w-full px-3 py-2 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500/30">
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Email Address *</label>
                            <input type="email" name="email" required placeholder="name@example.com" value="{{ Auth::user()?->email }}" class="w-full px-3 py-2 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500/30 font-mono">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Phone Number (Optional)</label>
                            <input type="tel" name="phone" placeholder="98765 43210" value="{{ Auth::user()?->phone }}" class="w-full px-3 py-2 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500/30 font-mono">
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Subject / Inquiry Type *</label>
                            <select name="subject" required class="w-full px-3 py-2 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500/30">
                                <option value="Order Tracking">Order Status & Tracking</option>
                                <option value="Product Return">Return / Replacement Request</option>
                                <option value="Payment / Invoice">Payment / Invoice Inquiry</option>
                                <option value="Warranty / Service">Brand Warranty & Technical Support</option>
                                <option value="General Feedback">General Feedback / Other</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Your Message / Query *</label>
                        <textarea name="message" rows="4" required placeholder="Please describe how we can assist you..." class="w-full px-3 py-2 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500/30"></textarea>
                    </div>

                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-brand-500 to-emerald-600 text-white font-bold text-xs shadow-glow hover:scale-[1.02] transition-transform">
                        Submit Message
                    </button>
                </form>
            </div>
            @endif
        </article>

        <!-- Sidebar: Quick Links & Store Help -->
        <aside class="lg:col-span-4 space-y-4">
            
            <!-- Quick Navigation of All Store Pages -->
            <div class="bg-white dark:bg-[#0f1723] rounded-3xl p-5 border border-slate-200 dark:border-white/10 shadow-soft">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-1.5">
                    <i data-lucide="book-open" class="w-3.5 h-3.5 text-brand-500"></i>
                    <span>Information & Policies</span>
                </h3>

                <nav class="space-y-1 text-xs">
                    @foreach(\App\Models\Page::where('is_published', true)->orderBy('sort_order', 'asc')->get() as $p)
                    <a href="{{ route('pages.show', $p->slug) }}" 
                       class="flex items-center justify-between px-3 py-2 rounded-xl transition-colors {{ $page->slug === $p->slug ? 'bg-brand-50 dark:bg-brand-950/60 text-brand-600 dark:text-brand-400 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5' }}">
                        <span>{{ $p->title }}</span>
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 opacity-50"></i>
                    </a>
                    @endforeach
                </nav>
            </div>

            <!-- Direct Support Card -->
            <div class="bg-gradient-to-br from-brand-900/40 via-brand-800/20 to-teal-900/40 rounded-3xl p-6 border border-brand-500/30 shadow-soft text-xs space-y-3">
                <div class="w-8 h-8 rounded-xl bg-brand-500/20 text-brand-400 flex items-center justify-center">
                    <i data-lucide="headphones" class="w-4 h-4"></i>
                </div>
                <h4 class="font-bold text-slate-900 dark:text-white text-sm">Need Direct Assistance?</h4>
                <p class="text-slate-400 text-xs">Our executive support line is active 9am to 9pm daily for instant assistance.</p>
                <div class="pt-2 border-t border-white/10 space-y-1 font-mono text-xs">
                    <div class="flex items-center gap-2 text-brand-400 font-bold">
                        <i data-lucide="phone" class="w-3.5 h-3.5"></i>
                        <span>{{ \App\Models\Setting::get('store_phone', '+91 8000 999 888') }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-slate-300">
                        <i data-lucide="mail" class="w-3.5 h-3.5"></i>
                        <span>{{ \App\Models\Setting::get('store_email', 'support@novamart.in') }}</span>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
