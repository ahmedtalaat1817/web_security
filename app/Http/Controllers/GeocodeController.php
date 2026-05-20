<?php

namespace App\Http\Controllers;

use App\Services\GeocodingService;
use Illuminate\Http\Request;

class GeocodeController extends Controller
{
    public function __construct(
        protected GeocodingService $geocodingService
    ) {}

    public function search(Request $request)
    {
        $request->validate(['q' => 'required|string']);

        $result = $this->geocodingService->searchPlaces($request->q);

        if ($result) {
            return response()->json($result);
        }

        return response()->json([], 422);
    }

    public function reverse(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $result = $this->geocodingService->reverseGeocode(
            (float) $request->lat,
            (float) $request->lng
        );

        if ($result) {
            return response()->json($result);
        }

        return response()->json([], 422);
    }
}