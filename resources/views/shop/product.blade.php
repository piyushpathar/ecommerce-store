@extends('layouts.app')

@section('title', $product->title . ' | NovaMart')
@section('meta_description', $product->short_description)
@section('og_image', $product->thumbnail)

@push('styles')
<!-- JSON-LD Product Schema for Google / Amazon Indexing -->
<script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "Product",
  "name": "{{ $product->title }}",
  "image": [
    @foreach($product->images as $img)
      "{{ $img }}"{{ !$loop->last ? ',' : '' }}
    @endforeach
  ],
  "description": "{{ $product->short_description }}",
  "sku": "{{ $product->sku }}",
  "brand": {
    "@type": "Brand",
    "name": "{{ $product->brand }}"
  },
  "offers": {
    "@type": "Offer",
    "url": "{{ url()->current() }}",
    "priceCurrency": "INR",
    "price": "{{ $product->price }}",
    "availability": "{{ $product->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}",
    "itemCondition": "https://schema.org/NewCondition"
  },
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "{{ $product->rating_avg }}",
    "reviewCount": "{{ $product->rating_count }}"
  }
}
</script>
@endpush

@section('content')
<div class="max-w-[1440px] mx-auto px-3 sm:px-6 lg:px-8 py-3 sm:py-5"
     x-data="{
         selectedImage: '{{ $product->thumbnail }}',
         selectedVariant: '{{ !empty($product->variants[0]['options'][0]['name']) ? $product->variants[0]['options'][0]['name'] : '' }}',
         pincode: '',
         deliveryEstimate: '',
         checkPincode() {
             if (this.pincode.length === 6) {
                 this.deliveryEstimate = 'FREE Express Delivery by Tomorrow, 5:00 PM to ' + this.pincode;
             } else {
                 this.deliveryEstimate = 'Please enter a valid 6-digit Indian Pincode.';
             }
         }
     }">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-brand-500">Home</a>
        <span>/</span>
        <a href="{{ route('shop.index') }}" class="hover:text-brand-500">Products</a>
        <span>/</span>
        <a href="{{ route('shop.category', $product->category_slug) }}" class="hover:text-brand-500">{{ $product->category_name }}</a>
        <span>/</span>
        <span class="text-slate-800 dark:text-slate-200 font-semibold truncate max-w-xs">{{ $product->title }}</span>
    </nav>

    <!-- Product Showcase & Buy Box (Amazon / Flipkart layout) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

        <!-- Left: Image Gallery (5 cols) -->
        <div class="lg:col-span-5 space-y-4">
            <div class="relative aspect-square rounded-3xl bg-white dark:bg-[#0f1723] p-6 border border-slate-200 dark:border-white/10 shadow-soft overflow-hidden flex items-center justify-center">
                @if($product->is_nova_choice ?? $product->is_nifty_choice)
                <div class="absolute top-4 left-4 z-10 bg-brand-600 text-white text-[11px] font-bold font-mono px-2.5 py-1 rounded-full uppercase tracking-wider">
                    Nova's Choice
                </div>
                @endif
                <img :src="selectedImage" alt="{{ $product->title }}" class="w-full h-full object-contain rounded-2xl transition-all duration-300">
            </div>

            <!-- Thumbnail Selector -->
            @if(count($product->images) > 1)
            <div class="flex items-center gap-3 overflow-x-auto pb-2">
                @foreach($product->images as $img)
                <button type="button"
                        @click="selectedImage = '{{ $img }}'"
                        :class="{ 'ring-2 ring-brand-500 border-brand-500': selectedImage === '{{ $img }}' }"
                        class="w-16 h-16 rounded-xl bg-white dark:bg-[#0f1723] p-1.5 border border-slate-200 dark:border-white/10 shrink-0 hover:border-brand-500 transition-all">
                    <img src="{{ $img }}" alt="" class="w-full h-full object-cover rounded-lg">
                </button>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Center: Product Info & Specifications (4 cols) -->
        <div class="lg:col-span-4 space-y-6">
            <div>
                <span class="text-xs font-mono font-bold uppercase tracking-wider text-brand-600 dark:text-brand-400">{{ $product->brand }}</span>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white mt-1 leading-snug">
                    {{ $product->title }}
                </h1>
                <div class="flex items-center gap-3 mt-2 text-xs">
                    <div class="flex items-center gap-1 bg-amber-500/10 text-amber-500 px-2 py-0.5 rounded font-bold font-mono">
                        <span>★</span>
                        <span>{{ $product->rating_avg }}</span>
                    </div>
                    <a href="#reviews" class="text-slate-500 hover:text-brand-500 underline">{{ $product->rating_count }} verified ratings</a>
                    <span class="text-slate-300">|</span>
                    <span class="text-slate-500 font-mono text-[11px]">SKU: {{ $product->sku }}</span>
                </div>
            </div>

            <!-- Price Breakdown -->
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 space-y-1">
                <div class="flex items-baseline gap-3 font-mono">
                    <span class="text-3xl font-black text-brand-600 dark:text-brand-400">{{ $product->formatted_price }}</span>
                    @if($product->compare_price)
                    <span class="text-base text-slate-400 line-through">{{ $product->formatted_compare_price }}</span>
                    <span class="text-xs font-bold text-rose-600 bg-rose-50 dark:bg-rose-950/60 px-2 py-0.5 rounded">
                        Save {{ $product->calculated_discount }}%
                    </span>
                    @endif
                </div>
                <p class="text-[11px] text-slate-500">Inclusive of all taxes &bull; Free express delivery above ₹999</p>
            </div>

            <!-- Variants Selection -->
            @if(!empty($product->variants))
                @foreach($product->variants as $variant)
                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                        {{ $variant['name'] }}: <span class="text-brand-500 font-mono" x-text="selectedVariant"></span>
                    </label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($variant['options'] as $opt)
                        <button type="button"
                                @click="selectedVariant = '{{ $opt['name'] }}'"
                                :class="{ 'bg-brand-600 text-white border-brand-600 ring-2 ring-brand-500/20': selectedVariant === '{{ $opt['name'] }}', 'bg-white dark:bg-[#0f1723] text-slate-700 dark:text-slate-300 border-slate-200 dark:border-white/10': selectedVariant !== '{{ $opt['name'] }}' }"
                                class="px-3 py-1.5 rounded-xl border text-xs font-semibold transition-all">
                            {{ $opt['name'] }}
                        </button>
                        @endforeach
                    </div>
                </div>
                @endforeach
            @endif

            <!-- Feature Summary -->
            <div class="space-y-2">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Highlights</h3>
                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">{{ $product->short_description }}</p>
            </div>

            <!-- Pincode Delivery Estimator -->
            <div class="pt-2">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-2">Check Delivery Date</label>
                <div class="flex items-center gap-2">
                    <input type="text"
                           x-model="pincode"
                           maxlength="6"
                           placeholder="Enter 6-digit Pincode (e.g. 400053)"
                           class="flex-1 px-3 py-2 bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-xs font-mono text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                    <button type="button"
                            @click="checkPincode()"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-white/10 dark:hover:bg-white/15 text-slate-900 dark:text-white rounded-xl text-xs font-bold transition-colors">
                        Check
                    </button>
                </div>
                <p x-show="deliveryEstimate" x-text="deliveryEstimate" class="text-xs text-brand-600 dark:text-brand-400 font-semibold mt-2 font-mono" x-cloak></p>
            </div>
        </div>

        <!-- Right: Flipkart/Amazon Buy Box (3 cols) -->
        <div class="lg:col-span-3">
            <div class="sticky top-28 bg-white dark:bg-[#0f1723] rounded-3xl p-6 border border-slate-200 dark:border-white/10 shadow-pop space-y-5">
                <div class="font-mono">
                    <div class="text-2xl font-black text-brand-600 dark:text-brand-400">{{ $product->formatted_price }}</div>
                    <div class="text-xs text-emerald-600 font-bold flex items-center gap-1 mt-1">
                        <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                        <span>In Stock &bull; Ready to Ship</span>
                    </div>
                </div>

                <div class="text-xs space-y-2 text-slate-500 border-y border-slate-100 dark:border-white/10 py-3">
                    <div class="flex justify-between">
                        <span>Ships from:</span>
                        <span class="font-semibold text-slate-700 dark:text-slate-300">NovaMart Logistics</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Sold by:</span>
                        <span class="font-semibold text-slate-700 dark:text-slate-300">NovaMart Official Store</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Return Policy:</span>
                        <span class="font-semibold text-slate-700 dark:text-slate-300">7-Day Replacement</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <!-- Add to Cart Form -->
                    <form action="{{ route('cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->_id }}">
                        <input type="hidden" name="variant" :value="selectedVariant">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="w-full py-3.5 rounded-2xl bg-brand-50 hover:bg-brand-100 dark:bg-brand-950/60 dark:hover:bg-brand-900/60 text-brand-700 dark:text-brand-300 border border-brand-200 dark:border-brand-800 font-bold text-xs shadow-soft transition-all flex items-center justify-center gap-2">
                            <i data-lucide="shopping-cart" class="w-4 h-4 text-brand-600"></i>
                            <span>Add to Cart</span>
                        </button>
                    </form>

                    <!-- Buy Now Direct Checkout Form -->
                    <form action="{{ route('cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->_id }}">
                        <input type="hidden" name="variant" :value="selectedVariant">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="w-full py-3.5 rounded-2xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs shadow-glow transition-all flex items-center justify-center gap-2">
                            <i data-lucide="zap" class="w-4 h-4"></i>
                            <span>Buy Now &bull; Instant Checkout</span>
                        </button>
                    </form>
                </div>

                <!-- Payment Assurance Pill -->
                <div class="pt-2 text-center text-[11px] text-slate-400 flex items-center justify-center gap-2">
                    <i data-lucide="lock" class="w-3.5 h-3.5 text-brand-500"></i>
                    <span>Razorpay 256-bit Encrypted</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Technical Specifications Table & Detailed Description -->
    <div class="mt-16 pt-12 border-t border-slate-200 dark:border-white/10 grid grid-cols-1 lg:grid-cols-12 gap-12">
        <div class="lg:col-span-7 space-y-6">
            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">Product Description & Technical Architecture</h2>
            <div class="prose dark:prose-invert max-w-none text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                <p>{{ $product->description }}</p>
            </div>
        </div>

        <div class="lg:col-span-5 space-y-4">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Technical Specifications</h3>
            <div class="rounded-2xl border border-slate-200 dark:border-white/10 overflow-hidden divide-y divide-slate-100 dark:divide-white/5 bg-white dark:bg-[#0f1723] text-xs">
                @foreach($product->specifications ?? [] as $spec)
                <div class="grid grid-cols-2 p-3">
                    <span class="font-medium text-slate-500">{{ $spec['key'] }}</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200 font-mono">{{ $spec['value'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Verified Customer Reviews Section (Amazon/Flipkart Style) -->
    <section id="reviews" class="mt-20 pt-12 border-t border-slate-200 dark:border-white/10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">

            <!-- Review Summary & Breakdown -->
            <div class="lg:col-span-4 space-y-6">
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Customer Reviews</h3>
                <div class="flex items-center gap-4">
                    <div class="text-5xl font-black font-mono text-brand-600 dark:text-brand-400">{{ $product->rating_avg }}</div>
                    <div>
                        <div class="text-amber-500 text-lg">★★★★★</div>
                        <p class="text-xs text-slate-500">{{ $totalReviews }} total ratings</p>
                    </div>
                </div>

                <!-- Star Breakdown Bars -->
                <div class="space-y-2 text-xs">
                    @for($s = 5; $s >= 1; $s--)
                    @php
                        $count = $breakdown[$s] ?? 0;
                        $pct = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
                    @endphp
                    <div class="flex items-center gap-3">
                        <span class="w-8 font-mono text-slate-500">{{ $s }} star</span>
                        <div class="flex-1 h-2 bg-slate-100 dark:bg-white/5 rounded-full overflow-hidden">
                            <div class="h-full bg-amber-400 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                        <span class="w-8 text-right font-mono text-slate-400">{{ $pct }}%</span>
                    </div>
                    @endfor
                </div>

                <!-- Write Review Trigger / Status -->
                <div class="pt-4">
                    @if($hasPurchased)
                    <div x-data="{
                        rating: 5,
                        hoverRating: 0,
                        ratingLabels: {
                            1: '1 Star - Poor',
                            2: '2 Stars - Disappointing',
                            3: '3 Stars - Average',
                            4: '4 Stars - Very Good',
                            5: '5 Stars - Outstanding'
                        }
                    }" class="p-4 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 space-y-3.5">
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-200 dark:border-white/10">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                <i data-lucide="badge-check" class="w-4 h-4"></i>
                                <span>Verified Purchaser Review</span>
                            </span>
                        </div>

                        <!-- Review Form -->
                        <form action="{{ route('shop.review.store', $product->slug) }}" method="POST" class="space-y-3 text-xs">
                            @csrf
                            <input type="hidden" name="rating" :value="rating">

                            <!-- Interactive Custom Star Rating Picker -->
                            <div>
                                <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-1">Overall Rating</label>
                                <div class="flex items-center gap-1.5 py-1">
                                    <template x-for="star in 5" :key="star">
                                        <button type="button" 
                                                @click="rating = star"
                                                @mouseenter="hoverRating = star"
                                                @mouseleave="hoverRating = 0"
                                                class="p-0.5 text-xl transition-transform hover:scale-125 focus:outline-none"
                                                :class="(hoverRating ? star <= hoverRating : star <= rating) ? 'text-amber-400' : 'text-slate-300 dark:text-slate-600'">
                                            ★
                                        </button>
                                    </template>
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-200 ml-2" x-text="ratingLabels[hoverRating || rating]"></span>
                                </div>
                            </div>

                            <!-- Custom Review Headline Input -->
                            <div>
                                <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-1">Review Headline</label>
                                <input type="text" 
                                       name="title" 
                                       required 
                                       placeholder="e.g. Exceptional build quality, stunning screen and all-day battery life!" 
                                       class="w-full px-3 py-2 bg-white dark:bg-[#0f1723] border border-slate-200 dark:border-white/10 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/40 focus:border-brand-500">
                            </div>

                            <!-- Custom Review Experience Textarea -->
                            <div>
                                <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-1">Your Detailed Experience</label>
                                <textarea name="comment" 
                                          rows="3" 
                                          required 
                                          placeholder="Share details about performance, build materials, camera, battery, and delivery packaging..." 
                                          class="w-full px-3 py-2 bg-white dark:bg-[#0f1723] border border-slate-200 dark:border-white/10 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/40 focus:border-brand-500"></textarea>
                            </div>

                            <button type="submit" 
                                    class="w-full py-2.5 bg-brand-600 hover:bg-brand-500 text-white rounded-xl text-xs font-bold transition-all shadow-soft flex items-center justify-center gap-1.5">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                <span>Submit Verified Review</span>
                            </button>
                        </form>
                    </div>
                    @elseif(Auth::check())
                    <!-- Logged in but has NOT purchased -->
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-center space-y-2">
                        <div class="w-9 h-9 rounded-full bg-slate-200 dark:bg-white/10 text-slate-600 dark:text-slate-300 flex items-center justify-center mx-auto">
                            <i data-lucide="shield-alert" class="w-4 h-4 text-brand-500"></i>
                        </div>
                        <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">Verified Buyers Only</h4>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            Only customers who purchased this item on NovaMart can submit a review to ensure 100% authentic community ratings.
                        </p>
                    </div>
                    @else
                    <!-- Not logged in -->
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-center space-y-2.5">
                        <div class="w-9 h-9 rounded-full bg-brand-50 dark:bg-brand-950/60 text-brand-600 dark:text-brand-400 flex items-center justify-center mx-auto">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </div>
                        <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">Have You Purchased This?</h4>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            Sign in with the account you used to order this product to write a verified customer review.
                        </p>
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs transition-colors">
                            <i data-lucide="log-in" class="w-3.5 h-3.5"></i>
                            <span>Sign In to Review</span>
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Reviews List -->
            <div class="lg:col-span-8 space-y-6">
                @if($reviews->isEmpty())
                <div class="p-8 text-center bg-white dark:bg-[#0f1723] rounded-2xl border border-slate-200 dark:border-white/10">
                    <p class="text-xs text-slate-500">No customer reviews yet. Be the first to share your thoughts!</p>
                </div>
                @else
                <div class="space-y-4">
                    @foreach($reviews as $rev)
                    <div class="p-5 rounded-2xl bg-white dark:bg-[#0f1723] border border-slate-200 dark:border-white/10 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-brand-600 text-white text-[11px] font-bold flex items-center justify-center">
                                    {{ strtoupper(substr($rev->user_name, 0, 1)) }}
                                </div>
                                <span class="text-xs font-bold text-slate-900 dark:text-white">{{ $rev->user_name }}</span>
                                @if($rev->is_verified_purchase)
                                <span class="text-[10px] text-brand-600 dark:text-brand-400 font-semibold flex items-center gap-0.5">
                                    <i data-lucide="badge-check" class="w-3.5 h-3.5"></i>
                                    <span>Verified Purchase</span>
                                </span>
                                @endif
                            </div>
                            <span class="text-[11px] text-slate-400 font-mono">{{ $rev->created_at->format('d M Y') }}</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="text-amber-500 text-xs">
                                @for($i = 0; $i < $rev->rating; $i++)★@endfor
                            </span>
                            <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ $rev->title }}</h4>
                        </div>

                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">{{ $rev->comment }}</p>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

        </div>
    </section>

    <!-- Related Products -->
    @if($relatedProducts->isNotEmpty())
    <section class="mt-12 sm:mt-16 pt-8 border-t border-slate-200 dark:border-white/10 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-base sm:text-lg font-black text-slate-900 dark:text-white">Customers Also Bought</h3>
            <a href="{{ route('shop.category', $product->category_slug) }}" class="text-xs font-bold text-brand-600 dark:text-brand-400 hover:underline">
                View More in {{ $product->category_name }} &rarr;
            </a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2.5 sm:gap-3.5">
            @foreach($relatedProducts as $rel)
                @include('components.product-card', ['prod' => $rel])
            @endforeach
        </div>
    </section>
    @endif

</div>
@endsection
