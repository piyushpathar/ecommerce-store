<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::orderBy('sort_order', 'asc')->get();
        return view('admin.plans.index', compact('plans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'interval' => 'required|string|in:daily,weekly,monthly,annual',
            'duration_days' => 'required|integer|min:1',
            'badge' => 'nullable|string|max:50',
            'features' => 'required|string', // newline separated
            'sort_order' => 'required|integer',
        ]);

        $features = array_filter(array_map('trim', explode("\n", $request->input('features'))));

        Plan::create([
            'name' => $request->input('name'),
            'slug' => Str::slug($request->input('name')),
            'price' => (float) $request->input('price'),
            'interval' => $request->input('interval'),
            'duration_days' => (int) $request->input('duration_days'),
            'badge' => $request->input('badge'),
            'features' => array_values($features),
            'sort_order' => (int) $request->input('sort_order'),
            'is_trial' => $request->boolean('is_trial'),
            'is_popular' => $request->boolean('is_popular'),
            'is_active' => true,
        ]);

        return back()->with('success', 'Pricing plan created successfully!');
    }

    public function update(string $id, Request $request)
    {
        $plan = Plan::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'interval' => 'required|string|in:daily,weekly,monthly,annual',
            'duration_days' => 'required|integer|min:1',
            'badge' => 'nullable|string|max:50',
            'features' => 'required|string',
            'sort_order' => 'required|integer',
        ]);

        $features = array_filter(array_map('trim', explode("\n", $request->input('features'))));

        $plan->update([
            'name' => $request->input('name'),
            'price' => (float) $request->input('price'),
            'interval' => $request->input('interval'),
            'duration_days' => (int) $request->input('duration_days'),
            'badge' => $request->input('badge'),
            'features' => array_values($features),
            'sort_order' => (int) $request->input('sort_order'),
            'is_trial' => $request->boolean('is_trial'),
            'is_popular' => $request->boolean('is_popular'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Plan updated successfully!');
    }

    public function destroy(string $id)
    {
        $plan = Plan::findOrFail($id);
        $plan->delete();

        return back()->with('success', 'Plan deleted.');
    }
}
