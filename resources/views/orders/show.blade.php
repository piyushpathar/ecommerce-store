@extends('layouts.app')

@section('title', 'Order #' . $order->order_number . ' | NovaMart')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">

    <!-- Order Header -->
    <div class="bg-white dark:bg-[#0f1723] rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-white/10 shadow-soft flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white font-mono">
                    Order #{{ $order->order_number }}
                </h1>
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase font-mono border {{ $order->status_badge_class }}">
                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1 font-mono">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</p>
        </div>

        <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-white/10 dark:hover:bg-white/15 text-slate-800 dark:text-white text-xs font-bold flex items-center gap-2 transition-colors">
            <i data-lucide="printer" class="w-4 h-4"></i>
            <span>Print Receipt</span>
        </button>
    </div>

    <!-- Amazon / Flipkart Tracking Timeline Progress Bar -->
    <div class="bg-white dark:bg-[#0f1723] rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-white/10 shadow-soft space-y-8">
        <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
            <i data-lucide="truck" class="w-4 h-4 text-brand-500"></i>
            <span>Shipment Status Tracker</span>
        </h2>

        @php
            $steps = [
                'pending' => 'Order Placed',
                'confirmed' => 'Confirmed',
                'shipped' => 'Shipped',
                'out_for_delivery' => 'Out for Delivery',
                'delivered' => 'Delivered'
            ];
            $currentStatus = $order->status;
            $stepKeys = array_keys($steps);
            $currentIndex = array_search($currentStatus, $stepKeys);
            if ($currentIndex === false) {
                $currentIndex = 0;
            }
        @endphp

        <!-- Visual Stepper Progress Bar -->
        <div class="relative">
            <div class="hidden sm:block absolute top-1/2 left-6 right-6 -translate-y-1/2 h-1 bg-slate-200 dark:bg-white/10 -z-0">
                <div class="h-full bg-brand-500 transition-all duration-500" style="width: {{ ($currentIndex / (count($steps) - 1)) * 100 }}%"></div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-5 gap-4 relative z-10">
                @foreach($steps as $k => $label)
                @php
                    $idx = array_search($k, $stepKeys);
                    $isPassed = $idx <= $currentIndex;
                    $isCurrent = $idx === $currentIndex;
                @endphp
                <div class="flex sm:flex-col items-center gap-3 sm:gap-2 sm:text-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs transition-all {{ $isPassed ? 'bg-brand-500 text-white shadow-glow' : 'bg-slate-100 dark:bg-white/5 text-slate-400 border border-slate-200 dark:border-white/10' }}">
                        @if($isPassed)
                        <i data-lucide="check" class="w-4 h-4"></i>
                        @else
                        {{ $loop->iteration }}
                        @endif
                    </div>
                    <div>
                        <div class="text-xs font-bold {{ $isCurrent ? 'text-brand-600 dark:text-brand-400 font-extrabold' : ($isPassed ? 'text-slate-900 dark:text-white' : 'text-slate-400') }}">
                            {{ $label }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        @if($order->tracking_number)
        <div class="p-4 rounded-2xl bg-brand-50/50 dark:bg-brand-950/20 border border-brand-200 dark:border-brand-800/40 text-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
            <div>
                <span class="text-slate-500">Carrier:</span>
                <strong class="text-slate-900 dark:text-white ml-1">{{ $order->tracking_carrier ?? 'BlueDart Express' }}</strong>
            </div>
            <div>
                <span class="text-slate-500">Tracking Number:</span>
                <strong class="text-brand-600 dark:text-brand-400 font-mono ml-1">{{ $order->tracking_number }}</strong>
            </div>
        </div>
        @endif

        <!-- Timestamped Activity Log -->
        @if(!empty($order->status_history))
        <div class="pt-4 border-t border-slate-100 dark:border-white/5 space-y-3">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Activity History</h4>
            <div class="space-y-2 text-xs">
                @foreach(array_reverse($order->status_history) as $history)
                <div class="flex items-start gap-3 text-slate-600 dark:text-slate-300">
                    <span class="w-2 h-2 rounded-full bg-brand-500 mt-1.5 shrink-0"></span>
                    <div class="flex-1">
                        <span class="font-semibold text-slate-800 dark:text-white">{{ $history['title'] ?? ucfirst($history['status']) }}</span>
                        <div class="text-[11px] text-slate-400 font-mono">{{ $history['timestamp'] ?? '' }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Order Items & Shipping Address Details -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">

        <!-- Line Items Summary -->
        <div class="bg-white dark:bg-[#0f1723] rounded-3xl p-6 border border-slate-200 dark:border-white/10 shadow-soft space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-white/10 pb-3">Items Purchased</h3>
            <div class="space-y-3 divide-y divide-slate-100 dark:divide-white/5">
                @foreach($order->items as $it)
                <div class="flex items-center gap-3 pt-3 first:pt-0">
                    <img src="{{ $it['image'] }}" class="w-12 h-12 rounded-xl object-cover bg-slate-100 dark:bg-white/5 shrink-0" alt="">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ $it['title'] }}</h4>
                        @if(!empty($it['variant']))
                        <div class="text-[10px] text-slate-500">{{ $it['variant'] }}</div>
                        @endif
                        <div class="text-xs font-mono text-slate-500">Qty: {{ $it['quantity'] }} &bull; ₹{{ number_format($it['price'], 2) }}</div>
                    </div>
                    <div class="text-xs font-bold font-mono text-brand-600 dark:text-brand-400">
                        ₹{{ number_format($it['price'] * $it['quantity'], 2) }}
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Price Breakdown -->
            <div class="pt-4 border-t border-slate-100 dark:border-white/10 space-y-2 text-xs">
                <div class="flex justify-between text-slate-500">
                    <span>Subtotal</span>
                    <span class="font-mono text-slate-800 dark:text-white">₹{{ number_format($order->subtotal, 2) }}</span>
                </div>
                @if($order->discount_amount > 0)
                <div class="flex justify-between text-emerald-600">
                    <span>Discount ({{ $order->coupon_code }})</span>
                    <span class="font-mono">-₹{{ number_format($order->discount_amount, 2) }}</span>
                </div>
                @endif
                <div class="flex justify-between text-slate-500">
                    <span>Shipping</span>
                    <span class="font-mono">{{ $order->shipping_fee == 0 ? 'FREE' : '₹' . number_format($order->shipping_fee, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm font-black text-slate-900 dark:text-white font-mono pt-2 border-t border-slate-100 dark:border-white/10">
                    <span>Total Paid</span>
                    <span class="text-brand-600 dark:text-brand-400">{{ $order->formatted_total }}</span>
                </div>
            </div>
        </div>

        <!-- Delivery & Payment Information -->
        <div class="space-y-6">
            <!-- Delivery Address -->
            <div class="bg-white dark:bg-[#0f1723] rounded-3xl p-6 border border-slate-200 dark:border-white/10 shadow-soft space-y-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-white/10 pb-3">Delivery Address</h3>
                <div class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                    <strong class="text-slate-900 dark:text-white">{{ $order->shipping_address['name'] ?? $order->customer_name }}</strong><br>
                    {{ $order->shipping_address['address_line1'] ?? '' }}, {{ $order->shipping_address['address_line2'] ?? '' }}<br>
                    {{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['state'] ?? '' }} - <span class="font-mono">{{ $order->shipping_address['pincode'] ?? '' }}</span><br>
                    <span class="font-mono text-slate-400">Contact: {{ $order->shipping_address['phone'] ?? $order->customer_phone }}</span>
                </div>
            </div>

            <!-- Payment Info -->
            <div class="bg-white dark:bg-[#0f1723] rounded-3xl p-6 border border-slate-200 dark:border-white/10 shadow-soft space-y-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-white/10 pb-3">Payment Details</h3>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Method:</span>
                        <span class="font-bold text-slate-800 dark:text-white uppercase font-mono">{{ $order->payment_method }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Status:</span>
                        <span class="font-bold text-emerald-600 uppercase font-mono">{{ $order->payment_status }}</span>
                    </div>
                    @if($order->razorpay_payment_id)
                    <div class="flex justify-between">
                        <span class="text-slate-500">Razorpay ID:</span>
                        <span class="font-mono text-slate-800 dark:text-white">{{ $order->razorpay_payment_id }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
