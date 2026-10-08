@php
    $slides = \App\Models\Setting::json('hero_slides');
    $totalSlides = count($slides);
@endphp

<div x-data="{
    active: 0,
    total: {{ $totalSlides }},
    timer: null,
    startTimer() {
        this.stopTimer();
        this.timer = setInterval(() => {
            this.active = (this.active + 1) % this.total;
        }, 4500);
    },
    stopTimer() {
        if (this.timer) clearInterval(this.timer);
    },
    next() {
        this.active = (this.active + 1) % this.total;
    },
    prev() {
        this.active = (this.active - 1 + this.total) % this.total;
    }
}" 
x-init="startTimer()" 
@mouseenter="stopTimer()" 
@mouseleave="startTimer()"
class="relative w-full h-44 sm:h-64 md:h-80 lg:h-[340px] overflow-hidden rounded-xl sm:rounded-2xl bg-slate-900 border border-slate-200 dark:border-white/10 shadow-soft select-none group">

    @foreach($slides as $idx => $slide)
    <div class="absolute inset-0 transition-opacity duration-700 ease-in-out"
         :class="active === {{ $idx }} ? 'opacity-100 z-10 pointer-events-auto' : 'opacity-0 z-0 pointer-events-none'">
        <img src="{{ $slide['image'] ?? 'https://images.unsplash.com/photo-1616469829941-c7200edec809?auto=format&fit=crop&w=1600&q=80' }}" 
             alt="{{ $slide['title'] ?? 'NovaMart Deal' }}" 
             class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-r {{ $slide['bg_gradient'] ?? 'from-black/90 via-black/60 to-transparent' }}"></div>
        <div class="absolute inset-0 flex flex-col justify-center px-5 sm:px-10 lg:px-14 max-w-xl text-white space-y-1.5 sm:space-y-3">
            @if(!empty($slide['badge']))
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-brand-500/90 text-white text-[10px] sm:text-xs font-mono font-bold uppercase tracking-wider w-fit">
                {{ $slide['badge'] }}
            </span>
            @endif
            <h2 class="text-lg sm:text-2xl md:text-4xl font-black tracking-tight leading-tight">
                {{ $slide['title'] ?? '' }}
            </h2>
            @if(!empty($slide['subtitle']))
            <p class="text-xs sm:text-sm text-slate-200 line-clamp-2 max-w-md">
                {{ $slide['subtitle'] }}
            </p>
            @endif
            @if(!empty($slide['button_text']) || !empty($slide['btn_text']))
            <div class="pt-1 sm:pt-2">
                <a href="{{ $slide['button_url'] ?? $slide['btn_url'] ?? route('shop.index') }}" 
                   class="inline-flex items-center gap-2 px-3.5 sm:px-5 py-1.5 sm:py-2.5 rounded-xl bg-brand-500 hover:bg-brand-400 text-white font-bold text-xs sm:text-sm shadow-glow transition-all hover:scale-105">
                    <span>{{ $slide['button_text'] ?? $slide['btn_text'] ?? 'Explore Now' }}</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
                </a>
            </div>
            @endif
        </div>
    </div>
    @endforeach

    @if($totalSlides > 1)
    <!-- Left & Right Arrow Navigation Buttons -->
    <button type="button" 
            @click="prev()" 
            aria-label="Previous slide"
            class="absolute left-2.5 sm:left-4 top-1/2 -translate-y-1/2 z-20 w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-black/40 hover:bg-black/70 text-white flex items-center justify-center backdrop-blur-md border border-white/10 opacity-70 group-hover:opacity-100 transition-all hover:scale-110">
        <i data-lucide="chevron-left" class="w-4 h-4 sm:w-5 sm:h-5"></i>
    </button>
    <button type="button" 
            @click="next()" 
            aria-label="Next slide"
            class="absolute right-2.5 sm:right-4 top-1/2 -translate-y-1/2 z-20 w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-black/40 hover:bg-black/70 text-white flex items-center justify-center backdrop-blur-md border border-white/10 opacity-70 group-hover:opacity-100 transition-all hover:scale-110">
        <i data-lucide="chevron-right" class="w-4 h-4 sm:w-5 sm:h-5"></i>
    </button>

    <!-- Slide Indicators / Dots -->
    <div class="absolute bottom-2.5 sm:bottom-4 left-1/2 -translate-x-1/2 z-20 flex items-center gap-1.5 sm:gap-2 bg-black/30 backdrop-blur-md px-2.5 py-1 rounded-full border border-white/10">
        <template x-for="i in total" :key="i">
            <button type="button" 
                    @click="active = i - 1"
                    :aria-label="'Go to slide ' + i"
                    class="h-1.5 rounded-full transition-all duration-300"
                    :class="active === (i - 1) ? 'w-5 sm:w-7 bg-brand-400' : 'w-1.5 sm:w-2 bg-white/40 hover:bg-white/70'"></button>
        </template>
    </div>
    @endif
</div>
