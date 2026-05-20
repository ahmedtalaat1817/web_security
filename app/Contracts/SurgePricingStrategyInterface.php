<?php

namespace App\Contracts;

use App\Models\Order;

interface SurgePricingStrategyInterface
{
    /**
     * Delivery-fee multiplier (1.0 = no surge). Capped by engine.
     */
    public function multiplier(?Order $order = null): float;
}
