<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Plan;
use App\Models\Review;
use App\Models\Setting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get();

        $featuredProducts = Product::where('is_active', true)
            ->where('is_featured', true)
            ->limit(8)
            ->get();

        $bestSellers = Product::where('is_active', true)
            ->where('is_bestseller', true)
            ->limit(8)
            ->get();

        $novaChoices = Product::where('is_active', true)
            ->where('is_nova_choice', true)
            ->limit(8)
            ->get();

        $newArrivals = Product::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        $dealsOfTheDay = Product::where('is_active', true)
            ->where('discount_percentage', '>', 0)
            ->orderBy('discount_percentage', 'desc')
            ->limit(6)
            ->get();

        $mobiles = Product::where('category_slug', 'smartphones-tablets')
            ->where('is_active', true)
            ->limit(6)
            ->get();

        $laptops = Product::where('category_slug', 'laptops-monitors')
            ->where('is_active', true)
            ->limit(6)
            ->get();

        $clothing = Product::where('category_slug', 'clothing-fashion')
            ->where('is_active', true)
            ->limit(6)
            ->get();

        $appliances = Product::where('category_slug', 'home-appliances')
            ->where('is_active', true)
            ->limit(6)
            ->get();

        $audio = Product::where('category_slug', 'audio-wearables')
            ->where('is_active', true)
            ->limit(6)
            ->get();

        $footwear = Product::where('category_slug', 'footwear-sneakers')
            ->where('is_active', true)
            ->limit(6)
            ->get();

        $beauty = Product::where('category_slug', 'beauty-grooming')
            ->where('is_active', true)
            ->limit(6)
            ->get();

        $fashion = $clothing; // alias for backwards compatibility
        $homeProducts = $appliances; // alias for backwards compatibility

        $reviews = Review::where('is_approved', true)
            ->where('rating', '>=', 4)
            ->limit(4)
            ->get();

        $announcement = Setting::get('announcement_bar', '⚡ MEGA SALE FESTIVAL: Flat 10% Off with Code NOVAMART10 · Free 1-Day Express Delivery Across India');

        return view('home', compact(
            'categories',
            'featuredProducts',
            'bestSellers',
            'dealsOfTheDay',
            'mobiles',
            'laptops',
            'clothing',
            'appliances',
            'audio',
            'footwear',
            'beauty',
            'fashion',
            'homeProducts',
            'novaChoices',
            'newArrivals',
            'reviews',
            'announcement'
        ));
    }
}

