<?php

namespace Tests\Unit;

use App\Enums\OrderStatus;
use PHPUnit\Framework\TestCase;

class OrderFsmTest extends TestCase
{
    public function test_delivered_cannot_transition_to_preparing(): void
    {
        $this->assertFalse(
            OrderStatus::DELIVERED->canTransitionTo(OrderStatus::PREPARING)
        );
    }

    public function test_placed_can_transition_to_confirmed(): void
    {
        $this->assertTrue(
            OrderStatus::PLACED->canTransitionTo(OrderStatus::CONFIRMED)
        );
    }

    public function test_from_string_rejects_invalid_status(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        OrderStatus::fromString('not-a-status');
    }
}
