<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $table = 'plans';

    protected $fillable = [
        'name',
        'slug',
        'badge',
        'price',
        'interval',
        'duration_days',
        'features',
        'is_trial',
        'is_popular',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'float',
        'duration_days' => 'integer',
        'is_trial' => 'boolean',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'features' => 'array',
    ];

    public function get_idAttribute()
    {
        return (string) $this->id;
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'plan_id');
    }

    public function getFormattedPriceAttribute(): string
    {
        return '₹' . number_format($this->price, 0);
    }
}
