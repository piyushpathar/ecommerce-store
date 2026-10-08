@props([
    // 'desktop' = category scope + "/" shortcut; 'mobile' = compact full-width bar
    'variant' => 'desktop',
    'categories' => [],
    'placeholder' => 'Search products',
    'popularSearches' => [],
])

@php
    $desktop = $variant === 'desktop';
@endphp

<div x-data="{
        query: @js((string) request('q', '')),
        open: false,
        catOpen: false,
        category: @js((string) request('category', '')),
        categories: @js(collect($categories)->map(fn ($c) => ['slug' => $c->slug, 'name' => $c->name])->values()),
        popular: @js(array_values($popularSearches)),
        results: { products: [], categories: [] },
        loading: false,
        active: -1,
        requestId: 0,

        get categoryName() {
            return this.categories.find(c => c.slug === this.category)?.name ?? 'All';
        },
        get hasQuery() {
            return this.query.trim().length >= 2;
        },
        get items() {
            if (!this.hasQuery) return this.popular.map(p => ({ term: p }));
            return [
                ...this.results.categories.map(c => ({ href: `/category/${c.slug}` })),
                ...this.results.products.map(p => ({ href: `/product/${p.slug}` })),
            ];
        },
        async fetchResults() {
            this.active = -1;
            if (!this.hasQuery) {
                this.results = { products: [], categories: [] };
                return;
            }
            const id = ++this.requestId;
            this.loading = true;
            try {
                const res = await fetch(`/api/search/suggest?q=${encodeURIComponent(this.query.trim())}`);
                const data = await res.json();
                if (id === this.requestId) this.results = data;
            } catch (e) {}
            if (id === this.requestId) this.loading = false;
        },
        move(step) {
            if (!this.open) { this.open = true; return; }
            const n = this.items.length;
            if (!n) return;
            this.active = (this.active + step + n) % n;
            this.$nextTick(() => this.$refs.panel?.querySelector('[data-active=true]')?.scrollIntoView({ block: 'nearest' }));
        },
        choose(e) {
            const item = this.items[this.active];
            if (!this.open || !item) return;
            e.preventDefault();
            if (item.href) window.location.href = item.href;
            else this.pick(item.term);
        },
        pick(term) {
            this.query = term;
            this.$refs.input.focus();
            this.fetchResults();
        },
        clear() {
            this.query = '';
            this.results = { products: [], categories: [] };
            this.active = -1;
            this.$refs.input.focus();
        },
        close() {
            this.open = false;
            this.catOpen = false;
            this.active = -1;
        }
     }"
     @click.outside="close()"
     @if($desktop)
     @keydown.window="if ($event.key === '/' && !$event.metaKey && !$event.ctrlKey && !['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement?.tagName) && !document.activeElement?.isContentEditable) { $event.preventDefault(); $refs.input.focus(); }"
     @endif
     {{ $attributes->merge(['class' => 'relative']) }}>

    <form action="{{ route('search.index') }}" method="GET" role="search"
          class="flex items-stretch {{ $desktop ? 'h-12' : 'h-11' }} p-1 rounded-full bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10 transition-colors hover:border-slate-300 dark:hover:border-white/20 focus-within:!border-brand-500 focus-within:bg-white dark:focus-within:bg-[#0f1723] focus-within:ring-4 focus-within:ring-brand-500/10">

        <input type="hidden" name="category" :value="category" :disabled="!category">

        @if($desktop)
        <!-- Category scope -->
        <button type="button"
                @click="catOpen = !catOpen; open = false"
                :aria-expanded="catOpen"
                class="shrink-0 flex items-center gap-1.5 pl-4 pr-3 rounded-full text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-200/70 dark:hover:bg-white/10 transition-colors">
            <span class="max-w-[96px] truncate" x-text="categoryName">All</span>
            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 transition-transform" :class="{ 'rotate-180': catOpen }"></i>
        </button>
        @endif

        <!-- Input -->
        @if($desktop)<span class="self-center w-px h-5 bg-slate-300 dark:bg-white/15 shrink-0"></span>@endif
        <label class="flex-1 min-w-0 flex items-center gap-2.5 {{ $desktop ? 'pl-3' : 'pl-3' }} pr-2 cursor-text">
            <i data-lucide="search" class="w-4 h-4 text-slate-400 shrink-0"></i>
            <input type="text"
                   name="q"
                   x-ref="input"
                   x-model="query"
                   @input.debounce.200ms="open = true; fetchResults()"
                   @focus="open = true; catOpen = false; if (hasQuery && !results.products.length && !results.categories.length) fetchResults()"
                   @keydown.arrow-down.prevent="move(1)"
                   @keydown.arrow-up.prevent="move(-1)"
                   @keydown.enter="choose($event)"
                   @keydown.escape="close(); $el.blur()"
                   @keydown.tab="close()"
                   placeholder="{{ $placeholder }}"
                   autocomplete="off"
                   spellcheck="false"
                   aria-label="Search products"
                   class="w-full min-w-0 h-full bg-transparent border-0 p-0 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 text-ellipsis focus:outline-none focus:ring-0">
        </label>

        <!-- Clear / shortcut hint -->
        <div class="shrink-0 flex items-center pr-2">
            <button type="button" x-show="query.length" x-cloak @click="clear()" aria-label="Clear search"
                    class="w-7 h-7 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-200/70 dark:hover:text-white dark:hover:bg-white/10 transition-colors">
                <i data-lucide="x" class="w-3.5 h-3.5"></i>
            </button>
            @if($desktop)
            <kbd x-show="!query.length" class="hidden xl:flex items-center justify-center w-5 h-5 rounded-md border border-slate-300 dark:border-white/15 bg-white dark:bg-white/5 text-[10px] font-mono text-slate-400">/</kbd>
            @endif
        </div>

        <button type="submit" aria-label="Search"
                class="shrink-0 aspect-square h-full rounded-full flex items-center justify-center bg-brand-600 hover:bg-brand-500 active:scale-95 text-white shadow-sm transition">
            <i data-lucide="search" class="w-4 h-4"></i>
        </button>
    </form>

    @if($desktop)
    <!-- Category scope menu (outside the form so overflow-hidden can't clip it) -->
    <div x-show="catOpen" x-cloak x-transition.opacity.duration.150ms
         class="absolute left-1 top-full mt-2 w-60 max-h-80 overflow-y-auto nm-thin-scroll bg-white dark:bg-[#121824] rounded-2xl shadow-pop border border-slate-200 dark:border-white/10 p-1.5 z-50">
        <template x-for="opt in [{ slug: '', name: 'All Categories' }, ...categories]" :key="opt.slug">
            <button type="button"
                    @click="category = opt.slug; catOpen = false; $refs.input.focus()"
                    class="w-full flex items-center justify-between gap-2 px-3 py-2 rounded-xl text-xs text-left transition-colors"
                    :class="category === opt.slug ? 'bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-300 font-semibold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5'">
                <span class="truncate" x-text="opt.name"></span>
                <svg x-show="category === opt.slug" class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
            </button>
        </template>
    </div>
    @endif

    <!-- Suggestions panel -->
    <div x-show="open" x-cloak x-ref="panel" x-transition.opacity.duration.150ms
         class="absolute left-0 right-0 top-full mt-2 max-h-[min(70vh,520px)] overflow-y-auto nm-thin-scroll bg-white dark:bg-[#121824] rounded-2xl shadow-pop border border-slate-200 dark:border-white/10 p-2 z-50">

        <!-- Empty query: trending -->
        <template x-if="!hasQuery">
            <div class="p-2" x-show="popular.length">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Trending searches</div>
                <div class="flex flex-wrap gap-1.5">
                    <template x-for="(term, i) in popular" :key="term">
                        <button type="button" @click="pick(term)" :data-active="active === i"
                                class="inline-flex items-center gap-1.5 h-7 px-2.5 rounded-lg text-xs font-medium transition-colors"
                                :class="active === i ? 'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-300' : 'bg-slate-100 dark:bg-white/5 text-slate-700 dark:text-slate-300 hover:bg-brand-50 hover:text-brand-700 dark:hover:bg-brand-500/10 dark:hover:text-brand-300'">
                            <svg class="w-3 h-3 text-brand-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m22 7-8.5 8.5-5-5L2 17"/><path d="M16 7h6v6"/></svg>
                            <span x-text="term"></span>
                        </button>
                    </template>
                </div>
            </div>
        </template>

        <!-- Query: results -->
        <template x-if="hasQuery">
            <div>
                <template x-if="results.categories.length">
                    <div class="mb-1">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 px-2 pt-1 pb-1.5">Categories</div>
                        <template x-for="(cat, i) in results.categories" :key="'c' + cat.id">
                            <a :href="`/category/${cat.slug}`" :data-active="active === i"
                               @mouseenter="active = i"
                               class="flex items-center gap-2.5 px-2 py-2 rounded-xl text-sm text-slate-700 dark:text-slate-200"
                               :class="active === i && 'bg-slate-100 dark:bg-white/5'">
                                <span class="w-8 h-8 rounded-lg bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
                                </span>
                                <span class="truncate font-medium" x-text="cat.name"></span>
                            </a>
                        </template>
                    </div>
                </template>

                <template x-if="results.products.length">
                    <div>
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 px-2 pt-1 pb-1.5">Products</div>
                        <template x-for="(prod, j) in results.products" :key="'p' + prod.id">
                            <a :href="`/product/${prod.slug}`"
                               :data-active="active === results.categories.length + j"
                               @mouseenter="active = results.categories.length + j"
                               class="flex items-center gap-3 px-2 py-1.5 rounded-xl"
                               :class="active === results.categories.length + j && 'bg-slate-100 dark:bg-white/5'">
                                <img :src="prod.thumbnail" alt="" loading="lazy" class="w-10 h-10 rounded-lg object-cover bg-slate-100 dark:bg-white/5 shrink-0">
                                <span class="flex-1 min-w-0">
                                    <span class="block text-sm font-medium text-slate-900 dark:text-slate-100 truncate" x-text="prod.title"></span>
                                    <span class="block text-[11px] text-slate-500 dark:text-slate-400 truncate" x-text="[prod.brand, prod.category_name].filter(Boolean).join(' · ')"></span>
                                </span>
                                <span class="shrink-0 text-xs font-bold font-mono text-slate-900 dark:text-white" x-text="'₹' + Number(prod.price).toLocaleString('en-IN')"></span>
                            </a>
                        </template>
                    </div>
                </template>

                <div x-show="loading && !items.length" class="py-6 text-center text-xs text-slate-400">Searching…</div>
                <div x-show="!loading && !items.length" class="py-6 text-center text-xs text-slate-400">
                    No quick matches. Press Enter to search everything.
                </div>

                <a :href="`{{ route('search.index') }}?q=${encodeURIComponent(query.trim())}`"
                   class="mt-1 flex items-center justify-between gap-2 px-2.5 py-2.5 rounded-xl border-t border-slate-100 dark:border-white/5 text-xs font-semibold text-brand-600 dark:text-brand-400 hover:bg-brand-50 dark:hover:bg-brand-500/10">
                    <span class="truncate">See all results for "<span x-text="query.trim()"></span>"</span>
                    <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </a>
            </div>
        </template>
    </div>
</div>
