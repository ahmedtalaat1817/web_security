<?php

namespace Database\Factories;

use App\Enums\UserType;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Restaurant>
 */
class RestaurantFactory extends Factory
{
    protected $model = Restaurant::class;

    public function definition(): array
    {
        $name = fake()->company();

        return [
            'user_id' => User::factory()->state(['user_type' => UserType::RESTAURANT]),
            'name' => $name,
            'slug' => Str::slug($name.'-'.fake()->unique()->numerify('####')),
            'description' => fake()->sentence(),
            'address' => fake()->streetAddress(),
            'latitude' => fake()->latitude(29, 31),
            'longitude' => fake()->longitude(30, 32),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->companyEmail(),
            'delivery_fee' => 2.99,
            'delivery_time_minutes' => 35,
            'minimum_order' => 0,
            'is_open' => true,
            'status' => 'active',
            'rating' => 4.5,
        ];
    }
}
