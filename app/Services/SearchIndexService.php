<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;

class SearchIndexService
{
    /**
     * Autocomplete suggestions for header search bar
     */
    public function getSuggestions(string $query, int $limit = 6): array
    {
        $query = trim($query);
        if (strlen($query) < 2) {
            return [
                'products' => [],
                'categories' => [],
            ];
        }

        $products = Product::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('brand', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%");
            })
            ->select(['id', 'title', 'slug', 'thumbnail', 'price', 'compare_price', 'category_name', 'brand'])
            ->limit($limit)
            ->get();

        $categories = Category::where('is_active', true)
            ->where('name', 'like', "%{$query}%")
            ->select(['id', 'name', 'slug', 'icon'])
            ->limit(4)
            ->get();

        return [
            'products' => $products,
            'categories' => $categories,
        ];
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
