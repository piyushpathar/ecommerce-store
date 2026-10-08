<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $query = Order::query();

        if ($status && in_array($status, ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'])) {
            $query->where('status', $status);
        }

        if ($request->filled('q')) {
            $q = trim($request->input('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('order_number', 'like', "%{$q}%")
                    ->orWhere('customer_name', 'like', "%{$q}%")
                    ->orWhere('customer_email', 'like', "%{$q}%")
                    ->orWhere('tracking_number', 'like', "%{$q}%");
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15);
        $counts = [
            'all' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'confirmed' => Order::where('status', 'confirmed')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'status', 'counts'));
    }

    public function show(string $id)
    {
        $order = Order::findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(string $id, Request $request)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,out_for_delivery,delivered,cancelled,refunded',
            'tracking_carrier' => 'nullable|string|max:100',
            'tracking_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $newStatus = $request->input('status');
        $closed = ['cancelled', 'refunded'];

        if (in_array($order->status, $closed, true) && !in_array($newStatus, $closed, true)) {
            return back()->with('error', "Order #{$order->order_number} is {$order->status} and cannot be reopened.");
        }

        // Return stock when an open order is cancelled or refunded
        if (!in_array($order->status, $closed, true) && in_array($newStatus, $closed, true)) {
            foreach ($order->items ?? [] as $item) {
                $product = Product::find($item['product_id'] ?? null);
                if ($product) {
                    $stock = $product->stock + (int) $item['quantity'];
                    $product->update([
                        'stock' => $stock,
                        'stock_status' => $stock > 0 ? 'in_stock' : 'out_of_stock',
                        'sales_count' => max(0, $product->sales_count - (int) $item['quantity']),
                    ]);
                }
            }
        }

        $carrier = $request->input('tracking_carrier', $order->tracking_carrier);
        $trackingNum = $request->input('tracking_number', $order->tracking_number);
        $notes = $request->input('notes', $order->notes);

        $history = $order->status_history ?? [];
        $title = match ($newStatus) {
            'confirmed' => 'Order Confirmed by Admin',
            'processing' => 'Order Being Packed at Warehouse',
            'shipped' => 'Dispatched with ' . ($carrier ?: 'Courier') . ' (' . ($trackingNum ?: 'Tracking Active') . ')',
            'out_for_delivery' => 'Out for Delivery to Customer Address',
            'delivered' => 'Package Delivered Successfully',
            'cancelled' => 'Order Cancelled',
            'refunded' => 'Order Refund Processed',
            default => 'Status updated to ' . ucfirst($newStatus),
        };

        $history[] = [
            'status' => $newStatus,
            'title' => $title,
            'timestamp' => now()->toDateTimeString(),
        ];

        $order->update([
            'status' => $newStatus,
            'tracking_carrier' => $carrier,
            'tracking_number' => $trackingNum,
            'notes' => $notes,
            'status_history' => $history,
        ]);

        return back()->with('success', "Order #{$order->order_number} status updated to " . ucfirst($newStatus));
    }
}
