@extends('layouts.app')

@section('title', 'Search results for "' . $query . '" | NovaMart')
@section('meta_description', 'Search results for ' . $query . ' at NovaMart')

@section('content')
<div class="max-w-[1440px] mx-auto px-3 sm:px-6 lg:px-8 py-3 sm:py-5" x-data="{ mobileFiltersOpen: false }">

    <!-- Compact Breadcrumbs & Sort Bar -->
    <div class="flex items-center justify-between gap-2 mb-3 sm:mb-4 pb-2.5 border-b border-slate-200 dark:border-white/10 text-xs">
        <nav class="flex items-center gap-1.5 text-slate-500 truncate">
            <a href="{{ route('home') }}" class="hover:text-brand-500">Home</a>
            <span>/</span>
            <span class="text-slate-900 dark:text-white font-semibold truncate">Search: "{{ $query }}"</span>
            <span class="text-slate-400 hidden sm:inline">&bull;</span>
            <span class="text-slate-500 font-mono text-[11px] hidden sm:inline">{{ $products->total() }} items</span>
        </nav>

        <div class="flex items-center gap-2 shrink-0">
            <!-- Mobile Filter Trigger -->
            <button type="button"
                    @click="mobileFiltersOpen = !mobileFiltersOpen"
                    class="lg:hidden h-8 px-2.5 rounded-lg bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-800 dark:text-slate-200 text-xs font-semibold flex items-center gap-1.5">
                <i data-lucide="sliders-horizontal" class="w-3.5 h-3.5 text-brand-500"></i>
                <span>Filters</span>
                @if(request()->anyFilled(['min_price', 'max_price', 'in_stock_only']))
                <span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>
                @endif
            </button>

            <!-- Custom Combobox: Sorting Dropdown -->
            <div class="relative" x-data="{
                open: false,
                currentSort: '{{ request('sort', 'relevance') }}',
                sortOptions: {
                    'relevance': 'Relevance',
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
                    <span x-text="sortOptions[currentSort] || 'Relevance'"></span>
                    <i data-lucide="chevron-down" class="w-3 h-3 text-slate-400 transition-transform" :class="{ 'rotate-180': open }"></i>
                </button>

                <div x-show="open" x-cloak class="absolute right-0 mt-1 w-44 bg-white dark:bg-[#121824] rounded-xl shadow-pop border border-slate-200 dark:border-white/10 p-1 z-50">
                    <template x-for="(label, key) in sortOptions" :key="key">
                        <button type="button"
                                @click="selectSort(key)"
                                class="w-full text-left px-2.5 py-1.5 rounded-lg text-xs font-medium hover:bg-slate-100 dark:hover:bg-white/5 flex items-center justify-between"
                                :class="{ 'text-brand-600 bg-brand-50 dark:bg-brand-950/40 font-bold': currentSort === key, 'text-slate-700 dark:text-slate-300': currentSort !== key }">
                            <span x-text="label"></span>
                            <i x-show="currentSort === key" data-lucide="check" class="w-3 h-3 text-brand-600"></i>
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Filter Slide-Over Drawer -->
    <div x-show="mobileFiltersOpen" 
         x-cloak 
         class="fixed inset-0 z-50 lg:hidden flex justify-end">
        <!-- Backdrop -->
        <div x-show="mobileFiltersOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="mobileFiltersOpen = false" 
             class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

        <!-- Sliding Panel -->
        <div x-show="mobileFiltersOpen" 
             x-transition:enter="transition ease-in-out duration-300 transform"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in-out duration-300 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="relative w-full max-w-xs sm:max-w-sm bg-white dark:bg-[#0b1019] h-full shadow-2xl flex flex-col z-10 overflow-hidden">
            
            <!-- Mobile Drawer Header -->
            <div class="p-4 border-b border-slate-200 dark:border-white/10 flex items-center justify-between bg-slate-50 dark:bg-white/5">
                <div class="flex items-center gap-2">
                    <i data-lucide="sliders-horizontal" class="w-4 h-4 text-brand-500"></i>
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white">Filter Search</h3>
                </div>
                <button type="button" @click="mobileFiltersOpen = false" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-white">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <!-- Drawer Body -->
            <div class="flex-1 overflow-y-auto p-3">
                <x-filter-sidebar :categories="$categories" :brands="$brands" :actionUrl="route('search.index')" :query="$query" :totalProducts="$products->total()" />
            </div>

            <!-- Drawer Footer -->
            <div class="p-3 border-t border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 flex items-center gap-2">
                <a href="{{ route('search.index', ['q' => $query]) }}" class="w-1/3 py-2.5 text-center rounded-xl border border-slate-200 dark:border-white/10 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5">
                    Reset
                </a>
                <button type="button" @click="mobileFiltersOpen = false" class="w-2/3 py-2.5 rounded-xl bg-gradient-to-r from-brand-500 to-emerald-600 text-white text-xs font-bold text-center shadow-glow">
                    Show {{ $products->total() }} Results
                </button>
            </div>
        </div>
    </div>

    <!-- Layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 sm:gap-4 lg:gap-6 items-start">

        <!-- Desktop Filters Sidebar (Sticky) -->
        <aside class="hidden lg:block lg:col-span-3 xl:col-span-3 2xl:col-span-2 sticky top-20">
            <x-filter-sidebar :categories="$categories" :brands="$brands" :actionUrl="route('search.index')" :query="$query" :totalProducts="$products->total()" />
        </aside>

        <!-- Product Cards Grid Area -->
        <div class="lg:col-span-9 xl:col-span-9 2xl:col-span-10 space-y-3">

            @if($products->isEmpty())
            <div class="bg-white dark:bg-[#0f1723] rounded-2xl p-8 text-center border border-slate-200 dark:border-white/10 shadow-soft space-y-3">
                <i data-lucide="package-search" class="w-10 h-10 text-slate-400 mx-auto"></i>
                <h3 class="text-sm font-bold text-slate-800 dark:text-white">No products found matching "{{ $query }}"</h3>
                <p class="text-xs text-slate-500">Try searching for keywords like "phone", "macbook", "headphones", or "dyson".</p>
                <a href="{{ route('shop.index') }}" class="inline-block px-4 py-2 rounded-xl bg-brand-600 text-white text-xs font-bold">Browse All Products</a>
            </div>
            @else

            <!-- 2-Col Mobile, 3-to-5 Col Desktop High Density Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 2xl:grid-cols-5 gap-2.5 sm:gap-3.5">
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
</div>
@endsection
