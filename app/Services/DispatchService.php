<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Rider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DispatchService
{
    protected string $fastApiUrl;

    public function __construct()
    {
        $this->fastApiUrl = rtrim((string) config('services.fastapi.url', 'http://localhost:8088'), '/');
    }

    protected function getAvailableRiders()
    {
        return Rider::where('is_active', true)
            ->where(function ($q) {
                $q->where('status', '!=', 'offline');
            })
            ->with('location')
            ->get()
            ->filter(function ($rider) {
                $activeCount = $rider->orders()
                    ->whereIn('status', ['confirmed', 'preparing', 'on_the_way'])
                    ->count();
                return $activeCount < 3;
            })
            ->values();
    }

    protected function riderActiveOrderCount(Rider $rider): int
    {
        return $rider->orders()
            ->whereIn('status', ['confirmed', 'preparing', 'on_the_way'])
            ->count();
    }

    public function dispatchRider(Order $order): ?Rider
    {
        $restaurant = $order->restaurant;

        $requestData = [
            'order_id' => $order->id,
            'restaurant' => [
                'lat' => (float) $restaurant->latitude,
                'lng' => (float) $restaurant->longitude,
            ],
            'customer' => [
                'lat' => (float) $order->delivery_latitude,
                'lng' => (float) $order->delivery_longitude,
            ],
            'riders' => $this->getAvailableRiders()
                ->map(function (Rider $rider) {
                    $loc = $rider->location;
                    return [
                        'id' => $rider->id,
                        'lat' => $loc ? (float) $loc->latitude : 0.0,
                        'lng' => $loc ? (float) $loc->longitude : 0.0,
                        'active_orders' => $this->riderActiveOrderCount($rider),
                    ];
                })
                ->values()
                ->all(),
        ];

        try {
            $http = Http::timeout(10)->acceptJson();
            $token = config('services.fastapi.internal_token');
            if ($token) {
                $http = $http->withToken($token);
            }

            $response = $http->post("{$this->fastApiUrl}/api/dispatch", $requestData);

            if ($response->successful()) {
                $data = $response->json();
                $rider = Rider::find($data['rider_id']);

                if ($rider) {
                    Log::info('Rider dispatched via FastAPI', [
                        'order_id' => $order->id,
                        'rider_id' => $rider->id,
                        'distance_km' => $data['distance_km'] ?? null,
                        'estimated_pickup_time' => $data['estimated_pickup_time'] ?? null,
                    ]);

                    return $rider;
                }
            }
        } catch (\Exception $e) {
            Log::error('FastAPI dispatch failed, falling back to local dispatch', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }

        return $this->dispatchLocally($order);
    }

    protected function dispatchLocally(Order $order): ?Rider
    {
        $restaurant = $order->restaurant;

        $candidates = $this->getAvailableRiders();

        if ($candidates->isEmpty()) {
            Log::warning('No riders with capacity for dispatch', ['order_id' => $order->id]);
            return null;
        }

        $nearestRider = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach ($candidates as $rider) {
            $location = $rider->location;

            if (!$location) {
                continue;
            }

            $distance = $this->calculateDistance(
                (float) $restaurant->latitude,
                (float) $restaurant->longitude,
                (float) $location->latitude,
                (float) $location->longitude
            );

            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearestRider = $rider;
            }
        }

        if ($nearestRider) {
            Log::info('Rider dispatched locally (fallback)', [
                'order_id' => $order->id,
                'rider_id' => $nearestRider->id,
                'distance_km' => round($minDistance, 2),
                'active_orders' => $this->riderActiveOrderCount($nearestRider),
            ]);
        }

        return $nearestRider;
    }

    protected function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}