<?php

namespace App\Models;

use App\Enums\UserType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable([
    'name', 'email', 'password', 'user_type', 'phone', 'latitude', 'longitude',
    'owner_name', 'national_id', 'commercial_registration_number', 'tax_id',
    'restaurant_name', 'restaurant_address', 'partner_package_id', 'partner_status',
    'payment_id', 'partner_since',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'phone',
        'latitude',
        'longitude',
        'owner_name',
        'national_id',
        'commercial_registration_number',
        'tax_id',
        'restaurant_name',
        'restaurant_address',
        'partner_package_id',
        'partner_status',
        'payment_id',
        'partner_since',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'partner_since' => 'datetime',
            'password' => 'hashed',
            'user_type' => UserType::class,
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    public function isCustomer(): bool
    {
        return $this->user_type === UserType::CUSTOMER || $this->user_type?->value === 'customer';
    }

    public function isRestaurant(): bool
    {
        return $this->user_type === UserType::RESTAURANT || $this->user_type?->value === 'restaurant';
    }

    public function isRider(): bool
    {
        return $this->user_type === UserType::RIDER || $this->user_type?->value === 'rider';
    }

    public function isAdmin(): bool
    {
        return $this->user_type === UserType::ADMIN || $this->user_type?->value === 'admin';
    }

    public function restaurant(): HasOne
    {
        return $this->hasOne(Restaurant::class);
    }

    public function rider(): HasOne
    {
        return $this->hasOne(Rider::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function customerOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function partnerPackage(): BelongsTo
    {
        return $this->belongsTo(PartnerPackage::class);
    }
}