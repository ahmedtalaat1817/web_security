<?php

namespace App\Services\Surge;

use App\Contracts\SurgePricingStrategyInterface;
use App\Models\Order;

class FlatSurgeStrategy implements SurgePricingStrategyInterface
{
    public function multiplier(?Order $order = null): float
    {
        if (! $order) {
            return 1.0;
        }

        $base = (float) config('surge.flat_extra', 0);
        if ($base <= 0) {
            return 1.0;
        }

        $delivery = max((float) $order->restaurant->delivery_fee, 0.01);

        return 1 + ($base / $delivery);
    }
}
