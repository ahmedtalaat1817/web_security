<?php

namespace App\Policies;

use App\Models\Rider;
use App\Models\User;

class RiderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->user_type === 'admin' || $user->isRestaurant();
    }

    public function view(User $user, Rider $rider): bool
    {
        return $rider->user_id === $user->id
            || $user->user_type === 'admin'
            || $user->isRestaurant();
    }

    public function update(User $user, Rider $rider): bool
    {
        return $rider->user_id === $user->id;
    }

    public function updateLocation(User $user, Rider $rider): bool
    {
        return $rider->user_id === $user->id;
    }

    public function updateStatus(User $user, Rider $rider): bool
    {
        return $rider->user_id === $user->id;
    }
}