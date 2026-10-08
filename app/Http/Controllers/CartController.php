<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        $summary = $this->cartService->getSummary();
        return view('cart.index', compact('summary'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'quantity' => 'nullable|integer|min:1',
            'variant' => 'nullable|string',
        ]);

        $product = Product::findOrFail($request->input('product_id'));
        $qty = (int) ($request->input('quantity', 1));
        $variant = $request->input('variant');

        $summary = $this->cartService->add($product, $qty, $variant);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Added {$product->title} to your cart!",
                'summary' => $summary,
            ]);
        }

        return redirect()->route('cart.index')->with('success', "Added {$product->title} to your cart!");
    }

    public function update(Request $request)
    {
        $request->validate([
            'item_key' => 'required|string',
            'quantity' => 'required|integer',
        ]);

        $summary = $this->cartService->updateQuantity($request->input('item_key'), (int) $request->input('quantity'));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'summary' => $summary]);
        }

        return back()->with('success', 'Cart updated successfully.');
    }

    public function remove(Request $request)
    {
        $request->validate([
            'item_key' => 'required|string',
        ]);

        $summary = $this->cartService->remove($request->input('item_key'));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'summary' => $summary]);
        }

        return back()->with('success', 'Item removed from cart.');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $result = $this->cartService->applyCoupon($request->input('code'));

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    public function removeCoupon(Request $request)
    {
        $this->cartService->removeCoupon();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Coupon removed.']);
        }

        return back()->with('success', 'Coupon removed.');
    }

    public function summary()
    {
        return response()->json($this->cartService->getSummary());
    }
}
