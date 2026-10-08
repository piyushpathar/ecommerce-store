<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Coupon;
use App\Models\Setting;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected string $sessionKey = 'novamart_cart';
    protected string $couponKey = 'novamart_applied_coupon';

    public function getItems(): array
    {
        return Session::get($this->sessionKey, []);
    }

    public function add(Product $product, int $quantity = 1, ?string $variant = null): array
    {
        $cart = $this->getItems();
        $itemKey = $product->_id . ($variant ? '_' . md5($variant) : '');

        if (isset($cart[$itemKey])) {
            $cart[$itemKey]['quantity'] += $quantity;
        } else {
            $cart[$itemKey] = [
                'product_id' => (string) $product->_id,
                'title' => $product->title,
                'slug' => $product->slug,
                'sku' => $product->sku,
                'price' => (float) $product->price,
                'compare_price' => (float) ($product->compare_price ?? $product->price),
                'quantity' => $quantity,
                'image' => $product->thumbnail ?? ($product->images[0] ?? ''),
                'variant' => $variant,
                'stock' => (int) $product->stock,
            ];
        }

        Session::put($this->sessionKey, $cart);
        return $this->getSummary();
    }

    public function updateQuantity(string $itemKey, int $quantity): array
    {
        $cart = $this->getItems();
        if (isset($cart[$itemKey])) {
            if ($quantity <= 0) {
                unset($cart[$itemKey]);
            } else {
                $cart[$itemKey]['quantity'] = $quantity;
            }
            Session::put($this->sessionKey, $cart);
        }
        return $this->getSummary();
    }

    public function remove(string $itemKey): array
    {
        $cart = $this->getItems();
        if (isset($cart[$itemKey])) {
            unset($cart[$itemKey]);
            Session::put($this->sessionKey, $cart);
        }
        return $this->getSummary();
    }

    public function clear(): void
    {
        Session::forget($this->sessionKey);
        Session::forget($this->couponKey);
    }

    public function applyCoupon(string $code): array
    {
        $code = strtoupper(trim($code));
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return ['success' => false, 'message' => 'Invalid promotional coupon code.'];
        }

        $subtotal = $this->getSubtotal();
        $check = $coupon->isValid($subtotal);

        if (!$check['valid']) {
            return ['success' => false, 'message' => $check['message']];
        }

        Session::put($this->couponKey, [
            'code' => $coupon->code,
            'discount' => $check['discount'],
            'discount_type' => $coupon->discount_type,
            'discount_value' => $coupon->discount_value,
        ]);

        return ['success' => true, 'message' => 'Coupon applied successfully!', 'discount' => $check['discount']];
    }

    public function removeCoupon(): void
    {
        Session::forget($this->couponKey);
    }

    public function getSubtotal(): float
    {
        $total = 0.0;
        foreach ($this->getItems() as $item) {
            $total += ($item['price'] * $item['quantity']);
        }
        return round($total, 2);
    }

    public function getDiscount(): float
    {
        $applied = Session::get($this->couponKey);
        if (!$applied) {
            return 0.0;
        }

        $coupon = Coupon::where('code', $applied['code'])->first();
        if (!$coupon) {
            $this->removeCoupon();
            return 0.0;
        }

        $check = $coupon->isValid($this->getSubtotal());
        if (!$check['valid']) {
            $this->removeCoupon();
            return 0.0;
        }

        return (float) $check['discount'];
    }

    public function getShippingFee(string $type = 'standard'): float
    {
        $subtotal = $this->getSubtotal();
        if ($subtotal === 0.0) {
            return 0.0;
        }

        $freeThreshold = (float) Setting::get('free_shipping_threshold', 999);
        $standardFee = (float) Setting::get('standard_shipping_fee', 99);
        $expressFee = (float) Setting::get('express_shipping_fee', 199);

        if ($type === 'express') {
            return $expressFee;
        }

        return ($subtotal >= $freeThreshold) ? 0.0 : $standardFee;
    }

    public function getSummary(string $shippingType = 'standard'): array
    {
        $items = $this->getItems();
        $subtotal = $this->getSubtotal();
        $discount = $this->getDiscount();
        $shipping = $this->getShippingFee($shippingType);
        $total = max(0.0, round($subtotal - $discount + $shipping, 2));

        $count = 0;
        foreach ($items as $item) {
            $count += $item['quantity'];
        }

        $appliedCoupon = Session::get($this->couponKey);
        $freeThreshold = (float) Setting::get('free_shipping_threshold', 999);
        $awayFromFree = max(0.0, $freeThreshold - $subtotal);

        return [
            'items' => array_values($items),
            'count' => $count,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'coupon' => $appliedCoupon,
            'shipping' => $shipping,
            'shipping_type' => $shippingType,
            'free_shipping_threshold' => $freeThreshold,
            'away_from_free_shipping' => $awayFromFree,
            'total' => $total,
        ];
    }
}
