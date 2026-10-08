@extends('layouts.app')

@section('title', 'Shopping Cart | NovaMart')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white mb-6">
        Shopping Cart ({{ $summary['count'] }} item{{ $summary['count'] !== 1 ? 's' : '' }})
    </h1>

    @if(empty($summary['items']))
    <div class="bg-white dark:bg-[#0f1723] rounded-3xl p-16 text-center border border-slate-200 dark:border-white/10 shadow-soft space-y-4">
        <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-400 mx-auto">
            <i data-lucide="shopping-cart" class="w-8 h-8"></i>
        </div>
        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Your shopping cart is empty</h2>
        <p class="text-xs text-slate-500 max-w-sm mx-auto">Explore our curated pro monitors, custom mechanical keyboards, and desk gear to get started.</p>
        <a href="{{ route('shop.index') }}" class="inline-block px-6 py-3 rounded-2xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs shadow-glow transition-all">
            Continue Shopping
        </a>
    </div>
    @else
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <!-- Left: Line Items List (8 cols) -->
        <div class="lg:col-span-8 space-y-4">

            <!-- Free Shipping Progress Bar -->
            <div class="p-4 rounded-2xl bg-white dark:bg-[#0f1723] border border-slate-200 dark:border-white/10 shadow-soft">
                @if($summary['away_from_free_shipping'] <= 0)
                <div class="flex items-center gap-2 text-xs font-bold text-emerald-600 dark:text-emerald-400">
                    <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                    <span>Your order qualifies for FREE Express Delivery!</span>
                </div>
                @else
                <div class="space-y-1.5">
                    <div class="flex justify-between text-xs font-medium">
                        <span class="text-slate-600 dark:text-slate-300">Add <strong class="text-brand-600 dark:text-brand-400 font-mono">₹{{ number_format($summary['away_from_free_shipping'], 2) }}</strong> more for <strong class="text-emerald-500">FREE Delivery</strong></span>
                        <span class="text-slate-400 font-mono text-[11px]">Threshold: ₹{{ $summary['free_shipping_threshold'] }}</span>
                    </div>
                    @php
                        $pct = min(100, round(($summary['subtotal'] / $summary['free_shipping_threshold']) * 100));
                    @endphp
                    <div class="h-2 w-full bg-slate-100 dark:bg-white/5 rounded-full overflow-hidden">
                        <div class="h-full bg-brand-500 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Items -->
            <div class="bg-white dark:bg-[#0f1723] rounded-3xl border border-slate-200 dark:border-white/10 shadow-soft divide-y divide-slate-100 dark:divide-white/5 overflow-hidden">
                @foreach($summary['items'] as $item)
                @php
                    $itemKey = $item['product_id'] . (!empty($item['variant']) ? '_' . md5($item['variant']) : '');
                @endphp
                <div class="p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="w-20 h-20 rounded-2xl object-cover bg-slate-100 dark:bg-white/5 border border-slate-200/50 dark:border-white/10 shrink-0">
                        <div class="space-y-1">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white line-clamp-1">
                                <a href="{{ route('shop.product', $item['slug']) }}" class="hover:text-brand-500 transition-colors">
                                    {{ $item['title'] }}
                                </a>
                            </h3>
                            @if(!empty($item['variant']))
                            <div class="text-[11px] text-slate-500 font-medium">Option: <span class="text-slate-700 dark:text-slate-300 font-semibold">{{ $item['variant'] }}</span></div>
                            @endif
                            <div class="text-xs text-slate-400 font-mono">SKU: {{ $item['sku'] }}</div>
                            <div class="text-sm font-black font-mono text-brand-600 dark:text-brand-400 sm:hidden pt-1">
                                ₹{{ number_format($item['price'], 2) }}
                            </div>
                        </div>
                    </div>

                    <!-- Quantity Adjusters & Subtotal -->
                    <div class="flex items-center justify-between sm:justify-end gap-6 w-full sm:w-auto pt-2 sm:pt-0 border-t sm:border-0 border-slate-100 dark:border-white/5">
                        <!-- Quantity Modifier -->
                        <div class="flex items-center border border-slate-200 dark:border-white/10 rounded-xl bg-slate-50 dark:bg-white/5 overflow-hidden">
                            <form action="{{ route('cart.update') }}" method="POST">
                                @csrf
                                <input type="hidden" name="item_key" value="{{ $itemKey }}">
                                <input type="hidden" name="quantity" value="{{ $item['quantity'] - 1 }}">
                                <button type="submit" class="w-8 h-8 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-white/10 font-bold transition-colors">
                                    -
                                </button>
                            </form>
                            <span class="w-10 text-center font-mono font-bold text-xs text-slate-900 dark:text-white">{{ $item['quantity'] }}</span>
                            <form action="{{ route('cart.update') }}" method="POST">
                                @csrf
                                <input type="hidden" name="item_key" value="{{ $itemKey }}">
                                <input type="hidden" name="quantity" value="{{ $item['quantity'] + 1 }}">
                                <button type="submit" class="w-8 h-8 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-white/10 font-bold transition-colors">
                                    +
                                </button>
                            </form>
                        </div>

                        <!-- Item Total -->
                        <div class="text-right hidden sm:block min-w-24">
                            <div class="text-base font-black font-mono text-slate-900 dark:text-white">
                                ₹{{ number_format($item['price'] * $item['quantity'], 2) }}
                            </div>
                            <div class="text-[10px] text-slate-400 font-mono">₹{{ number_format($item['price'], 2) }} each</div>
                        </div>

                        <!-- Remove Button -->
                        <form action="{{ route('cart.remove') }}" method="POST">
                            @csrf
                            <input type="hidden" name="item_key" value="{{ $itemKey }}">
                            <button type="submit" class="p-2 text-slate-400 hover:text-rose-500 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/20 transition-colors" title="Remove item">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Coupon Box -->
            <div class="p-6 rounded-3xl bg-white dark:bg-[#0f1723] border border-slate-200 dark:border-white/10 shadow-soft">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-3 flex items-center gap-2">
                    <i data-lucide="tag" class="w-4 h-4 text-brand-500"></i>
                    <span>Apply Promotional Coupon Code</span>
                </h4>

                @if($summary['coupon'])
                <div class="flex items-center justify-between p-3 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-300 dark:border-emerald-800 text-xs text-emerald-800 dark:text-emerald-300">
                    <div class="flex items-center gap-2">
                        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                        <span>Coupon <strong class="font-mono">{{ $summary['coupon']['code'] }}</strong> applied: Saved ₹{{ number_format($summary['discount'], 2) }}</span>
                    </div>
                    <form action="{{ route('cart.coupon.remove') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-rose-600 font-bold hover:underline">Remove</button>
                    </form>
                </div>
                @else
                <form action="{{ route('cart.coupon.apply') }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="text" name="code" placeholder="Try NOVAMART10 or WELCOME50" required class="flex-1 px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-xs font-mono uppercase text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-950 text-xs font-bold hover:bg-brand-600 dark:hover:bg-brand-400 transition-colors">
                        Apply
                    </button>
                </form>
                @endif
            </div>
        </div>

        <!-- Right: Flipkart/Amazon Price Summary Box (4 cols) -->
        <div class="lg:col-span-4">
            <div class="sticky top-28 bg-white dark:bg-[#0f1723] rounded-3xl p-6 border border-slate-200 dark:border-white/10 shadow-pop space-y-5">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 dark:border-white/10 pb-3">
                    Order Price Details
                </h3>

                <div class="space-y-3 text-xs">
                    <div class="flex justify-between text-slate-600 dark:text-slate-400">
                        <span>Price ({{ $summary['count'] }} items)</span>
                        <span class="font-mono text-slate-900 dark:text-white font-semibold">₹{{ number_format($summary['subtotal'], 2) }}</span>
                    </div>

                    @if($summary['discount'] > 0)
                    <div class="flex justify-between text-emerald-600 dark:text-emerald-400">
                        <span>Coupon Discount</span>
                        <span class="font-mono font-bold">-₹{{ number_format($summary['discount'], 2) }}</span>
                    </div>
                    @endif

                    <div class="flex justify-between text-slate-600 dark:text-slate-400">
                        <span>Delivery Charges</span>
                        <span class="font-mono font-semibold">
                            @if($summary['shipping'] == 0)
                                <span class="text-emerald-600 font-bold">FREE</span>
                            @else
                                ₹{{ number_format($summary['shipping'], 2) }}
                            @endif
                        </span>
                    </div>

                    <div class="pt-3 border-t border-slate-200 dark:border-white/10 flex justify-between items-baseline font-mono">
                        <span class="text-sm font-bold text-slate-900 dark:text-white font-sans">Total Payable</span>
                        <span class="text-2xl font-black text-brand-600 dark:text-brand-400">₹{{ number_format($summary['total'], 2) }}</span>
                    </div>
                </div>

                <a href="{{ route('checkout.index') }}" class="w-full py-4 rounded-2xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-sm shadow-glow text-center block transition-all">
                    Proceed to Buy &rarr;
                </a>

                <div class="text-center text-[11px] text-slate-400 flex items-center justify-center gap-1.5">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5 text-brand-500"></i>
                    <span>Safe & Secure 256-bit Razorpay Checkout</span>
                </div>
            </div>
        </div>

    </div>
    @endif

</div>
@endsection
