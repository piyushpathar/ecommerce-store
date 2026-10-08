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
          cartCount: {{ session('novamart_cart') ? count(session('novamart_cart')) : 0 }},
          searchOpen: false,
          searchQuery: '',
          suggestions: { products: [], categories: [] },
          isSearching: false,
          async fetchSuggestions() {
              if (this.searchQuery.trim().length < 2) {
                  this.suggestions = { products: [], categories: [] };
                  return;
              }
              this.isSearching = true;
              try {
                  const res = await fetch(`/api/search/suggest?q=${encodeURIComponent(this.searchQuery)}`);
                  const data = await res.json();
                  this.suggestions = data;
              } catch(e) {}
              this.isSearching = false;
          }
      }">

    <!-- Announcement Bar -->
    @if(\App\Models\Setting::get('announcement_enabled', '1') == '1' && ($announcement = \App\Models\Setting::get('announcement_bar', '⚡ MEGA SALE FESTIVAL: Flat 10% Off with Code NOVAMART10 · Free 1-Day Express Delivery Across India')))
    <div class="relative bg-gradient-to-r {{ \App\Models\Setting::get('announcement_bg', 'from-brand-800 via-brand-600 to-teal-700') }} text-white text-xs py-2 px-4 text-center font-medium shadow-xs">
        <div class="max-w-7xl mx-auto flex items-center justify-center gap-2">
            <span>{{ $announcement }}</span>
        </div>
    </div>
    @endif

    <!-- Main Navigation Header -->
    <header class="sticky top-0 z-40 bg-white/95 dark:bg-[#0c1117]/95 backdrop-blur-md border-b border-slate-200 dark:border-white/10 transition-colors">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <!-- Row 1: Logo, Desktop Search, & Right Actions -->
            <div class="flex items-center justify-between h-14 sm:h-16 lg:h-20 gap-3">

                <!-- Logo & Brand -->
                <div class="flex items-center gap-4 lg:gap-6 shrink-0">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 sm:gap-2.5 group">
                        @if($logoImg = \App\Models\Setting::get('logo_image_url'))
                            <img src="{{ $logoImg }}" alt="{{ \App\Models\Setting::get('store_name', 'NovaMart') }}" class="h-8 sm:h-10 object-contain">
                        @else
                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-tr {{ \App\Models\Setting::get('site_icon_gradient', 'from-brand-600 to-teal-400') }} flex items-center justify-center text-white font-mono font-black text-base sm:text-xl shadow-glow group-hover:scale-105 transition-transform">
                                {{ \App\Models\Setting::get('site_icon_text', 'NM') }}
                            </div>
                            <div class="flex flex-col">
                                <span class="font-extrabold text-base sm:text-xl tracking-tight text-ink dark:text-white flex items-center">
                                    {{ \App\Models\Setting::get('logo_text_prefix', 'NOVA') }}<span class="text-brand-500">{{ \App\Models\Setting::get('logo_text_highlight', 'MART') }}</span>
                                </span>
                                <span class="text-[10px] text-slate-500 dark:text-slate-400 font-medium tracking-wide -mt-0.5 hidden sm:block">{{ \App\Models\Setting::get('logo_subtitle', 'Marketplace') }}</span>
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

                    <!-- Dynamic Header Navigation Links -->
                    @php
                        $headerMenuJson = \App\Models\Setting::get('header_menu_items');
                        $headerNavItems = $headerMenuJson ? json_decode($headerMenuJson, true) : null;
                        $headerPages = \App\Models\Page::where('is_published', true)->where('show_in_header', true)->orderBy('sort_order', 'asc')->get();
                    @endphp
                    @if(!empty($headerNavItems) && is_array($headerNavItems))
                    <nav class="hidden 2xl:flex items-center gap-2 text-xs font-semibold text-slate-600 dark:text-slate-300">
                        @foreach(array_slice($headerNavItems, 0, 3) as $nav)
                        <a href="{{ $nav['url'] ?? '#' }}" class="hover:text-brand-600 dark:hover:text-brand-400 transition-colors flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-white/5">
                            @if(!empty($nav['icon']))
                                <i data-lucide="{{ $nav['icon'] }}" class="w-3.5 h-3.5 text-brand-500"></i>
                            @endif
                            <span>{{ $nav['title'] ?? '' }}</span>
                        </a>
                        @endforeach
                        @foreach($headerPages as $hp)
                        <a href="{{ route('pages.show', $hp->slug) }}" class="hover:text-brand-600 dark:hover:text-brand-400 transition-colors px-2.5 py-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-white/5">
                            {{ $hp->title }}
                        </a>
                        @endforeach
                    </nav>
                    @endif
                </div>

                <!-- Global Custom Combobox Search Bar (Desktop) -->
                @php
                    $searchTagsString = \App\Models\Setting::get('search_popular_tags', 'iPhone 16 Pro, MacBook Pro M4, Sony WH-1000XM5, Nike Air Jordan, Dyson V15');
                    $searchTags = array_filter(array_map('trim', explode(',', $searchTagsString)));
                    $searchPlaceholder = \App\Models\Setting::get('search_placeholder', 'Search smartphones, laptops, audio, fashion...');
                @endphp
                <div class="hidden lg:block flex-1 max-w-2xl relative" x-data="{
                    selectedCategory: '',
                    selectedCategoryName: 'All',
                    categoryDropdownOpen: false,
                    popularSearches: @js(array_values($searchTags))
                }">
                    <form action="{{ route('search.index') }}" method="GET" class="relative flex items-center">
                        <input type="hidden" name="category" :value="selectedCategory">

                        <div class="relative w-full flex items-center bg-slate-100/90 dark:bg-white/5 border border-slate-200/90 dark:border-white/10 rounded-2xl focus-within:ring-2 focus-within:ring-brand-500/30 focus-within:border-brand-500 shadow-xs transition-all overflow-hidden">
                            <!-- Category Scope Combobox -->
                            <div class="relative shrink-0 border-r border-slate-200 dark:border-white/10" @click.outside="categoryDropdownOpen = false">
                                <button type="button"
                                        @click="categoryDropdownOpen = !categoryDropdownOpen"
                                        class="h-10 sm:h-11 px-3 sm:px-3.5 bg-slate-50 dark:bg-white/5 hover:bg-slate-200/60 dark:hover:bg-white/10 text-xs font-semibold text-slate-700 dark:text-slate-200 flex items-center gap-1.5 transition-colors">
                                    <span class="max-w-[70px] sm:max-w-[90px] truncate" x-text="selectedCategoryName">All</span>
                                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 transition-transform" :class="{ 'rotate-180': categoryDropdownOpen }"></i>
                                </button>
                                <div x-show="categoryDropdownOpen" x-cloak class="absolute left-0 top-full mt-1.5 w-52 bg-white dark:bg-[#121824] rounded-2xl shadow-pop border border-slate-200 dark:border-white/10 p-1.5 z-50">
                                    <button type="button"
                                            @click="selectedCategory = ''; selectedCategoryName = 'All'; categoryDropdownOpen = false"
                                            class="w-full text-left px-3 py-2 rounded-xl text-xs font-semibold hover:bg-slate-100 dark:hover:bg-white/5 flex items-center justify-between"
                                            :class="{ 'text-brand-600 bg-brand-50 dark:bg-brand-950/40': selectedCategory === '' }">
                                        <span>All Categories</span>
                                        <i x-show="selectedCategory === ''" data-lucide="check" class="w-3.5 h-3.5 text-brand-600"></i>
                                    </button>
                                    @foreach(\App\Models\Category::where('is_active', true)->orderBy('sort_order', 'asc')->get() as $cat)
                                    <button type="button"
                                            @click="selectedCategory = '{{ $cat->slug }}'; selectedCategoryName = '{{ $cat->name }}'; categoryDropdownOpen = false"
                                            class="w-full text-left px-3 py-2 rounded-xl text-xs font-semibold hover:bg-slate-100 dark:hover:bg-white/5 flex items-center justify-between"
                                            :class="{ 'text-brand-600 bg-brand-50 dark:bg-brand-950/40': selectedCategory === '{{ $cat->slug }}' }">
                                        <span class="truncate">{{ $cat->name }}</span>
                                        <i x-show="selectedCategory === '{{ $cat->slug }}'" data-lucide="check" class="w-3.5 h-3.5 text-brand-600"></i>
                                    </button>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Search Input -->
                            <div class="relative flex-1 flex items-center">
                                <i data-lucide="search" class="w-4 h-4 text-slate-400 ml-3 shrink-0 pointer-events-none"></i>
                                <input type="text"
                                       name="q"
                                       placeholder="{{ $searchPlaceholder }}"
                                       x-model="searchQuery"
                                       @input.debounce.200ms="fetchSuggestions()"
                                       @focus="searchOpen = true"
                                       @click.outside="searchOpen = false"
                                       @keydown.escape="searchOpen = false"
                                       class="w-full pl-2.5 pr-16 py-2 sm:py-2.5 bg-transparent border-0 text-xs sm:text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-0">

                                <!-- Clear Button -->
                                <button type="button"
                                        x-show="searchQuery.length > 0"
                                        @click="searchQuery = ''; suggestions = { products: [], categories: [] }"
                                        class="p-1 rounded-full text-slate-400 hover:text-slate-600 dark:hover:text-white mr-1">
                                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                </button>

                                <!-- Keyboard Shortcut Badge -->
                                <div class="mr-2 hidden md:flex items-center text-[10px] text-slate-400 font-mono bg-slate-200/70 dark:bg-white/10 px-1.5 py-0.5 rounded border border-slate-300/60 dark:border-white/10 pointer-events-none">
                                    /
                                </div>
                            </div>

                            <!-- Search Submit Button -->
                            <button type="submit" aria-label="Search" class="h-10 sm:h-11 px-4 sm:px-5 bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs flex items-center justify-center transition-colors">
                                <i data-lucide="search" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </form>

                    <!-- Custom Autocomplete Combobox Dropdown -->
                    <div x-show="searchOpen"
                         x-cloak
                         class="absolute left-0 right-0 mt-2 bg-white dark:bg-[#121824] rounded-2xl shadow-pop border border-slate-200 dark:border-white/10 p-3 z-50">
                        
                        <!-- When user has typed query and results exist -->
                        <template x-if="searchQuery.trim().length >= 2">
                            <div>
                                <template x-if="suggestions.categories.length > 0">
                                    <div class="mb-3">
                                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5 px-2">Matching Categories</div>
                                        <div class="grid grid-cols-2 gap-1.5">
                                            <template x-for="cat in suggestions.categories" :key="cat.id">
                                                <a :href="`/category/${cat.slug}`" class="flex items-center gap-2 p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-white/5 text-xs font-semibold text-slate-700 dark:text-slate-200">
                                                    <i data-lucide="folder" class="w-3.5 h-3.5 text-brand-500"></i>
                                                    <span x-text="cat.name"></span>
                                                </a>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="suggestions.products.length > 0">
                                    <div>
                                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5 px-2">Products</div>
                                        <div class="space-y-1">
                                            <template x-for="prod in suggestions.products" :key="prod.id">
                                                <a :href="`/product/${prod.slug}`" class="flex items-center gap-3 p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-white/5 group transition-colors">
                                                    <img :src="prod.thumbnail" class="w-10 h-10 rounded-lg object-cover bg-slate-100 dark:bg-white/5 shrink-0" alt="">
                                                    <div class="flex-1 min-w-0">
                                                        <div class="text-xs font-semibold text-slate-900 dark:text-slate-100 truncate group-hover:text-brand-600" x-text="prod.title"></div>
                                                        <div class="text-[11px] text-slate-500 flex items-center gap-2">
                                                            <span x-text="prod.brand"></span>
                                                            <span>&bull;</span>
                                                            <span class="font-bold text-brand-600 dark:text-brand-400 font-mono" x-text="'₹' + Number(prod.price).toLocaleString('en-IN')"></span>
                                                        </div>
                                                    </div>
                                                </a>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="suggestions.products.length === 0 && suggestions.categories.length === 0 && !isSearching">
                                    <div class="py-4 text-center text-xs text-slate-400">
                                        No exact matches found. Press Enter to search all items.
                                    </div>
                                </template>

                                <div class="border-t border-slate-100 dark:border-white/5 mt-2 pt-2 px-2 flex justify-between items-center text-xs">
                                    <span class="text-slate-400 font-mono text-[11px]">Press Enter to search</span>
                                    <a :href="`/search?q=${encodeURIComponent(searchQuery)}`" class="font-semibold text-brand-600 dark:text-brand-400 hover:underline">
                                        View all results &rarr;
                                    </a>
                                </div>
                            </div>
                        </template>

                        <!-- When search is empty: show trending search chips -->
                        <template x-if="searchQuery.trim().length < 2">
                            <div class="p-2 space-y-2">
                                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Trending Searches</div>
                                <div class="flex flex-wrap gap-1.5">
                                    <template x-for="item in popularSearches" :key="item">
                                        <button type="button"
                                                @click="searchQuery = item; fetchSuggestions()"
                                                class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-white/5 hover:bg-brand-50 hover:text-brand-600 dark:hover:bg-brand-950/40 dark:hover:text-brand-400 text-slate-700 dark:text-slate-300 text-xs font-medium transition-colors flex items-center gap-1.5">
                                            <i data-lucide="trending-up" class="w-3 h-3 text-brand-500"></i>
                                            <span x-text="item"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Right Action Bar: Deals, Theme, Account, Cart -->
                <div class="flex items-center gap-2 sm:gap-3 shrink-0">

                    <!-- Flash Deals Link -->
                    <a href="{{ route('shop.index') }}" class="hidden xl:flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full bg-brand-50 hover:bg-brand-100 dark:bg-brand-950/60 dark:hover:bg-brand-900/60 text-brand-700 dark:text-brand-300 border border-brand-200 dark:border-brand-800/80 transition-all">
                        <i data-lucide="tag" class="w-3.5 h-3.5 text-brand-600"></i>
                        <span>Flash Deals</span>
                    </a>

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
                                <a href="{{ route('auth.google') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-white/5">
                                    <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24"><path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.15z"/><path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/><path fill="#FBBC05" d="M5.28 14.27A7.19 7.19 0 0 1 4.9 12c0-.79.14-1.57.38-2.27V6.58H1.25A11.97 11.97 0 0 0 0 12c0 1.92.45 3.74 1.25 5.42l4.03-3.15z"/><path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/></svg>
                                    <span>Gmail 1-Click Login</span>
                                </a>
                            @endauth
                        </div>
                    </div>

                    <!-- Cart Trigger Button -->
                    <a href="{{ route('cart.index') }}" class="relative flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-2 rounded-full bg-brand-600 hover:bg-brand-500 text-white font-semibold text-xs shadow-soft transition-all group">
                        <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                        <span class="hidden sm:inline">Cart</span>
                        <span class="bg-brand-950 text-white text-[11px] font-mono px-1.5 py-0.2 rounded-full min-w-5 text-center font-bold" x-text="cartCount"></span>
                    </a>
                </div>
            </div>

            <!-- Row 2: Full-Width Search Bar on Mobile & Tablet Screens -->
            <div class="lg:hidden pb-2.5 pt-0.5">
                <form action="{{ route('search.index') }}" method="GET" class="relative flex items-center">
                    <div class="relative w-full flex items-center bg-slate-100/90 dark:bg-white/5 border border-slate-200/90 dark:border-white/10 rounded-xl focus-within:ring-2 focus-within:ring-brand-500/30 focus-within:border-brand-500 shadow-2xs overflow-hidden">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 ml-3 shrink-0 pointer-events-none"></i>
                        <input type="text"
                               name="q"
                               placeholder="{{ $searchPlaceholder }}"
                               x-model="searchQuery"
                               @input.debounce.200ms="fetchSuggestions()"
                               @focus="searchOpen = true"
                               @click.outside="searchOpen = false"
                               class="w-full pl-2.5 pr-8 py-2 bg-transparent border-0 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-0">
                        <button type="submit" aria-label="Search" class="h-9 px-3.5 bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs flex items-center justify-center">
                            <i data-lucide="search" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                </form>
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
                            <img src="{{ $footerLogoImg }}" alt="{{ \App\Models\Setting::get('store_name', 'NovaMart') }}" class="h-8 object-contain">
                        @else
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-tr {{ \App\Models\Setting::get('site_icon_gradient', 'from-brand-600 to-teal-400') }} flex items-center justify-center text-white font-mono font-black text-base shadow-glow">
                                {{ \App\Models\Setting::get('site_icon_text', 'NM') }}
                            </div>
                            <span class="font-extrabold text-xl tracking-tight text-ink dark:text-white">
                                {{ \App\Models\Setting::get('logo_text_prefix', 'NOVA') }}<span class="text-brand-500">{{ \App\Models\Setting::get('logo_text_highlight', 'MART') }}</span>
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed max-w-sm">
                        {{ \App\Models\Setting::get('store_tagline', 'India\'s Premier Online Marketplace for Flagship Smartphones, Creator Laptops, Audio & Smart Living.') }}
                    </p>
                    <div class="space-y-1 text-xs text-slate-500 dark:text-slate-400">
                        <p class="flex items-center gap-2">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-brand-500 shrink-0"></i>
                            <span>{{ \App\Models\Setting::get('store_address', 'Tower 4, Horizon Tech Hub, SG Highway, Ahmedabad, Gujarat 380054') }}</span>
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
                        <li><a href="mailto:{{ \App\Models\Setting::get('store_email', 'support@novamart.in') }}" class="hover:text-brand-600 dark:hover:text-brand-400 transition-colors">Email: {{ \App\Models\Setting::get('store_email', 'support@novamart.in') }}</a></li>
                        <li><a href="tel:{{ \App\Models\Setting::get('store_phone', '+91 8000 999 888') }}" class="hover:text-brand-600 dark:hover:text-brand-400 transition-colors font-mono">Helpline: {{ \App\Models\Setting::get('store_phone', '+91 8000 999 888') }}</a></li>
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
                <p>{{ \App\Models\Setting::get('footer_copyright', '© ' . date('Y') . ' NovaMart Marketplace. All rights reserved. Powered by Laravel 11 & MySQL Enterprise.') }}</p>
                <div class="flex items-center gap-4 text-xs font-mono">
                    <span>{{ \App\Models\Setting::get('payment_methods_text', '⚡ UPI / Cards / NetBanking / EMI · 100% Brand Sealed Delivery') }}</span>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
