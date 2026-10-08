<?php

/*
|--------------------------------------------------------------------------
| Store Setting Defaults
|--------------------------------------------------------------------------
|
| Single source of truth for every admin-configurable store setting.
| Setting::get() falls back to these values until an admin saves the
| setting from Admin > Settings. Array values are JSON-encoded settings.
|
*/

return [

    'defaults' => [

        // Branding
        'store_name' => 'NovaMart',
        'site_icon_text' => 'NM',
        'site_icon_gradient' => 'from-brand-600 to-teal-400',
        'site_favicon_url' => '',
        'logo_text_prefix' => 'NOVA',
        'logo_text_highlight' => 'MART',
        'logo_subtitle' => 'Marketplace',
        'logo_image_url' => '',

        // Header & Announcement
        'announcement_enabled' => '1',
        'announcement_bar' => '⚡ MEGA SALE FESTIVAL: Flat 10% Off with Code NOVAMART10 · Free 1-Day Express Delivery Across India',
        'announcement_bg' => 'from-brand-800 via-brand-600 to-teal-700',
        'header_menu_items' => [
            ['title' => 'All Products', 'url' => '/products', 'icon' => 'grid'],
            ['title' => 'Flash Deals', 'url' => '/products?sort=sales', 'icon' => 'zap'],
            ['title' => 'Clothing', 'url' => '/category/clothing-fashion', 'icon' => 'shirt'],
            ['title' => 'Home Appliances', 'url' => '/category/home-appliances', 'icon' => 'home'],
            ['title' => 'Track Orders', 'url' => '/account?tab=orders', 'icon' => 'truck'],
        ],

        // Search
        'search_placeholder' => 'Search smartphones, laptops, clothing, appliances...',
        'search_popular_tags' => 'iPhone 16 Pro, MacBook Pro M4, Sony WH-1000XM5, Nike Air Jordan, Dyson V15, Zara Jacket',

        // Homepage
        'hero_slides' => [
            [
                'badge' => '⚡ FLASH SALE · LIMITED INVENTORY',
                'title' => 'iPhone 16 Pro & Galaxy S24 Ultra',
                'subtitle' => 'Experience next-gen titanium craftsmanship, pro camera systems & fastest A18 Pro / Snapdragon 8 Gen 3 chips.',
                'image' => 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?auto=format&fit=crop&w=1200&q=80',
                'button_text' => 'Shop Flagships',
                'button_url' => '/category/smartphones-tablets',
                'bg_gradient' => 'from-slate-950/95 via-slate-900/80 to-transparent',
            ],
            [
                'badge' => 'NEW SEASON ARRIVALS',
                'title' => 'Trendsetting Clothing & Fashion',
                'subtitle' => 'Explore premium trucker jackets, relaxed tailoring, organic tees & luxury statement pieces.',
                'image' => 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?auto=format&fit=crop&w=1200&q=80',
                'button_text' => 'Explore Fashion',
                'button_url' => '/category/clothing-fashion',
                'bg_gradient' => 'from-indigo-950/95 via-slate-900/80 to-transparent',
            ],
            [
                'badge' => 'UP TO 35% OFF',
                'title' => 'Home & Kitchen Appliances',
                'subtitle' => 'Transform your living space with intelligent cordless vacuums, smart air fryers & Italian barista espresso makers.',
                'image' => 'https://images.unsplash.com/photo-1556911220-e15b29be8c8f?auto=format&fit=crop&w=1200&q=80',
                'button_text' => 'Discover Appliances',
                'button_url' => '/category/home-appliances',
                'bg_gradient' => 'from-emerald-950/95 via-slate-900/80 to-transparent',
            ],
            [
                'badge' => 'STUDIO AUDIO MASTERY',
                'title' => 'Sony ANC & Apple AirPods Max',
                'subtitle' => 'Lossless spatial audio, industry-leading active noise cancellation & up to 30 hours of continuous playback.',
                'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=1200&q=80',
                'button_text' => 'Shop Audio',
                'button_url' => '/category/audio-wearables',
                'bg_gradient' => 'from-purple-950/95 via-slate-900/80 to-transparent',
            ],
        ],
        'deals_title' => '⚡ Deals of the Day',
        'deals_subtitle' => 'Limited Time Offers · Up to 40% Off Handpicked Flagships',
        'mobiles_title' => 'Smartphones & Flagship Devices',
        'mobiles_subtitle' => 'Next-gen 5G processors, titanium frames & professional camera systems',
        'laptops_title' => 'Laptops & Creator Workstations',
        'laptops_subtitle' => 'Apple Silicon, Intel Core Ultra, OLED displays & RTX graphics',
        'clothing_title' => 'Trendsetting Clothing & Fashion',
        'clothing_subtitle' => 'Premium denim, structured outerwear, organic cottons & tailoring',
        'appliances_title' => 'Home & Kitchen Appliances',
        'appliances_subtitle' => 'Smart cordless vacuums, precision espresso, smart air fryers & robotics',
        'footwear_title' => 'Footwear & Sneakers',
        'footwear_subtitle' => 'Iconic retro classics, court sneakers & cushioned runners',
        'audio_title' => 'Audio & Wearables',
        'audio_subtitle' => 'Lossless spatial audio, noise cancellation & pro sound systems',

        // Customer Assurance
        'assurance_items' => [
            ['title' => '100% Genuine Brand Sealed', 'desc' => 'Direct from authorized manufacturers with warranty', 'icon' => 'shield-check'],
            ['title' => 'Free Delivery on ₹499+', 'desc' => 'Across 19,000+ Indian pincodes', 'icon' => 'truck'],
            ['title' => '7-Day Easy Replacement', 'desc' => 'Hassle-free return & instant doorstep pickup', 'icon' => 'rotate-ccw'],
            ['title' => 'Razorpay Encrypted Pay', 'desc' => 'UPI, Cards, NetBanking with 256-bit bank security', 'icon' => 'credit-card'],
        ],

        // Footer & Contacts ({year} is replaced with the current year)
        'store_tagline' => 'India\'s Premier Online Marketplace for Flagship Smartphones, Creator Laptops, Audio & Smart Living.',
        'store_email' => 'support@novamart.in',
        'store_phone' => '+91 8000 999 888',
        'store_address' => 'Tower 4, Horizon Tech Hub, SG Highway, Ahmedabad, Gujarat 380054',
        'footer_copyright' => '© {year} NovaMart Marketplace. All rights reserved.',
        'payment_methods_text' => '⚡ UPI / Cards / NetBanking / EMI · 100% Brand Sealed Delivery',
        'social_instagram' => 'https://instagram.com',
        'social_twitter' => 'https://twitter.com',
        'social_youtube' => 'https://youtube.com',

        // Razorpay (mock mode is ignored in production)
        'razorpay_enabled' => '1',
        'razorpay_key_id' => env('RAZORPAY_KEY_ID', ''),
        'razorpay_key_secret' => env('RAZORPAY_KEY_SECRET', ''),
        'razorpay_webhook_secret' => env('RAZORPAY_WEBHOOK_SECRET', ''),
        'razorpay_mock_mode' => '1',

        // Google Sign-In (empty redirect URI = this app's /auth/google/callback)
        'google_login_enabled' => '1',
        'google_client_id' => env('GOOGLE_CLIENT_ID', ''),
        'google_client_secret' => env('GOOGLE_CLIENT_SECRET', ''),
        'google_redirect_uri' => env('GOOGLE_REDIRECT_URI', ''),

        // Homepage section visibility
        'section_hero_carousel_enabled' => '1',
        'section_categories_strip_enabled' => '1',
        'section_deals_enabled' => '1',
        'section_mobiles_enabled' => '1',
        'section_laptops_enabled' => '1',
        'section_clothing_enabled' => '1',
        'section_appliances_enabled' => '1',
        'section_audio_enabled' => '1',
        'section_footwear_enabled' => '1',
        'section_assurance_enabled' => '1',

        // Shipping (INR)
        'free_shipping_threshold' => '499',
        'standard_shipping_fee' => '49',
        'express_shipping_fee' => '99',
    ],

    // Settings rendered as checkboxes in Admin > Settings (unchecked = '0')
    'toggles' => [
        'announcement_enabled',
        'razorpay_enabled',
        'razorpay_mock_mode',
        'google_login_enabled',
        'section_hero_carousel_enabled',
        'section_categories_strip_enabled',
        'section_deals_enabled',
        'section_mobiles_enabled',
        'section_laptops_enabled',
        'section_clothing_enabled',
        'section_appliances_enabled',
        'section_audio_enabled',
        'section_footwear_enabled',
        'section_assurance_enabled',
    ],

    // Settings that must hold a JSON array
    'json' => ['header_menu_items', 'hero_slides', 'assurance_items'],

];
