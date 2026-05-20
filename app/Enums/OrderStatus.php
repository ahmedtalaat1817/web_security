<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PLACED = 'placed';
    case CONFIRMED = 'confirmed';
    case PREPARING = 'preparing';
    case ON_THE_WAY = 'on_the_way';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';

    public function canTransitionTo(self $newStatus): bool
    {
        return match ($this) {
            self::PLACED => in_array($newStatus, [self::CONFIRMED, self::CANCELLED]),
            self::CONFIRMED => in_array($newStatus, [self::PREPARING, self::CANCELLED]),
            self::PREPARING => in_array($newStatus, [self::ON_THE_WAY, self::CANCELLED]),
            self::ON_THE_WAY => in_array($newStatus, [self::DELIVERED, self::CANCELLED]),
            self::DELIVERED => false,
            self::CANCELLED => false,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::PLACED => 'Placed',
            self::CONFIRMED => 'Confirmed',
            self::PREPARING => 'Preparing',
            self::ON_THE_WAY => 'On The Way',
            self::DELIVERED => 'Delivered',
            self::CANCELLED => 'Cancelled',
        };
    }

    public static function fromString(string $value): self
    {
        return self::tryFrom($value)
            ?? throw new \InvalidArgumentException("Invalid order status: {$value}");
    }
}