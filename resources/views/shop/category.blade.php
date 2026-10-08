@extends('layouts.app')

@section('title', $category->name . ' | NovaMart')
@section('meta_description', $category->description)

@section('content')
<div class="max-w-[1440px] mx-auto px-2.5 sm:px-4 lg:px-8 py-2 sm:py-4 space-y-3 sm:space-y-4">

    <!-- 1. Visual Category Navigation Strip -->
    <x-category-strip :categories="$categories" :activeSlug="$category->slug" />

    <!-- 2. Category Hero Showcase Banner -->
    <div class="relative bg-gradient-to-r from-slate-50 via-emerald-50/40 to-white dark:from-[#0f1723] dark:via-[#131f30] dark:to-[#0f1723] rounded-xl sm:rounded-2xl p-3.5 sm:p-5 border border-slate-200 dark:border-white/10 shadow-2xs overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="space-y-2 flex-1">
                <!-- Breadcrumbs -->
                <nav class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400">
                    <a href="{{ route('home') }}" class="hover:text-brand-600 transition-colors">Home</a>
                    <span>/</span>
                    <a href="{{ route('shop.index') }}" class="hover:text-brand-600 transition-colors">Categories</a>
                    <span>/</span>
                    <span class="text-brand-600 dark:text-brand-400 font-bold truncate">{{ $category->name }}</span>
                </nav>

                <!-- Heading & Assurances -->
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    <h1 class="text-lg sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                        {{ $category->name }}
                    </h1>
                    <span class="inline-flex items-center gap-1 text-[11px] font-mono font-bold px-2.5 py-0.5 rounded-full bg-brand-50 dark:bg-brand-950/60 text-brand-700 dark:text-brand-300 border border-brand-200 dark:border-brand-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-brand-500 animate-pulse"></span>
                        {{ $products->total() }} Products
                    </span>
                </div>

                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 max-w-2xl leading-relaxed">
                    {{ $category->description }}
                </p>

                <!-- Value Trust Badges -->
                <div class="flex flex-wrap items-center gap-2 pt-1 text-[11px] font-semibold text-slate-600 dark:text-slate-300">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5 text-brand-500"></i>
                        <span>100% Genuine</span>
                    </span>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10">
                        <i data-lucide="zap" class="w-3.5 h-3.5 text-amber-500"></i>
                        <span>Fast Express Dispatch</span>
                    </span>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10">
                        <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-teal-500"></i>
                        <span>7-Day Replacement</span>
                    </span>
                </div>
            </div>

            <!-- Visual Category Showcase Card -->
            <div class="hidden md:flex items-center gap-3 shrink-0">
                <div class="w-24 h-24 lg:w-28 lg:h-28 rounded-2xl overflow-hidden border-2 border-brand-500/30 p-1 bg-white dark:bg-[#121824] shadow-md group">
                    <img src="{{ $category->image }}" 
                         alt="{{ $category->name }}" 
                         class="w-full h-full object-cover rounded-xl transition-transform duration-300 group-hover:scale-110">
                </div>
            </div>
        </div>

        <!-- Horizontal Quick-Filter Brand Chips -->
        @if(count($brands) > 0)
        <div class="mt-3 sm:mt-4 pt-3 border-t border-slate-200 dark:border-white/10 flex items-center gap-1.5 sm:gap-2 overflow-x-auto no-scrollbar py-0.5">
            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 shrink-0 mr-1 flex items-center gap-1">
                <i data-lucide="sparkles" class="w-3 h-3 text-brand-500"></i>
                <span>Brands:</span>
            </span>
            <a href="{{ route('shop.category', ['slug' => $category->slug] + request()->except(['brand', 'page'])) }}"
               class="px-3 py-1 rounded-full text-xs font-semibold transition-all shrink-0 {{ !request('brand') ? 'bg-brand-600 text-white font-bold shadow-xs' : 'bg-white dark:bg-white/5 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/10 border border-slate-200 dark:border-white/10' }}">
                All Brands
            </a>
            @foreach($brands as $b)
            <a href="{{ route('shop.category', ['slug' => $category->slug, 'brand' => (request('brand') === $b ? null : $b)] + request()->except(['brand', 'page'])) }}"
               class="px-3 py-1 rounded-full text-xs font-semibold transition-all shrink-0 flex items-center gap-1.5 {{ request('brand') === $b ? 'bg-brand-600 text-white font-bold shadow-xs ring-2 ring-brand-500/40' : 'bg-white dark:bg-white/5 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/10 border border-slate-200 dark:border-white/10' }}">
                <span>{{ $b }}</span>
                @if(request('brand') === $b)
                <span class="text-white/80 hover:text-white font-bold ml-0.5">&times;</span>
                @endif
            </a>
            @endforeach
        </div>
        @endif
    </div>

    <!-- 3. Filter Bar & Controls -->
    <div class="flex items-center justify-between gap-2 pb-2 border-b border-slate-200 dark:border-white/10 text-xs">
        <!-- Active Filter Indicator / Items Count -->
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-0.5">
            <span class="text-slate-500 dark:text-slate-400 font-semibold shrink-0">Showing {{ $products->count() }} of {{ $products->total() }} items</span>

            @if(request('brand'))
            <a href="{{ route('shop.category', ['slug' => $category->slug] + request()->except(['brand', 'page'])) }}" 
               class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-brand-50 dark:bg-brand-950/60 text-brand-600 dark:text-brand-400 border border-brand-200 dark:border-brand-800 text-[11px] font-bold shrink-0">
                <span>Brand: {{ request('brand') }}</span>
                <i data-lucide="x" class="w-3 h-3"></i>
            </a>
            @endif

            @if(request('min_price') || request('max_price'))
            <a href="{{ route('shop.category', ['slug' => $category->slug] + request()->except(['min_price', 'max_price', 'page'])) }}" 
               class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-brand-50 dark:bg-brand-950/60 text-brand-600 dark:text-brand-400 border border-brand-200 dark:border-brand-800 text-[11px] font-bold shrink-0">
                <span>₹{{ request('min_price', 0) }} - ₹{{ request('max_price', 'Any') }}</span>
                <i data-lucide="x" class="w-3 h-3"></i>
            </a>
            @endif

            @if(request('min_rating'))
            <a href="{{ route('shop.category', ['slug' => $category->slug] + request()->except(['min_rating', 'page'])) }}" 
               class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-brand-50 dark:bg-brand-950/60 text-brand-600 dark:text-brand-400 border border-brand-200 dark:border-brand-800 text-[11px] font-bold shrink-0">
                <span>{{ request('min_rating') }}★ & Above</span>
                <i data-lucide="x" class="w-3 h-3"></i>
            </a>
            @endif

            @if(request()->anyFilled(['brand', 'min_price', 'max_price', 'min_rating', 'in_stock_only']))
            <a href="{{ route('shop.category', $category->slug) }}" class="text-[11px] font-bold text-rose-500 hover:underline shrink-0 ml-1">
                Clear All
            </a>
            @endif
        </div>

        <div class="flex items-center gap-2 shrink-0">

            <!-- Custom Combobox: Sorting Dropdown -->
            <div class="relative hidden lg:block" x-data="{
                open: false,
                currentSort: '{{ request('sort', 'relevance') }}',
                sortOptions: {
                    'relevance': 'Featured',
                    'price_asc': 'Price: Low-High',
                    'price_desc': 'Price: High-Low',
                    'rating': 'Top Rated',
                    'newest': 'Newest'
                },
                selectSort(val) {
                    const url = new URL(window.location.href);
                    url.searchParams.set('sort', val);
                    window.location.href = url.toString();
                }
            }" @click.outside="open = false">
                <button type="button"
                        @click="open = !open"
                        class="h-8 px-2.5 sm:px-3 rounded-lg bg-white dark:bg-[#0f1723] border border-slate-200 dark:border-white/10 text-slate-800 dark:text-slate-200 text-xs font-semibold flex items-center gap-1.5 shadow-2xs hover:border-brand-500 transition-colors">
                    <span class="text-slate-400 font-normal hidden sm:inline">Sort:</span>
                    <span x-text="sortOptions[currentSort] || 'Featured'"></span>
                    <i data-lucide="chevron-down" class="w-3 h-3 text-slate-400 transition-transform" :class="{ 'rotate-180': open }"></i>
                </button>

                <div x-show="open" x-cloak class="absolute right-0 mt-1 w-44 bg-white dark:bg-[#121824] rounded-xl shadow-pop border border-slate-200 dark:border-white/10 p-1 z-50">
                    <template x-for="(label, key) in sortOptions" :key="key">
                        <button type="button"
                                @click="selectSort(key)"
                                class="w-full text-left px-2.5 py-1.5 rounded-lg text-xs font-medium flex items-center justify-between transition-colors"
                                :class="{ 'bg-brand-50 dark:bg-brand-950/40 text-brand-600 dark:text-brand-400 font-bold': currentSort === key, 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5': currentSort !== key }">
                            <span x-text="label"></span>
                            <i x-show="currentSort === key" data-lucide="check" class="w-3 h-3 text-brand-600"></i>
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>


    <!-- 4. Layout Grid: Sidebar + Product Grid -->
    <div class="flex flex-col lg:flex-row gap-3 lg:gap-5 items-start">

        <!-- Desktop Filters Sidebar (Sticky) -->
        <aside class="hidden lg:block w-[280px] xl:w-[300px] shrink-0 sticky top-20 max-h-[calc(100vh-6rem)] overflow-y-auto no-scrollbar rounded-2xl">
            <x-filter-sidebar :categories="$categories" :brands="$brands" :activeCategory="$category" :actionUrl="route('shop.category', $category->slug)" :totalProducts="$products->total()" />
        </aside>

        <!-- Product Cards Grid Area -->
        <div class="flex-1 min-w-0 w-full">

            @if($products->isEmpty())
            <div class="bg-white dark:bg-[#0f1723] rounded-2xl p-8 sm:p-12 text-center border border-slate-200 dark:border-white/10 shadow-2xs space-y-3">
                <i data-lucide="package-search" class="w-12 h-12 text-slate-400 mx-auto"></i>
                <h3 class="text-base font-bold text-slate-800 dark:text-white">No products found matching criteria</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">Try clearing your filters or selecting a different brand to see available products.</p>
                <div class="pt-2">
                    <a href="{{ route('shop.category', $category->slug) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-brand-600 text-white text-xs font-bold hover:bg-brand-500 transition-colors">
                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                        <span>Reset All Filters</span>
                    </a>
                </div>
            </div>
            @else

            <!-- High-density 2-Col Mobile, 3-to-5 Col Desktop Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-2 sm:gap-3">
                @foreach($products as $prod)
                    @include('components.product-card', ['prod' => $prod])
                @endforeach
            </div>

            <div class="pt-4 sm:pt-6">
                {{ $products->links('vendor.pagination.tailwind') }}
            </div>
            @endif

        </div>
    </div>
    <!-- Mobile: sticky Sort / Filter bar with bottom sheets -->
    <x-mobile-filter-bar :categories="$categories" :brands="$brands" :activeCategory="$category" :actionUrl="route('shop.category', $category->slug)" :totalProducts="$products->total()" />
</div>
@endsection
