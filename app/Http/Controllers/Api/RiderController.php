<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rider;
use App\Models\RiderLocation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $riders = Rider::query()
            ->with('user', 'location')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->is_active !== null, fn($q) => $q->where('is_active', $request->is_active))
            ->paginate(20);

        return response()->json($riders);
    }

    public function show(Rider $rider): JsonResponse
    {
        $rider->load(['user', 'location', 'orders' => fn($q) => $q->latest()->take(10)]);

        return response()->json(['rider' => $rider]);
    }

    public function availableRiders(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|numeric|min:0.1|max:50',
        ]);

        $radius = $request->radius ?? 10;
        $latitude = $request->latitude;
        $longitude = $request->longitude;

        $riders = Rider::selectRaw(
            '*,
            (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) * 1000 AS distance',
            [$latitude, $longitude, $latitude]
        )
            ->having('distance', '<=', $radius * 1000)
            ->where('status', 'available')
            ->where('is_active', true)
            ->with('user', 'location')
            ->orderBy('distance')
            ->get();

        return response()->json(['riders' => $riders]);
    }

    public function updateLocation(Request $request): JsonResponse
    {
        $rider = Auth::user()->rider;

        if (!$rider) {
            return response()->json(['message' => 'Rider profile not found'], 403);
        }

        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $rider->locations()->create([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'recorded_at' => now(),
        ]);

        event(new \App\Events\RiderLocationUpdated($rider, $request->latitude, $request->longitude));

        return response()->json(['message' => 'Location updated']);
    }

    public function getLocation(Rider $rider): JsonResponse
    {
        $location = $rider->location;

        if (!$location) {
            return response()->json(['message' => 'Location not available'], 404);
        }

        return response()->json(['location' => $location]);
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $rider = Auth::user()->rider;

        if (!$rider) {
            return response()->json(['message' => 'Rider profile not found'], 403);
        }

        $request->validate([
            'status' => 'required|in:available,busy,offline',
            'is_active' => 'nullable|boolean',
        ]);

        $rider->update($request->only(['status', 'is_active']));

        return response()->json(['message' => 'Status updated', 'rider' => $rider]);
    }
}