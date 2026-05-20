<?php

use App\Models\Order;
use App\Models\Restaurant;
use App\Models\Rider;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('order.{orderId}', function ($user, int $orderId) {
    $order = Order::find($orderId);
    if (! $order) {
        return false;
    }

    if ($user->isAdmin()) {
        return ['id' => $user->id, 'name' => $user->name];
    }

    if ($order->customer_id === $user->id) {
        return ['id' => $user->id, 'name' => $user->name];
    }

    if ($user->restaurant && $order->restaurant_id === $user->restaurant->id) {
        return ['id' => $user->id, 'name' => $user->name];
    }

    if ($user->rider && $order->rider_id === $user->rider->id) {
        return ['id' => $user->id, 'name' => $user->name];
    }

    return false;
});

Broadcast::channel('restaurant.{restaurantId}', function ($user, int $restaurantId) {
    if ($user->isAdmin()) {
        return ['id' => $user->id, 'name' => $user->name];
    }

    $restaurant = Restaurant::find($restaurantId);

    return $restaurant && $user->restaurant && $restaurant->id === $user->restaurant->id
        ? ['id' => $user->id, 'name' => $user->name]
        : false;
});

Broadcast::channel('rider.{riderId}', function ($user, int $riderId) {
    if ($user->isAdmin()) {
        return ['id' => $user->id, 'name' => $user->name];
    }

    $rider = Rider::find($riderId);

    return $rider && $user->rider && $rider->id === $user->rider->id
        ? ['id' => $user->id, 'name' => $user->name]
        : false;
});
