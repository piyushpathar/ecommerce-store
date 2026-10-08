<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'title',
        'slug',
        'sku',
        'category_id',
        'category_slug',
        'category_name',
        'brand',
        'short_description',
        'description',
        'price',
        'compare_price',
        'discount_percentage',
        'stock',
        'stock_status',
        'images',
        'thumbnail',
        'is_featured',
        'is_bestseller',
        'is_nova_choice',
        'rating_avg',
        'rating_count',
        'specifications',
        'variants',
        'tags',
        'seo',
        'view_count',
        'sales_count',
        'is_active',
    ];

    protected $casts = [
        'price' => 'float',
        'compare_price' => 'float',
        'discount_percentage' => 'integer',
        'stock' => 'integer',
        'rating_avg' => 'float',
        'rating_count' => 'integer',
        'is_featured' => 'boolean',
        'is_bestseller' => 'boolean',
        'is_nova_choice' => 'boolean',
        'is_active' => 'boolean',
        'images' => 'array',
        'specifications' => 'array',
        'variants' => 'array',
        'tags' => 'array',
        'seo' => 'array',
        'view_count' => 'integer',
        'sales_count' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id');
    }

    public function getFormattedPriceAttribute(): string
    {
        return '₹' . number_format($this->price, 2);
    }

    public function getFormattedComparePriceAttribute(): string
    {
        return $this->compare_price ? '₹' . number_format($this->compare_price, 2) : '';
    }

    public function getCalculatedDiscountAttribute(): int
    {
        if ($this->compare_price && $this->compare_price > $this->price) {
            return (int) round((($this->compare_price - $this->price) / $this->compare_price) * 100);
        }
        return $this->discount_percentage ?? 0;
    }
}
