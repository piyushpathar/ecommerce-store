@props(['prod'])

<div class="group relative bg-white dark:bg-[#0f1723] rounded-xl border border-slate-200/90 dark:border-white/10 hover:border-brand-500/60 hover:shadow-md dark:hover:shadow-brand-500/5 transition-all duration-200 flex flex-col justify-between overflow-hidden h-full">
    <div>
        <!-- Badges on Image -->
        <div class="absolute top-2 left-2 z-10 flex flex-col gap-1 pointer-events-none">
            @if($prod->calculated_discount > 0)
            <span class="bg-rose-600 text-white font-mono font-bold text-[9px] sm:text-[10px] px-1.5 py-0.5 rounded shadow-xs">
                -{{ $prod->calculated_discount }}%
            </span>
            @endif
        </div>

        <div class="absolute top-2 right-2 z-10 flex flex-col gap-1 pointer-events-none">
            @if($prod->is_bestseller)
            <span class="bg-amber-500 text-slate-950 font-bold text-[8px] sm:text-[9px] px-1.5 py-0.5 rounded shadow-xs uppercase tracking-wider">
                Hot
            </span>
            @elseif($prod->is_nova_choice ?? $prod->is_nifty_choice)
            <span class="bg-brand-600 text-white font-bold text-[8px] sm:text-[9px] px-1.5 py-0.5 rounded shadow-xs uppercase tracking-wider">
                Choice
            </span>
            @endif
        </div>

        <!-- Product Image Container: Proper edge-to-edge fill with 1:1 aspect ratio -->
        <a href="{{ route('shop.product', $prod->slug) }}" class="block relative w-full aspect-square overflow-hidden bg-slate-100 dark:bg-slate-800/50">
            <img src="{{ $prod->thumbnail }}" 
                 alt="{{ $prod->title }}" 
                 loading="lazy" 
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
        </a>

        <!-- Content Area -->
        <div class="p-2 sm:p-2.5 space-y-1">
            <!-- Brand & Rating Row -->
            <div class="flex items-center justify-between text-[10px] sm:text-[11px]">
                <span class="font-bold text-brand-600 dark:text-brand-400 uppercase tracking-wide truncate max-w-[80px] sm:max-w-[100px]">
                    {{ $prod->brand }}
                </span>
                <div class="flex items-center gap-0.5 text-amber-500 font-bold">
                    <span>★</span>
                    <span class="text-slate-700 dark:text-slate-300">{{ $prod->rating_avg }}</span>
                    <span class="text-slate-400 text-[9px] hidden sm:inline">({{ $prod->rating_count }})</span>
                </div>
            </div>

            <!-- Product Title (Clamped to 2 lines with fixed height for perfect alignment across row) -->
            <h3 class="text-xs sm:text-[13px] font-semibold text-slate-900 dark:text-white line-clamp-2 leading-snug group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors h-8 sm:h-9">
                <a href="{{ route('shop.product', $prod->slug) }}">
                    {{ $prod->title }}
                </a>
            </h3>
        </div>
    </div>

    <!-- Bottom Price & Quick Add Button -->
    <div class="p-2 sm:p-2.5 pt-0">
        <div class="pt-1.5 border-t border-slate-100 dark:border-white/5 flex items-center justify-between gap-1 mt-auto">
            <div class="min-w-0">
                <div class="text-xs sm:text-sm font-black font-mono text-slate-900 dark:text-white truncate">
                    {{ $prod->formatted_price }}
                </div>
                @if($prod->compare_price)
                <div class="text-[10px] text-slate-400 line-through font-mono truncate">
                    {{ $prod->formatted_compare_price }}
                </div>
                @endif
            </div>

            <form action="{{ route('cart.add') }}" method="POST" class="shrink-0">
                @csrf
                <input type="hidden" name="product_id" value="{{ $prod->_id }}">
                <button type="submit" 
                        class="h-7 w-7 sm:h-8 sm:w-8 rounded-lg bg-brand-50 hover:bg-brand-600 text-brand-600 hover:text-white dark:bg-brand-950/60 dark:hover:bg-brand-500 dark:text-brand-400 dark:hover:text-white border border-brand-200 dark:border-brand-800 transition-all flex items-center justify-center shadow-2xs group/btn" 
                        title="Add to Cart">
                    <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i>
                </button>
            </form>
        </div>
    </div>
</div>
