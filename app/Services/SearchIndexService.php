<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;

class SearchIndexService
{
    /**
     * Autocomplete suggestions for header search bar.
     * Every word of the query must match the title, brand, category or SKU,
     * so "apple watch" or "watch apple" both find "Apple Watch Ultra 2".
     */
    public function getSuggestions(string $query, int $limit = 6): array
    {
        $query = trim($query);
        $words = array_values(array_filter(preg_split('/\s+/', mb_strtolower($query))));
        if (mb_strlen($query) < 2 || !$words) {
            return ['products' => [], 'categories' => [], 'brands' => []];
        }

        $products = Product::where('is_active', true)
            ->where(function ($q) use ($words) {
                foreach ($words as $word) {
                    // Match from the start of any word: "son" finds "Sony", not "Dyson"
                    $word = addcslashes($word, '%_\\');
                    $q->where(function ($w) use ($word) {
                        foreach (['title', 'brand', 'category_name'] as $col) {
                            $w->orWhere($col, 'like', $word . '%')
                                ->orWhere($col, 'like', '% ' . $word . '%')
                                ->orWhere($col, 'like', '%-' . $word . '%')
                                ->orWhere($col, 'like', '%(' . $word . '%');
                        }
                        $w->orWhere('sku', 'like', '%' . $word . '%');
                    });
                }
            })
            // Title prefix > brand prefix > best sellers
            ->orderByRaw('CASE WHEN title LIKE ? THEN 0 WHEN brand LIKE ? THEN 1 ELSE 2 END', [$query . '%', $query . '%'])
            ->orderByDesc('sales_count')
            ->orderByDesc('rating_avg')
            ->select(['id', 'title', 'slug', 'thumbnail', 'price', 'compare_price', 'category_name', 'brand', 'rating_avg'])
            ->limit($limit)
            ->get();

        $brands = Product::where('is_active', true)
            ->where(function ($q) use ($words) {
                foreach ($words as $word) {
                    $word = addcslashes($word, '%_\\');
                    $q->orWhere('brand', 'like', $word . '%')
                        ->orWhere('brand', 'like', '% ' . $word . '%');
                }
            })
            ->selectRaw('brand as name, COUNT(*) as product_count')
            ->groupBy('brand')
            ->orderByDesc('product_count')
            ->limit(3)
            ->get();

        $categoryNames = $products->pluck('category_name')->filter()->unique();
        $categories = Category::where('is_active', true)
            ->where(function ($q) use ($query, $categoryNames) {
                $term = addcslashes($query, '%_\\');
                $q->where('name', 'like', $term . '%')
                    ->orWhere('name', 'like', '% ' . $term . '%')
                    ->orWhereIn('name', $categoryNames);
            })
            ->select(['id', 'name', 'slug', 'icon'])
            ->limit(3)
            ->get();

        return [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
        ];
    }

    /**
     * What to show in the search dropdown before the user types:
     * best-selling brands as quick search terms, plus top-selling products.
     */
    public function getTrending(): array
    {
        return cache()->remember('search.trending', now()->addMinutes(30), function () {
            $terms = Product::where('is_active', true)
                ->whereNotNull('brand')
                ->selectRaw('brand, SUM(sales_count) as sold')
                ->groupBy('brand')
                ->orderByDesc('sold')
                ->limit(8)
                ->pluck('brand')
                ->all();

            $products = Product::where('is_active', true)
                ->orderByDesc('sales_count')
                ->orderByDesc('rating_avg')
                ->limit(4)
                ->get(['id', 'title', 'slug', 'thumbnail', 'price', 'brand', 'category_name'])
                ->toArray();

            return ['terms' => $terms, 'products' => $products];
        });
    }

    /**
     * Full faceted search
     */
    public function search(array $filters = [], int $perPage = 15)
    {
        $query = Product::where('is_active', true);

        // Keyword search
        if (!empty($filters['q'])) {
            $keyword = trim($filters['q']);
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('brand', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%")
                    ->orWhere('short_description', 'like', "%{$keyword}%")
                    ->orWhere('sku', 'like', "%{$keyword}%");
            });
        }

        // Category filter
        if (!empty($filters['category'])) {
            $query->where('category_slug', $filters['category']);
        }

        // Brand filter
        if (!empty($filters['brand'])) {
            if (is_array($filters['brand'])) {
                $query->whereIn('brand', $filters['brand']);
            } else {
                $query->where('brand', $filters['brand']);
            }
        }

        // Price range
        if (isset($filters['min_price']) && is_numeric($filters['min_price'])) {
            $query->where('price', '>=', (float) $filters['min_price']);
        }
        if (isset($filters['max_price']) && is_numeric($filters['max_price'])) {
            $query->where('price', '<=', (float) $filters['max_price']);
        }

        // Rating filter
        if (!empty($filters['min_rating']) && is_numeric($filters['min_rating'])) {
            $query->where('rating_avg', '>=', (float) $filters['min_rating']);
        }

        // Stock status
        if (!empty($filters['in_stock_only'])) {
            $query->where('stock', '>', 0);
        }

        // Discount percentage
        if (!empty($filters['min_discount']) && is_numeric($filters['min_discount'])) {
            $query->where('discount_percentage', '>=', (int) $filters['min_discount']);
        }

        // Sorting
        $sort = $filters['sort'] ?? 'relevance';
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating_avg', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'sales':
                $query->orderBy('sales_count', 'desc');
                break;
            default:
                $query->orderBy('is_bestseller', 'desc')
                    ->orderBy('is_featured', 'desc')
                    ->orderBy('rating_avg', 'desc');
                break;
        }

        return $query->paginate($perPage)->appends(request()->query());
    }
}
