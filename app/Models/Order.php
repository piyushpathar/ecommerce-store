<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'order_number',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'items',
        'plan_id',
        'is_subscription',
        'subtotal',
        'discount_amount',
        'coupon_code',
        'shipping_fee',
        'tax_amount',
        'total_amount',
        'shipping_address',
        'status',
        'payment_method',
        'payment_status',
        'razorpay_order_id',
        'razorpay_payment_id',
        'razorpay_signature',
        'tracking_carrier',
        'tracking_number',
        'status_history',
        'notes',
    ];

    protected $casts = [
        'items' => 'array',
        'shipping_address' => 'array',
        'status_history' => 'array',
        'subtotal' => 'float',
        'discount_amount' => 'float',
        'shipping_fee' => 'float',
        'tax_amount' => 'float',
        'total_amount' => 'float',
        'is_subscription' => 'boolean',
    ];

    public function get_idAttribute()
    {
        return (string) $this->id;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function getFormattedTotalAttribute(): string
    {
        return '₹' . number_format($this->total_amount, 2);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'delivered' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800',
            'shipped', 'out_for_delivery' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-400 dark:border-blue-800',
            'processing', 'confirmed' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800',
            'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-800',
            default => 'bg-slate-50 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
        };
    }
}
