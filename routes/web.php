<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\ProductController as AdminProduct;
use App\Http\Controllers\Admin\CategoryController as AdminCategory;
use App\Http\Controllers\Admin\OrderController as AdminOrder;
use App\Http\Controllers\Admin\PlanController as AdminPlan;
use App\Http\Controllers\Admin\CouponController as AdminCoupon;
use App\Http\Controllers\Admin\UserController as AdminUser;
use App\Http\Controllers\Admin\SettingController as AdminSetting;

/*
|--------------------------------------------------------------------------
| Public Storefront Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [ShopController::class, 'index'])->name('shop.index');
Route::get('/category/{slug}', [ShopController::class, 'category'])->name('shop.category');
Route::get('/product/{slug}', [ShopController::class, 'product'])->name('shop.product');
Route::post('/product/{slug}/review', [ShopController::class, 'storeReview'])->name('shop.review.store');

Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
Route::get('/plans/{slug}/checkout', [PlanController::class, 'subscribe'])->name('plans.subscribe');

Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::get('/api/search/suggest', [SearchController::class, 'suggest'])->name('search.suggest');

/*
|--------------------------------------------------------------------------
| Dynamic Content & Policy Pages Routes
|--------------------------------------------------------------------------
*/
Route::get('/page/{slug}', [\App\Http\Controllers\PageController::class, 'show'])->name('pages.show');
Route::post('/page/contact/submit', [\App\Http\Controllers\PageController::class, 'contactSubmit'])->name('pages.contact.submit');

/*
|--------------------------------------------------------------------------
| Shopping Cart Routes
|--------------------------------------------------------------------------
*/
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/coupon/apply', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
Route::post('/cart/coupon/remove', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');
Route::get('/api/cart/summary', [CartController::class, 'summary'])->name('cart.summary');

/*
|--------------------------------------------------------------------------
| Checkout & Orders Routes
|--------------------------------------------------------------------------
*/
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/address', [CheckoutController::class, 'addAddress'])->name('checkout.address.add');
Route::post('/checkout/payment/prepare', [CheckoutController::class, 'preparePayment'])->name('checkout.payment.prepare');
Route::post('/checkout/payment/verify', [CheckoutController::class, 'verifyPayment'])->name('checkout.payment.verify');
Route::post('/checkout/place-cod', [CheckoutController::class, 'placeCod'])->name('checkout.place.cod');
Route::get('/orders/{orderNumber}', [CheckoutController::class, 'show'])->name('orders.show');
Route::post('/webhook/razorpay', [\App\Http\Controllers\WebhookController::class, 'handleRazorpay'])->name('webhook.razorpay');

/*
|--------------------------------------------------------------------------
| User Account Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/account', [AccountController::class, 'index'])->name('account.index');
    Route::post('/account/profile', [AccountController::class, 'updateProfile'])->name('account.profile.update');
    Route::post('/account/address', [AccountController::class, 'addAddress'])->name('account.address.add');
    Route::delete('/account/address/{id}', [AccountController::class, 'deleteAddress'])->name('account.address.delete');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Google / Gmail OAuth & 1-Click Simulation
Route::get('/auth/google', [AuthController::class, 'googleRedirect'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'googleCallback'])->name('auth.google.callback');
Route::get('/auth/google/simulate', [AuthController::class, 'googleSimulate'])->name('auth.google.simulate');

/*
|--------------------------------------------------------------------------
| SEO & Indexing Routes
|--------------------------------------------------------------------------
*/
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('seo.sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('seo.robots');

/*
|--------------------------------------------------------------------------
| Admin Control Panel Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/', [AdminDashboard::class, 'index'])->name('dashboard');

    // Products
    Route::get('/products', [AdminProduct::class, 'index'])->name('products.index');
    Route::get('/products/create', [AdminProduct::class, 'create'])->name('products.create');
    Route::post('/products', [AdminProduct::class, 'store'])->name('products.store');
    Route::get('/products/{id}/edit', [AdminProduct::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}', [AdminProduct::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [AdminProduct::class, 'destroy'])->name('products.destroy');

    // Categories
    Route::get('/categories', [AdminCategory::class, 'index'])->name('categories.index');
    Route::post('/categories', [AdminCategory::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}', [AdminCategory::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [AdminCategory::class, 'destroy'])->name('categories.destroy');

    // Orders
    Route::get('/orders', [AdminOrder::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [AdminOrder::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/status', [AdminOrder::class, 'updateStatus'])->name('orders.status');

    // Plans
    Route::get('/plans', [AdminPlan::class, 'index'])->name('plans.index');
    Route::post('/plans', [AdminPlan::class, 'store'])->name('plans.store');
    Route::put('/plans/{id}', [AdminPlan::class, 'update'])->name('plans.update');
    Route::delete('/plans/{id}', [AdminPlan::class, 'destroy'])->name('plans.destroy');

    // Coupons
    Route::get('/coupons', [AdminCoupon::class, 'index'])->name('coupons.index');
    Route::post('/coupons', [AdminCoupon::class, 'store'])->name('coupons.store');
    Route::put('/coupons/{id}', [AdminCoupon::class, 'update'])->name('coupons.update');
    Route::delete('/coupons/{id}', [AdminCoupon::class, 'destroy'])->name('coupons.destroy');

    // Users
    Route::get('/users', [AdminUser::class, 'index'])->name('users.index');
    Route::post('/users/{id}/toggle-status', [AdminUser::class, 'toggleStatus'])->name('users.toggle.status');
    Route::post('/users/{id}/toggle-role', [AdminUser::class, 'toggleRole'])->name('users.toggle.role');

    // Settings & Site Customizer
    Route::get('/settings', [AdminSetting::class, 'index'])->name('settings.index');
    Route::post('/settings', [AdminSetting::class, 'update'])->name('settings.update');

    // Store Pages Manager
    Route::get('/pages', [\App\Http\Controllers\Admin\PageController::class, 'index'])->name('pages.index');
    Route::get('/pages/create', [\App\Http\Controllers\Admin\PageController::class, 'create'])->name('pages.create');
    Route::post('/pages', [\App\Http\Controllers\Admin\PageController::class, 'store'])->name('pages.store');
    Route::get('/pages/{id}/edit', [\App\Http\Controllers\Admin\PageController::class, 'edit'])->name('pages.edit');
    Route::put('/pages/{id}', [\App\Http\Controllers\Admin\PageController::class, 'update'])->name('pages.update');
    Route::delete('/pages/{id}', [\App\Http\Controllers\Admin\PageController::class, 'destroy'])->name('pages.destroy');
    Route::post('/pages/{id}/toggle', [\App\Http\Controllers\Admin\PageController::class, 'togglePublish'])->name('pages.toggle');
});
