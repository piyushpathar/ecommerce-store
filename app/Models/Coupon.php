<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $table = 'coupons';

    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'min_spend',
        'max_discount',
        'usage_limit',
        'used_count',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'float',
        'min_spend' => 'float',
        'max_discount' => 'float',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function isValid(float $subtotal): array
    {
        if (!$this->is_active) {
            return ['valid' => false, 'message' => 'Coupon is inactive.'];
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return ['valid' => false, 'message' => 'Coupon has expired.'];
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return ['valid' => false, 'message' => 'Coupon usage limit reached.'];
        }

        if ($this->min_spend && $subtotal < $this->min_spend) {
            return ['valid' => false, 'message' => 'Minimum order amount of ₹' . number_format($this->min_spend, 2) . ' required.'];
        }

        $discount = 0.0;
        if ($this->discount_type === 'percentage') {
            $discount = ($subtotal * $this->discount_value) / 100;
            if ($this->max_discount && $discount > $this->max_discount) {
                $discount = $this->max_discount;
            }
        } else {
            $discount = min($this->discount_value, $subtotal);
        }

        return ['valid' => true, 'discount' => round($discount, 2)];
    }
}
