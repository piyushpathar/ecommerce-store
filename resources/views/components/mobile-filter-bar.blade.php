@props([
    'categories' => [],
    'brands' => [],
    'activeCategory' => null,
    'actionUrl' => route('shop.index'),
    'query' => null,
    'totalProducts' => 0,
    'sortOptions' => [
        'relevance' => 'Featured',
        'price_asc' => 'Price: Low to High',
        'price_desc' => 'Price: High to Low',
        'rating' => 'Top Rated',
        'newest' => 'Newest First',
    ],
])

@php
    $currentSort = request('sort', array_key_first($sortOptions));
    $activeCount = collect([
        request('category'),
        request('brand'),
        request('min_price') || request('max_price'),
        request('min_rating'),
        request('min_discount'),
        request('in_stock_only'),
    ])->filter()->count();
    $resetUrl = $actionUrl . ($query ? '?q=' . urlencode($query) : '');
@endphp

<div x-data="{
        filterOpen: false,
        sortOpen: false,
        selectSort(val) {
            const url = new URL(window.location.href);
            url.searchParams.set('sort', val);
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }
     }"
     x-effect="document.body.classList.toggle('overflow-hidden', filterOpen || sortOpen)"
     @keydown.escape.window="filterOpen = false; sortOpen = false"
     class="lg:hidden">

    <!-- Spacer so the fixed bar never covers the last row / pagination -->
    <div class="h-16"></div>

    <!-- Sticky bottom action bar -->
    <div class="fixed inset-x-0 bottom-0 z-40 bg-white/95 dark:bg-[#0b1019]/95 backdrop-blur-md border-t border-slate-200 dark:border-white/10 shadow-[0_-4px_16px_-6px_rgba(15,23,42,0.12)] pb-[env(safe-area-inset-bottom)]">
        <div class="grid grid-cols-2 divide-x divide-slate-200 dark:divide-white/10">
            <button type="button" @click="sortOpen = true" class="h-14 flex items-center justify-center gap-2 active:bg-slate-50 dark:active:bg-white/5">
                <i data-lucide="arrow-up-down" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                <span class="text-left leading-tight">
                    <span class="block text-xs font-bold text-slate-900 dark:text-white">Sort</span>
                    <span class="block text-[10px] text-slate-500 dark:text-slate-400 truncate max-w-[120px]">{{ $sortOptions[$currentSort] ?? reset($sortOptions) }}</span>
                </span>
            </button>
            <button type="button" @click="filterOpen = true" class="h-14 flex items-center justify-center gap-2 active:bg-slate-50 dark:active:bg-white/5">
                <span class="relative">
                    <i data-lucide="sliders-horizontal" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                    @if($activeCount > 0)
                    <span class="absolute -top-2 -right-2.5 min-w-[16px] h-4 px-1 rounded-full bg-brand-500 text-white text-[9px] font-bold flex items-center justify-center">{{ $activeCount }}</span>
                    @endif
                </span>
                <span class="text-left leading-tight">
                    <span class="block text-xs font-bold text-slate-900 dark:text-white">Filter</span>
                    <span class="block text-[10px] text-slate-500 dark:text-slate-400">{{ $activeCount > 0 ? $activeCount . ' applied' : number_format($totalProducts) . ' products' }}</span>
                </span>
            </button>
        </div>
    </div>

    <!-- Backdrop (shared) -->
    <div x-show="filterOpen || sortOpen"
         x-cloak
         x-transition.opacity.duration.200ms
         @click="filterOpen = false; sortOpen = false"
         class="fixed inset-0 z-50 bg-black/50 backdrop-blur-[2px]"></div>

    <!-- Sort bottom sheet -->
    <div x-show="sortOpen"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         role="dialog" aria-modal="true" aria-label="Sort products"
         class="fixed inset-x-0 bottom-0 z-[60] bg-white dark:bg-[#0b1019] rounded-t-3xl shadow-pop pb-[env(safe-area-inset-bottom)]">
        <div class="pt-2.5 pb-1 flex justify-center"><span class="w-10 h-1 rounded-full bg-slate-300 dark:bg-white/20"></span></div>
        <div class="px-4 pb-2 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Sort by</h3>
            <button type="button" @click="sortOpen = false" class="p-2 -mr-2 rounded-full text-slate-400 hover:bg-slate-100 dark:hover:bg-white/10" aria-label="Close">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <div class="px-2 pb-4 space-y-0.5">
            @foreach($sortOptions as $key => $label)
            <label class="nm-option min-h-[48px] text-sm">
                <input type="radio" name="mobile_sort" value="{{ $key }}" @checked($currentSort === $key) @change="selectSort('{{ $key }}')" class="nm-radio">
                <span>{{ $label }}</span>
            </label>
            @endforeach
        </div>
    </div>

    <!-- Filter bottom sheet -->
    <div x-show="filterOpen"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         role="dialog" aria-modal="true" aria-label="Filter products"
         class="fixed inset-x-0 bottom-0 z-[60] h-[85vh] max-h-[85dvh] flex flex-col bg-white dark:bg-[#0b1019] rounded-t-3xl shadow-pop overflow-hidden">
        <div class="pt-2.5 pb-1 flex justify-center shrink-0"><span class="w-10 h-1 rounded-full bg-slate-300 dark:bg-white/20"></span></div>
        <div class="px-4 pb-3 flex items-center justify-between border-b border-slate-200 dark:border-white/10 shrink-0">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                Filters
                @if($activeCount > 0)
                <span class="min-w-[18px] h-[18px] px-1 rounded-full bg-brand-500 text-white text-[10px] font-bold inline-flex items-center justify-center">{{ $activeCount }}</span>
                @endif
            </h3>
            <button type="button" @click="filterOpen = false" class="p-2 -mr-2 rounded-full text-slate-400 hover:bg-slate-100 dark:hover:bg-white/10" aria-label="Close">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <div class="flex-1 min-h-0">
            <x-filter-sidebar variant="sheet"
                              formId="mobile-filter-form"
                              :categories="$categories"
                              :brands="$brands"
                              :activeCategory="$activeCategory"
                              :actionUrl="$actionUrl"
                              :query="$query"
                              :totalProducts="$totalProducts" />
        </div>

        <div class="shrink-0 grid grid-cols-[1fr_2fr] gap-2 p-3 border-t border-slate-200 dark:border-white/10 bg-white dark:bg-[#0b1019] pb-[calc(0.75rem+env(safe-area-inset-bottom))]">
            <a href="{{ $resetUrl }}" class="h-11 flex items-center justify-center rounded-xl border border-slate-200 dark:border-white/10 text-xs font-bold text-slate-700 dark:text-slate-300 active:bg-slate-50 dark:active:bg-white/5">
                Clear all
            </a>
            <button type="submit" form="mobile-filter-form" class="h-11 rounded-xl bg-brand-600 active:bg-brand-700 text-white text-xs font-bold shadow-glow">
                Apply filters
            </button>
        </div>
    </div>
</div>
