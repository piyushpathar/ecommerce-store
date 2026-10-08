<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::orderBy('sort_order', 'asc')->get();
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'slug' => 'nullable|string|max:200|unique:pages,slug',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:200',
            'meta_description' => 'nullable|string|max:300',
            'footer_column' => 'nullable|string|in:company,legal,help',
            'sort_order' => 'nullable|integer',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $validated['is_published'] = $request->has('is_published');
        $validated['show_in_header'] = $request->has('show_in_header');
        $validated['show_in_footer'] = $request->has('show_in_footer');
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        Page::create($validated);

        return redirect()->route('admin.pages.index')->with('success', 'Page created successfully.');
    }

    public function edit(string $id)
    {
        $page = Page::findOrFail($id);
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, string $id)
    {
        $page = Page::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'slug' => 'required|string|max:200',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:200',
            'meta_description' => 'nullable|string|max:300',
            'footer_column' => 'nullable|string|in:company,legal,help',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_published'] = $request->has('is_published');
        $validated['show_in_header'] = $request->has('show_in_header');
        $validated['show_in_footer'] = $request->has('show_in_footer');
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        $page->update($validated);

        return redirect()->route('admin.pages.index')->with('success', 'Page updated successfully.');
    }

    public function destroy(string $id)
    {
        $page = Page::findOrFail($id);
        $page->delete();

        return redirect()->route('admin.pages.index')->with('success', 'Page deleted successfully.');
    }

    public function togglePublish(string $id)
    {
        $page = Page::findOrFail($id);
        $page->update(['is_published' => !$page->is_published]);

        return back()->with('success', 'Page publication status updated.');
    }
}
