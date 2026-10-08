<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Services\SearchIndexService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    protected SearchIndexService $searchService;

    public function __construct(SearchIndexService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function index(Request $request)
    {
        $query = $request->input('q', '');
        $categories = Category::where('is_active', true)->orderBy('sort_order', 'asc')->get();
        $perPage = (int) $request->input('per_page', 15);
        $products = $this->searchService->search($request->all(), $perPage);

        $brands = Product::where('is_active', true)->pluck('brand')->unique()->filter()->values()->all();

        return view('shop.search', compact('products', 'categories', 'query', 'brands'));
    }

    public function suggest(Request $request)
    {
        $query = $request->input('q', '');
        $results = $this->searchService->getSuggestions($query, 6);

        return response()->json([
            'products' => $results['products'],
            'categories' => $results['categories'],
            'brands' => $results['brands'],
        ]);
    }
}
