<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'stripe_payment_intent_id',
        'stripe_charge_id',
        'method',
        'amount',
        'platform_fee',
        'restaurant_amount',
        'rider_amount',
        'status',
        'currency',
        'customer_email',
        'failure_reason',
        'idempotency_key',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'restaurant_amount' => 'decimal:2',
        'rider_amount' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isSuccessful(): bool
    {
        return $this->status === 'succeeded';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
}