@props([
    'categories' => [],
    'brands' => [],
    'activeCategory' => null,
    'actionUrl' => route('shop.index'),
    'query' => null,
    'totalProducts' => 0,
    // 'sidebar' = desktop card that applies on change; 'sheet' = mobile bottom-sheet body applied via an external submit button
    'variant' => 'sidebar',
    'formId' => null,
])

@php
    $sheet = $variant === 'sheet';
    $formId = $formId ?? ($sheet ? 'mobile-filter-form' : 'sidebar-filter-form');

    $currentMinPrice = request('min_price', '');
    $currentMaxPrice = request('max_price', '');
    $currentRating = request('min_rating', '');
    $currentDiscount = request('min_discount', '');
    $currentCategory = request('category', is_object($activeCategory) ? $activeCategory->slug : (is_string($activeCategory) ? $activeCategory : ''));
    $currentBrand = request('brand', '');
    $showCategories = count($categories) > 0 && !is_object($activeCategory);

    $activeCount = collect([
        request('category'),
        request('brand'),
        request('min_price') || request('max_price'),
        request('min_rating'),
        request('min_discount'),
        request('in_stock_only'),
    ])->filter()->count();

    $tabs = array_filter([
        'category' => $showCategories ? ['Category', (bool) request('category')] : null,
        'brand' => count($brands) > 0 ? ['Brand', (bool) $currentBrand] : null,
        'price' => ['Price', $currentMinPrice !== '' || $currentMaxPrice !== ''],
        'rating' => ['Rating', (bool) $currentRating],
        'discount' => ['Discount', (bool) $currentDiscount],
        'availability' => ['Availability', (bool) request('in_stock_only')],
    ]);

    $sectionHeader = 'w-full flex items-center justify-between gap-2 text-left';
    $sectionTitle = 'text-[11px] font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-1.5';
    $pill = 'h-8 px-2 rounded-lg border text-[11px] font-semibold transition-colors text-center truncate';
    $pillOn = 'bg-brand-50 dark:bg-brand-500/10 border-brand-500 text-brand-700 dark:text-brand-300';
    $pillOff = 'border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-400 hover:border-brand-400 hover:bg-slate-50 dark:hover:bg-white/5';
@endphp

<div x-data="{
    sheet: @js($sheet),
    tab: @js(array_key_first($tabs)),
    open: { category: true, brand: true, price: true, rating: true, discount: true, availability: true },
    minPrice: @js((string) $currentMinPrice),
    maxPrice: @js((string) $currentMaxPrice),

    submit() {
        this.$nextTick(() => this.$refs.filterForm.submit());
    },
    setPriceRange(min, max) {
        this.minPrice = min !== null ? String(min) : '';
        this.maxPrice = max !== null ? String(max) : '';
        if (!this.sheet) this.submit();
    },
    isRange(min, max) {
        return this.minPrice === String(min ?? '') && this.maxPrice === String(max ?? '');
    }
}" class="{{ $sheet ? 'flex h-full min-h-0' : 'w-full' }}">

    @if($sheet)
    <!-- Sheet: left tab rail -->
    <nav class="w-[34%] max-w-[140px] shrink-0 overflow-y-auto no-scrollbar bg-slate-50 dark:bg-white/[0.03] border-r border-slate-200 dark:border-white/10">
        @foreach($tabs as $key => [$label, $isActive])
        <button type="button"
                @click="tab = '{{ $key }}'"
                class="relative w-full flex items-center justify-between gap-1 px-3 py-3.5 text-left text-xs transition-colors"
                :class="tab === '{{ $key }}' ? 'bg-white dark:bg-[#0b1019] text-brand-700 dark:text-brand-300 font-bold' : 'text-slate-600 dark:text-slate-400 font-medium'">
            <span x-show="tab === '{{ $key }}'" class="absolute left-0 inset-y-2 w-[3px] rounded-r-full bg-brand-500"></span>
            <span class="truncate">{{ $label }}</span>
            @if($isActive)
            <span class="w-1.5 h-1.5 rounded-full bg-brand-500 shrink-0"></span>
            @endif
        </button>
        @endforeach
    </nav>
    @else
    <div class="bg-white dark:bg-[#0f1723] rounded-2xl border border-slate-200/80 dark:border-white/10 shadow-soft overflow-hidden">

        <!-- Header -->
        <div class="px-4 py-3.5 border-b border-slate-100 dark:border-white/10 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-brand-500/10 text-brand-600 dark:text-brand-400 flex items-center justify-center shrink-0">
                    <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                </div>
                <div class="leading-tight">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                        Filters
                        @if($activeCount > 0)
                        <span class="min-w-[18px] h-[18px] px-1 rounded-full bg-brand-500 text-white text-[10px] font-bold inline-flex items-center justify-center">{{ $activeCount }}</span>
                        @endif
                    </h3>
                    <p class="text-[11px] text-slate-400">{{ number_format($totalProducts) }} products</p>
                </div>
            </div>

            @if($activeCount > 0)
            <a href="{{ $actionUrl }}{{ $query ? '?q=' . urlencode($query) : '' }}"
               class="text-[11px] font-bold text-rose-500 hover:text-rose-600 dark:text-rose-400 flex items-center gap-1 px-2 py-1 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors">
                <i data-lucide="rotate-ccw" class="w-3 h-3"></i>
                <span>Clear all</span>
            </a>
            @endif
        </div>
    @endif

        <!-- Filter Form -->
        <form id="{{ $formId }}"
              x-ref="filterForm"
              method="GET"
              action="{{ $actionUrl }}"
              @change="if (!sheet && $event.target.hasAttribute('data-autosubmit')) submit()"
              class="{{ $sheet ? 'flex-1 min-w-0 overflow-y-auto overscroll-contain px-3 py-3' : 'divide-y divide-slate-100 dark:divide-white/5' }}">
            @if($query)
            <input type="hidden" name="q" value="{{ $query }}">
            @endif
            @if(request('sort'))
            <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif

            <!-- 1. Category -->
            @if($showCategories)
            <section x-show="!sheet || tab === 'category'" class="{{ $sheet ? '' : 'p-4' }}">
                @unless($sheet)
                <button type="button" @click="open.category = !open.category" class="{{ $sectionHeader }}">
                    <span class="{{ $sectionTitle }}">
                        <i data-lucide="layers" class="w-3.5 h-3.5 text-brand-500"></i>
                        Category
                        @if($currentCategory)<span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>@endif
                    </span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open.category }"></i>
                </button>
                @endunless

                <div x-show="sheet || open.category" x-collapse>
                    <div class="{{ $sheet ? '' : 'mt-2.5' }} space-y-0.5">
                        <label class="nm-option">
                            <input type="radio" name="category" value="" @checked(!$currentCategory) data-autosubmit class="nm-radio">
                            <span>All Categories</span>
                        </label>
                        @foreach($categories as $cat)
                        <label class="nm-option">
                            <input type="radio" name="category" value="{{ $cat->slug }}" @checked($currentCategory === $cat->slug) data-autosubmit class="nm-radio">
                            <span class="truncate">{{ $cat->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </section>
            @endif

            <!-- 2. Brand (live search) -->
            @if(count($brands) > 0)
            <section x-show="!sheet || tab === 'brand'"
                     class="{{ $sheet ? '' : 'p-4' }}"
                     x-data="{
                        brandItems: @js(array_values(is_array($brands) ? $brands : collect($brands)->all())),
                        selectedBrand: @js((string) $currentBrand),
                        brandSearch: '',
                        showAllBrands: false,
                        get filteredBrands() {
                            const q = this.brandSearch.trim().toLowerCase();
                            if (q) return this.brandItems.filter(b => b.toLowerCase().includes(q));
                            if (this.sheet || this.showAllBrands) return this.brandItems;
                            const list = this.brandItems.slice(0, 6);
                            if (this.selectedBrand && !list.includes(this.selectedBrand)) list.unshift(this.selectedBrand);
                            return list;
                        }
                     }">
                @unless($sheet)
                <button type="button" @click="open.brand = !open.brand" class="{{ $sectionHeader }}">
                    <span class="{{ $sectionTitle }}">
                        <i data-lucide="tag" class="w-3.5 h-3.5 text-brand-500"></i>
                        Brand
                        <span class="text-[10px] text-slate-400 font-medium normal-case tracking-normal">({{ count($brands) }})</span>
                        @if($currentBrand)<span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>@endif
                    </span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open.brand }"></i>
                </button>
                @endunless

                <div x-show="sheet || open.brand" x-collapse>
                    <div class="{{ $sheet ? '' : 'mt-2.5' }} space-y-2">
                        {{-- Radios are unnamed so filtering the list never drops the selection; this carries the value --}}
                        <input type="hidden" name="brand" :value="selectedBrand">

                        @if(count($brands) > 5)
                        <div class="relative">
                            <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                            <input type="text"
                                   x-model="brandSearch"
                                   @keydown.enter.prevent
                                   placeholder="Search brands"
                                   aria-label="Search brands"
                                   class="nm-input pl-8 pr-8">
                            <button type="button" x-show="brandSearch" x-cloak @click="brandSearch = ''" class="absolute right-2 top-1/2 -translate-y-1/2 p-1 rounded-md text-slate-400 hover:text-slate-600 dark:hover:text-white" aria-label="Clear brand search">
                                <i data-lucide="x" class="w-3 h-3"></i>
                            </button>
                        </div>
                        @endif

                        <div class="space-y-0.5 {{ $sheet ? '' : 'max-h-60 overflow-y-auto pr-1' }}">
                            <label class="nm-option" x-show="!brandSearch.trim()">
                                <input type="radio" value="" x-model="selectedBrand" data-autosubmit class="nm-radio">
                                <span>All Brands</span>
                            </label>

                            <template x-for="b in filteredBrands" :key="b">
                                <label class="nm-option">
                                    <input type="radio" :value="b" x-model="selectedBrand" data-autosubmit class="nm-radio">
                                    <span class="truncate" x-text="b"></span>
                                </label>
                            </template>

                            <p x-show="filteredBrands.length === 0" x-cloak class="text-center py-3 text-xs text-slate-400">
                                No brands match "<span x-text="brandSearch"></span>"
                            </p>
                        </div>

                        @if(count($brands) > 6 && !$sheet)
                        <button type="button"
                                x-show="!brandSearch.trim()"
                                @click="showAllBrands = !showAllBrands"
                                class="text-[11px] font-bold text-brand-600 dark:text-brand-400 hover:underline px-2.5"
                                x-text="showAllBrands ? 'Show less' : '+ {{ count($brands) - 6 }} more'"></button>
                        @endif
                    </div>
                </div>
            </section>
            @endif

            <!-- 3. Price -->
            <section x-show="!sheet || tab === 'price'" class="{{ $sheet ? '' : 'p-4' }}">
                @unless($sheet)
                <button type="button" @click="open.price = !open.price" class="{{ $sectionHeader }}">
                    <span class="{{ $sectionTitle }}">
                        <i data-lucide="indian-rupee" class="w-3.5 h-3.5 text-brand-500"></i>
                        Price
                        @if($currentMinPrice !== '' || $currentMaxPrice !== '')<span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>@endif
                    </span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open.price }"></i>
                </button>
                @endunless

                <div x-show="sheet || open.price" x-collapse>
                    <div class="{{ $sheet ? '' : 'mt-3' }} space-y-3">
                        <div class="grid grid-cols-2 gap-1.5">
                            @foreach([[null, 2000, 'Under ₹2K'], [2000, 10000, '₹2K – ₹10K'], [10000, 50000, '₹10K – ₹50K'], [50000, null, 'Above ₹50K']] as [$min, $max, $label])
                            <button type="button"
                                    @click="setPriceRange(@js($min), @js($max))"
                                    class="{{ $pill }}"
                                    :class="isRange(@js($min), @js($max)) ? '{{ $pillOn }}' : '{{ $pillOff }}'">
                                {{ $label }}
                            </button>
                            @endforeach
                        </div>

                        <div>
                            <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Custom range</div>
                            <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-1.5">
                                <div class="relative">
                                    <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none">₹</span>
                                    <input type="number" name="min_price" x-model="minPrice" min="0" inputmode="numeric" placeholder="Min" aria-label="Minimum price" class="nm-input pl-6 pr-2">
                                </div>
                                <span class="text-slate-300 dark:text-slate-600 text-xs">–</span>
                                <div class="relative">
                                    <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none">₹</span>
                                    <input type="number" name="max_price" x-model="maxPrice" min="0" inputmode="numeric" placeholder="Max" aria-label="Maximum price" class="nm-input pl-6 pr-2">
                                </div>
                            </div>
                            @unless($sheet)
                            <button type="submit" class="mt-2 w-full h-9 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs transition-colors active:scale-[0.98]">
                                Apply price
                            </button>
                            @endunless
                        </div>
                    </div>
                </div>
            </section>

            <!-- 4. Rating -->
            <section x-show="!sheet || tab === 'rating'" class="{{ $sheet ? '' : 'p-4' }}">
                @unless($sheet)
                <button type="button" @click="open.rating = !open.rating" class="{{ $sectionHeader }}">
                    <span class="{{ $sectionTitle }}">
                        <i data-lucide="star" class="w-3.5 h-3.5 text-amber-500"></i>
                        Customer Rating
                        @if($currentRating)<span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>@endif
                    </span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open.rating }"></i>
                </button>
                @endunless

                <div x-show="sheet || open.rating" x-collapse>
                    <div class="{{ $sheet ? '' : 'mt-2.5' }} space-y-0.5">
                        <label class="nm-option">
                            <input type="radio" name="min_rating" value="" @checked(!$currentRating) data-autosubmit class="nm-radio">
                            <span>Any rating</span>
                        </label>
                        @foreach([4, 3, 2] as $star)
                        <label class="nm-option">
                            <input type="radio" name="min_rating" value="{{ $star }}" @checked($currentRating == $star) data-autosubmit class="nm-radio">
                            <span class="flex items-center gap-1.5">
                                <span class="text-sm leading-none tracking-tight">
                                    @for($i = 1; $i <= 5; $i++)<span class="{{ $i <= $star ? 'text-amber-400' : 'text-slate-300 dark:text-slate-600' }}">★</span>@endfor
                                </span>
                                <span>&amp; up</span>
                            </span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- 5. Discount -->
            <section x-show="!sheet || tab === 'discount'" class="{{ $sheet ? '' : 'p-4' }}">
                @unless($sheet)
                <button type="button" @click="open.discount = !open.discount" class="{{ $sectionHeader }}">
                    <span class="{{ $sectionTitle }}">
                        <i data-lucide="percent" class="w-3.5 h-3.5 text-emerald-500"></i>
                        Discount
                        @if($currentDiscount)<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>@endif
                    </span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open.discount }"></i>
                </button>
                @endunless

                <div x-show="sheet || open.discount" x-collapse>
                    <div class="{{ $sheet ? '' : 'mt-2.5' }} space-y-0.5">
                        <label class="nm-option">
                            <input type="radio" name="min_discount" value="" @checked(!$currentDiscount) data-autosubmit class="nm-radio">
                            <span>Any discount</span>
                        </label>
                        @foreach([30, 20, 10] as $disc)
                        <label class="nm-option">
                            <input type="radio" name="min_discount" value="{{ $disc }}" @checked($currentDiscount == $disc) data-autosubmit class="nm-radio">
                            <span class="flex-1">{{ $disc }}% or more</span>
                            <span class="px-1.5 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold">{{ $disc }}%+</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- 6. Availability -->
            <section x-show="!sheet || tab === 'availability'" class="{{ $sheet ? '' : 'p-4' }}">
                @unless($sheet)
                <button type="button" @click="open.availability = !open.availability" class="{{ $sectionHeader }}">
                    <span class="{{ $sectionTitle }}">
                        <i data-lucide="package-check" class="w-3.5 h-3.5 text-brand-500"></i>
                        Availability
                        @if(request('in_stock_only'))<span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>@endif
                    </span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open.availability }"></i>
                </button>
                @endunless

                <div x-show="sheet || open.availability" x-collapse>
                    <label class="{{ $sheet ? '' : 'mt-2.5' }} flex items-center justify-between gap-3 p-3 rounded-xl cursor-pointer border border-slate-200 dark:border-white/10 hover:border-brand-400 transition-colors has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50/60 dark:has-[:checked]:bg-brand-500/10">
                        <span class="leading-tight">
                            <span class="text-xs font-bold text-slate-800 dark:text-white block">In stock only</span>
                            <span class="text-[11px] text-slate-400">Hide out-of-stock items</span>
                        </span>
                        <input type="checkbox" name="in_stock_only" value="1" @checked(request('in_stock_only')) data-autosubmit class="nm-switch" role="switch">
                    </label>
                </div>
            </section>
        </form>

    @unless($sheet)
    </div>
    @endunless
</div>
