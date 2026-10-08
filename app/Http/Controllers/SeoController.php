<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Plan;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $categories = Category::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();
        $plans = Plan::where('is_active', true)->get();

        $content = view('seo.sitemap', compact('categories', 'products', 'plans'))->render();

        return response($content, 200)->header('Content-Type', 'text/xml');
    }

    public function robots(): Response
    {
        $sitemapUrl = url('/sitemap.xml');
        $robots = "User-agent: *\nDisallow: /admin/\nDisallow: /checkout/\nDisallow: /account/\nAllow: /\n\nSitemap: {$sitemapUrl}\n";

        return response($robots, 200)->header('Content-Type', 'text/plain');
    }
}
