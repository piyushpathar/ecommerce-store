<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('icon')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Products
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('sku')->nullable()->index();
            $table->string('category_id')->nullable()->index();
            $table->string('category_slug')->nullable()->index();
            $table->string('category_name')->nullable();
            $table->string('brand')->nullable()->index();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('compare_price', 12, 2)->nullable();
            $table->integer('discount_percentage')->default(0);
            $table->integer('stock')->default(0);
            $table->string('stock_status')->default('in_stock');
            $table->json('images')->nullable();
            $table->string('thumbnail')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_bestseller')->default(false);
            $table->boolean('is_nova_choice')->default(false);
            $table->boolean('is_nifty_choice')->default(false);
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->json('specifications')->nullable();
            $table->json('variants')->nullable();
            $table->json('tags')->nullable();
            $table->json('seo')->nullable();
            $table->integer('view_count')->default(0);
            $table->integer('sales_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Orders
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('user_id')->nullable()->index();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable()->index();
            $table->string('customer_phone')->nullable();
            $table->json('items')->nullable();
            $table->string('plan_id')->nullable();
            $table->boolean('is_subscription')->default(false);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->string('coupon_code')->nullable();
            $table->decimal('shipping_fee', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->json('shipping_address')->nullable();
            $table->string('status')->default('pending')->index();
            $table->string('payment_method')->default('cod');
            $table->string('payment_status')->default('pending')->index();
            $table->string('razorpay_order_id')->nullable()->index();
            $table->string('razorpay_payment_id')->nullable()->index();
            $table->string('razorpay_signature')->nullable();
            $table->string('tracking_carrier')->nullable();
            $table->string('tracking_number')->nullable()->index();
            $table->json('status_history')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 4. Coupons
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('discount_type')->default('percentage');
            $table->decimal('discount_value', 12, 2)->default(0);
            $table->decimal('min_spend', 12, 2)->nullable();
            $table->decimal('max_discount', 12, 2)->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('used_count')->default(0);
            $table->dateTime('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. Pages (CMS)
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->boolean('is_published')->default(true);
            $table->boolean('show_in_header')->default(false);
            $table->boolean('show_in_footer')->default(false);
            $table->string('footer_column')->default('company');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 6. Settings (Dynamic Visual Store Customizer)
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->timestamps();
        });

        // 7. Reviews
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string('product_id')->nullable()->index();
            $table->string('user_id')->nullable()->index();
            $table->string('user_name')->nullable();
            $table->integer('rating')->default(5);
            $table->string('title')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('is_verified_purchase')->default(false);
            $table->boolean('is_approved')->default(true);
            $table->timestamps();
        });

        // 8. Plans
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('badge')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->string('interval')->default('month');
            $table->integer('duration_days')->default(30);
            $table->json('features')->nullable();
            $table->boolean('is_trial')->default(false);
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
    }
};
