<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProjectCategory extends Model
{
    protected $fillable = ['name', 'slug', 'is_visible', 'order'];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }
}
