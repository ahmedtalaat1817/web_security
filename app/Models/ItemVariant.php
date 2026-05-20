<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemVariant extends Model
{
    protected $fillable = [
        'menu_item_id',
        'name',
        'price_modifier',
        'is_available',
    ];

    protected $casts = [
        'price_modifier' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function getEffectivePrice(): float
    {
        return (float) $this->menuItem->price + (float) $this->price_modifier;
    }
}