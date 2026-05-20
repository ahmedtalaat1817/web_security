<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GeocodingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GeocodingController extends Controller
{
    public function __construct(
        protected GeocodingService $geocodingService
    ) {}

    public function geocode(Request $request): JsonResponse
    {
        $request->validate([
            'address' => 'required|string',
        ]);

        $result = $this->geocodingService->getCoordinatesFromAddress($request->address);

        if (!$result) {
            return response()->json([
                'message' => 'Unable to geocode address',
                'error' => 'Please check the address or API key',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    public function validateAddress(Request $request): JsonResponse
    {
        $request->validate([
            'address' => 'required|string',
        ]);

        $result = $this->geocodingService->getCoordinatesFromAddress($request->address);

        return response()->json([
            'valid' => $result !== null,
            'latitude' => $result['latitude'] ?? null,
            'longitude' => $result['longitude'] ?? null,
            'formatted_address' => $result['formatted_address'] ?? null,
        ]);
    }

    public function calculateDistance(Request $request): JsonResponse
    {
        $request->validate([
            'lat1' => 'required|numeric',
            'lng1' => 'required|numeric',
            'lat2' => 'required|numeric',
            'lng2' => 'required|numeric',
        ]);

        $distance = $this->geocodingService->getDistanceBetweenPoints(
            $request->lat1,
            $request->lng1,
            $request->lat2,
            $request->lng2
        );

        return response()->json([
            'distance_km' => round($distance, 2),
            'distance_miles' => round($distance * 0.621371, 2),
        ]);
    }
}