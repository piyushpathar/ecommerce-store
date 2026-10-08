<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('q')) {
            $q = trim($request->input('q'));
            $query->where('title', 'like', "%{$q}%");
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(15);
        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required',
            'brand' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'sku' => 'nullable|string|max:50',
            'short_description' => 'required|string|max:500',
            'description' => 'required|string',
            'thumbnail' => 'required|url',
            'images' => 'nullable|string', // comma separated URLs
            'tags' => 'nullable|string',
            'spec_keys' => 'nullable|array',
            'spec_values' => 'nullable|array',
            'is_featured' => 'nullable|boolean',
            'is_bestseller' => 'nullable|boolean',
            'is_nifty_choice' => 'nullable|boolean',
        ]);

        $category = Category::findOrFail($request->input('category_id'));
        $slug = Str::slug($validated['title']) . '-' . Str::random(5);

        // Parse images
        $images = [$validated['thumbnail']];
        if (!empty($request->input('images'))) {
            $extra = array_map('trim', explode(',', $request->input('images')));
            $images = array_merge($images, array_filter($extra));
        }

        // Parse tags
        $tags = [];
        if (!empty($request->input('tags'))) {
            $tags = array_map('trim', explode(',', strtolower($request->input('tags'))));
        }

        // Parse specs
        $specs = [];
        $keys = $request->input('spec_keys', []);
        $vals = $request->input('spec_values', []);
        foreach ($keys as $i => $k) {
            if (!empty($k) && !empty($vals[$i])) {
                $specs[] = ['key' => trim($k), 'value' => trim($vals[$i])];
            }
        }

        $discount = 0;
        if (!empty($validated['compare_price']) && $validated['compare_price'] > $validated['price']) {
            $discount = (int) round((($validated['compare_price'] - $validated['price']) / $validated['compare_price']) * 100);
        }

        Product::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'sku' => $validated['sku'] ?: 'NM-' . strtoupper(Str::random(6)),
            'category_id' => $category->_id,
            'category_slug' => $category->slug,
            'category_name' => $category->name,
            'brand' => $validated['brand'],
            'price' => (float) $validated['price'],
            'compare_price' => !empty($validated['compare_price']) ? (float) $validated['compare_price'] : null,
            'discount_percentage' => $discount,
            'stock' => (int) $validated['stock'],
            'stock_status' => (int) $validated['stock'] > 0 ? 'in_stock' : 'out_of_stock',
            'short_description' => $validated['short_description'],
            'description' => $validated['description'],
            'thumbnail' => $validated['thumbnail'],
            'images' => array_values(array_unique($images)),
            'tags' => array_values(array_unique($tags)),
            'specifications' => $specs,
            'variants' => [],
            'seo' => [
                'meta_title' => $validated['title'] . ' | NovaMart',
                'meta_description' => $validated['short_description'],
            ],
            'is_featured' => $request->boolean('is_featured'),
            'is_bestseller' => $request->boolean('is_bestseller'),
            'is_nova_choice' => $request->boolean('is_nova_choice') || $request->boolean('is_nifty_choice'),
            'is_nifty_choice' => $request->boolean('is_nova_choice') || $request->boolean('is_nifty_choice'),
            'rating_avg' => 5.0,
            'rating_count' => 1,
            'view_count' => 0,
            'sales_count' => 0,
            'is_active' => true,
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product published successfully!');
    }

    public function edit(string $id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(string $id, Request $request)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required',
            'brand' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'sku' => 'nullable|string|max:50',
            'short_description' => 'required|string|max:500',
            'description' => 'required|string',
            'thumbnail' => 'required|url',
            'images' => 'nullable|string',
            'tags' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
            'is_bestseller' => 'nullable|boolean',
            'is_nifty_choice' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $category = Category::findOrFail($request->input('category_id'));

        $discount = 0;
        if (!empty($validated['compare_price']) && $validated['compare_price'] > $validated['price']) {
            $discount = (int) round((($validated['compare_price'] - $validated['price']) / $validated['compare_price']) * 100);
        }

        // Parse images
        $images = [$validated['thumbnail']];
        if (!empty($request->input('images'))) {
            $extra = array_map('trim', explode(',', $request->input('images')));
            $images = array_merge($images, array_filter($extra));
        }

        // Parse tags
        $tags = [];
        if (!empty($request->input('tags'))) {
            $tags = array_map('trim', explode(',', strtolower($request->input('tags'))));
        }

        $product->update([
            'title' => $validated['title'],
            'sku' => $validated['sku'] ?: $product->sku,
            'category_id' => $category->_id,
            'category_slug' => $category->slug,
            'category_name' => $category->name,
            'brand' => $validated['brand'],
            'price' => (float) $validated['price'],
            'compare_price' => !empty($validated['compare_price']) ? (float) $validated['compare_price'] : null,
            'discount_percentage' => $discount,
            'stock' => (int) $validated['stock'],
            'stock_status' => (int) $validated['stock'] > 0 ? 'in_stock' : 'out_of_stock',
            'short_description' => $validated['short_description'],
            'description' => $validated['description'],
            'thumbnail' => $validated['thumbnail'],
            'images' => array_values(array_unique($images)),
            'tags' => array_values(array_unique($tags)),
            'is_featured' => $request->boolean('is_featured'),
            'is_bestseller' => $request->boolean('is_bestseller'),
            'is_nova_choice' => $request->boolean('is_nova_choice') || $request->boolean('is_nifty_choice'),
            'is_nifty_choice' => $request->boolean('is_nova_choice') || $request->boolean('is_nifty_choice'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }
}
