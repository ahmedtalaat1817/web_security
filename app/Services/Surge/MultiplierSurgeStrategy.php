<?php

namespace App\Services\Surge;

use App\Contracts\SurgePricingStrategyInterface;
use App\Models\Order;
use Illuminate\Support\Facades\Cache;

class MultiplierSurgeStrategy implements SurgePricingStrategyInterface
{
    public function multiplier(?Order $order = null): float
    {
        $demand = min(20, (int) Cache::get('surge:active_orders', 0));

        return 1.0 + ($demand * 0.05);
    }
}
