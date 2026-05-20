<?php

namespace App\Services;

use App\Enums\UserType;
use App\Models\Rider;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function registerCustomer(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'user_type' => UserType::CUSTOMER,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
        ]);

        event(new Registered($user));

        Log::info('Customer registered', ['user_id' => $user->id, 'email' => $user->email]);

        return $user;
    }

    public function registerRestaurant(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'],
            'user_type' => UserType::RESTAURANT,
        ]);

        $restaurant = Restaurant::create([
            'user_id' => $user->id,
            'name' => $data['restaurant_name'],
            'description' => $data['description'] ?? null,
            'address' => $data['address'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'delivery_fee' => $data['delivery_fee'] ?? 0,
            'delivery_time_minutes' => $data['delivery_time_minutes'] ?? 30,
            'minimum_order' => $data['minimum_order'] ?? 0,
        ]);

        event(new Registered($user));

        Log::info('Restaurant registered', ['user_id' => $user->id, 'restaurant_id' => $restaurant->id]);

        return ['user' => $user, 'restaurant' => $restaurant];
    }

    public function registerRider(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'],
            'user_type' => UserType::RIDER,
        ]);

        $rider = Rider::create([
            'user_id' => $user->id,
            'vehicle_type' => $data['vehicle_type'],
            'vehicle_plate' => $data['vehicle_plate'],
            'license_number' => $data['license_number'],
            'phone' => $data['phone'],
            'status' => 'offline',
        ]);

        event(new Registered($user));

        Log::info('Rider registered', ['user_id' => $user->id, 'rider_id' => $rider->id]);

        return ['user' => $user, 'rider' => $rider];
    }

    public function login(string $email, string $password): ?User
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return null;
        }

        Log::info('User logged in', ['user_id' => $user->id, 'email' => $user->email]);

        return $user;
    }

    public function createToken(User $user, string $name = 'api-token'): string
    {
        return $user->createToken($name)->plainTextToken;
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
        Log::info('User logged out', ['user_id' => $user->id]);
    }

    public function updateRiderLocation(int $riderId, float $latitude, float $longitude): void
    {
        $rider = Rider::findOrFail($riderId);

        $rider->locations()->create([
            'latitude' => $latitude,
            'longitude' => $longitude,
            'recorded_at' => now(),
        ]);

        Log::debug('Rider location updated', [
            'rider_id' => $riderId,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);
    }

    public function updateRiderStatus(int $riderId, string $status): void
    {
        $rider = Rider::findOrFail($riderId);
        $rider->update(['status' => $status]);

        Log::info('Rider status updated', ['rider_id' => $riderId, 'status' => $status]);
    }
}