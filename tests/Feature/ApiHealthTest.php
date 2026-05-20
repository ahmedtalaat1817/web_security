<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiHealthTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_health_endpoint(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertOk()
            ->assertJson(['status' => 'ok']);
    }

    public function test_geocode_distance_route_exists(): void
    {
        $response = $this->postJson('/api/geocode/distance', [
            'origin_lat' => 30.0,
            'origin_lng' => 31.0,
            'dest_lat' => 30.1,
            'dest_lng' => 31.1,
        ]);

        $this->assertContains($response->status(), [200, 422, 500]);
    }
}
