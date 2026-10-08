<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Services\SearchIndexService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    protected SearchIndexService $searchService;

    public function __construct(SearchIndexService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function index(Request $request)
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order', 'asc')->get();
        $perPage = (int) $request->input('per_page', 15);
        $products = $this->searchService->search($request->all(), $perPage);

        $brands = Product::where('is_active', true)->pluck('brand')->unique()->filter()->values()->all();

        return view('shop.index', compact('products', 'categories', 'brands'));
    }

    public function category(string $slug, Request $request)
    {
        $category = Category::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $categories = Category::where('is_active', true)->orderBy('sort_order', 'asc')->get();

        $filters = $request->all();
        $filters['category'] = $category->slug;
        $perPage = (int) $request->input('per_page', 15);
        $products = $this->searchService->search($filters, $perPage);

        $brands = Product::where('category_slug', $category->slug)
            ->where('is_active', true)
            ->pluck('brand')
            ->unique()
            ->filter()
            ->values()
            ->all();

        return view('shop.category', compact('category', 'categories', 'products', 'brands'));
    }

    public function product(string $slug)
    {
        $product = Product::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $product->increment('view_count');

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('_id', '!=', $product->_id)
            ->where('is_active', true)
            ->limit(4)
            ->get();

        $reviews = Review::where('product_id', $product->_id)
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate rating breakdown
        $totalReviews = $reviews->count();
        $breakdown = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        foreach ($reviews as $rev) {
            $r = (int) $rev->rating;
            if (isset($breakdown[$r])) {
                $breakdown[$r]++;
            }
        }

        $hasPurchased = false;
        if (Auth::check()) {
            $hasPurchased = \App\Models\Order::where('user_id', Auth::id())
                ->where('items.product_id', (string) $product->_id)
                ->exists();
        }

        return view('shop.product', compact('product', 'relatedProducts', 'reviews', 'breakdown', 'totalReviews', 'hasPurchased'));
    }

    public function storeReview(string $slug, Request $request)
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        if (!Auth::check()) {
            return back()->with('error', 'Please sign in to write a review.');
        }

        $hasPurchased = \App\Models\Order::where('user_id', Auth::id())
            ->where('items.product_id', (string) $product->_id)
            ->exists();

        if (!$hasPurchased) {
            return back()->with('error', 'Only verified buyers who have purchased this product on NovaMart can submit a review.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:120',
            'comment' => 'required|string|min:10|max:1000',
        ]);

        $userName = Auth::user()->name;
        $userId = Auth::id();

        Review::create([
            'product_id' => $product->_id,
            'user_id' => $userId,
            'user_name' => $userName,
            'rating' => (int) $request->input('rating'),
            'title' => $request->input('title'),
            'comment' => $request->input('comment'),
            'is_verified_purchase' => true,
            'is_approved' => true,
        ]);

        // Recalculate average rating
        $reviews = Review::where('product_id', $product->_id)->where('is_approved', true)->get();
        $avg = $reviews->avg('rating');
        $product->update([
            'rating_avg' => round($avg, 1),
            'rating_count' => $reviews->count(),
        ]);

        return back()->with('success', 'Thank you! Your verified review has been published.');
    }
}
