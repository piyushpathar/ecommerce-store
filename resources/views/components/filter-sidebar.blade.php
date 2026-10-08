@props([
    'categories' => [],
    'brands' => [],
    'activeCategory' => null,
    'actionUrl' => route('shop.index'),
    'query' => null,
    'totalProducts' => 0
])

@php
    $hasActiveFilters = request()->anyFilled(['category', 'brand', 'min_price', 'max_price', 'min_rating', 'min_discount', 'in_stock_only']);
    $activeCount = 0;
    if (request('category')) $activeCount++;
    if (request('brand')) $activeCount++;
    if (request('min_price') || request('max_price')) $activeCount++;
    if (request('min_rating')) $activeCount++;
    if (request('min_discount')) $activeCount++;
    if (request('in_stock_only')) $activeCount++;

    $currentMinPrice = request('min_price', '');
    $currentMaxPrice = request('max_price', '');
    $currentRating = request('min_rating', '');
    $currentDiscount = request('min_discount', '');
    $currentCategory = request('category', is_object($activeCategory) ? $activeCategory->slug : (is_string($activeCategory) ? $activeCategory : ''));
    $currentBrand = request('brand', '');
@endphp

<div x-data="{
    openCategory: true,
    openBrand: true,
    openPrice: true,
    openRating: true,
    openDiscount: true,
    openAvailability: true,
    brandSearch: '',
    showAllBrands: false,
    minPrice: '{{ $currentMinPrice }}',
    maxPrice: '{{ $currentMaxPrice }}',

    setPriceRange(min, max) {
        this.minPrice = min !== null ? min : '';
        this.maxPrice = max !== null ? max : '';
        this.$nextTick(() => {
            $refs.filterForm.submit();
        });
    },

    clearPrice() {
        this.minPrice = '';
        this.maxPrice = '';
        this.$nextTick(() => {
            $refs.filterForm.submit();
        });
    }
}" class="w-full">

    <div class="bg-white dark:bg-[#0f1723] rounded-3xl border border-slate-200/80 dark:border-white/10 shadow-soft overflow-hidden transition-all">
        
        <!-- Header: Title, Active Filter Badge, Reset All -->
        <div class="p-4 sm:p-4.5 bg-gradient-to-b from-slate-50/80 to-white dark:from-white/[0.04] dark:to-transparent border-b border-slate-100 dark:border-white/10 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-xl bg-brand-500/10 dark:bg-brand-500/20 text-brand-600 dark:text-brand-400 flex items-center justify-center shrink-0">
                    <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-1.5">
                        <span>Filters</span>
                        @if($activeCount > 0)
                        <span class="px-1.5 py-0.5 rounded-full bg-brand-500 text-white text-[10px] font-black leading-none">
                            {{ $activeCount }}
                        </span>
                        @endif
                    </h3>
                    <p class="text-[10px] text-slate-400 font-medium">Refine your search</p>
                </div>
            </div>

            @if($hasActiveFilters)
            <a href="{{ $actionUrl }}" 
               class="text-[11px] font-bold text-rose-500 hover:text-rose-600 dark:text-rose-400 dark:hover:text-rose-300 transition-colors flex items-center gap-1 px-2 py-1 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/30">
                <i data-lucide="rotate-ccw" class="w-3 h-3"></i>
                <span>Reset All</span>
            </a>
            @endif
        </div>

        <!-- Filter Form -->
        <form x-ref="filterForm" method="GET" action="{{ $actionUrl }}" class="divide-y divide-slate-100 dark:divide-white/5 text-xs">
            @if($query)
            <input type="hidden" name="q" value="{{ $query }}">
            @endif
            @if(request('sort'))
            <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif

            <!-- 1. Categories Accordion -->
            @if(count($categories) > 0 && !is_object($activeCategory))
            <div class="p-4">
                <button type="button" 
                        @click="openCategory = !openCategory" 
                        class="w-full flex items-center justify-between text-left group">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                        <i data-lucide="layers" class="w-3.5 h-3.5 text-brand-500"></i>
                        <span>Category</span>
                        @if($currentCategory)
                        <span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>
                        @endif
                    </span>
                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': openCategory }"></i>
                </button>

                <div x-show="openCategory" x-collapse class="mt-2.5 space-y-1">
                    <label class="flex items-center justify-between py-1.5 px-2 rounded-xl cursor-pointer transition-all hover:bg-slate-50 dark:hover:bg-white/5 {{ !$currentCategory ? 'bg-brand-50/80 dark:bg-brand-950/40 text-brand-600 dark:text-brand-400 font-bold' : 'text-slate-700 dark:text-slate-300' }}">
                        <span class="flex items-center gap-2">
                            <input type="radio" name="category" value="" {{ !$currentCategory ? 'checked' : '' }} onchange="this.form.submit()" class="text-brand-600 focus:ring-brand-500/20 w-3.5 h-3.5">
                            <span class="text-xs">All Categories</span>
                        </span>
                    </label>

                    @foreach($categories as $cat)
                    <label class="flex items-center justify-between py-1.5 px-2 rounded-xl cursor-pointer transition-all hover:bg-slate-50 dark:hover:bg-white/5 {{ $currentCategory === $cat->slug ? 'bg-brand-50/80 dark:bg-brand-950/40 text-brand-600 dark:text-brand-400 font-bold shadow-2xs' : 'text-slate-700 dark:text-slate-300' }}">
                        <span class="flex items-center gap-2 truncate">
                            <input type="radio" name="category" value="{{ $cat->slug }}" {{ $currentCategory === $cat->slug ? 'checked' : '' }} onchange="this.form.submit()" class="text-brand-600 focus:ring-brand-500/20 w-3.5 h-3.5">
                            <span class="text-xs truncate">{{ $cat->name }}</span>
                        </span>
                        @if($currentCategory === $cat->slug)
                        <i data-lucide="check" class="w-3.5 h-3.5 text-brand-500 shrink-0"></i>
                        @endif
                    </label>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- 2. Brands Accordion with Instant Live Search -->
            @if(count($brands) > 0)
            <div class="p-4" x-data="{
                brandItems: @js($brands),
                get filteredBrands() {
                    if (!this.brandSearch.trim()) {
                        return this.showAllBrands ? this.brandItems : this.brandItems.slice(0, 6);
                    }
                    const q = this.brandSearch.toLowerCase();
                    return this.brandItems.filter(b => b.toLowerCase().includes(q));
                }
            }">
                <button type="button" 
                        @click="openBrand = !openBrand" 
                        class="w-full flex items-center justify-between text-left group">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                        <i data-lucide="tag" class="w-3.5 h-3.5 text-brand-500"></i>
                        <span>Brand</span>
                        <span class="text-[10px] text-slate-400 font-normal">({{ count($brands) }})</span>
                        @if($currentBrand)
                        <span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>
                        @endif
                    </span>
                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': openBrand }"></i>
                </button>

                <div x-show="openBrand" x-collapse class="mt-2.5 space-y-2">
                    <!-- Brand Quick Search Filter Input -->
                    @if(count($brands) > 5)
                    <div class="relative">
                        <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5"></i>
                        <input type="text" 
                               x-model="brandSearch" 
                               placeholder="Search {{ count($brands) }} brands..." 
                               class="w-full pl-8 pr-2.5 py-1.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/30 transition-all">
                        <button type="button" x-show="brandSearch" @click="brandSearch = ''" class="absolute right-2 top-2 text-slate-400 hover:text-slate-600">
                            <i data-lucide="x" class="w-3 h-3"></i>
                        </button>
                    </div>
                    @endif

                    <!-- Brand Radio List -->
                    <div class="space-y-1 max-h-48 overflow-y-auto no-scrollbar pr-0.5">
                        <label class="flex items-center justify-between py-1.5 px-2 rounded-xl cursor-pointer transition-all hover:bg-slate-50 dark:hover:bg-white/5 {{ !$currentBrand ? 'bg-brand-50/80 dark:bg-brand-950/40 text-brand-600 dark:text-brand-400 font-bold' : 'text-slate-700 dark:text-slate-300' }}">
                            <span class="flex items-center gap-2">
                                <input type="radio" name="brand" value="" {{ !$currentBrand ? 'checked' : '' }} onchange="this.form.submit()" class="text-brand-600 focus:ring-brand-500/20 w-3.5 h-3.5">
                                <span class="text-xs">All Brands</span>
                            </span>
                        </label>

                        <template x-for="b in filteredBrands" :key="b">
                            <label class="flex items-center justify-between py-1.5 px-2 rounded-xl cursor-pointer transition-all hover:bg-slate-50 dark:hover:bg-white/5"
                                   :class="'{{ $currentBrand }}' === b ? 'bg-brand-50/80 dark:bg-brand-950/40 text-brand-600 dark:text-brand-400 font-bold shadow-2xs' : 'text-slate-700 dark:text-slate-300'">
                                <span class="flex items-center gap-2 truncate">
                                    <input type="radio" 
                                           name="brand" 
                                           :value="b" 
                                           :checked="'{{ $currentBrand }}' === b" 
                                           onchange="this.form.submit()" 
                                           class="text-brand-600 focus:ring-brand-500/20 w-3.5 h-3.5">
                                    <span class="text-xs truncate" x-text="b"></span>
                                </span>
                                <i x-show="'{{ $currentBrand }}' === b" data-lucide="check" class="w-3.5 h-3.5 text-brand-500 shrink-0"></i>
                            </label>
                        </template>

                        <div x-show="filteredBrands.length === 0" class="text-center py-2 text-xs text-slate-400">
                            No brand matching "<span x-text="brandSearch"></span>"
                        </div>
                    </div>

                    <!-- Show More / Less Toggle -->
                    @if(count($brands) > 6)
                    <div x-show="!brandSearch.trim()" class="pt-1">
                        <button type="button" 
                                @click="showAllBrands = !showAllBrands" 
                                class="text-[11px] font-bold text-brand-600 dark:text-brand-400 hover:underline flex items-center gap-1">
                            <span x-text="showAllBrands ? 'Show Less Brands' : '+ Show {{ count($brands) - 6 }} More Brands'"></span>
                        </button>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- 3. Price Range with Quick Budget Pills & Dual Inputs -->
            <div class="p-4">
                <button type="button" 
                        @click="openPrice = !openPrice" 
                        class="w-full flex items-center justify-between text-left group">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                        <i data-lucide="indian-rupee" class="w-3.5 h-3.5 text-brand-500"></i>
                        <span>Price Range</span>
                        @if($currentMinPrice || $currentMaxPrice)
                        <span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>
                        @endif
                    </span>
                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': openPrice }"></i>
                </button>

                <div x-show="openPrice" x-collapse class="mt-2.5 space-y-3">
                    <!-- Quick Budget Preset Pills -->
                    <div>
                        <div class="text-[10px] font-semibold text-slate-400 mb-1.5">Quick Budget:</div>
                        <div class="grid grid-cols-2 gap-1.5">
                            <button type="button" 
                                    @click="setPriceRange(0, 2000)"
                                    class="px-2 py-1.5 rounded-xl border text-[11px] font-semibold transition-all text-center truncate"
                                    :class="minPrice == '0' && maxPrice == '2000' ? 'bg-brand-50 dark:bg-brand-950/60 border-brand-500 text-brand-600 dark:text-brand-400 font-bold' : 'border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-400 hover:border-brand-500/50 hover:bg-slate-50 dark:hover:bg-white/5'">
                                Under ₹2K
                            </button>
                            <button type="button" 
                                    @click="setPriceRange(2000, 10000)"
                                    class="px-2 py-1.5 rounded-xl border text-[11px] font-semibold transition-all text-center truncate"
                                    :class="minPrice == '2000' && maxPrice == '10000' ? 'bg-brand-50 dark:bg-brand-950/60 border-brand-500 text-brand-600 dark:text-brand-400 font-bold' : 'border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-400 hover:border-brand-500/50 hover:bg-slate-50 dark:hover:bg-white/5'">
                                ₹2K - ₹10K
                            </button>
                            <button type="button" 
                                    @click="setPriceRange(10000, 50000)"
                                    class="px-2 py-1.5 rounded-xl border text-[11px] font-semibold transition-all text-center truncate"
                                    :class="minPrice == '10000' && maxPrice == '50000' ? 'bg-brand-50 dark:bg-brand-950/60 border-brand-500 text-brand-600 dark:text-brand-400 font-bold' : 'border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-400 hover:border-brand-500/50 hover:bg-slate-50 dark:hover:bg-white/5'">
                                ₹10K - ₹50K
                            </button>
                            <button type="button" 
                                    @click="setPriceRange(50000, null)"
                                    class="px-2 py-1.5 rounded-xl border text-[11px] font-semibold transition-all text-center truncate"
                                    :class="minPrice == '50000' && !maxPrice ? 'bg-brand-50 dark:bg-brand-950/60 border-brand-500 text-brand-600 dark:text-brand-400 font-bold' : 'border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-400 hover:border-brand-500/50 hover:bg-slate-50 dark:hover:bg-white/5'">
                                Above ₹50K
                            </button>
                        </div>
                    </div>

                    <!-- Custom Price Input Boxes with Currency Symbol -->
                    <div>
                        <div class="text-[10px] font-semibold text-slate-400 mb-1.5">Custom Range:</div>
                        <div class="flex items-center gap-1.5">
                            <div class="relative flex-1">
                                <span class="absolute left-2.5 top-2 text-[11px] text-slate-400 font-mono">₹</span>
                                <input type="number" 
                                       name="min_price" 
                                       x-model="minPrice" 
                                       placeholder="Min" 
                                       class="w-full pl-6 pr-2 py-1.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-xs font-mono text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                            </div>
                            <span class="text-slate-400 font-bold text-xs">-</span>
                            <div class="relative flex-1">
                                <span class="absolute left-2.5 top-2 text-[11px] text-slate-400 font-mono">₹</span>
                                <input type="number" 
                                       name="max_price" 
                                       x-model="maxPrice" 
                                       placeholder="Max" 
                                       class="w-full pl-6 pr-2 py-1.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-xs font-mono text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                            </div>
                            <button type="submit" 
                                    class="h-8 px-3 rounded-xl bg-gradient-to-r from-brand-500 to-emerald-600 hover:from-brand-600 hover:to-emerald-700 text-white font-bold text-xs shadow-glow transition-all active:scale-95 shrink-0">
                                Apply
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Customer Rating Accordion -->
            <div class="p-4">
                <button type="button" 
                        @click="openRating = !openRating" 
                        class="w-full flex items-center justify-between text-left group">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                        <i data-lucide="star" class="w-3.5 h-3.5 text-amber-500"></i>
                        <span>Customer Rating</span>
                        @if($currentRating)
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        @endif
                    </span>
                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': openRating }"></i>
                </button>

                <div x-show="openRating" x-collapse class="mt-2.5 space-y-1">
                    <label class="flex items-center justify-between py-1.5 px-2 rounded-xl cursor-pointer transition-all hover:bg-slate-50 dark:hover:bg-white/5 {{ !$currentRating ? 'bg-brand-50/80 dark:bg-brand-950/40 text-brand-600 dark:text-brand-400 font-bold' : 'text-slate-700 dark:text-slate-300' }}">
                        <span class="flex items-center gap-2">
                            <input type="radio" name="min_rating" value="" {{ !$currentRating ? 'checked' : '' }} onchange="this.form.submit()" class="text-brand-600 focus:ring-brand-500/20 w-3.5 h-3.5">
                            <span class="text-xs">All Ratings</span>
                        </span>
                    </label>

                    @foreach([4 => '4★ & Above', 3 => '3★ & Above', 2 => '2★ & Above'] as $star => $label)
                    <label class="flex items-center justify-between py-1.5 px-2 rounded-xl cursor-pointer transition-all hover:bg-slate-50 dark:hover:bg-white/5 {{ $currentRating == $star ? 'bg-brand-50/80 dark:bg-brand-950/40 text-brand-600 dark:text-brand-400 font-bold shadow-2xs' : 'text-slate-700 dark:text-slate-300' }}">
                        <div class="flex items-center gap-2">
                            <input type="radio" name="min_rating" value="{{ $star }}" {{ $currentRating == $star ? 'checked' : '' }} onchange="this.form.submit()" class="text-brand-600 focus:ring-brand-500/20 w-3.5 h-3.5">
                            <div class="flex items-center gap-1">
                                <div class="flex text-amber-400 text-xs">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="{{ $i <= $star ? 'text-amber-400' : 'text-slate-300 dark:text-slate-700' }}">★</span>
                                    @endfor
                                </div>
                                <span class="text-xs ml-1">{{ $label }}</span>
                            </div>
                        </div>
                        @if($currentRating == $star)
                        <i data-lucide="check" class="w-3.5 h-3.5 text-brand-500 shrink-0"></i>
                        @endif
                    </label>
                    @endforeach
                </div>
            </div>

            <!-- 5. Discount / Special Offers Accordion -->
            <div class="p-4">
                <button type="button" 
                        @click="openDiscount = !openDiscount" 
                        class="w-full flex items-center justify-between text-left group">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                        <i data-lucide="percent" class="w-3.5 h-3.5 text-emerald-500"></i>
                        <span>Discount / Deals</span>
                        @if($currentDiscount)
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        @endif
                    </span>
                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': openDiscount }"></i>
                </button>

                <div x-show="openDiscount" x-collapse class="mt-2.5 space-y-1">
                    <label class="flex items-center justify-between py-1.5 px-2 rounded-xl cursor-pointer transition-all hover:bg-slate-50 dark:hover:bg-white/5 {{ !$currentDiscount ? 'bg-brand-50/80 dark:bg-brand-950/40 text-brand-600 dark:text-brand-400 font-bold' : 'text-slate-700 dark:text-slate-300' }}">
                        <span class="flex items-center gap-2">
                            <input type="radio" name="min_discount" value="" {{ !$currentDiscount ? 'checked' : '' }} onchange="this.form.submit()" class="text-brand-600 focus:ring-brand-500/20 w-3.5 h-3.5">
                            <span class="text-xs">All Discounts</span>
                        </span>
                    </label>

                    @foreach([30 => '30% or more Off', 20 => '20% or more Off', 10 => '10% or more Off'] as $disc => $label)
                    <label class="flex items-center justify-between py-1.5 px-2 rounded-xl cursor-pointer transition-all hover:bg-slate-50 dark:hover:bg-white/5 {{ $currentDiscount == $disc ? 'bg-brand-50/80 dark:bg-brand-950/40 text-brand-600 dark:text-brand-400 font-bold shadow-2xs' : 'text-slate-700 dark:text-slate-300' }}">
                        <span class="flex items-center gap-2">
                            <input type="radio" name="min_discount" value="{{ $disc }}" {{ $currentDiscount == $disc ? 'checked' : '' }} onchange="this.form.submit()" class="text-brand-600 focus:ring-brand-500/20 w-3.5 h-3.5">
                            <span class="text-xs">{{ $label }}</span>
                        </span>
                        <span class="px-1.5 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold">
                            {{ $disc }}%+
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>

            <!-- 6. Availability & Fast Shipping -->
            <div class="p-4">
                <button type="button" 
                        @click="openAvailability = !openAvailability" 
                        class="w-full flex items-center justify-between text-left group">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                        <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-brand-500"></i>
                        <span>Availability</span>
                        @if(request('in_stock_only'))
                        <span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>
                        @endif
                    </span>
                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': openAvailability }"></i>
                </button>

                <div x-show="openAvailability" x-collapse class="mt-2.5">
                    <label class="flex items-center justify-between p-2 rounded-2xl cursor-pointer transition-all bg-slate-50 dark:bg-white/5 hover:border-brand-500 border border-slate-200/60 dark:border-white/5">
                        <div class="flex items-center gap-2">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            <div>
                                <span class="text-xs font-bold text-slate-800 dark:text-white block">In Stock Only</span>
                                <span class="text-[10px] text-slate-400 block">Exclude out-of-stock items</span>
                            </div>
                        </div>
                        <input type="checkbox" 
                               name="in_stock_only" 
                               value="1" 
                               {{ request('in_stock_only') ? 'checked' : '' }} 
                               onchange="this.form.submit()" 
                               class="text-brand-600 rounded-lg w-4 h-4 focus:ring-brand-500/20">
                    </label>
                </div>
            </div>
        </form>
    </div>
</div>
