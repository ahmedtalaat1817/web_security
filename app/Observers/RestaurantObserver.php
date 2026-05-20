<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Support\Str;

class RestaurantObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        Restaurant::create([
            'user_id' => $user->id,
            'name' => $user->name . "'s Restaurant",
            'slug' => Str::slug($user->name . '-' . $user->id),
            'email' => $user->email,
            'is_open' => true,
        ]);
    }
}