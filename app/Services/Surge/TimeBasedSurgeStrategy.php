<?php

namespace App\Services\Surge;

use App\Contracts\SurgePricingStrategyInterface;
use App\Models\Order;

class TimeBasedSurgeStrategy implements SurgePricingStrategyInterface
{
    public function multiplier(?Order $order = null): float
    {
        $hour = (int) now()->hour;
        $windows = config('surge.time_windows', []);

        foreach ($windows as $window) {
            if ($hour >= ($window['start'] ?? 0) && $hour < ($window['end'] ?? 24)) {
                return (float) ($window['multiplier'] ?? 1.0);
            }
        }

        return 1.0;
    }
}
