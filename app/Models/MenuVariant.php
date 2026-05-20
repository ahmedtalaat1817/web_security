<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MenuVariant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'menu_item_id',
        'name',
        'price_adjustment',
        'is_available',
    ];

    protected $casts = [
        'price_adjustment' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }
}