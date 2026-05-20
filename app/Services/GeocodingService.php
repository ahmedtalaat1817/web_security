<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.tomtom.com/search/2';

    public function __construct()
    {
        $this->apiKey = config('services.tomtom.api_key');
    }

    public function getCoordinatesFromAddress(string $address): ?array
    {
        if (empty($this->apiKey)) {
            Log::warning('TomTom API key not configured');
            return null;
        }

        try {
            $encodedAddress = urlencode($address);

            $response = Http::get("{$this->baseUrl}/geocode/{$encodedAddress}.json", [
                'key' => $this->apiKey,
                'limit' => 1,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['results']) && count($data['results']) > 0) {
                    $result = $data['results'][0];
                    $position = $result['position'];

                    Log::info('Geocoding successful', [
                        'address' => $address,
                        'latitude' => $position['lat'],
                        'longitude' => $position['lon'],
                    ]);

                    return [
                        'latitude' => (float) $position['lat'],
                        'longitude' => (float) $position['lon'],
                        'formatted_address' => $result['address']['freeformAddress'] ?? $address,
                        'street' => $result['address']['streetName'] ?? null,
                        'city' => $result['address']['municipality'] ?? null,
                        'country' => $result['address']['country'] ?? null,
                    ];
                }
            }

            Log::warning('TomTom geocoding failed', [
                'address' => $address,
                'status' => $response->status(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('TomTom geocoding exception', [
                'address' => $address,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function getCoordinatesBatch(array $addresses): array
    {
        $results = [];

        foreach ($addresses as $address) {
            $results[] = $this->getCoordinatesFromAddress($address);
        }

        return $results;
    }

    public function getDistanceBetweenPoints(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371;

        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLng = deg2rad($lng2 - $lng1);

        $a = sin($deltaLat / 2) ** 2 +
            cos($lat1Rad) * cos($lat2Rad) * sin($deltaLng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function searchPlaces(string $query): ?array
    {
        if (empty($this->apiKey)) {
            return null;
        }

        try {
            $encodedQuery = urlencode($query);

            $response = Http::get("{$this->baseUrl}/search/{$encodedQuery}.json", [
                'key' => $this->apiKey,
                'limit' => 5,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['results'])) {
                    return array_map(function ($result) {
                        return [
                            'id' => $result['id'],
                            'name' => $result['poi']['name'] ?? $result['address']['freeformAddress'],
                            'address' => $result['address']['freeformAddress'],
                            'latitude' => $result['position']['lat'],
                            'longitude' => $result['position']['lon'],
                        ];
                    }, $data['results']);
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('TomTom search exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function reverseGeocode(float $lat, float $lng): ?array
    {
        if (empty($this->apiKey)) {
            return null;
        }

        try {
            $response = Http::get("{$this->baseUrl}/reverseGeocode/{$lat},{$lng}.json", [
                'key' => $this->apiKey,
                'limit' => 1,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['addresses']) && count($data['addresses']) > 0) {
                    $addr = $data['addresses'][0];
                    return [
                        'formatted_address' => $addr['address']['freeformAddress'] ?? null,
                        'street' => $addr['address']['streetName'] ?? null,
                        'city' => $addr['address']['municipality'] ?? null,
                        'country' => $addr['address']['country'] ?? null,
                    ];
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('TomTom reverse geocode exception', ['error' => $e->getMessage()]);
            return null;
        }
    }
}