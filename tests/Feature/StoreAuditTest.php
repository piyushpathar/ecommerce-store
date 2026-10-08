<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Page;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Services\RazorpayService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StoreAuditTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::flushCache();
    }

    protected function admin(): User
    {
        return User::where('email', 'admin@novamart.com')->first();
    }

    protected function customer(): User
    {
        return User::where('email', 'demo@novamart.com')->first();
    }

    protected function settingsPayload(array $overrides = []): array
    {
        $payload = [];
        foreach (config('store.defaults') as $key => $_) {
            $payload[$key] = Setting::get($key);
        }
        // Checkboxes: browsers omit unchecked ones entirely
        foreach (config('store.toggles') as $toggle) {
            if ($payload[$toggle] !== '1') {
                unset($payload[$toggle]);
            }
        }
        return array_merge($payload, $overrides);
    }

    protected function address(): array
    {
        return ['name' => 'Test', 'phone' => '9999999999', 'address_line1' => 'Line 1', 'city' => 'Pune', 'state' => 'Maharashtra', 'pincode' => '411001', 'address_type' => 'home'];
    }

    protected function checkoutPayload(): array
    {
        return ['shipping_address' => $this->address(), 'customer_name' => 'Test', 'customer_email' => 'test@example.com', 'customer_phone' => '9999999999', 'shipping_type' => 'standard'];
    }

    public function test_saving_settings_unchanged_keeps_every_section_visible(): void
    {
        $this->actingAs($this->admin())->post('/admin/settings', $this->settingsPayload())->assertSessionHas('success');

        foreach (config('store.toggles') as $toggle) {
            $this->assertSame(config("store.defaults.$toggle"), Setting::get($toggle), "$toggle changed after a plain save");
        }
    }

    public function test_settings_drive_the_storefront(): void
    {
        $this->actingAs($this->admin())->post('/admin/settings', $this->settingsPayload([
            'store_name' => 'Acme Store',
            'mobiles_title' => 'Phones Zone',
            'mobiles_subtitle' => 'Subtitle Check',
            'deals_title' => 'Deals Zone',
            'footer_copyright' => '© {year} Acme',
            'standard_shipping_fee' => '77',
            'free_shipping_threshold' => '100000',
            'section_deals_enabled' => null,
        ]))->assertSessionHas('success');

        $home = $this->get('/')->assertOk();
        $home->assertSee('Phones Zone')->assertSee('Subtitle Check')->assertSee('© ' . date('Y') . ' Acme')->assertDontSee('Deals Zone');

        $product = Product::where('is_active', true)->where('stock', '>', 0)->where('price', '<', 100000)->first();
        $this->post('/cart/add', ['product_id' => $product->id]);
        $this->getJson('/api/cart/summary')->assertJsonPath('shipping', 77);
    }

    public function test_invalid_json_setting_is_rejected(): void
    {
        $this->actingAs($this->admin())->post('/admin/settings', $this->settingsPayload(['hero_slides' => '{broken']))
            ->assertSessionHas('error');
        $this->assertIsArray(json_decode(Setting::get('hero_slides'), true));
    }

    public function test_disabling_razorpay_hides_it_and_blocks_prepare(): void
    {
        Setting::set('razorpay_enabled', '0');
        $product = Product::where('is_active', true)->where('stock', '>', 0)->first();
        $this->post('/cart/add', ['product_id' => $product->id]);

        $this->get('/checkout')->assertOk()->assertDontSee('Razorpay (Instant UPI');
        $this->postJson('/checkout/payment/prepare', [])->assertStatus(400);
    }

    public function test_cod_order_reserves_stock_and_cancel_returns_it(): void
    {
        $product = Product::where('is_active', true)->where('stock', '>', 2)->first();
        [$stock, $sales] = [$product->stock, $product->sales_count];

        $this->post('/cart/add', ['product_id' => $product->id, 'quantity' => 2])->assertSessionHasNoErrors();
        $this->post('/checkout/place-cod', $this->checkoutPayload())->assertRedirect();

        $product->refresh();
        $this->assertSame($stock - 2, $product->stock);
        $this->assertSame($sales + 2, $product->sales_count);

        $order = Order::latest('id')->first();
        $this->assertStringStartsWith('NM-', $order->order_number);
        $this->actingAs($this->admin())->post("/admin/orders/{$order->id}/status", ['status' => 'cancelled']);
        $this->assertSame($stock, $product->fresh()->stock);

        $this->post("/admin/orders/{$order->id}/status", ['status' => 'confirmed'])->assertSessionHas('error');
    }

    public function test_cannot_add_more_than_stock(): void
    {
        $product = Product::where('is_active', true)->first();
        $product->update(['stock' => 1]);

        $this->post('/cart/add', ['product_id' => $product->id, 'quantity' => 5])->assertSessionHas('error');
        $this->getJson('/api/cart/summary')->assertJsonPath('count', 0);
    }

    public function test_admin_price_change_updates_existing_cart(): void
    {
        $product = Product::where('is_active', true)->where('stock', '>', 0)->first();
        $this->post('/cart/add', ['product_id' => $product->id]);
        $product->update(['price' => 123.45]);

        $this->getJson('/api/cart/summary')->assertJsonPath('items.0.price', 123.45);
    }

    public function test_mock_payment_flow_and_replay_protection(): void
    {
        $product = Product::where('is_active', true)->where('stock', '>', 0)->first();
        $this->post('/cart/add', ['product_id' => $product->id]);

        // Verify without a prepared payment is rejected
        $this->postJson('/checkout/payment/verify', $this->checkoutPayload() + ['razorpay_order_id' => 'order_fake', 'razorpay_payment_id' => 'pay_x'])->assertStatus(400);

        $prep = $this->postJson('/checkout/payment/prepare', ['shipping_type' => 'standard'])->assertOk()->json();
        $verify = $this->checkoutPayload() + ['razorpay_order_id' => $prep['razorpay_order']['id'], 'razorpay_payment_id' => 'pay_test123'];

        $first = $this->postJson('/checkout/payment/verify', $verify)->assertOk()->json('redirect');
        $this->assertSame('paid', Order::where('razorpay_payment_id', 'pay_test123')->value('payment_status'));

        // Same payment again returns the same order instead of creating a second one
        $this->assertSame($first, $this->postJson('/checkout/payment/verify', $verify)->json('redirect'));
        $this->assertSame(1, Order::where('razorpay_payment_id', 'pay_test123')->count());
    }

    public function test_production_never_accepts_mock_or_prefixed_payments(): void
    {
        Setting::set('razorpay_mock_mode', '1');
        $this->app['env'] = 'production';

        $service = new RazorpayService();
        $this->assertFalse($service->isMockMode());
        $this->assertFalse($service->verifySignature('order_anything', 'pay_anything', 'sig'));
    }

    public function test_guest_can_add_address_and_see_it_at_checkout(): void
    {
        $product = Product::where('is_active', true)->where('stock', '>', 0)->first();
        $this->post('/cart/add', ['product_id' => $product->id]);

        $this->post('/checkout/address', $this->address())->assertSessionHas('success');
        $this->get('/checkout')->assertOk()->assertSee('411001');
    }

    public function test_admin_can_deactivate_product(): void
    {
        $product = Product::where('is_active', true)->first();
        $this->actingAs($this->admin())->put("/admin/products/{$product->id}", [
            'title' => $product->title, 'category_id' => $product->category_id, 'brand' => $product->brand,
            'price' => $product->price, 'stock' => $product->stock, 'short_description' => $product->short_description,
            'description' => $product->description, 'thumbnail' => $product->thumbnail,
            // is_active checkbox left unchecked
        ])->assertRedirect();

        $this->assertFalse($product->fresh()->is_active);
        $this->get("/product/{$product->slug}")->assertNotFound();
    }

    public function test_category_rules(): void
    {
        $this->actingAs($this->admin());
        $existing = Category::has('products')->first();

        $this->post('/admin/categories', ['name' => $existing->name, 'description' => 'x', 'icon' => 'x', 'image' => 'https://example.com/a.png'])
            ->assertSessionHasErrors('slug');
        $this->delete("/admin/categories/{$existing->id}")->assertSessionHas('error');
        $this->assertNotNull($existing->fresh());

        $this->post("/admin/categories/{$existing->id}/toggle");
        $this->assertFalse($existing->fresh()->is_active);
        $this->get("/category/{$existing->slug}")->assertNotFound();

        $this->put("/admin/categories/{$existing->id}", ['name' => 'Renamed Cat', 'description' => 'x', 'icon' => 'x', 'image' => 'https://example.com/a.png', 'sort_order' => 1, 'is_active' => 1]);
        $this->assertSame('Renamed Cat', Product::where('category_id', $existing->id)->value('category_name'));
    }

    public function test_coupon_rules(): void
    {
        $this->actingAs($this->admin());
        $this->post('/admin/coupons', ['code' => 'audit10', 'discount_type' => 'percentage', 'discount_value' => 10])->assertSessionHas('success');
        $this->post('/admin/coupons', ['code' => 'AUDIT10', 'discount_type' => 'percentage', 'discount_value' => 10])->assertSessionHasErrors('code');

        $coupon = Coupon::where('code', 'AUDIT10')->first();
        $this->post("/admin/coupons/{$coupon->id}/toggle");
        $this->assertFalse($coupon->fresh()->is_active);
    }

    public function test_page_slug_must_be_unique(): void
    {
        $this->actingAs($this->admin());
        $pages = Page::take(2)->get();

        $this->post('/admin/pages', ['title' => $pages[0]->title, 'slug' => $pages[0]->slug, 'content' => 'x'])->assertSessionHasErrors('slug');
        $this->put("/admin/pages/{$pages[1]->id}", ['title' => 'x', 'slug' => $pages[0]->slug, 'content' => 'x'])->assertSessionHasErrors('slug');
    }

    public function test_disabled_users_are_locked_out(): void
    {
        $admin = $this->admin();
        $customer = $this->customer();

        $this->actingAs($admin)->post("/admin/users/{$admin->id}/toggle-role")->assertSessionHas('error');
        $this->assertTrue($admin->fresh()->isAdmin());

        $this->post("/admin/users/{$customer->id}/toggle-status");
        $this->assertFalse($customer->fresh()->is_active);

        $this->actingAs($customer->fresh())->get('/account')->assertRedirect('/login');
        $this->assertGuest();

        $this->post('/login', ['email' => $customer->email, 'password' => 'Password@123']);
        $this->assertGuest();
    }

    public function test_review_rules(): void
    {
        $customer = $this->customer();
        $order = Order::where('user_id', $customer->id)->first();
        $product = Product::find($order->items[0]['product_id']);
        $review = ['rating' => 5, 'title' => 'Great', 'comment' => 'Really great product overall.'];

        // Customer already reviewed this product in the seed data
        $this->actingAs($customer)->post("/product/{$product->slug}/review", $review)->assertSessionHas('error', 'You have already reviewed this product.');

        $order->update(['status' => 'cancelled']);
        \App\Models\Review::where('user_id', $customer->id)->delete();
        $this->post("/product/{$product->slug}/review", $review)->assertSessionHas('error');
    }

    public function test_google_login_toggle_hides_buttons(): void
    {
        Setting::set('google_login_enabled', '0');
        $this->get('/login')->assertOk()->assertDontSee('Continue with Google');
        Setting::set('google_login_enabled', '1');
        $this->get('/login')->assertOk()->assertSee('Continue with Google');
    }
}
