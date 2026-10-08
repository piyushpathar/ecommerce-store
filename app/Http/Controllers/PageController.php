<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{
    public function show(string $slug)
    {
        $query = Page::where('slug', $slug);

        // If not admin, only show published pages
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            $query->where('is_published', true);
        }

        $page = $query->firstOrFail();

        return view('pages.show', compact('page'));
    }

    public function contactSubmit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:200',
            'message' => 'required|string|min:10|max:2000',
        ]);

        return back()->with('success', 'Thank you! Your message has been received. Our support team will respond within 2 hours.');
    }
}
