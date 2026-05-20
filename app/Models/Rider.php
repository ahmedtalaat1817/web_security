<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Rider extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vehicle_type',
        'vehicle_plate',
        'license_number',
        'phone',
        'rating',
        'total_deliveries',
        'status',
        'is_active',
    ];

    protected $casts = [
        'rating' => 'decimal:2',
        'total_deliveries' => 'integer',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function currentOrder(): HasOne
    {
        return $this->hasOne(Order::class)->whereIn('status', ['confirmed', 'preparing', 'on_the_way']);
    }

    public function location(): HasOne
    {
        return $this->hasOne(RiderLocation::class)->latestOfMany('recorded_at');
    }

    public function locations(): HasMany
    {
        return $this->hasMany(RiderLocation::class)->orderBy('recorded_at', 'desc');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class, 'recipient_id')->where('recipient_type', 'rider');
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available' && $this->is_active;
    }
}
