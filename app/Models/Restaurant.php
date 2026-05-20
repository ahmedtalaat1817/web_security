<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Restaurant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'address',
        'latitude',
        'longitude',
        'phone',
        'email',
        'logo',
        'cover_image',
        'status',
        'rating',
        'total_reviews',
        'delivery_fee',
        'delivery_time_minutes',
        'minimum_order',
        'is_open',
        'average_rating',
        'stripe_connect_account_id',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'rating' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'minimum_order' => 'decimal:2',
        'delivery_time_minutes' => 'integer',
        'total_reviews' => 'integer',
        'is_open' => 'boolean',
        'average_rating' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(MenuCategory::class)->orderBy('sort_order');
    }

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('type', 'restaurant');
    }

    public function recalculateRating(): void
    {
        $avg = $this->reviews()->avg('rating');
        $count = $this->reviews()->count();

        $this->update([
            'rating' => round($avg ?? 0, 1),
            'total_reviews' => $count,
        ]);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class, 'recipient_id')->where('recipient_type', 'restaurant');
    }

    public function activeCategories(): HasMany
    {
        return $this->hasMany(MenuCategory::class)->where('is_active', true)->orderBy('sort_order');
    }
}