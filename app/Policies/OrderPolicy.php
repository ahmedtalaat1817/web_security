<?php

namespace App\Policies;

use App\Enums\UserType;
use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function view(User $user, Order $order): bool
    {
        return $order->customer_id === $user->id
            || $order->restaurant->user_id === $user->id
            || $order->rider_id === $user->rider?->id
            || $user->user_type === UserType::ADMIN;
    }

    public function update(User $user, Order $order): bool
    {
        if ($order->status === 'delivered' || $order->status === 'cancelled') {
            return false;
        }

        return $order->customer_id === $user->id
            || $order->restaurant->user_id === $user->id
            || $order->rider_id === $user->rider?->id
            || $user->user_type === UserType::ADMIN;
    }

    public function cancel(User $user, Order $order): bool
    {
        return in_array($order->status, ['placed', 'confirmed'])
            && ($order->customer_id === $user->id
                || $order->restaurant->user_id === $user->id
                || $user->user_type === UserType::ADMIN);
    }

    public function confirm(User $user, Order $order): bool
    {
        return $order->status === 'placed'
            && $order->restaurant->user_id === $user->id;
    }

    public function startPreparing(User $user, Order $order): bool
    {
        return $order->status === 'confirmed'
            && $order->restaurant->user_id === $user->id;
    }

    public function markOnTheWay(User $user, Order $order): bool
    {
        return $order->status === 'preparing'
            && $order->rider_id === $user->rider?->id;
    }

    public function deliver(User $user, Order $order): bool
    {
        return $order->status === 'on_the_way'
            && $order->rider_id === $user->rider?->id;
    }
}