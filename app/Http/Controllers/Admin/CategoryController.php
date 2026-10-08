<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('sort_order', 'asc')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:500',
            'icon' => 'required|string|max:50',
            'image' => 'required|url',
        ]);

        Category::create([
            'name' => $request->input('name'),
            'slug' => Str::slug($request->input('name')),
            'description' => $request->input('description'),
            'icon' => $request->input('icon'),
            'image' => $request->input('image'),
            'is_featured' => $request->boolean('is_featured', true),
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
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Category updated successfully!');
    }

    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return back()->with('success', 'Category deleted.');
    }
}
