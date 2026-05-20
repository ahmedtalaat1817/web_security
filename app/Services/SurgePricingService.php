<?php

namespace App\Services;

use App\Contracts\SurgePricingStrategyInterface;
use App\Models\Order;
use App\Services\Surge\FlatSurgeStrategy;
use App\Services\Surge\MultiplierSurgeStrategy;
use App\Services\Surge\TimeBasedSurgeStrategy;
use Illuminate\Support\Facades\Cache;

class SurgePricingService
{
    public function strategy(): SurgePricingStrategyInterface
    {
        return match (config('surge.strategy', 'multiplier')) {
            'flat' => app(FlatSurgeStrategy::class),
            'time' => app(TimeBasedSurgeStrategy::class),
            default => app(MultiplierSurgeStrategy::class),
        };
    }

    public function rawMultiplier(?Order $order = null): float
    {
        return max(1.0, $this->strategy()->multiplier($order));
    }

    public function cappedMultiplier(?Order $order = null): float
    {
        return min($this->rawMultiplier($order), (float) config('surge.max_multiplier', 2.5));
    }

    /**
     * Recompute surge_amount and total while keeping restaurant base delivery_fee.
     */
    public function syncOrderSurge(Order $order): void
    {
        $order->loadMissing('restaurant');
        $base = (float) $order->restaurant->delivery_fee;
        $mult = $this->cappedMultiplier($order);
        $surgeFromMultiplier = round($base * ($mult - 1), 2);

        $maxExtra = (float) config('surge.max_extra_delivery', 8.0);
        $surgeExtra = min(max(0, $surgeFromMultiplier), $maxExtra);

        $order->delivery_fee = $base;
        $order->surge_amount = $surgeExtra;
        $order->total = round(
            (float) $order->subtotal + $base + (float) $order->platform_fee + $surgeExtra - (float) $order->discount,
            2
        );
        $order->save();
    }

    public function registerDemandSignal(): void
    {
        $v = min(50, (int) Cache::get('surge:active_orders', 0) + 1);
        Cache::put('surge:active_orders', $v, now()->addMinutes(15));
    }

    public function releaseDemandSignal(): void
    {
        $v = max(0, (int) Cache::get('surge:active_orders', 0) - 1);
        Cache::put('surge:active_orders', $v, now()->addMinutes(15));
    }
}
