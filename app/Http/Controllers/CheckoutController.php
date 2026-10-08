<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Coupon;
use App\Services\CartService;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $addresses = $user ? ($user->addresses ?? []) : [];

        return view('checkout.index', compact('summary', 'user', 'addresses', 'shippingType'));
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
            'is_default' => empty(Auth::user()->addresses),
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
        $shippingType = $request->input('shipping_type', 'standard');
        $summary = $this->cartService->getSummary($shippingType);

        if (empty($summary['items'])) {
            return response()->json(['success' => false, 'message' => 'Cart is empty'], 400);
        }

        $receipt = 'RCPT-' . strtoupper(Str::random(8));
        $rpOrder = $this->razorpayService->createOrder(
            $summary['total'],
            $receipt,
            ['customer_email' => Auth::user()?->email ?? $request->input('customer_email', 'guest@novamart.com')]
        );

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

        $isValid = $this->razorpayService->verifySignature($orderId, $paymentId, $signature);

        if (!$isValid) {
            return response()->json(['success' => false, 'message' => 'Payment verification failed.'], 400);
        }

        $shippingType = $request->input('shipping_type', 'standard');
        $summary = $this->cartService->getSummary($shippingType);

        $orderNumber = 'NP-' . date('Y') . '-' . strtoupper(Str::random(6));

        $order = Order::create([
            'order_number' => $orderNumber,
            'user_id' => Auth::id(),
            'customer_name' => $request->input('customer_name'),
            'customer_email' => $request->input('customer_email'),
            'customer_phone' => $request->input('customer_phone'),
            'items' => $summary['items'],
            'is_subscription' => false,
            'subtotal' => $summary['subtotal'],
            'discount_amount' => $summary['discount'],
            'coupon_code' => $summary['coupon']['code'] ?? null,
            'shipping_fee' => $summary['shipping'],
            'tax_amount' => 0,
            'total_amount' => $summary['total'],
            'shipping_address' => $request->input('shipping_address'),
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

        // If coupon applied, increment used count
        if (!empty($summary['coupon']['code'])) {
            Coupon::where('code', $summary['coupon']['code'])->increment('used_count');
        }

        // Clear cart
        $this->cartService->clear();

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

        $orderNumber = 'NP-' . date('Y') . '-' . strtoupper(Str::random(6));

        $order = Order::create([
            'order_number' => $orderNumber,
            'user_id' => Auth::id(),
            'customer_name' => $request->input('customer_name'),
            'customer_email' => $request->input('customer_email'),
            'customer_phone' => $request->input('customer_phone'),
            'items' => $summary['items'],
            'is_subscription' => false,
            'subtotal' => $summary['subtotal'],
            'discount_amount' => $summary['discount'],
            'coupon_code' => $summary['coupon']['code'] ?? null,
            'shipping_fee' => $summary['shipping'],
            'tax_amount' => 0,
            'total_amount' => $summary['total'],
            'shipping_address' => $request->input('shipping_address'),
            'status' => 'pending',
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'status_history' => [
                ['status' => 'pending', 'title' => 'Cash on Delivery Order Placed', 'timestamp' => now()->toDateTimeString()],
            ],
            'notes' => 'Cash on delivery selected. Payment due upon arrival.',
        ]);

        // If coupon applied, increment used count
        if (!empty($summary['coupon']['code'])) {
            Coupon::where('code', $summary['coupon']['code'])->increment('used_count');
        }

        $this->cartService->clear();

        return redirect()->route('orders.show', $order->order_number)->with('success', 'Order placed successfully!');
    }

    public function show(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        return view('orders.show', compact('order'));
    }
}
