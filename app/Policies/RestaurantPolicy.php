<?php

namespace App\Policies;

use App\Models\Restaurant;
use App\Models\User;

class RestaurantPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Restaurant $restaurant): bool
    {
        return true;
    }

    public function update(User $user, Restaurant $restaurant): bool
    {
        return $restaurant->user_id === $user->id;
    }

    public function delete(User $user, Restaurant $restaurant): bool
    {
        return $restaurant->user_id === $user->id;
    }

    public function manageMenu(User $user, Restaurant $restaurant): bool
    {
        return $restaurant->user_id === $user->id;
    }

    public function manageOrders(User $user, Restaurant $restaurant): bool
    {
        return $restaurant->user_id === $user->id;
    }
}