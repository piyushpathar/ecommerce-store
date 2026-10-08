@extends('layouts.app')

@section('title', 'Search results for "' . $query . '" | NovaMart')
@section('meta_description', 'Search results for ' . $query . ' at NovaMart')

@section('content')
<div class="max-w-[1440px] mx-auto px-3 sm:px-6 lg:px-8 py-3 sm:py-5">

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

            <!-- Custom Combobox: Sorting Dropdown -->
            <div class="relative hidden lg:block" x-data="{
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


    <!-- Layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 sm:gap-4 lg:gap-6 items-start">

        <!-- Desktop Filters Sidebar (Sticky) -->
        <aside class="hidden lg:block lg:col-span-3 xl:col-span-3 2xl:col-span-2 sticky top-20 max-h-[calc(100vh-6rem)] overflow-y-auto rounded-2xl">
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
    <!-- Mobile: sticky Sort / Filter bar with bottom sheets -->
    <x-mobile-filter-bar :categories="$categories" :brands="$brands" :actionUrl="route('search.index')" :query="$query" :totalProducts="$products->total()" :sortOptions="['relevance' => 'Relevance', 'price_asc' => 'Price: Low to High', 'price_desc' => 'Price: High to Low', 'rating' => 'Top Rated', 'newest' => 'Newest First']" />
</div>
@endsection
