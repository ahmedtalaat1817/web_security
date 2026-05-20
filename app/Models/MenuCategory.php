<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuCategory extends Model
{
    protected $fillable = [
        'restaurant_id',
        'name',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'category_id')
        ->orderBy('sort_order');
    }

    public function activeMenuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'category_id')
            ->where('is_available', true)
            ->orderBy('sort_order');
    }
}