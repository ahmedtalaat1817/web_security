<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\SurgePricingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RecalculateSurge implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $backoff = 15;

    public function __construct(
        public Order $order
    ) {}

    public function handle(SurgePricingService $surgePricingService): void
    {
        $order = $this->order->fresh(['restaurant']);

        if (! $order) {
            return;
        }

        $before = (float) $order->surge_amount;
        $surgePricingService->syncOrderSurge($order);
        $order->refresh();

        if (abs($before - (float) $order->surge_amount) > 0.01) {
            Log::info('Surge recalculated', [
                'order_id' => $order->id,
                'previous_surge' => $before,
                'new_surge' => $order->surge_amount,
                'new_total' => $order->total,
            ]);
        }
    }
}
