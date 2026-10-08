<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'NovaMart | India\'s Premier Electronics, Fashion & Smart Living Marketplace')</title>
    <meta name="description" content="@yield('meta_description', 'Shop flagship 5G smartphones, creator laptops, studio audio, smart home appliances, and lifestyle fashion at NovaMart with 1-day express delivery.')">
    <meta name="keywords" content="@yield('meta_keywords', 'novamart, online shopping, electronics, smartphones, laptops, headphones, deals, razorpay, fast delivery')">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Open Graph / SEO -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', 'NovaMart Marketplace')">
    <meta property="og:description" content="@yield('meta_description', 'India\'s Premier Electronics, Fashion & Smart Living Online Marketplace.')">
    <meta property="og:image" content="@yield('og_image', 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&fit=crop&w=1200&q=80')">
    <meta name="twitter:card" content="summary_large_image">

    <!-- Styles & Scripts via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Razorpay Checkout JS SDK -->
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

    @if($favicon = \App\Models\Setting::get('site_favicon_url'))
        <link rel="icon" href="{{ $favicon }}">
        <link rel="apple-touch-icon" href="{{ $favicon }}">
    @endif

    @stack('styles')
</head>
<body class="bg-[#f8fafc] text-[#0b1220] dark:bg-[#070d1a] dark:text-[#f1f5f9] min-h-screen flex flex-col font-sans antialiased transition-colors duration-200"
      x-data="{
          cartDrawerOpen: false,
          cartCount: {{ session('novamart_cart') ? count(session('novamart_cart')) : 0 }}
      }">

    <!-- Announcement Bar -->
    @if(\App\Models\Setting::get('announcement_enabled') == '1' && ($announcement = \App\Models\Setting::get('announcement_bar')))
    <div class="relative bg-gradient-to-r {{ \App\Models\Setting::get('announcement_bg') }} text-white text-xs py-2 px-4 text-center font-medium shadow-xs">
        <div class="max-w-7xl mx-auto flex items-center justify-center gap-2">
            <span>{{ $announcement }}</span>
        </div>
    </div>
    @endif

    <!-- Main Navigation Header -->
    <header class="sticky top-0 z-40 bg-white/95 dark:bg-[#0c1117]/95 backdrop-blur-md border-b border-slate-200 dark:border-white/10 transition-colors">
        <div class="max-w-[1440px] mx-auto px-3 sm:px-6 lg:px-8">
            <!-- Row 1: Logo, Desktop Search, & Right Actions -->
            <div class="flex items-center justify-between h-14 sm:h-16 lg:h-[72px] gap-3 lg:gap-6">

                <!-- Logo & Brand -->
                <div class="flex items-center gap-4 lg:gap-6 shrink-0">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 sm:gap-2.5 group">
                        @if($logoImg = \App\Models\Setting::get('logo_image_url'))
                            <img src="{{ $logoImg }}" alt="{{ \App\Models\Setting::get('store_name') }}" class="h-8 sm:h-10 object-contain">
                        @else
                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-tr {{ \App\Models\Setting::get('site_icon_gradient') }} flex items-center justify-center text-white font-mono font-black text-base sm:text-xl shadow-glow group-hover:scale-105 transition-transform">
                                {{ \App\Models\Setting::get('site_icon_text') }}
                            </div>
                            <div class="flex flex-col">
                                <span class="font-extrabold text-base sm:text-xl tracking-tight text-ink dark:text-white flex items-center">
                                    {{ \App\Models\Setting::get('logo_text_prefix') }}<span class="text-brand-500">{{ \App\Models\Setting::get('logo_text_highlight') }}</span>
                                </span>
                                <span class="text-[10px] text-slate-500 dark:text-slate-400 font-medium tracking-wide -mt-0.5 hidden sm:block">{{ \App\Models\Setting::get('logo_subtitle') }}</span>
                            </div>
                        @endif
                    </a>

                    <!-- Category Megamenu Dropdown (Desktop) -->
                    <div class="relative hidden lg:block" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" class="flex items-center gap-1.5 text-sm font-semibold text-slate-700 dark:text-slate-200 hover:text-brand-600 dark:hover:text-brand-400 py-2">
                            <i data-lucide="grid" class="w-4 h-4 text-brand-600"></i>
                            <span>Categories</span>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 transition-transform" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" x-cloak class="absolute left-0 mt-2 w-80 bg-white dark:bg-[#121824] rounded-2xl shadow-pop border border-slate-200 dark:border-white/10 p-2 z-50">
                            @foreach(\App\Models\Category::where('is_active', true)->orderBy('sort_order', 'asc')->get() as $cat)
                            <a href="{{ route('shop.category', $cat->slug) }}" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-slate-100 dark:hover:bg-white/5 transition-colors group">
                                <div class="w-10 h-10 rounded-xl overflow-hidden border border-slate-200 dark:border-white/10 shrink-0 bg-slate-100 dark:bg-slate-800 shadow-2xs group-hover:scale-105 transition-transform">
                                    <img src="{{ $cat->image }}" alt="{{ $cat->name }}" class="w-full h-full object-cover">
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <span class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100 group-hover:text-brand-600 transition-colors truncate">{{ $cat->name }}</span>
                                    <span class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-1">{{ $cat->description }}</span>
                                </div>
                            </a>
                            @endforeach
                            <div class="border-t border-slate-100 dark:border-white/5 mt-1 pt-1">
                                <a href="{{ route('shop.index') }}" class="flex items-center justify-between px-3 py-2 text-xs font-semibold text-brand-600 dark:text-brand-400 hover:underline">
                                    <span>Browse All Products &rarr;</span>
                                </a>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Search (Desktop) -->
                @php
                    $searchTags = array_filter(array_map('trim', explode(',', (string) \App\Models\Setting::get('search_popular_tags'))));
                    $searchPlaceholder = \App\Models\Setting::get('search_placeholder') ?: 'Search products, brands and more';
                    $searchCategories = \App\Models\Category::where('is_active', true)->orderBy('sort_order', 'asc')->get(['slug', 'name']);
                @endphp
                <x-header-search variant="desktop" class="hidden lg:block flex-1 max-w-3xl mx-auto" :categories="$searchCategories" :placeholder="$searchPlaceholder" :popularSearches="$searchTags" />

                <!-- Right Action Bar: Deals, Theme, Account, Cart -->
                <div class="flex items-center gap-2 sm:gap-3 shrink-0">


                    <!-- Dark / Light Mode Switcher -->
                    <button onclick="toggleDarkMode()" aria-label="Toggle Theme" class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-slate-100 dark:bg-white/5 hover:bg-slate-200 dark:hover:bg-white/10 text-slate-600 dark:text-slate-300 flex items-center justify-center transition-colors">
                        <i data-lucide="moon" class="w-4 h-4 hidden dark:block"></i>
                        <i data-lucide="sun" class="w-4 h-4 block dark:hidden"></i>
                    </button>

                    <!-- User Account Dropdown -->
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" class="flex items-center gap-1.5 p-1 rounded-full hover:bg-slate-100 dark:hover:bg-white/5 transition-colors">
                            @auth
                                <div class="w-8 h-8 rounded-full bg-brand-600 text-white flex items-center justify-center font-bold text-xs ring-2 ring-brand-500/20">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            @else
                                <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-white/10 text-slate-600 dark:text-slate-300 flex items-center justify-center">
                                    <i data-lucide="user" class="w-4 h-4"></i>
                                </div>
                            @endauth
                        </button>

                        <div x-show="open" x-cloak class="absolute right-0 mt-2 w-56 bg-white dark:bg-[#121824] rounded-2xl shadow-pop border border-slate-200 dark:border-white/10 p-2 z-50">
                            @auth
                                <div class="px-3 py-2 border-b border-slate-100 dark:border-white/5 mb-1">
                                    <p class="text-xs font-semibold text-slate-900 dark:text-white truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-[11px] text-slate-500 truncate font-mono">{{ Auth::user()->email }}</p>
                                </div>
                                <a href="{{ route('account.index', ['tab' => 'orders']) }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-white/5">
                                    <i data-lucide="package" class="w-4 h-4 text-slate-400"></i>
                                    <span>My Orders</span>
                                </a>
                                <a href="{{ route('account.index', ['tab' => 'addresses']) }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-white/5">
                                    <i data-lucide="map-pin" class="w-4 h-4 text-slate-400"></i>
                                    <span>Saved Addresses</span>
                                </a>
                                @if(Auth::user()->isAdmin())
                                <div class="border-t border-slate-100 dark:border-white/5 my-1"></div>
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold text-brand-600 dark:text-brand-400 hover:bg-brand-50 dark:hover:bg-brand-950/40">
                                    <i data-lucide="shield-check" class="w-4 h-4"></i>
                                    <span>Control Panel (Admin)</span>
                                </a>
                                @endif
                                <div class="border-t border-slate-100 dark:border-white/5 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/20">
                                        <i data-lucide="log-out" class="w-4 h-4"></i>
                                        <span>Sign Out</span>
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-brand-600 dark:text-brand-400 hover:bg-brand-50 dark:hover:bg-brand-950/40">
                                    <i data-lucide="log-in" class="w-4 h-4"></i>
                                    <span>Sign In</span>
                                </a>
                                <a href="{{ route('register') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-white/5">
                                    <i data-lucide="user-plus" class="w-4 h-4 text-slate-400"></i>
                                    <span>Create Account</span>
                                </a>
                                <div class="border-t border-slate-100 dark:border-white/5 my-1"></div>
                                @if(\App\Models\Setting::get('google_login_enabled') === '1')
                                <a href="{{ route('auth.google') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-white/5">
                                    <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24"><path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.15z"/><path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/><path fill="#FBBC05" d="M5.28 14.27A7.19 7.19 0 0 1 4.9 12c0-.79.14-1.57.38-2.27V6.58H1.25A11.97 11.97 0 0 0 0 12c0 1.92.45 3.74 1.25 5.42l4.03-3.15z"/><path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/></svg>
                                    <span>Gmail 1-Click Login</span>
                                </a>
                                @endif
                            @endauth
                        </div>
                    </div>

                    <!-- Cart Trigger Button -->
                    <a href="{{ route('cart.index') }}" class="relative flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-2 rounded-full bg-brand-600 hover:bg-brand-500 text-white font-semibold text-xs shadow-soft transition-all group">
                        <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                        <span class="hidden sm:inline">Cart</span>
                        <span class="bg-brand-950 text-white text-[11px] font-mono px-1.5 py-0.5 rounded-full min-w-5 text-center font-bold" x-text="cartCount"></span>
                    </a>
                </div>
            </div>

            <!-- Search (Mobile & Tablet) -->
            <div class="lg:hidden pb-3">
                <x-header-search variant="mobile" :placeholder="$searchPlaceholder" :popularSearches="$searchTags" />
            </div>
        </div>
    </header>

    <!-- Global Toast Alert Messages -->
    @if(session('success'))
    <div class="fixed bottom-6 right-6 z-50 bg-emerald-900/90 text-emerald-100 border border-emerald-500/40 backdrop-blur-md px-5 py-3 rounded-2xl shadow-lift flex items-center gap-3 animate-bounce">
        <i data-lucide="check-circle" class="w-5 h-5 text-brand-400"></i>
        <span class="text-sm font-semibold">{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="fixed bottom-6 right-6 z-50 bg-rose-900/90 text-rose-100 border border-rose-500/40 backdrop-blur-md px-5 py-3 rounded-2xl shadow-lift flex items-center gap-3">
        <i data-lucide="alert-circle" class="w-5 h-5 text-rose-400"></i>
        <span class="text-sm font-semibold">{{ session('error') }}</span>
    </div>
    @endif

    <!-- Content Slot -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Storefront Footer -->
    <footer class="bg-white dark:bg-[#080d16] border-t border-slate-200 dark:border-white/10 mt-20 pt-16 pb-12 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8 pb-12 border-b border-slate-200 dark:border-white/5">

                <!-- Company Info -->
                <div class="lg:col-span-2 space-y-4">
                    <div class="flex items-center gap-2.5">
                        @if($footerLogoImg = \App\Models\Setting::get('logo_image_url'))
                            <img src="{{ $footerLogoImg }}" alt="{{ \App\Models\Setting::get('store_name') }}" class="h-8 object-contain">
                        @else
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-tr {{ \App\Models\Setting::get('site_icon_gradient') }} flex items-center justify-center text-white font-mono font-black text-base shadow-glow">
                                {{ \App\Models\Setting::get('site_icon_text') }}
                            </div>
                            <span class="font-extrabold text-xl tracking-tight text-ink dark:text-white">
                                {{ \App\Models\Setting::get('logo_text_prefix') }}<span class="text-brand-500">{{ \App\Models\Setting::get('logo_text_highlight') }}</span>
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed max-w-sm">
                        {{ \App\Models\Setting::get('store_tagline') }}
                    </p>
                    <div class="space-y-1 text-xs text-slate-500 dark:text-slate-400">
                        <p class="flex items-center gap-2">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-brand-500 shrink-0"></i>
                            <span>{{ \App\Models\Setting::get('store_address') }}</span>
                        </p>
                    </div>
                    <div class="flex items-center gap-3 pt-1 text-slate-400">
                        <span class="text-xs font-semibold text-slate-600 dark:text-slate-300">Payment Security:</span>
                        <span class="text-xs font-mono font-bold text-brand-600 dark:text-brand-400">Razorpay 256-bit SSL</span>
                    </div>
                </div>

                <!-- Catalog Links -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white mb-4">Shop Categories</h4>
                    <ul class="space-y-2.5 text-xs text-slate-600 dark:text-slate-400">
                        @foreach(\App\Models\Category::where('is_active', true)->limit(5)->get() as $c)
                        <li><a href="{{ route('shop.category', $c->slug) }}" class="hover:text-brand-600 dark:hover:text-brand-400 transition-colors">{{ $c->name }}</a></li>
                        @endforeach
                        <li><a href="{{ route('shop.index') }}" class="hover:text-brand-600 dark:hover:text-brand-400 transition-colors font-semibold">All Categories &rarr;</a></li>
                    </ul>
                </div>

                <!-- Store Policies & Dynamic CMS Pages -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white mb-4">Policies &amp; Company</h4>
                    <ul class="space-y-2.5 text-xs text-slate-600 dark:text-slate-400">
                        @foreach(\App\Models\Page::where('is_published', true)->where('show_in_footer', true)->orderBy('sort_order', 'asc')->get() as $p)
                        <li>
                            <a href="{{ route('pages.show', $p->slug) }}" class="hover:text-brand-600 dark:hover:text-brand-400 transition-colors">
                                {{ $p->title }}
                            </a>
                        </li>
                        @endforeach
                        <li><a href="{{ route('seo.sitemap') }}" class="hover:text-brand-600 dark:hover:text-brand-400 transition-colors font-mono">XML Sitemap</a></li>
                    </ul>
                </div>

                <!-- Customer Care & Socials -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white mb-4">Customer Care</h4>
                    <ul class="space-y-2.5 text-xs text-slate-600 dark:text-slate-400">
                        <li><a href="{{ route('account.index', ['tab' => 'orders']) }}" class="hover:text-brand-600 dark:hover:text-brand-400 transition-colors">Track Orders</a></li>
                        <li><a href="mailto:{{ \App\Models\Setting::get('store_email') }}" class="hover:text-brand-600 dark:hover:text-brand-400 transition-colors">Email: {{ \App\Models\Setting::get('store_email') }}</a></li>
                        <li><a href="tel:{{ \App\Models\Setting::get('store_phone') }}" class="hover:text-brand-600 dark:hover:text-brand-400 transition-colors font-mono">Helpline: {{ \App\Models\Setting::get('store_phone') }}</a></li>
                    </ul>

                    <!-- Dynamic Social Media Links -->
                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-white/5 flex items-center gap-2.5">
                        @if($ig = \App\Models\Setting::get('social_instagram'))
                        <a href="{{ $ig }}" target="_blank" rel="noopener" class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-white/5 hover:bg-brand-50 hover:text-brand-600 dark:hover:bg-brand-950/40 text-slate-500 flex items-center justify-center transition-colors" title="Instagram">
                            <i data-lucide="instagram" class="w-4 h-4"></i>
                        </a>
                        @endif
                        @if($tw = \App\Models\Setting::get('social_twitter'))
                        <a href="{{ $tw }}" target="_blank" rel="noopener" class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-white/5 hover:bg-brand-50 hover:text-brand-600 dark:hover:bg-brand-950/40 text-slate-500 flex items-center justify-center transition-colors" title="Twitter / X">
                            <i data-lucide="twitter" class="w-4 h-4"></i>
                        </a>
                        @endif
                        @if($yt = \App\Models\Setting::get('social_youtube'))
                        <a href="{{ $yt }}" target="_blank" rel="noopener" class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-white/5 hover:bg-brand-50 hover:text-brand-600 dark:hover:bg-brand-950/40 text-slate-500 flex items-center justify-center transition-colors" title="YouTube">
                            <i data-lucide="youtube" class="w-4 h-4"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Bottom Strip -->
            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500 dark:text-slate-500">
                <p>{{ str_replace('{year}', date('Y'), \App\Models\Setting::get('footer_copyright')) }}</p>
                <div class="flex items-center gap-4 text-xs font-mono">
                    <span>{{ \App\Models\Setting::get('payment_methods_text') }}</span>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
