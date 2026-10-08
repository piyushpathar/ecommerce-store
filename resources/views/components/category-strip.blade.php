@props(['categories', 'activeSlug' => null])

<div class="bg-white dark:bg-[#0f1723] rounded-xl sm:rounded-2xl p-2 sm:p-2.5 border border-slate-200 dark:border-white/10 shadow-2xs">
    <div class="flex items-center gap-2 sm:gap-4 lg:gap-6 overflow-x-auto no-scrollbar py-0.5 px-0.5 sm:justify-around">
        @foreach($categories as $cat)
        <a href="{{ route('shop.category', $cat->slug) }}" 
           class="flex flex-col items-center text-center group shrink-0 w-[68px] sm:w-20 md:w-24 py-0.5 rounded-xl transition-all">
            <div class="relative w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16 rounded-full overflow-hidden p-0.5 border-2 transition-all shadow-xs group-hover:scale-105 {{ $activeSlug === $cat->slug ? 'border-brand-500 ring-2 ring-brand-500/40 shadow-glow' : 'border-slate-200 dark:border-white/10 group-hover:border-brand-500' }}">
                <img src="{{ $cat->image }}" 
                     alt="{{ $cat->name }}" 
                     class="w-full h-full object-cover rounded-full bg-slate-100 dark:bg-slate-800 transition-transform duration-300 group-hover:scale-110">
            </div>
            <span class="text-[11px] sm:text-xs font-semibold leading-tight mt-1 sm:mt-1.5 truncate w-full text-center transition-colors {{ $activeSlug === $cat->slug ? 'text-brand-600 dark:text-brand-400 font-extrabold' : 'text-slate-700 dark:text-slate-300 group-hover:text-brand-600 dark:group-hover:text-brand-400' }}">
                {{ $cat->name }}
            </span>
        </a>
        @endforeach

        <a href="{{ route('shop.index') }}" 
           class="flex flex-col items-center text-center group shrink-0 w-[68px] sm:w-20 md:w-24 py-0.5 rounded-xl transition-all">
            <div class="w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16 rounded-full bg-gradient-to-tr from-brand-600 to-teal-400 text-white flex items-center justify-center transition-all shadow-xs group-hover:scale-105 p-0.5 border-2 {{ $activeSlug === 'all' ? 'border-brand-400 ring-2 ring-brand-500/40 shadow-glow' : 'border-transparent group-hover:border-brand-400' }}">
                <i data-lucide="grid" class="w-5 h-5 sm:w-6 sm:h-6"></i>
            </div>
            <span class="text-[11px] sm:text-xs font-semibold leading-tight mt-1 sm:mt-1.5 truncate w-full text-center transition-colors {{ $activeSlug === 'all' ? 'text-brand-600 dark:text-brand-400 font-extrabold' : 'text-slate-700 dark:text-slate-300 group-hover:text-brand-600 dark:group-hover:text-brand-400' }}">
                All Products
            </span>
        </a>
    </div>
</div>
