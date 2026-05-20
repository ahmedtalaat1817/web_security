<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\UserType;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class OrderServiceTransitionTest extends TestCase
{
    use RefreshDatabase;

    public function test_invalid_transition_delivered_to_preparing_fails(): void
    {
        $customer = User::factory()->create(['user_type' => UserType::CUSTOMER]);
        $owner = User::factory()->create(['user_type' => UserType::RESTAURANT]);
        $restaurant = Restaurant::factory()->create(['user_id' => $owner->id]);

        $order = Order::create([
            'order_number' => 'ORD-TEST-'.Str::upper(Str::random(6)),
            'customer_id' => $customer->id,
            'restaurant_id' => $restaurant->id,
            'status' => OrderStatus::DELIVERED->value,
            'subtotal' => 10,
            'delivery_fee' => 2,
            'platform_fee' => 1,
            'surge_amount' => 0,
            'discount' => 0,
            'total' => 13,
            'delivery_address' => '1 Test St',
            'delivery_latitude' => 30.0,
            'delivery_longitude' => 31.0,
            'customer_phone' => '123',
            'customer_name' => 'Test',
        ]);

        $service = app(OrderService::class);
        $this->assertFalse(
            $service->transitionOrder($order, OrderStatus::PREPARING, $owner, 'invalid')
        );
    }
}
