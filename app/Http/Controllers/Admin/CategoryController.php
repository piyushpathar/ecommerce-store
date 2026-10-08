<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('sort_order', 'asc')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->merge(['slug' => Str::slug($request->input('name'))]);

        $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'required|unique:categories,slug',
            'description' => 'required|string|max:500',
            'icon' => 'required|string|max:50',
            'image' => 'required|url',
        ], [
            'slug.unique' => 'A category with this name already exists.',
        ]);

        Category::create([
            'name' => $request->input('name'),
            'slug' => $request->input('slug'),
            'description' => $request->input('description'),
            'icon' => $request->input('icon'),
            'image' => $request->input('image'),
            'is_featured' => $request->boolean('is_featured'),
            'sort_order' => (int) $request->input('sort_order', 1),
            'is_active' => true,
        ]);

        return back()->with('success', 'Category created successfully!');
    }

    public function update(string $id, Request $request)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:500',
            'icon' => 'required|string|max:50',
            'image' => 'required|url',
            'sort_order' => 'required|integer',
        ]);

        $category->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'icon' => $request->input('icon'),
            'image' => $request->input('image'),
            'sort_order' => (int) $request->input('sort_order'),
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active'),
        ]);

        // Products keep a copy of the category name for listings
        Product::where('category_id', $category->id)->update(['category_name' => $category->name]);

        return back()->with('success', 'Category updated successfully!');
    }

    public function toggle(string $id)
    {
        $category = Category::findOrFail($id);
        $category->update(['is_active' => !$category->is_active]);

        return back()->with('success', "Category {$category->name} " . ($category->is_active ? 'enabled.' : 'disabled.'));
    }

    public function destroy(string $id)
    {
        $category = Category::withCount('products')->findOrFail($id);

        if ($category->products_count > 0) {
            return back()->with('error', "Cannot delete {$category->name}: it still has {$category->products_count} product(s). Move or delete them first, or disable the category.");
        }

        $category->delete();

        return back()->with('success', 'Category deleted.');
    }
}
