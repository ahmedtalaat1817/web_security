<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Models\Rider;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string'],
            'type' => ['required', 'in:customer,restaurant,rider'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'user_type' => UserType::from($request->type),
        ]);

        if ($request->type === 'restaurant') {
            Restaurant::create([
                'user_id' => $user->id,
                'name' => $request->restaurant_name ?? 'My Restaurant',
                'address' => $request->address_search ?? $request->address ?? 'Cairo',
                'latitude' => $request->latitude ?? 30.0444,
                'longitude' => $request->longitude ?? 31.2357,
                'phone' => $request->phone,
                'email' => $request->email,
            ]);
        } elseif ($request->type === 'rider') {
            Rider::create([
                'user_id' => $user->id,
                'vehicle_type' => $request->vehicle_type ?? 'motorcycle',
                'vehicle_plate' => $request->vehicle_plate ?? 'DEFAULT',
                'license_number' => $request->license_number ?? 'DEFAULT',
                'phone' => $request->phone,
                'status' => 'offline',
            ]);
        }

        event(new Registered($user));

        return redirect()
            ->route('login')
            ->with('status', 'Registration successful. You can log in now.');
    }
}
