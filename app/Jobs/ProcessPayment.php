<?php

namespace App\Jobs;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public Order $order,
        public ?string $paymentMethodId = null
    ) {}

    public function handle(PaymentService $paymentService, OrderService $orderService): void
    {
        $order = $this->order->fresh();

        if (! $order || $order->status !== OrderStatus::PLACED->value) {
            Log::info('Order not eligible for payment job', [
                'order_id' => $this->order->id,
                'status' => $order?->status,
            ]);

            return;
        }

        $idempotencyKey = 'order_'.$order->id.'_capture';

        try {
            if (! config('services.stripe.secret')) {
                Log::notice('Stripe not configured; marking payment as simulated success', [
                    'order_id' => $order->id,
                ]);
                $orderService->transitionOrder(
                    $order,
                    OrderStatus::CONFIRMED,
                    $order->customer,
                    'Payment simulated (no Stripe secret configured)'
                );

                return;
            }

            if (! $this->paymentMethodId) {
                Log::info('Stripe configured but no payment method on job; deferring capture', [
                    'order_id' => $order->id,
                ]);

                return;
            }

            $payment = $paymentService->processPayment(
                $order,
                $this->paymentMethodId,
                $idempotencyKey
            );

            if ($payment->isSuccessful()) {
                $orderService->transitionOrder(
                    $order->fresh(),
                    OrderStatus::CONFIRMED,
                    $order->customer,
                    'Payment captured'
                );
            }
        } catch (\Exception $e) {
            Log::error('Payment processing failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            $orderService->cancelOrder(
                $order->fresh(),
                $order->customer,
                'Payment failed: '.$e->getMessage()
            );

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Payment job failed permanently', [
            'order_id' => $this->order->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
