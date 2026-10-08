<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::orderBy('created_at', 'desc')->get();
        return view('admin.coupons.index', compact('coupons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:30|unique:coupons,code',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:1',
            'min_spend' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
        ]);

        Coupon::create([
            'code' => strtoupper(trim($request->input('code'))),
            'discount_type' => $request->input('discount_type'),
            'discount_value' => (float) $request->input('discount_value'),
            'min_spend' => (float) $request->input('min_spend', 0),
            'max_discount' => $request->input('max_discount') ? (float) $request->input('max_discount') : null,
            'usage_limit' => $request->input('usage_limit') ? (int) $request->input('usage_limit') : null,
            'used_count' => 0,
            'expires_at' => $request->input('expires_at') ? now()->parse($request->input('expires_at')) : null,
            'is_active' => true,
        ]);

        return back()->with('success', 'Promo coupon code created!');
    }

    public function update(string $id, Request $request)
    {
        $coupon = Coupon::findOrFail($id);

        $request->validate([
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:1',
            'min_spend' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        $coupon->update([
            'discount_type' => $request->input('discount_type'),
            'discount_value' => (float) $request->input('discount_value'),
            'min_spend' => (float) $request->input('min_spend', 0),
            'max_discount' => $request->input('max_discount') ? (float) $request->input('max_discount') : null,
            'usage_limit' => $request->input('usage_limit') ? (int) $request->input('usage_limit') : null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Coupon updated successfully!');
    }

    public function destroy(string $id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        return back()->with('success', 'Coupon deleted.');
    }
}
