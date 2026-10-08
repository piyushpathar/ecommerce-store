@extends('layouts.app')

@section('title', 'Secure Checkout | NovaMart')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
     x-data="{
         selectedAddressId: '{{ !empty($addresses[0]['id']) ? $addresses[0]['id'] : '' }}',
         showAddressModal: false,
         shippingType: '{{ $shippingType }}',
         paymentMethod: 'razorpay',
         customerName: '{{ $user->name ?? '' }}',
         customerEmail: '{{ $user->email ?? '' }}',
         customerPhone: '{{ $user->phone ?? '' }}',
         addresses: {{ json_encode($addresses) }},
         getSelectedAddress() {
             return this.addresses.find(a => a.id === this.selectedAddressId) || this.addresses[0] || null;
         },
         isProcessing: false,
         async initiatePayment() {
             const addr = this.getSelectedAddress();
             if (!addr) {
                 alert('Please add or select a delivery address first.');
                 return;
             }

             if (this.paymentMethod === 'cod') {
                 document.getElementById('codForm').submit();
                 return;
             }

             this.isProcessing = true;

             try {
                 // 1. Prepare payment order
                 const res = await fetch('{{ route('checkout.payment.prepare') }}', {
                     method: 'POST',
                     headers: {
                         'Content-Type': 'application/json',
                         'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                     },
                     body: JSON.stringify({
                         shipping_type: this.shippingType,
                         customer_email: this.customerEmail
                     })
                 });
                 const data = await res.json();

                 if (!data.success) {
                     alert('Payment preparation failed: ' + (data.message || 'Error'));
                     this.isProcessing = false;
                     return;
                 }

                 // 2. Open Razorpay or Sandbox simulator
                 if (data.is_mock) {
                     // Local Sandbox Mock
                     const confirmPay = confirm('Razorpay Sandbox Mode: Confirm simulated payment of ₹' + (data.amount / 100).toLocaleString('en-IN') + '?');
                     if (confirmPay) {
                         const verifyRes = await fetch('{{ route('checkout.payment.verify') }}', {
                             method: 'POST',
                             headers: {
                                 'Content-Type': 'application/json',
                                 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                             },
                             body: JSON.stringify({
                                 razorpay_order_id: data.razorpay_order.id,
                                 razorpay_payment_id: 'pay_mock_' + Math.random().toString(36).substring(2, 10),
                                 razorpay_signature: 'sig_mock_verified',
                                 shipping_address: addr,
                                 customer_name: this.customerName,
                                 customer_email: this.customerEmail,
                                 customer_phone: this.customerPhone,
                                 shipping_type: this.shippingType
                             })
                         });
                         const verifyData = await verifyRes.json();
                         if (verifyData.success) {
                             window.location.href = verifyData.redirect;
                         } else {
                             alert('Payment verification failed.');
                         }
                     }
                     this.isProcessing = false;
                     return;
                 }

                 // Real Razorpay SDK
                 const options = {
                     key: data.key,
                     amount: data.amount,
                     currency: data.currency,
                     name: 'NovaMart Marketplace',
                     description: 'Order Checkout',
                     order_id: data.razorpay_order.id,
                     handler: async function (response) {
                         const verifyRes = await fetch('{{ route('checkout.payment.verify') }}', {
                             method: 'POST',
                             headers: {
                                 'Content-Type': 'application/json',
                                 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                             },
                             body: JSON.stringify({
                                 razorpay_order_id: response.razorpay_order_id,
                                 razorpay_payment_id: response.razorpay_payment_id,
                                 razorpay_signature: response.razorpay_signature,
                                 shipping_address: addr,
                                 customer_name: this.customerName,
                                 customer_email: this.customerEmail,
                                 customer_phone: this.customerPhone,
                                 shipping_type: this.shippingType
                             })
                         });
                         const verifyData = await verifyRes.json();
                         if (verifyData.success) {
                             window.location.href = verifyData.redirect;
                         }
                     },
                     prefill: {
                         name: this.customerName,
                         email: this.customerEmail,
                         contact: this.customerPhone
                     },
                     theme: { color: '#059669' }
                 };
                 const rzp = new Razorpay(options);
                 rzp.open();
                 this.isProcessing = false;
             } catch (err) {
                 alert('Error during checkout: ' + err.message);
                 this.isProcessing = false;
             }
         }
     }">

    <!-- Checkout Steps Header -->
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">Secure Checkout</h1>
        <p class="text-xs text-slate-500 mt-1">Order completion with SSL encryption &bull; 100% Purchase Protection</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <!-- Left: Steps Container (8 cols) -->
        <div class="lg:col-span-8 space-y-6">

            <!-- STEP 1: Delivery Address -->
            <div class="bg-white dark:bg-[#0f1723] rounded-3xl p-6 border border-slate-200 dark:border-white/10 shadow-soft space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-white/10">
                    <div class="flex items-center gap-2.5">
                        <span class="w-6 h-6 rounded-full bg-brand-600 text-white font-mono font-bold text-xs flex items-center justify-center">1</span>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">Select Delivery Address</h2>
                    </div>
                    <button type="button"
                            @click="showAddressModal = true"
                            class="text-xs font-bold text-brand-600 dark:text-brand-400 hover:underline flex items-center gap-1">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span>Add New Address</span>
                    </button>
                </div>

                <!-- Addresses List -->
                @if(empty($addresses))
                <div class="p-6 text-center border-2 border-dashed border-slate-200 dark:border-white/10 rounded-2xl space-y-2">
                    <p class="text-xs text-slate-500">No delivery address saved yet.</p>
                    <button type="button" @click="showAddressModal = true" class="px-4 py-2 rounded-xl bg-brand-600 text-white font-bold text-xs">
                        + Add Delivery Address
                    </button>
                </div>
                @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($addresses as $addr)
                    <label class="relative p-4 rounded-2xl border cursor-pointer transition-all flex flex-col justify-between"
                           :class="{ 'border-brand-500 ring-2 ring-brand-500/20 bg-brand-50/30 dark:bg-brand-950/20': selectedAddressId === '{{ $addr['id'] }}', 'border-slate-200 dark:border-white/10 bg-white dark:bg-[#121824]': selectedAddressId !== '{{ $addr['id'] }}' }">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-bold text-xs text-slate-900 dark:text-white">{{ $addr['name'] }}</span>
                                <span class="text-[10px] font-mono uppercase font-bold px-2 py-0.5 rounded bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-slate-300">
                                    {{ $addr['address_type'] ?? 'home' }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                                {{ $addr['address_line1'] }}, {{ $addr['address_line2'] ?? '' }}<br>
                                {{ $addr['city'] }}, {{ $addr['state'] }} - <strong class="font-mono">{{ $addr['pincode'] }}</strong>
                            </p>
                            <p class="text-[11px] text-slate-400 font-mono mt-1">Phone: {{ $addr['phone'] }}</p>
                        </div>

                        <div class="pt-3 mt-3 border-t border-slate-100 dark:border-white/5 flex items-center gap-2">
                            <input type="radio" name="selected_address" value="{{ $addr['id'] }}" x-model="selectedAddressId" class="text-brand-600 focus:ring-brand-500">
                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-200">Deliver to this address</span>
                        </div>
                    </label>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- STEP 2: Delivery Speed Selection -->
            <div class="bg-white dark:bg-[#0f1723] rounded-3xl p-6 border border-slate-200 dark:border-white/10 shadow-soft space-y-4">
                <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100 dark:border-white/10">
                    <span class="w-6 h-6 rounded-full bg-brand-600 text-white font-mono font-bold text-xs flex items-center justify-center">2</span>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Choose Delivery Speed</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <label class="p-4 rounded-2xl border cursor-pointer flex items-center justify-between transition-all"
                           :class="{ 'border-brand-500 ring-2 ring-brand-500/20 bg-brand-50/30 dark:bg-brand-950/20': shippingType === 'standard', 'border-slate-200 dark:border-white/10': shippingType !== 'standard' }">
                        <div class="space-y-1">
                            <div class="font-bold text-slate-900 dark:text-white">Standard Delivery (2-4 Days)</div>
                            <p class="text-slate-500">Reliable pan-India delivery with BlueDart / Delhivery.</p>
                        </div>
                        <div class="text-right font-mono font-bold">
                            @if($summary['subtotal'] >= $summary['free_shipping_threshold'])
                            <span class="text-emerald-500">FREE</span>
                            @else
                            <span>₹99</span>
                            @endif
                        </div>
                    </label>

                    <label class="p-4 rounded-2xl border cursor-pointer flex items-center justify-between transition-all"
                           :class="{ 'border-brand-500 ring-2 ring-brand-500/20 bg-brand-50/30 dark:bg-brand-950/20': shippingType === 'express', 'border-slate-200 dark:border-white/10': shippingType !== 'express' }">
                        <div class="space-y-1">
                            <div class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                                <span>⚡ NovaMart Express (Next Day)</span>
                            </div>
                            <p class="text-slate-500">Priority packaging and guaranteed next-day arrival.</p>
                        </div>
                        <div class="text-right font-mono font-bold text-brand-600">
                            ₹199
                        </div>
                    </label>
                </div>
            </div>

            <!-- STEP 3: Payment Method Selection -->
            <div class="bg-white dark:bg-[#0f1723] rounded-3xl p-6 border border-slate-200 dark:border-white/10 shadow-soft space-y-4">
                <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100 dark:border-white/10">
                    <span class="w-6 h-6 rounded-full bg-brand-600 text-white font-mono font-bold text-xs flex items-center justify-center">3</span>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Select Payment Method</h2>
                </div>

                <div class="space-y-3">
                    <!-- Razorpay -->
                    <label class="p-4 rounded-2xl border cursor-pointer flex items-center justify-between transition-all"
                           :class="{ 'border-brand-500 ring-2 ring-brand-500/20 bg-brand-50/30 dark:bg-brand-950/20': paymentMethod === 'razorpay', 'border-slate-200 dark:border-white/10': paymentMethod !== 'razorpay' }">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="pay_method" value="razorpay" x-model="paymentMethod" class="text-brand-600 focus:ring-brand-500">
                            <div>
                                <div class="text-xs font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <span>Razorpay (Instant UPI, Cards & NetBanking)</span>
                                    <span class="text-[10px] bg-brand-500 text-white font-bold px-2 py-0.2 rounded font-mono">Recommended</span>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5">Google Pay, PhonePe, Paytm, Visa, Mastercard, RuPay & Net Banking</p>
                            </div>
                        </div>
                        <i data-lucide="shield-check" class="w-5 h-5 text-brand-500"></i>
                    </label>

                    <!-- Cash on Delivery (COD) -->
                    <label class="p-4 rounded-2xl border cursor-pointer flex items-center justify-between transition-all"
                           :class="{ 'border-brand-500 ring-2 ring-brand-500/20 bg-brand-50/30 dark:bg-brand-950/20': paymentMethod === 'cod', 'border-slate-200 dark:border-white/10': paymentMethod !== 'cod' }">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="pay_method" value="cod" x-model="paymentMethod" class="text-brand-600 focus:ring-brand-500">
                            <div>
                                <div class="text-xs font-bold text-slate-900 dark:text-white">Cash on Delivery (Pay at Doorstep)</div>
                                <p class="text-[11px] text-slate-500 mt-0.5">Pay in cash or UPI when your shipment arrives.</p>
                            </div>
                        </div>
                        <i data-lucide="banknote" class="w-5 h-5 text-slate-400"></i>
                    </label>
                </div>

                <!-- Hidden form for Cash on Delivery submission -->
                <form id="codForm" action="{{ route('checkout.place.cod') }}" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="shipping_type" :value="shippingType">
                    <input type="hidden" name="customer_name" :value="customerName">
                    <input type="hidden" name="customer_email" :value="customerEmail">
                    <input type="hidden" name="customer_phone" :value="customerPhone">
                    <input type="hidden" name="shipping_address" :value="JSON.stringify(getSelectedAddress())">
                </form>
            </div>

        </div>

        <!-- Right: Order Summary Box (4 cols) -->
        <div class="lg:col-span-4">
            <div class="sticky top-28 bg-white dark:bg-[#0f1723] rounded-3xl p-6 border border-slate-200 dark:border-white/10 shadow-pop space-y-5">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 dark:border-white/10 pb-3">
                    Order Summary ({{ $summary['count'] }} items)
                </h3>

                <!-- Items Mini List -->
                <div class="max-h-48 overflow-y-auto space-y-2.5 pr-1 divide-y divide-slate-100 dark:divide-white/5">
                    @foreach($summary['items'] as $it)
                    <div class="flex items-center gap-3 pt-2 first:pt-0">
                        <img src="{{ $it['image'] }}" class="w-10 h-10 rounded-lg object-cover bg-slate-100 dark:bg-white/5 shrink-0" alt="">
                        <div class="flex-1 min-w-0">
                            <h4 class="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate">{{ $it['title'] }}</h4>
                            <div class="text-[11px] text-slate-400 font-mono">Qty: {{ $it['quantity'] }} &bull; ₹{{ number_format($it['price'], 2) }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="space-y-2.5 text-xs pt-3 border-t border-slate-100 dark:border-white/10">
                    <div class="flex justify-between text-slate-600 dark:text-slate-400">
                        <span>Items Subtotal</span>
                        <span class="font-mono text-slate-900 dark:text-white font-semibold">₹{{ number_format($summary['subtotal'], 2) }}</span>
                    </div>

                    @if($summary['discount'] > 0)
                    <div class="flex justify-between text-emerald-600 dark:text-emerald-400">
                        <span>Discount</span>
                        <span class="font-mono font-bold">-₹{{ number_format($summary['discount'], 2) }}</span>
                    </div>
                    @endif

                    <div class="flex justify-between text-slate-600 dark:text-slate-400">
                        <span>Shipping Fee</span>
                        <span class="font-mono font-semibold">
                            @if($summary['shipping'] == 0)
                                <span class="text-emerald-600 font-bold">FREE</span>
                            @else
                                ₹{{ number_format($summary['shipping'], 2) }}
                            @endif
                        </span>
                    </div>

                    <div class="pt-3 border-t border-slate-200 dark:border-white/10 flex justify-between items-baseline font-mono">
                        <span class="text-sm font-bold text-slate-900 dark:text-white font-sans">Total Amount</span>
                        <span class="text-2xl font-black text-brand-600 dark:text-brand-400">₹{{ number_format($summary['total'], 2) }}</span>
                    </div>
                </div>

                <!-- Pay Button -->
                <button type="button"
                        @click="initiatePayment()"
                        :disabled="isProcessing"
                        class="w-full py-4 rounded-2xl bg-brand-600 hover:bg-brand-500 disabled:opacity-50 text-white font-bold text-sm shadow-glow text-center flex items-center justify-center gap-2 transition-all">
                    <i data-lucide="lock" class="w-4 h-4"></i>
                    <span x-text="isProcessing ? 'Processing...' : (paymentMethod === 'cod' ? 'Place Cash on Delivery Order' : 'Pay ₹' + Number({{ $summary['total'] }}).toLocaleString('en-IN'))"></span>
                </button>

                <div class="text-center text-[11px] text-slate-400">
                    By placing your order, you agree to NovaMart Marketplace Terms of Service and Privacy Policy.
                </div>
            </div>
        </div>

    </div>

    <!-- Modal: Add New Address -->
    <div x-show="showAddressModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="bg-white dark:bg-[#0f1723] rounded-3xl p-6 sm:p-8 max-w-lg w-full border border-slate-200 dark:border-white/10 shadow-pop space-y-4" @click.outside="showAddressModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-white/10">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Add New Delivery Address</h3>
                <button @click="showAddressModal = false" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>

            <form action="{{ route('checkout.address.add') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Full Name *</label>
                        <input type="text" name="name" required placeholder="e.g. Rahul Sharma" class="w-full px-3 py-2 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500/30">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Mobile Phone *</label>
                        <div class="relative flex items-center">
                            <span class="absolute left-3 text-xs font-mono font-bold text-slate-400 border-r border-slate-200 dark:border-white/10 pr-2">+91</span>
                            <input type="tel" name="phone" required placeholder="98765 43210" maxlength="10" class="w-full pl-14 pr-3 py-2 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-xs font-mono text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500/30">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Flat / House No. / Building *</label>
                    <input type="text" name="address_line1" required placeholder="e.g. Flat 502, Tower 4, Orchid Heights" class="w-full px-3 py-2 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500/30">
                </div>

                <div>
                    <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Street / Area / Sector</label>
                    <input type="text" name="address_line2" placeholder="e.g. SG Highway, Bodakdev / Link Road" class="w-full px-3 py-2 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500/30">
                </div>

                <!-- State & City Custom Searchable Comboboxes + Pincode -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <x-india-state-city />

                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Pincode *</label>
                        <input type="text" name="pincode" maxlength="6" required placeholder="e.g. 380015" class="w-full px-3 py-2 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-xs font-mono text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500/30">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Landmark (Optional)</label>
                        <input type="text" name="landmark" placeholder="e.g. Near Iscon Cross Road / Opposite Mall" class="w-full px-3 py-2 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500/30">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Address Type</label>
                        <div class="grid grid-cols-3 gap-1.5 pt-0.5" x-data="{ addrType: 'home' }">
                            <label class="cursor-pointer">
                                <input type="radio" name="address_type" value="home" x-model="addrType" class="sr-only">
                                <div class="px-2 py-1.5 rounded-xl border text-center transition-all text-xs font-semibold"
                                     :class="addrType === 'home' ? 'bg-brand-50 dark:bg-brand-950/60 border-brand-500 text-brand-600 dark:text-brand-400 font-bold' : 'border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5'">
                                    🏠 Home
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="address_type" value="work" x-model="addrType" class="sr-only">
                                <div class="px-2 py-1.5 rounded-xl border text-center transition-all text-xs font-semibold"
                                     :class="addrType === 'work' ? 'bg-brand-50 dark:bg-brand-950/60 border-brand-500 text-brand-600 dark:text-brand-400 font-bold' : 'border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5'">
                                    🏢 Work
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="address_type" value="other" x-model="addrType" class="sr-only">
                                <div class="px-2 py-1.5 rounded-xl border text-center transition-all text-xs font-semibold"
                                     :class="addrType === 'other' ? 'bg-brand-50 dark:bg-brand-950/60 border-brand-500 text-brand-600 dark:text-brand-400 font-bold' : 'border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5'">
                                    📍 Other
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" @click="showAddressModal = false" class="px-4 py-2 rounded-xl text-slate-400 hover:text-white">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold">Save Address</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
