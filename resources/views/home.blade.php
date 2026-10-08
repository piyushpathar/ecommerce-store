@extends('layouts.app')

@section('title', 'NovaMart | Electronics, Smartphones, Laptops & Lifestyle Marketplace')
@section('meta_description', 'Shop flagship 5G smartphones, laptops, audio, and smart home essentials at NovaMart with 1-day express delivery.')

@section('content')
<div class="max-w-[1440px] mx-auto px-2.5 sm:px-4 lg:px-8 py-2 sm:py-4 space-y-3.5 sm:space-y-5">

    <!-- 1. Visual Category Navigation Strip -->
    @if(\App\Models\Setting::get('section_categories_strip_enabled', '1') == '1')
    <x-category-strip :categories="$categories" />
    @endif

    <!-- 2. Hero Promotional Deals Image Carousel -->
    @if(\App\Models\Setting::get('section_hero_carousel_enabled', '1') == '1')
    <x-hero-carousel />
    @endif

    <!-- 3. SECTION: Deals of the Day (Dense Product Grid) -->
    @if(\App\Models\Setting::get('section_deals_enabled', '1') == '1')
    <section class="bg-white dark:bg-[#0f1723] rounded-xl sm:rounded-2xl p-3 sm:p-4 border border-slate-200 dark:border-white/10 shadow-soft space-y-3">
        <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-white/10">
            <div class="flex items-center gap-2">
                <div class="w-2 h-5 rounded-full bg-rose-500"></div>
                <h2 class="text-sm sm:text-base font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <span>{{ \App\Models\Setting::get('deals_title', '⚡ Deals of the Day') }}</span>
                    <span class="hidden sm:inline-flex items-center text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900">
                        {{ \App\Models\Setting::get('deals_subtitle', 'Limited Time Offers') }}
                    </span>
                </h2>
            </div>
            <a href="{{ route('shop.index', ['sort' => 'price_asc']) }}" class="text-xs font-bold text-brand-600 dark:text-brand-400 hover:underline flex items-center gap-1">
                <span>See All Deals</span>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2 sm:gap-3">
            @foreach($dealsOfTheDay as $prod)
                @include('components.product-card', ['prod' => $prod])
            @endforeach
        </div>
    </section>
    @endif

    <!-- 4. SECTION: Smartphones & Flagship Devices -->
    @if(\App\Models\Setting::get('section_mobiles_enabled', '1') == '1')
    <section class="bg-white dark:bg-[#0f1723] rounded-xl sm:rounded-2xl p-3 sm:p-4 border border-slate-200 dark:border-white/10 shadow-soft space-y-3">
        <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-white/10">
            <div class="flex items-center gap-2">
                <div class="w-2 h-5 rounded-full bg-brand-500"></div>
                <h2 class="text-sm sm:text-base font-black text-slate-900 dark:text-white tracking-tight">
                    {{ \App\Models\Setting::get('mobiles_title', 'Smartphones & Flagship Devices') }}
                </h2>
            </div>
            <a href="{{ route('shop.category', 'smartphones-tablets') }}" class="text-xs font-bold text-brand-600 dark:text-brand-400 hover:underline flex items-center gap-1">
                <span>View All Mobiles</span>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2 sm:gap-3">
            @foreach($mobiles as $prod)
                @include('components.product-card', ['prod' => $prod])
            @endforeach
        </div>
    </section>
    @endif

    <!-- 5. SECTION: Clothing & Fashion Apparel -->
    @if(\App\Models\Setting::get('section_clothing_enabled', '1') == '1')
    <section class="bg-white dark:bg-[#0f1723] rounded-xl sm:rounded-2xl p-3 sm:p-4 border border-slate-200 dark:border-white/10 shadow-soft space-y-3">
        <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-white/10">
            <div class="flex items-center gap-2">
                <div class="w-2 h-5 rounded-full bg-emerald-500"></div>
                <h2 class="text-sm sm:text-base font-black text-slate-900 dark:text-white tracking-tight">
                    {{ \App\Models\Setting::get('clothing_title', 'Clothing & Trendsetting Fashion') }}
                </h2>
            </div>
            <a href="{{ route('shop.category', 'clothing-fashion') }}" class="text-xs font-bold text-brand-600 dark:text-brand-400 hover:underline flex items-center gap-1">
                <span>View All Clothing</span>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2 sm:gap-3">
            @foreach($clothing as $prod)
                @include('components.product-card', ['prod' => $prod])
            @endforeach
        </div>
    </section>
    @endif

    <!-- 6. SECTION: Home & Kitchen Appliances -->
    @if(\App\Models\Setting::get('section_appliances_enabled', '1') == '1')
    <section class="bg-white dark:bg-[#0f1723] rounded-xl sm:rounded-2xl p-3 sm:p-4 border border-slate-200 dark:border-white/10 shadow-soft space-y-3">
        <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-white/10">
            <div class="flex items-center gap-2">
                <div class="w-2 h-5 rounded-full bg-amber-500"></div>
                <h2 class="text-sm sm:text-base font-black text-slate-900 dark:text-white tracking-tight">
                    {{ \App\Models\Setting::get('appliances_title', 'Home & Kitchen Appliances') }}
                </h2>
            </div>
            <a href="{{ route('shop.category', 'home-appliances') }}" class="text-xs font-bold text-brand-600 dark:text-brand-400 hover:underline flex items-center gap-1">
                <span>View All Appliances</span>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2 sm:gap-3">
            @foreach($appliances as $prod)
                @include('components.product-card', ['prod' => $prod])
            @endforeach
        </div>
    </section>
    @endif

    <!-- 7. SECTION: Laptops & Pro Displays -->
    @if(\App\Models\Setting::get('section_laptops_enabled', '1') == '1')
    <section class="bg-white dark:bg-[#0f1723] rounded-xl sm:rounded-2xl p-3 sm:p-4 border border-slate-200 dark:border-white/10 shadow-soft space-y-3">
        <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-white/10">
            <div class="flex items-center gap-2">
                <div class="w-2 h-5 rounded-full bg-blue-500"></div>
                <h2 class="text-sm sm:text-base font-black text-slate-900 dark:text-white tracking-tight">
                    {{ \App\Models\Setting::get('laptops_title', 'Laptops & Pro Displays') }}
                </h2>
            </div>
            <a href="{{ route('shop.category', 'laptops-monitors') }}" class="text-xs font-bold text-brand-600 dark:text-brand-400 hover:underline flex items-center gap-1">
                <span>View All Computers</span>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2 sm:gap-3">
            @foreach($laptops as $prod)
                @include('components.product-card', ['prod' => $prod])
            @endforeach
        </div>
    </section>
    @endif

    <!-- 8. SECTION: Audio & Wearables -->
    @if(\App\Models\Setting::get('section_audio_enabled', '1') == '1')
    <section class="bg-white dark:bg-[#0f1723] rounded-xl sm:rounded-2xl p-3 sm:p-4 border border-slate-200 dark:border-white/10 shadow-soft space-y-3">
        <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-white/10">
            <div class="flex items-center gap-2">
                <div class="w-2 h-5 rounded-full bg-teal-500"></div>
                <h2 class="text-sm sm:text-base font-black text-slate-900 dark:text-white tracking-tight">
                    {{ \App\Models\Setting::get('audio_title', 'Audio & Wearables') }}
                </h2>
            </div>
            <a href="{{ route('shop.category', 'audio-wearables') }}" class="text-xs font-bold text-brand-600 dark:text-brand-400 hover:underline flex items-center gap-1">
                <span>View All Audio</span>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2 sm:gap-3">
            @foreach($audio as $prod)
                @include('components.product-card', ['prod' => $prod])
            @endforeach
        </div>
    </section>
    @endif

    <!-- 9. SECTION: Footwear & Sneakers -->
    @if(\App\Models\Setting::get('section_footwear_enabled', '1') == '1')
    <section class="bg-white dark:bg-[#0f1723] rounded-xl sm:rounded-2xl p-3 sm:p-4 border border-slate-200 dark:border-white/10 shadow-soft space-y-3">
        <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-white/10">
            <div class="flex items-center gap-2">
                <div class="w-2 h-5 rounded-full bg-purple-500"></div>
                <h2 class="text-sm sm:text-base font-black text-slate-900 dark:text-white tracking-tight">
                    {{ \App\Models\Setting::get('footwear_title', 'Trending Footwear & Sneakers') }}
                </h2>
            </div>
            <a href="{{ route('shop.category', 'footwear-sneakers') }}" class="text-xs font-bold text-brand-600 dark:text-brand-400 hover:underline flex items-center gap-1">
                <span>View All Footwear</span>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2 sm:gap-3">
            @foreach($footwear as $prod)
                @include('components.product-card', ['prod' => $prod])
            @endforeach
        </div>
    </section>
    @endif

    <!-- 10. Compact Customer Assurance Strip -->
    @if(\App\Models\Setting::get('section_assurance_enabled', '1') == '1')
    @php
        $assuranceJson = \App\Models\Setting::get('assurance_items');
        $assuranceCards = $assuranceJson ? json_decode($assuranceJson, true) : null;
        if (empty($assuranceCards) || !is_array($assuranceCards)) {
            $assuranceCards = [
                ['title' => '100% Genuine', 'desc' => 'Official Brand Sealed', 'icon' => 'shield-check'],
                ['title' => 'Free Delivery', 'desc' => 'On orders above ₹499', 'icon' => 'truck'],
                ['title' => '7-Day Replacement', 'desc' => 'Hassle-free policy', 'icon' => 'refresh-cw'],
                ['title' => 'Razorpay Secured', 'desc' => 'UPI, Cards & NetBanking', 'icon' => 'lock'],
            ];
        }
        $colors = [
            ['bg' => 'bg-brand-50 dark:bg-brand-950/60', 'text' => 'text-brand-600 dark:text-brand-400'],
            ['bg' => 'bg-blue-50 dark:bg-blue-950/60', 'text' => 'text-blue-600 dark:text-blue-400'],
            ['bg' => 'bg-amber-50 dark:bg-amber-950/60', 'text' => 'text-amber-600 dark:text-amber-400'],
            ['bg' => 'bg-emerald-50 dark:bg-emerald-950/60', 'text' => 'text-emerald-600 dark:text-emerald-400'],
        ];
    @endphp
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3">
        @foreach($assuranceCards as $idx => $card)
        @php $color = $colors[$idx % count($colors)]; @endphp
        <div class="bg-white dark:bg-[#0f1723] rounded-xl p-3 border border-slate-200 dark:border-white/10 flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg {{ $color['bg'] }} {{ $color['text'] }} flex items-center justify-center shrink-0">
                <i data-lucide="{{ $card['icon'] ?? 'shield-check' }}" class="w-4 h-4"></i>
            </div>
            <div class="min-w-0">
                <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ $card['title'] ?? '' }}</h4>
                <p class="text-[10px] text-slate-400 truncate">{{ $card['desc'] ?? '' }}</p>
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>
@endsection
