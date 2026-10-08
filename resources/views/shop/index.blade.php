@extends('layouts.app')

@section('title', 'All Products & Flagship Marketplace | NovaMart')
@section('meta_description', 'Browse flagship 5G smartphones, creator laptops, studio headphones, smart appliances, and lifestyle fashion at NovaMart.')

@section('content')
<div class="max-w-[1440px] mx-auto px-3 sm:px-6 lg:px-8 py-3 sm:py-5">

    <!-- Visual Category Strip -->
    <div class="mb-3 sm:mb-4">
        <x-category-strip :categories="$categories" activeSlug="all" />
    </div>

    <!-- Compact Breadcrumb & Control Bar -->
    <div class="flex items-center justify-between gap-2 mb-3 sm:mb-4 pb-2.5 border-b border-slate-200 dark:border-white/10 text-xs">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-1.5 text-slate-500 truncate">
            <a href="{{ route('home') }}" class="hover:text-brand-500">Home</a>
            <span>/</span>
            <span class="text-slate-900 dark:text-white font-semibold truncate">All Products</span>
            <span class="text-slate-400 hidden sm:inline">&bull;</span>
            <span class="text-slate-500 font-mono text-[11px] hidden sm:inline">{{ $products->total() }} items</span>
        </nav>

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


    <!-- Layout Grid -->
    <div class="flex flex-col lg:flex-row gap-4 lg:gap-6 items-start">

        <!-- Desktop Filters Sidebar (Sticky, Viewport-optimized) -->
        <aside class="hidden lg:block w-[280px] xl:w-[300px] shrink-0 sticky top-20 max-h-[calc(100vh-6rem)] overflow-y-auto no-scrollbar rounded-2xl">
            <x-filter-sidebar :categories="$categories" :brands="$brands" :actionUrl="route('shop.index')" :totalProducts="$products->total()" />
        </aside>

        <!-- Product Cards Grid Area -->
        <div class="flex-1 min-w-0 w-full space-y-3">

            @if(request()->anyFilled(['category', 'brand', 'min_price', 'max_price', 'min_rating', 'min_discount', 'in_stock_only']))
            <!-- Active filter chips -->
            <div class="flex items-center gap-1.5 flex-wrap text-xs pb-1 bg-white/50 dark:bg-white/[0.02] p-2 rounded-2xl border border-slate-200/60 dark:border-white/5">
                <span class="text-slate-400 font-semibold text-[11px] flex items-center gap-1">
                    <i data-lucide="filter" class="w-3 h-3 text-brand-500"></i>
                    <span>Applied:</span>
                </span>
                @if(request('category'))
                <a href="{{ route('shop.index', request()->except(['category', 'page'])) }}" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-brand-50 dark:bg-brand-950/60 text-brand-600 dark:text-brand-400 border border-brand-200 dark:border-brand-800 text-[11px] font-bold hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-colors">
                    <span>Category: {{ request('category') }}</span>
                    <i data-lucide="x" class="w-3 h-3"></i>
                </a>
                @endif
                @if(request('brand'))
                <a href="{{ route('shop.index', request()->except(['brand', 'page'])) }}" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-brand-50 dark:bg-brand-950/60 text-brand-600 dark:text-brand-400 border border-brand-200 dark:border-brand-800 text-[11px] font-bold hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-colors">
                    <span>Brand: {{ request('brand') }}</span>
                    <i data-lucide="x" class="w-3 h-3"></i>
                </a>
                @endif
                @if(request('min_price') || request('max_price'))
                <a href="{{ route('shop.index', request()->except(['min_price', 'max_price', 'page'])) }}" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-brand-50 dark:bg-brand-950/60 text-brand-600 dark:text-brand-400 border border-brand-200 dark:border-brand-800 text-[11px] font-bold hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-colors">
                    <span>₹{{ request('min_price', 0) }} - ₹{{ request('max_price', 'Any') }}</span>
                    <i data-lucide="x" class="w-3 h-3"></i>
                </a>
                @endif
                @if(request('min_rating'))
                <a href="{{ route('shop.index', request()->except(['min_rating', 'page'])) }}" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800 text-[11px] font-bold hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-colors">
                    <span>{{ request('min_rating') }}★ & Above</span>
                    <i data-lucide="x" class="w-3 h-3"></i>
                </a>
                @endif
                @if(request('min_discount'))
                <a href="{{ route('shop.index', request()->except(['min_discount', 'page'])) }}" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 text-[11px] font-bold hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-colors">
                    <span>{{ request('min_discount') }}%+ Off</span>
                    <i data-lucide="x" class="w-3 h-3"></i>
                </a>
                @endif
                @if(request('in_stock_only'))
                <a href="{{ route('shop.index', request()->except(['in_stock_only', 'page'])) }}" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-white/10 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-white/10 text-[11px] font-bold hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-colors">
                    <span>In Stock Only</span>
                    <i data-lucide="x" class="w-3 h-3"></i>
                </a>
                @endif
                <a href="{{ route('shop.index') }}" class="text-[11px] font-bold text-rose-500 hover:underline ml-auto flex items-center gap-1">
                    <i data-lucide="rotate-ccw" class="w-3 h-3"></i>
                    <span>Clear All</span>
                </a>
            </div>
            @endif

            @if($products->isEmpty())
            <div class="bg-white dark:bg-[#0f1723] rounded-2xl p-8 text-center border border-slate-200 dark:border-white/10 shadow-soft space-y-3">
                <i data-lucide="package-search" class="w-10 h-10 text-slate-400 mx-auto"></i>
                <h3 class="text-sm font-bold text-slate-800 dark:text-white">No products found</h3>
                <p class="text-xs text-slate-500">Try adjusting your filters or price range.</p>
                <a href="{{ route('shop.index') }}" class="inline-block px-4 py-2 rounded-xl bg-brand-600 text-white text-xs font-bold">Clear Filters</a>
            </div>
            @else

            <!-- 2-Column Mobile, 3-to-5 Column Desktop High-Density Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-2.5 sm:gap-3.5">
                @foreach($products as $prod)
                    @include('components.product-card', ['prod' => $prod])
                @endforeach
            </div>

            <!-- Custom Pagination -->
            <div class="pt-4 sm:pt-6">
                {{ $products->links('vendor.pagination.tailwind') }}
            </div>
            @endif

        </div>
    </div>
    <!-- Mobile: sticky Sort / Filter bar with bottom sheets -->
    <x-mobile-filter-bar :categories="$categories" :brands="$brands" :actionUrl="route('shop.index')" :totalProducts="$products->total()" />
</div>
@endsection
