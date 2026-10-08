<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'icon',
        'image',
        'is_featured',
        'sort_order',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'is_active',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function get_idAttribute()
    {
        return (string) $this->id;
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
}
