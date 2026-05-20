<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string',
            'type' => 'required|in:customer,restaurant,rider',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $data = $request->only(['name', 'email', 'password', 'phone', 'latitude', 'longitude']);

        $user = match ($request->type) {
            'restaurant' => $this->authService->registerRestaurant($request->only([
                'name', 'email', 'password', 'phone', 'restaurant_name', 'description',
                'address', 'latitude', 'longitude', 'delivery_fee', 'delivery_time_minutes', 'minimum_order'
            ])),
            'rider' => $this->authService->registerRider($request->only([
                'name', 'email', 'password', 'phone', 'vehicle_type', 'vehicle_plate', 'license_number'
            ])),
            default => $this->authService->registerCustomer($data),
        };

        $token = $this->authService->createToken($user['user'] ?? $user);

        return response()->json([
            'message' => 'Registration successful',
            'user' => $user['user'] ?? $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = $this->authService->login($request->email, $request->password);

        if (!$user) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $this->authService->createToken($user);

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout(Auth::user());

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function me(): JsonResponse
    {
        return response()->json(['user' => Auth::user()]);
    }

    public function updateLocation(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $rider = Auth::user()->rider;

        if (!$rider) {
            return response()->json(['message' => 'Rider profile not found'], 403);
        }

        $this->authService->updateRiderLocation(
            $rider->id,
            $request->latitude,
            $request->longitude
        );

        return response()->json(['message' => 'Location updated']);
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:available,busy,offline',
        ]);

        $rider = Auth::user()->rider;

        if (!$rider) {
            return response()->json(['message' => 'Rider profile not found'], 403);
        }

        $this->authService->updateRiderStatus($rider->id, $request->status);

        return response()->json(['message' => 'Status updated', 'status' => $request->status]);
    }
}