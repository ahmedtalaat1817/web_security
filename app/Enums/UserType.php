<?php

namespace App\Enums;

enum UserType: string
{
    case CUSTOMER = 'customer';
    case RESTAURANT = 'restaurant';
    case RIDER = 'rider';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::CUSTOMER => 'Customer',
            self::RESTAURANT => 'Restaurant',
            self::RIDER => 'Rider',
            self::ADMIN => 'Admin',
        };
    }
}