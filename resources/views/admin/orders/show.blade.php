@extends('layouts.admin')

@section('page_title', 'Order Fulfillment #' . $order->order_number)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-bold text-white font-mono">Order #{{ $order->order_number }}</h2>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase font-mono border {{ $order->status_badge_class }}">
                    {{ $order->status }}
                </span>
            </div>
            <p class="text-xs text-slate-400 font-mono mt-0.5">Placed: {{ $order->created_at->format('d M Y, h:i A') }}</p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="text-xs text-slate-400 hover:text-white">&larr; Back to Orders</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <!-- Left: Status Update Box & Items (8 cols) -->
        <div class="lg:col-span-8 space-y-6">

            <!-- Fulfill & Status Update Form -->
            <div class="p-6 rounded-3xl bg-[#0c1117] border border-brand-500/30 shadow-soft space-y-4">
                <h3 class="text-sm font-bold text-white flex items-center gap-2 border-b border-white/10 pb-3">
                    <i data-lucide="package-check" class="w-4 h-4 text-brand-400"></i>
                    <span>Fulfillment Status & Courier Tracking</span>
                </h3>

                <form action="{{ route('admin.orders.status', $order->_id) }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="font-bold text-slate-300">Update Order Status *</label>
                            <select name="status" class="w-full mt-1 p-2 bg-white/5 border border-white/10 rounded-xl text-white">
                                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing (Packaging)</option>
                                <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped (In Transit)</option>
                                <option value="out_for_delivery" {{ $order->status === 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                                <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <div>
                            <label class="font-bold text-slate-300">Courier Carrier</label>
                            <input type="text" name="tracking_carrier" value="{{ old('tracking_carrier', $order->tracking_carrier ?? 'BlueDart Express') }}" placeholder="e.g. BlueDart, Delhivery" class="w-full mt-1 p-2 bg-white/5 border border-white/10 rounded-xl text-white">
                        </div>

                        <div>
                            <label class="font-bold text-slate-300">Tracking AWB / Airway Bill</label>
                            <input type="text" name="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}" placeholder="e.g. BD-9823419082" class="w-full mt-1 p-2 bg-white/5 border border-white/10 rounded-xl text-white font-mono">
                        </div>
                    </div>

                    <div>
                        <label class="font-bold text-slate-300">Admin Fulfillment Notes</label>
                        <input type="text" name="notes" value="{{ old('notes', $order->notes) }}" placeholder="Internal instructions or delivery remarks..." class="w-full mt-1 p-2 bg-white/5 border border-white/10 rounded-xl text-white">
                    </div>

                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold transition-all shadow-glow">
                        Update Fulfillment Status
                    </button>
                </form>
            </div>

            <!-- Ordered Items List -->
            <div class="p-6 rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft space-y-4">
                <h3 class="text-sm font-bold text-white border-b border-white/10 pb-3">Line Items ({{ count($order->items ?? []) }})</h3>
                <div class="space-y-3 divide-y divide-white/5">
                    @foreach($order->items as $it)
                    <div class="flex items-center gap-4 pt-3 first:pt-0">
                        <img src="{{ $it['image'] }}" class="w-14 h-14 rounded-xl object-cover bg-black/20 shrink-0" alt="">
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-white text-xs">{{ $it['title'] }}</h4>
                            @if(!empty($it['variant']))
                            <div class="text-[11px] text-slate-400">Variant: {{ $it['variant'] }}</div>
                            @endif
                            <div class="text-[11px] text-slate-500 font-mono">SKU: {{ $it['sku'] ?? 'NP-ITEM' }} &bull; Qty: {{ $it['quantity'] }}</div>
                        </div>
                        <div class="text-right font-mono text-xs font-bold text-brand-400">
                            ₹{{ number_format($it['price'] * $it['quantity'], 2) }}
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="pt-4 border-t border-white/10 space-y-1.5 text-xs font-mono">
                    <div class="flex justify-between text-slate-400">
                        <span>Subtotal:</span>
                        <span>₹{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                    <div class="flex justify-between text-emerald-400">
                        <span>Discount ({{ $order->coupon_code }}):</span>
                        <span>-₹{{ number_format($order->discount_amount, 2) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-slate-400">
                        <span>Shipping Fee:</span>
                        <span>₹{{ number_format($order->shipping_fee, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-base font-black text-white pt-2 border-t border-white/10">
                        <span>Total Paid:</span>
                        <span class="text-brand-400">{{ $order->formatted_total }}</span>
                    </div>
                </div>
            </div>

            <!-- Activity Log -->
            <div class="p-6 rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Status History Timeline</h3>
                <div class="space-y-2 text-xs">
                    @foreach($order->status_history ?? [] as $sh)
                    <div class="flex items-center gap-3 p-2 rounded-xl bg-white/5">
                        <span class="w-2 h-2 rounded-full bg-brand-400"></span>
                        <div class="flex-1">
                            <span class="font-semibold text-slate-200">{{ $sh['title'] ?? ucfirst($sh['status']) }}</span>
                            <span class="text-[10px] text-slate-500 font-mono ml-2">{{ $sh['timestamp'] }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- Right: Customer & Address Sidebar (4 cols) -->
        <div class="lg:col-span-4 space-y-6">

            <!-- Customer Details -->
            <div class="p-6 rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft space-y-3 text-xs">
                <h3 class="font-bold text-white border-b border-white/10 pb-3">Customer Information</h3>
                <div class="space-y-2">
                    <div>
                        <span class="text-slate-500 block">Name:</span>
                        <strong class="text-slate-200">{{ $order->customer_name }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block">Email:</span>
                        <span class="font-mono text-slate-300">{{ $order->customer_email }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block">Phone:</span>
                        <span class="font-mono text-slate-300">{{ $order->customer_phone }}</span>
                    </div>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="p-6 rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft space-y-3 text-xs">
                <h3 class="font-bold text-white border-b border-white/10 pb-3">Shipping Destination</h3>
                <div class="text-slate-300 leading-relaxed">
                    <strong class="text-white">{{ $order->shipping_address['name'] ?? $order->customer_name }}</strong><br>
                    {{ $order->shipping_address['address_line1'] ?? '' }}, {{ $order->shipping_address['address_line2'] ?? '' }}<br>
                    {{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['state'] ?? '' }} - <span class="font-mono font-bold">{{ $order->shipping_address['pincode'] ?? '' }}</span><br>
                    <span class="font-mono text-slate-400">Phone: {{ $order->shipping_address['phone'] ?? $order->customer_phone }}</span>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="p-6 rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft space-y-3 text-xs">
                <h3 class="font-bold text-white border-b border-white/10 pb-3">Payment Info</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Method:</span>
                        <span class="font-bold uppercase font-mono text-slate-200">{{ $order->payment_method }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Payment Status:</span>
                        <span class="font-bold uppercase font-mono text-emerald-400">{{ $order->payment_status }}</span>
                    </div>
                    @if($order->razorpay_order_id)
                    <div class="flex justify-between">
                        <span class="text-slate-500">Razorpay Order ID:</span>
                        <span class="font-mono text-[11px] text-slate-300">{{ $order->razorpay_order_id }}</span>
                    </div>
                    @endif
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
