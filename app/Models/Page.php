<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table = 'pages';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
        'is_published',
        'show_in_header',
        'show_in_footer',
        'footer_column',
        'sort_order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'show_in_header' => 'boolean',
        'show_in_footer' => 'boolean',
        'sort_order' => 'integer',
    ];
}
