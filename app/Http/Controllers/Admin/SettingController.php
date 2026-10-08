<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $defaultHeroSlides = [
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
        ];

        $defaultHeaderMenuItems = [
            ['title' => 'All Products', 'url' => '/products', 'icon' => 'grid'],
            ['title' => 'Flash Deals', 'url' => '/products?sort=sales', 'icon' => 'zap'],
            ['title' => 'Clothing', 'url' => '/category/clothing-fashion', 'icon' => 'shirt'],
            ['title' => 'Home Appliances', 'url' => '/category/home-appliances', 'icon' => 'home'],
            ['title' => 'Track Orders', 'url' => '/account?tab=orders', 'icon' => 'truck'],
        ];

        $defaultAssuranceItems = [
            ['title' => '100% Genuine Brand Sealed', 'desc' => 'Direct from authorized manufacturers with warranty', 'icon' => 'shield-check'],
            ['title' => 'Free Delivery on ₹499+', 'desc' => 'Across 19,000+ Indian pincodes', 'icon' => 'truck'],
            ['title' => '7-Day Easy Replacement', 'desc' => 'Hassle-free return & instant doorstep pickup', 'icon' => 'rotate-ccw'],
            ['title' => 'Razorpay Encrypted Pay', 'desc' => 'UPI, Cards, NetBanking with 256-bit bank security', 'icon' => 'credit-card'],
        ];

        $settings = [
            // Branding
            'store_name' => Setting::get('store_name', 'NovaMart'),
            'site_icon_text' => Setting::get('site_icon_text', 'NM'),
            'site_icon_gradient' => Setting::get('site_icon_gradient', 'from-brand-600 to-teal-400'),
            'site_favicon_url' => Setting::get('site_favicon_url', ''),
            'logo_text_prefix' => Setting::get('logo_text_prefix', 'NOVA'),
            'logo_text_highlight' => Setting::get('logo_text_highlight', 'MART'),
            'logo_subtitle' => Setting::get('logo_subtitle', 'Marketplace'),
            'logo_image_url' => Setting::get('logo_image_url', ''),

            // Header & Announcement
            'announcement_enabled' => Setting::get('announcement_enabled', '1'),
            'announcement_bar' => Setting::get('announcement_bar', '⚡ MEGA SALE FESTIVAL: Flat 10% Off with Code NOVAMART10 · Free 1-Day Express Delivery Across India'),
            'announcement_bg' => Setting::get('announcement_bg', 'from-brand-800 via-brand-600 to-teal-700'),
            'header_menu_items' => Setting::get('header_menu_items', json_encode($defaultHeaderMenuItems, JSON_PRETTY_PRINT)),

            // Search suggestions & trending
            'search_placeholder' => Setting::get('search_placeholder', 'Search 50,000+ smartphones, laptops, clothing, appliances...'),
            'search_popular_tags' => Setting::get('search_popular_tags', 'iPhone 16 Pro, MacBook Pro M4, Sony WH-1000XM5, Nike Air Jordan, Dyson V15, Zara Jacket'),

            // Homepage texts
            'hero_slides' => Setting::get('hero_slides', json_encode($defaultHeroSlides, JSON_PRETTY_PRINT)),
            'deals_title' => Setting::get('deals_title', '⚡ Deals of the Day'),
            'deals_subtitle' => Setting::get('deals_subtitle', 'Limited Time Offers · Up to 40% Off Handpicked Flagships'),
            'mobiles_title' => Setting::get('mobiles_title', 'Smartphones & Flagship Devices'),
            'mobiles_subtitle' => Setting::get('mobiles_subtitle', 'Next-gen 5G processors, titanium frames & professional camera systems'),
            'laptops_title' => Setting::get('laptops_title', 'Laptops & Creator Workstations'),
            'laptops_subtitle' => Setting::get('laptops_subtitle', 'Apple Silicon, Intel Core Ultra, OLED displays & RTX graphics'),
            'clothing_title' => Setting::get('clothing_title', 'Trendsetting Clothing & Fashion'),
            'clothing_subtitle' => Setting::get('clothing_subtitle', 'Premium denim, structured outerwear, organic cottons & tailoring'),
            'appliances_title' => Setting::get('appliances_title', 'Home & Kitchen Appliances'),
            'appliances_subtitle' => Setting::get('appliances_subtitle', 'Smart cordless vacuums, precision espresso, smart air fryers & robotics'),
            'footwear_title' => Setting::get('footwear_title', 'Footwear & Sneakers'),
            'footwear_subtitle' => Setting::get('footwear_subtitle', 'Iconic retro classics, court sneakers & cushioned runners'),
            'audio_title' => Setting::get('audio_title', 'Audio & Wearables'),
            'audio_subtitle' => Setting::get('audio_subtitle', 'Lossless spatial audio, noise cancellation & pro sound systems'),

            // Customer Assurance
            'assurance_items' => Setting::get('assurance_items', json_encode($defaultAssuranceItems, JSON_PRETTY_PRINT)),

            // Footer & Contacts
            'store_tagline' => Setting::get('store_tagline', 'India\'s Premier Online Marketplace for Flagship Smartphones, Creator Laptops, Audio & Smart Living.'),
            'store_email' => Setting::get('store_email', 'support@novamart.in'),
            'store_phone' => Setting::get('store_phone', '+91 8000 999 888'),
            'store_address' => Setting::get('store_address', 'Tower 4, Horizon Tech Hub, SG Highway, Ahmedabad, Gujarat 380054'),
            'footer_copyright' => Setting::get('footer_copyright', '© ' . date('Y') . ' NovaMart Marketplace. All rights reserved. Powered by Laravel 11 & MySQL.'),
            'payment_methods_text' => Setting::get('payment_methods_text', '⚡ UPI / Cards / NetBanking / EMI · 100% Brand Sealed Delivery'),
            'social_instagram' => Setting::get('social_instagram', 'https://instagram.com'),
            'social_twitter' => Setting::get('social_twitter', 'https://twitter.com'),
            'social_youtube' => Setting::get('social_youtube', 'https://youtube.com'),

            // Payment Gateways (Razorpay)
            'razorpay_enabled' => Setting::get('razorpay_enabled', '1'),
            'razorpay_key_id' => Setting::get('razorpay_key_id', 'rzp_test_NovaMart2026'),
            'razorpay_key_secret' => Setting::get('razorpay_key_secret', 'secret_NovaMartSecret2026'),
            'razorpay_webhook_secret' => Setting::get('razorpay_webhook_secret', 'whsec_novamart_live_webhook_token_99'),
            'razorpay_mock_mode' => Setting::get('razorpay_mock_mode', '1'),

            // Google OAuth & Authentication
            'google_login_enabled' => Setting::get('google_login_enabled', '1'),
            'google_client_id' => Setting::get('google_client_id', '1029384756-mockclientid.apps.googleusercontent.com'),
            'google_client_secret' => Setting::get('google_client_secret', 'GOCSPX-mocksecretvalue998811'),
            'google_redirect_uri' => Setting::get('google_redirect_uri', 'http://127.0.0.1:8000/auth/google/callback'),

            // WordPress-Style Section Visibility Controls (Enable/Disable any section)
            'section_announcement_enabled' => Setting::get('section_announcement_enabled', '1'),
            'section_hero_carousel_enabled' => Setting::get('section_hero_carousel_enabled', '1'),
            'section_categories_strip_enabled' => Setting::get('section_categories_strip_enabled', '1'),
            'section_deals_enabled' => Setting::get('section_deals_enabled', '1'),
            'section_mobiles_enabled' => Setting::get('section_mobiles_enabled', '1'),
            'section_clothing_enabled' => Setting::get('section_clothing_enabled', '1'),
            'section_appliances_enabled' => Setting::get('section_appliances_enabled', '1'),
            'section_laptops_enabled' => Setting::get('section_laptops_enabled', '1'),
            'section_audio_enabled' => Setting::get('section_audio_enabled', '1'),
            'section_footwear_enabled' => Setting::get('section_footwear_enabled', '1'),
            'section_assurance_enabled' => Setting::get('section_assurance_enabled', '1'),
            'section_newsletter_enabled' => Setting::get('section_newsletter_enabled', '1'),

            // Shipping
            'free_shipping_threshold' => Setting::get('free_shipping_threshold', '499'),
            'standard_shipping_fee' => Setting::get('standard_shipping_fee', '49'),
            'express_shipping_fee' => Setting::get('express_shipping_fee', '99'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $textFields = [
            'store_name',
            'site_icon_text',
            'site_icon_gradient',
            'site_favicon_url',
            'logo_text_prefix',
            'logo_text_highlight',
            'logo_subtitle',
            'logo_image_url',
            'announcement_bar',
            'announcement_bg',
            'header_menu_items',
            'search_placeholder',
            'search_popular_tags',
            'hero_slides',
            'deals_title',
            'deals_subtitle',
            'mobiles_title',
            'mobiles_subtitle',
            'laptops_title',
            'laptops_subtitle',
            'clothing_title',
            'clothing_subtitle',
            'appliances_title',
            'appliances_subtitle',
            'audio_title',
            'audio_subtitle',
            'footwear_title',
            'footwear_subtitle',
            'assurance_items',
            'store_tagline',
            'store_email',
            'store_phone',
            'store_address',
            'footer_copyright',
            'payment_methods_text',
            'social_instagram',
            'social_twitter',
            'social_youtube',
            'razorpay_key_id',
            'razorpay_key_secret',
            'razorpay_webhook_secret',
            'google_client_id',
            'google_client_secret',
            'google_redirect_uri',
            'free_shipping_threshold',
            'standard_shipping_fee',
            'express_shipping_fee',
        ];

        foreach ($textFields as $field) {
            if ($request->has($field)) {
                Setting::set($field, $request->input($field));
            }
        }

        // Toggles / Checkboxes (WordPress Style modular switches)
        $toggleFields = [
            'announcement_enabled',
            'razorpay_enabled',
            'razorpay_mock_mode',
            'google_login_enabled',
            'section_announcement_enabled',
            'section_hero_carousel_enabled',
            'section_categories_strip_enabled',
            'section_deals_enabled',
            'section_mobiles_enabled',
            'section_clothing_enabled',
            'section_appliances_enabled',
            'section_laptops_enabled',
            'section_audio_enabled',
            'section_footwear_enabled',
            'section_assurance_enabled',
            'section_newsletter_enabled',
        ];

        foreach ($toggleFields as $toggle) {
            Setting::set($toggle, $request->has($toggle) ? '1' : '0');
        }

        return back()->with('success', 'Store settings and visual layout configurations updated successfully.');
    }
}
