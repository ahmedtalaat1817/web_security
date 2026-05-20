<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_id',
        'restaurant_id',
        'rider_id',
        'status',
        'subtotal',
        'delivery_fee',
        'platform_fee',
        'surge_amount',
        'discount',
        'total',
        'delivery_address',
        'delivery_latitude',
        'delivery_longitude',
        'delivery_instructions',
        'customer_phone',
        'customer_name',
        'estimated_delivery_time',
        'confirmed_at',
        'preparing_at',
        'picked_up_at',
        'delivered_at',
        'cancelled_at',
        'cancellation_reason',
        'cancelled_by',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'surge_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'delivery_latitude' => 'decimal:8',
        'delivery_longitude' => 'decimal:8',
        'estimated_delivery_time' => 'datetime',
        'confirmed_at' => 'datetime',
        'preparing_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-'.date('Ymd').'-'.strtoupper(Str::random(8));
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function rider(): BelongsTo
    {
        return $this->belongsTo(Rider::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(OrderStatusLog::class)->orderBy('timestamp', 'desc');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function canTransitionTo(OrderStatus $newStatus): bool
    {
        $currentStatus = OrderStatus::fromString($this->status);

        return $currentStatus->canTransitionTo($newStatus);
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['placed', 'confirmed', 'preparing', 'on_the_way']);
    }

    public function calculatePlatformFee(float $commissionRate = 0.10): float
    {
        return round($this->subtotal * $commissionRate, 2);
    }
}
