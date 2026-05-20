<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\DispatchService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DispatchRider implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(
        public Order $order
    ) {}

    public function handle(DispatchService $dispatchService): void
    {
        $rider = $dispatchService->dispatchRider($this->order);

        if ($rider) {
            $this->order->rider_id = $rider->id;
            $this->order->save();

            $activeOrders = $rider->orders()
                ->whereIn('status', ['confirmed', 'preparing', 'on_the_way'])
                ->count();

            if ($activeOrders >= 3) {
                $rider->update(['status' => 'busy']);
            } elseif ($rider->status === 'offline') {
                $rider->update(['status' => 'available']);
            }

            Log::info('Rider assigned to order', [
                'order_id' => $this->order->id,
                'rider_id' => $rider->id,
                'active_orders' => $activeOrders,
            ]);

            event(new \App\Events\RiderAssigned($this->order, $rider));
        } else {
            Log::warning('Failed to dispatch rider, will retry', [
                'order_id' => $this->order->id,
            ]);

            $this->release(60);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Rider dispatch job failed permanently', [
            'order_id' => $this->order->id,
            'error' => $exception->getMessage(),
        ]);
    }
}