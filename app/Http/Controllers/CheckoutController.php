<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Coupon;
use App\Services\CartService;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    protected CartService $cartService;
    protected RazorpayService $razorpayService;

    public function __construct(CartService $cartService, RazorpayService $razorpayService)
    {
        $this->cartService = $cartService;
        $this->razorpayService = $razorpayService;
    }

    public function index(Request $request)
    {
        $shippingType = $request->query('shipping', 'standard');
        $summary = $this->cartService->getSummary($shippingType);

        if (empty($summary['items'])) {
            return redirect()->route('cart.index')->with('error', 'Your shopping cart is empty.');
        }

        $user = Auth::user();
        $addresses = $user ? ($user->addresses ?? []) : array_values(array_filter([session('guest_address')]));
        $razorpayEnabled = $this->razorpayService->isEnabled();

        return view('checkout.index', compact('summary', 'user', 'addresses', 'shippingType', 'razorpayEnabled'));
    }

    public function addAddress(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
            'landmark' => 'nullable|string|max:100',
            'address_type' => 'required|string|in:home,work,other',
        ]);

        $newAddress = [
            'id' => 'addr_' . Str::random(8),
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'address_line1' => $request->input('address_line1'),
            'address_line2' => $request->input('address_line2'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'pincode' => $request->input('pincode'),
            'landmark' => $request->input('landmark'),
            'address_type' => $request->input('address_type'),
            'is_default' => empty(Auth::user()?->addresses),
        ];

        if (Auth::check()) {
            $user = Auth::user();
            $addresses = $user->addresses ?? [];
            $addresses[] = $newAddress;
            $user->addresses = $addresses;
            $user->save();
        } else {
            session(['guest_address' => $newAddress]);
        }

        return back()->with('success', 'Delivery address saved successfully.');
    }

    public function preparePayment(Request $request)
    {
        if (!$this->razorpayService->isEnabled()) {
            return response()->json(['success' => false, 'message' => 'Online payments are currently disabled. Please choose Cash on Delivery.'], 400);
        }

        $shippingType = $request->input('shipping_type', 'standard');
        $summary = $this->cartService->getSummary($shippingType);

        if (empty($summary['items'])) {
            return response()->json(['success' => false, 'message' => 'Cart is empty'], 400);
        }

        if ($error = $this->cartService->stockError()) {
            return response()->json(['success' => false, 'message' => $error], 400);
        }

        $receipt = 'RCPT-' . strtoupper(Str::random(8));

        try {
            $rpOrder = $this->razorpayService->createOrder(
                $summary['total'],
                $receipt,
                ['customer_email' => Auth::user()?->email ?? $request->input('customer_email', '')]
            );
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }

        // Remember which gateway order belongs to this cart so verify can't be replayed or swapped
        session(['razorpay_pending' => ['order_id' => $rpOrder['id'], 'amount' => $rpOrder['amount']]]);

        return response()->json([
            'success' => true,
            'razorpay_order' => $rpOrder,
            'key' => $this->razorpayService->getKeyId(),
            'amount' => $rpOrder['amount'],
            'currency' => 'INR',
            'is_mock' => $this->razorpayService->isMockMode(),
            'summary' => $summary,
        ]);
    }

    public function verifyPayment(Request $request)
    {
        $request->validate([
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'nullable|string',
            'shipping_address' => 'required|array',
            'customer_name' => 'required|string',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string',
            'shipping_type' => 'nullable|string',
        ]);

        $orderId = $request->input('razorpay_order_id');
        $paymentId = $request->input('razorpay_payment_id');
        $signature = $request->input('razorpay_signature', '');

        // Same payment submitted twice (double click / refresh): return the existing order
        if ($existing = Order::where('razorpay_payment_id', $paymentId)->first()) {
            return response()->json(['success' => true, 'redirect' => route('orders.show', $existing->order_number)]);
        }

        $pending = session('razorpay_pending');
        if (!$pending || $pending['order_id'] !== $orderId) {
            return response()->json(['success' => false, 'message' => 'Payment session expired. Please try again.'], 400);
        }

        if (!$this->razorpayService->verifySignature($orderId, $paymentId, $signature)) {
            return response()->json(['success' => false, 'message' => 'Payment verification failed.'], 400);
        }

        $shippingType = $request->input('shipping_type', 'standard');
        $summary = $this->cartService->getSummary($shippingType);

        // Cart changed after the gateway order was created: amount paid no longer matches
        if ((int) round($summary['total'] * 100) !== (int) $pending['amount']) {
            return response()->json(['success' => false, 'message' => 'Your cart changed during payment. Please contact support with payment ID ' . $paymentId . '.'], 409);
        }

        try {
            $order = $this->placeOrder($request, $summary, [
                'status' => 'confirmed',
                'payment_method' => 'razorpay',
                'payment_status' => 'paid',
                'razorpay_order_id' => $orderId,
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature' => $signature,
                'status_history' => [
                    ['status' => 'pending', 'title' => 'Order Placed', 'timestamp' => now()->toDateTimeString()],
                    ['status' => 'confirmed', 'title' => 'Payment Verified via Razorpay', 'timestamp' => now()->toDateTimeString()],
                ],
                'notes' => 'Online payment completed successfully via Razorpay.',
            ]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage() . ' Your payment ID is ' . $paymentId . '; please contact support.'], 409);
        }

        session()->forget('razorpay_pending');

        return response()->json([
            'success' => true,
            'redirect' => route('orders.show', $order->order_number),
        ]);
    }

    public function placeCod(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|array',
            'customer_name' => 'required|string',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string',
            'shipping_type' => 'nullable|string',
        ]);

        $shippingType = $request->input('shipping_type', 'standard');
        $summary = $this->cartService->getSummary($shippingType);

        if (empty($summary['items'])) {
            return back()->with('error', 'Your shopping cart is empty.');
        }

        try {
            $order = $this->placeOrder($request, $summary, [
                'status' => 'pending',
                'payment_method' => 'cod',
                'payment_status' => 'pending',
                'status_history' => [
                    ['status' => 'pending', 'title' => 'Cash on Delivery Order Placed', 'timestamp' => now()->toDateTimeString()],
                ],
                'notes' => 'Cash on delivery selected. Payment due upon arrival.',
            ]);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('orders.show', $order->order_number)->with('success', 'Order placed successfully!');
    }

    public function show(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        return view('orders.show', compact('order'));
    }

    /**
     * Create the order, reserve stock and redeem the coupon atomically.
     *
     * @throws \RuntimeException when an item is no longer in stock
     */
    protected function placeOrder(Request $request, array $summary, array $paymentFields): Order
    {
        $order = DB::transaction(function () use ($request, $summary, $paymentFields) {
            foreach ($summary['items'] as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);
                if (!$product || !$product->is_active || $product->stock < $item['quantity']) {
                    throw new \RuntimeException("Sorry, \"{$item['title']}\" no longer has enough stock.");
                }

                $remaining = $product->stock - $item['quantity'];
                $product->update([
                    'stock' => $remaining,
                    'stock_status' => $remaining > 0 ? 'in_stock' : 'out_of_stock',
                    'sales_count' => $product->sales_count + $item['quantity'],
                ]);
            }

            if (!empty($summary['coupon']['code'])) {
                Coupon::where('code', $summary['coupon']['code'])->increment('used_count');
            }

            return Order::create(array_merge([
                'order_number' => 'NM-' . date('Y') . '-' . strtoupper(Str::random(6)),
                'user_id' => Auth::id(),
                'customer_name' => $request->input('customer_name'),
                'customer_email' => $request->input('customer_email'),
                'customer_phone' => $request->input('customer_phone'),
                'items' => $summary['items'],
                'subtotal' => $summary['subtotal'],
                'discount_amount' => $summary['discount'],
                'coupon_code' => $summary['coupon']['code'] ?? null,
                'shipping_fee' => $summary['shipping'],
                'tax_amount' => 0,
                'total_amount' => $summary['total'],
                'shipping_address' => $request->input('shipping_address'),
            ], $paymentFields));
        });

        $this->cartService->clear();

        return $order;
    }
}
