@extends('layouts.admin')

@section('title', 'Visual Site Customizer & Store Settings | Admin')

@section('content')
<div class="space-y-6" x-data="{
    activeTab: 'branding',
    logoPrefix: @js($settings['logo_text_prefix']),
    logoHighlight: @js($settings['logo_text_highlight']),
    logoSubtitle: @js($settings['logo_subtitle']),
    logoImageUrl: @js($settings['logo_image_url']),
    iconText: @js($settings['site_icon_text']),
    announcementText: @js($settings['announcement_bar']),
    announcementEnabled: {{ $settings['announcement_enabled'] ? 'true' : 'false' }},
}">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <i data-lucide="palette" class="w-5 h-5 text-brand-400"></i>
                <span>Visual Site Customizer & Dynamic Settings</span>
            </h2>
            <p class="text-xs text-slate-400">Control site logo, icons, menus, search suggestions, homepage texts, and store policies</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('home') }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 font-bold text-xs flex items-center gap-1.5">
                <i data-lucide="external-link" class="w-4 h-4"></i>
                <span>View Live Site</span>
            </a>
        </div>
    </div>

    <!-- Tabbed Navigation Bar -->
    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar p-1.5 bg-[#0c1117] rounded-2xl border border-white/10 text-xs">
        <button type="button" 
                @click="activeTab = 'branding'" 
                class="px-4 py-2 rounded-xl font-bold flex items-center gap-2 transition-all shrink-0"
                :class="activeTab === 'branding' ? 'bg-brand-500 text-white shadow-glow' : 'text-slate-400 hover:text-white hover:bg-white/5'">
            <i data-lucide="image" class="w-4 h-4"></i>
            <span>Logo & Site Icon</span>
        </button>

        <button type="button" 
                @click="activeTab = 'header'" 
                class="px-4 py-2 rounded-xl font-bold flex items-center gap-2 transition-all shrink-0"
                :class="activeTab === 'header' ? 'bg-brand-500 text-white shadow-glow' : 'text-slate-400 hover:text-white hover:bg-white/5'">
            <i data-lucide="menu" class="w-4 h-4"></i>
            <span>Announcement & Menus</span>
        </button>

        <button type="button" 
                @click="activeTab = 'search'" 
                class="px-4 py-2 rounded-xl font-bold flex items-center gap-2 transition-all shrink-0"
                :class="activeTab === 'search' ? 'bg-brand-500 text-white shadow-glow' : 'text-slate-400 hover:text-white hover:bg-white/5'">
            <i data-lucide="search" class="w-4 h-4"></i>
            <span>Search & Suggestions</span>
        </button>

        <button type="button" 
                @click="activeTab = 'homepage'" 
                class="px-4 py-2 rounded-xl font-bold flex items-center gap-2 transition-all shrink-0"
                :class="activeTab === 'homepage' ? 'bg-brand-500 text-white shadow-glow' : 'text-slate-400 hover:text-white hover:bg-white/5'">
            <i data-lucide="home" class="w-4 h-4"></i>
            <span>Homepage & Banners</span>
        </button>

        <button type="button" 
                @click="activeTab = 'assurance'" 
                class="px-4 py-2 rounded-xl font-bold flex items-center gap-2 transition-all shrink-0"
                :class="activeTab === 'assurance' ? 'bg-brand-500 text-white shadow-glow' : 'text-slate-400 hover:text-white hover:bg-white/5'">
            <i data-lucide="shield-check" class="w-4 h-4"></i>
            <span>Trust & Assurance</span>
        </button>

        <button type="button" 
                @click="activeTab = 'footer'" 
                class="px-4 py-2 rounded-xl font-bold flex items-center gap-2 transition-all shrink-0"
                :class="activeTab === 'footer' ? 'bg-brand-500 text-white shadow-glow' : 'text-slate-400 hover:text-white hover:bg-white/5'">
            <i data-lucide="phone" class="w-4 h-4"></i>
            <span>Footer & Contacts</span>
        </button>

        <button type="button" 
                @click="activeTab = 'gateway'" 
                class="px-4 py-2 rounded-xl font-bold flex items-center gap-2 transition-all shrink-0"
                :class="activeTab === 'gateway' ? 'bg-brand-500 text-white shadow-glow' : 'text-slate-400 hover:text-white hover:bg-white/5'">
            <i data-lucide="credit-card" class="w-4 h-4"></i>
            <span>Gateway & Shipping</span>
        </button>
    </div>

    <!-- Main Settings Form -->
    <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6 text-xs">
        @csrf

        <!-- TAB 1: Branding & Visuals -->
        <div x-show="activeTab === 'branding'" x-cloak class="space-y-6">
            
            <!-- Live Preview Card -->
            <div class="p-6 rounded-3xl bg-gradient-to-r from-slate-900 to-[#0c1117] border border-white/10 shadow-soft">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-3">Live Header Branding Preview:</span>
                <div class="p-4 rounded-2xl bg-white/95 dark:bg-[#0c1117]/95 border border-slate-200 dark:border-white/10 flex items-center gap-4 inline-flex">
                    <template x-if="!logoImageUrl">
                        <div class="flex items-center gap-2.5">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-teal-400 flex items-center justify-center text-white font-mono font-black text-xl shadow-glow">
                                <span x-text="iconText || 'NM'"></span>
                            </div>
                            <div class="flex flex-col">
                                <span class="font-extrabold text-xl tracking-tight text-slate-900 dark:text-white">
                                    <span x-text="logoPrefix || 'NOVA'"></span><span class="text-brand-500" x-text="logoHighlight || 'MART'"></span>
                                </span>
                                <span class="text-[10px] text-slate-500 dark:text-slate-400 font-medium tracking-wide -mt-0.5" x-text="logoSubtitle || 'Marketplace'"></span>
                            </div>
                        </div>
                    </template>
                    <template x-if="logoImageUrl">
                        <img :src="logoImageUrl" alt="Logo Preview" class="h-10 object-contain">
                    </template>
                </div>
            </div>

            <!-- Fields -->
            <div class="p-6 rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft space-y-4">
                <h3 class="text-sm font-bold text-white border-b border-white/10 pb-3">Logo Configuration</h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="font-bold text-slate-300 block mb-1">Store Name *</label>
                        <input type="text" name="store_name" value="{{ $settings['store_name'] }}" required class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white text-xs">
                    </div>
                    <div>
                        <label class="font-bold text-slate-300 block mb-1">Logo Text (Prefix)</label>
                        <input type="text" name="logo_text_prefix" x-model="logoPrefix" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white text-xs">
                    </div>
                    <div>
                        <label class="font-bold text-slate-300 block mb-1">Logo Text (Highlighted Accent)</label>
                        <input type="text" name="logo_text_highlight" x-model="logoHighlight" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="font-bold text-slate-300 block mb-1">Logo Subtitle / Descriptor</label>
                        <input type="text" name="logo_subtitle" x-model="logoSubtitle" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white text-xs">
                    </div>
                    <div>
                        <label class="font-bold text-slate-300 block mb-1">Custom Image Logo URL (Optional)</label>
                        <input type="url" name="logo_image_url" x-model="logoImageUrl" placeholder="https://.../logo.png" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white text-xs font-mono">
                    </div>
                </div>
            </div>

            <div class="p-6 rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft space-y-4">
                <h3 class="text-sm font-bold text-white border-b border-white/10 pb-3">Site Icon & Favicon</h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="font-bold text-slate-300 block mb-1">Site Icon Initials (2-3 chars)</label>
                        <input type="text" name="site_icon_text" x-model="iconText" maxlength="4" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white text-xs font-mono">
                    </div>
                    <div>
                        <label class="font-bold text-slate-300 block mb-1">Icon Gradient Palette</label>
                        <select name="site_icon_gradient" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white text-xs">
                            <option value="from-brand-600 to-teal-400" {{ $settings['site_icon_gradient'] === 'from-brand-600 to-teal-400' ? 'selected' : '' }}>Emerald to Teal (Default)</option>
                            <option value="from-indigo-600 to-violet-500" {{ $settings['site_icon_gradient'] === 'from-indigo-600 to-violet-500' ? 'selected' : '' }}>Indigo to Violet</option>
                            <option value="from-rose-600 to-amber-500" {{ $settings['site_icon_gradient'] === 'from-rose-600 to-amber-500' ? 'selected' : '' }}>Rose to Amber</option>
                            <option value="from-cyan-600 to-blue-500" {{ $settings['site_icon_gradient'] === 'from-cyan-600 to-blue-500' ? 'selected' : '' }}>Cyan to Blue</option>
                        </select>
                    </div>
                    <div>
                        <label class="font-bold text-slate-300 block mb-1">Custom Favicon URL (.ico / .png / .svg)</label>
                        <input type="url" name="site_favicon_url" value="{{ $settings['site_favicon_url'] }}" placeholder="https://.../favicon.png" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white text-xs font-mono">
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: Announcement & Header Menus -->
        <div x-show="activeTab === 'header'" x-cloak class="space-y-6">
            
            <!-- Live Announcement Bar Preview -->
            <div x-show="announcementEnabled" class="p-3 rounded-2xl bg-gradient-to-r from-brand-800 via-brand-600 to-teal-700 text-white text-xs text-center font-medium shadow-xs">
                <span x-text="announcementText"></span>
            </div>

            <div class="p-6 rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft space-y-4">
                <div class="flex items-center justify-between border-b border-white/10 pb-3">
                    <h3 class="text-sm font-bold text-white">Top Announcement Banner</h3>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <span class="text-xs text-slate-300 font-bold">Enable Banner</span>
                        <input type="checkbox" name="announcement_enabled" value="1" x-model="announcementEnabled" class="text-brand-600 rounded w-4 h-4">
                    </label>
                </div>

                <div>
                    <label class="font-bold text-slate-300 block mb-1">Announcement Message *</label>
                    <input type="text" name="announcement_bar" x-model="announcementText" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white text-xs">
                </div>
            </div>

            <div class="p-6 rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft space-y-4">
                <h3 class="text-sm font-bold text-white border-b border-white/10 pb-3">Navigation Menu Items (JSON Format)</h3>
                <p class="text-xs text-slate-400">Configure custom navigation links appearing in header navigation bars.</p>

                <textarea name="header_menu_items" rows="7" class="w-full p-4 bg-white/5 border border-white/10 rounded-2xl text-white font-mono text-xs leading-relaxed">{{ $settings['header_menu_items'] }}</textarea>
            </div>
        </div>

        <!-- TAB 3: Search Bar & Suggestions -->
        <div x-show="activeTab === 'search'" x-cloak class="space-y-6">
            <div class="p-6 rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft space-y-4">
                <h3 class="text-sm font-bold text-white border-b border-white/10 pb-3">Search Experience & Auto-Suggestions</h3>

                <div class="space-y-4">
                    <div>
                        <label class="font-bold text-slate-300 block mb-1">Global Search Input Placeholder Text</label>
                        <input type="text" name="search_placeholder" value="{{ $settings['search_placeholder'] }}" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white text-xs">
                    </div>

                    <div>
                        <label class="font-bold text-slate-300 block mb-1">Popular / Trending Search Tags (Comma separated)</label>
                        <input type="text" name="search_popular_tags" value="{{ $settings['search_popular_tags'] }}" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white text-xs">
                        <span class="text-[11px] text-slate-400 block mt-1">Shown in the search dropdown popover to guide shoppers to top trends.</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 4: Homepage Sections & Carousel Banners -->
        <div x-show="activeTab === 'homepage'" x-cloak class="space-y-6">
            <div class="p-6 rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft space-y-4">
                <h3 class="text-sm font-bold text-white border-b border-white/10 pb-3">Hero Deals Image Carousel (JSON Slides)</h3>
                <p class="text-xs text-slate-400">Configure deals carousel slides with custom headline, subtitle, high-res image, and button link.</p>

                <textarea name="hero_slides" rows="12" class="w-full p-4 bg-white/5 border border-white/10 rounded-2xl text-white font-mono text-xs leading-relaxed">{{ $settings['hero_slides'] }}</textarea>
            </div>

            <div class="p-6 rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft space-y-4">
                <h3 class="text-sm font-bold text-white border-b border-white/10 pb-3">Homepage Section Headings & Subtitles</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="font-bold text-slate-300 block mb-1">Deals of the Day Heading</label>
                        <input type="text" name="deals_title" value="{{ $settings['deals_title'] }}" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-xl text-white text-xs">
                    </div>
                    <div>
                        <label class="font-bold text-slate-300 block mb-1">Deals of the Day Subtitle</label>
                        <input type="text" name="deals_subtitle" value="{{ $settings['deals_subtitle'] }}" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-xl text-white text-xs">
                    </div>

                    <div>
                        <label class="font-bold text-slate-300 block mb-1">Smartphones Section Heading</label>
                        <input type="text" name="mobiles_title" value="{{ $settings['mobiles_title'] }}" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-xl text-white text-xs">
                    </div>
                    <div>
                        <label class="font-bold text-slate-300 block mb-1">Smartphones Section Subtitle</label>
                        <input type="text" name="mobiles_subtitle" value="{{ $settings['mobiles_subtitle'] }}" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-xl text-white text-xs">
                    </div>

                    <div>
                        <label class="font-bold text-slate-300 block mb-1">Laptops Section Heading</label>
                        <input type="text" name="laptops_title" value="{{ $settings['laptops_title'] }}" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-xl text-white text-xs">
                    </div>
                    <div>
                        <label class="font-bold text-slate-300 block mb-1">Laptops Section Subtitle</label>
                        <input type="text" name="laptops_subtitle" value="{{ $settings['laptops_subtitle'] }}" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-xl text-white text-xs">
                    </div>

                    <div>
                        <label class="font-bold text-slate-300 block mb-1">Clothing & Fashion Heading</label>
                        <input type="text" name="clothing_title" value="{{ $settings['clothing_title'] }}" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-xl text-white text-xs">
                    </div>
                    <div>
                        <label class="font-bold text-slate-300 block mb-1">Clothing & Fashion Subtitle</label>
                        <input type="text" name="clothing_subtitle" value="{{ $settings['clothing_subtitle'] }}" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-xl text-white text-xs">
                    </div>

                    <div>
                        <label class="font-bold text-slate-300 block mb-1">Home Appliances Heading</label>
                        <input type="text" name="appliances_title" value="{{ $settings['appliances_title'] }}" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-xl text-white text-xs">
                    </div>
                    <div>
                        <label class="font-bold text-slate-300 block mb-1">Home Appliances Subtitle</label>
                        <input type="text" name="appliances_subtitle" value="{{ $settings['appliances_subtitle'] }}" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-xl text-white text-xs">
                    </div>

                    <div>
                        <label class="font-bold text-slate-300 block mb-1">Footwear Section Heading</label>
                        <input type="text" name="footwear_title" value="{{ $settings['footwear_title'] }}" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-xl text-white text-xs">
                    </div>
                    <div>
                        <label class="font-bold text-slate-300 block mb-1">Footwear Section Subtitle</label>
                        <input type="text" name="footwear_subtitle" value="{{ $settings['footwear_subtitle'] }}" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-xl text-white text-xs">
                    </div>

                    <div>
                        <label class="font-bold text-slate-300 block mb-1">Audio & Wearables Section Heading</label>
                        <input type="text" name="audio_title" value="{{ $settings['audio_title'] }}" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-xl text-white text-xs">
                    </div>
                    <div>
                        <label class="font-bold text-slate-300 block mb-1">Audio & Wearables Section Subtitle</label>
                        <input type="text" name="audio_subtitle" value="{{ $settings['audio_subtitle'] }}" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-xl text-white text-xs">
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 5: Trust Badges & Customer Assurance -->
        <div x-show="activeTab === 'assurance'" x-cloak class="space-y-6">
            <div class="p-6 rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft space-y-4">
                <h3 class="text-sm font-bold text-white border-b border-white/10 pb-3">Customer Assurance Badges (JSON Format)</h3>
                <p class="text-xs text-slate-400">Configure the 4 customer assurance badges shown on homepage and checkout.</p>

                <textarea name="assurance_items" rows="10" class="w-full p-4 bg-white/5 border border-white/10 rounded-2xl text-white font-mono text-xs leading-relaxed">{{ $settings['assurance_items'] }}</textarea>
            </div>
        </div>

        <!-- TAB 6: Footer, Contacts & Social Media -->
        <div x-show="activeTab === 'footer'" x-cloak class="space-y-6">
            <div class="p-6 rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft space-y-4">
                <h3 class="text-sm font-bold text-white border-b border-white/10 pb-3">Footer Contact & Store Details</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="font-bold text-slate-300 block mb-1">Support Helpline</label>
                        <input type="text" name="store_phone" value="{{ $settings['store_phone'] }}" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white font-mono text-xs">
                    </div>
                    <div>
                        <label class="font-bold text-slate-300 block mb-1">Support Email</label>
                        <input type="email" name="store_email" value="{{ $settings['store_email'] }}" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white font-mono text-xs">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="font-bold text-slate-300 block mb-1">Store Tagline / Description</label>
                        <input type="text" name="store_tagline" value="{{ $settings['store_tagline'] }}" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white text-xs">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="font-bold text-slate-300 block mb-1">Corporate HQ Address</label>
                        <input type="text" name="store_address" value="{{ $settings['store_address'] }}" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white text-xs">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="font-bold text-slate-300 block mb-1">Copyright Footer Text</label>
                        <input type="text" name="footer_copyright" value="{{ $settings['footer_copyright'] }}" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white text-xs">
                    </div>
                </div>

                <div class="pt-4 border-t border-white/10">
                    <h4 class="text-xs font-bold text-white mb-3">Social Media Profiles</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="font-bold text-slate-300 block mb-1">Instagram URL</label>
                            <input type="url" name="social_instagram" value="{{ $settings['social_instagram'] }}" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-xl text-white text-xs font-mono">
                        </div>
                        <div>
                            <label class="font-bold text-slate-300 block mb-1">X (Twitter) URL</label>
                            <input type="url" name="social_twitter" value="{{ $settings['social_twitter'] }}" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-xl text-white text-xs font-mono">
                        </div>
                        <div>
                            <label class="font-bold text-slate-300 block mb-1">YouTube URL</label>
                            <input type="url" name="social_youtube" value="{{ $settings['social_youtube'] }}" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-xl text-white text-xs font-mono">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 7: Payment Gateway & Shipping -->
        <div x-show="activeTab === 'gateway'" x-cloak class="space-y-6">
            <div class="p-6 rounded-3xl bg-[#0c1117] border border-brand-500/30 shadow-soft space-y-4">
                <div class="flex items-center justify-between border-b border-white/10 pb-3">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i data-lucide="credit-card" class="w-4 h-4 text-brand-400"></i>
                        <span>Razorpay Payment Gateway Setup</span>
                    </h3>
                    <span class="text-[10px] font-mono bg-brand-500/20 text-brand-400 font-bold px-2 py-0.5 rounded border border-brand-500/30">
                        256-Bit SSL
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="font-bold text-slate-300 block mb-1">Razorpay Key ID</label>
                        <input type="text" name="razorpay_key_id" value="{{ $settings['razorpay_key_id'] }}" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white font-mono text-xs">
                    </div>
                    <div>
                        <label class="font-bold text-slate-300 block mb-1">Razorpay Key Secret</label>
                        <input type="password" name="razorpay_key_secret" value="{{ $settings['razorpay_key_secret'] }}" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white font-mono text-xs">
                    </div>
                </div>

                <div class="p-3.5 rounded-2xl bg-white/5 border border-white/5 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-slate-200 block text-xs">Developer Sandbox / Mock Checkout Mode</span>
                        <span class="text-[11px] text-slate-400">Allows instant simulated test orders without requiring live banking or active test card charges.</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="razorpay_mock_mode" value="1" {{ $settings['razorpay_mock_mode'] ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-600"></div>
                    </label>
                </div>
            </div>

            <div class="p-6 rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft space-y-4">
                <h3 class="text-sm font-bold text-white border-b border-white/10 pb-3">Shipping & Delivery Fees (INR)</h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 font-mono">
                    <div>
                        <label class="font-bold text-slate-300 font-sans block mb-1">Free Delivery Threshold (₹)</label>
                        <input type="number" step="0.01" name="free_shipping_threshold" value="{{ $settings['free_shipping_threshold'] }}" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white text-xs">
                    </div>
                    <div>
                        <label class="font-bold text-slate-300 font-sans block mb-1">Standard Delivery Fee (₹)</label>
                        <input type="number" step="0.01" name="standard_shipping_fee" value="{{ $settings['standard_shipping_fee'] }}" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white text-xs">
                    </div>
                    <div>
                        <label class="font-bold text-slate-300 font-sans block mb-1">Express 1-Day Delivery Fee (₹)</label>
                        <input type="number" step="0.01" name="express_shipping_fee" value="{{ $settings['express_shipping_fee'] }}" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white text-xs">
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Save Button -->
        <div class="sticky bottom-4 p-4 rounded-2xl bg-[#0c1117]/95 backdrop-blur-md border border-brand-500/30 shadow-2xl flex items-center justify-between">
            <span class="text-xs text-slate-400">All changes apply dynamically to the live marketplace instantly.</span>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-brand-500 to-emerald-600 hover:from-brand-600 hover:to-emerald-700 text-white font-bold text-xs shadow-glow transition-all hover:scale-[1.02] active:scale-95 flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Save All Visual Settings</span>
            </button>
        </div>
    </form>
</div>
@endsection
