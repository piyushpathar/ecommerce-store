@if ($paginator->hasPages())
<nav role="navigation" aria-label="Pagination Navigation" class="flex flex-col sm:flex-row items-center justify-between gap-4 py-4">
    <!-- Results Counter -->
    <div class="text-xs text-slate-500 font-mono order-2 sm:order-1">
        Showing
        <span class="font-bold text-slate-900 dark:text-white">{{ $paginator->firstItem() ?? 0 }}</span>
        to
        <span class="font-bold text-slate-900 dark:text-white">{{ $paginator->lastItem() ?? 0 }}</span>
        of
        <span class="font-bold text-slate-900 dark:text-white">{{ $paginator->total() }}</span>
        products
    </div>

    <!-- Navigation Buttons -->
    <div class="flex items-center gap-1.5 order-1 sm:order-2">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-2 rounded-xl bg-slate-100 dark:bg-white/5 text-slate-400 dark:text-slate-600 text-xs font-semibold cursor-not-allowed border border-slate-200/50 dark:border-white/5 flex items-center gap-1">
                <i data-lucide="chevron-left" class="w-3.5 h-3.5"></i>
                <span class="hidden sm:inline">Previous</span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="px-3 py-2 rounded-xl bg-white dark:bg-[#0f1723] hover:bg-brand-50 hover:text-brand-600 dark:hover:bg-brand-950/40 dark:hover:text-brand-400 text-slate-700 dark:text-slate-200 text-xs font-semibold border border-slate-200 dark:border-white/10 shadow-xs transition-colors flex items-center gap-1">
                <i data-lucide="chevron-left" class="w-3.5 h-3.5"></i>
                <span class="hidden sm:inline">Previous</span>
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="px-3 py-2 text-xs text-slate-400 font-mono cursor-default">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page" class="w-9 h-9 rounded-xl bg-brand-600 text-white font-bold text-xs flex items-center justify-center font-mono shadow-soft">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" class="w-9 h-9 rounded-xl bg-white dark:bg-[#0f1723] hover:bg-slate-100 dark:hover:bg-white/10 text-slate-700 dark:text-slate-200 text-xs font-semibold flex items-center justify-center font-mono border border-slate-200 dark:border-white/10 shadow-xs transition-colors">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="px-3 py-2 rounded-xl bg-white dark:bg-[#0f1723] hover:bg-brand-50 hover:text-brand-600 dark:hover:bg-brand-950/40 dark:hover:text-brand-400 text-slate-700 dark:text-slate-200 text-xs font-semibold border border-slate-200 dark:border-white/10 shadow-xs transition-colors flex items-center gap-1">
                <span class="hidden sm:inline">Next</span>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            </a>
        @else
            <span class="px-3 py-2 rounded-xl bg-slate-100 dark:bg-white/5 text-slate-400 dark:text-slate-600 text-xs font-semibold cursor-not-allowed border border-slate-200/50 dark:border-white/5 flex items-center gap-1">
                <span class="hidden sm:inline">Next</span>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            </span>
        @endif
    </div>
</nav>
@endif
