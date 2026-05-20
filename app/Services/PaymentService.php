<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentService
{
    protected ?string $stripeKey;
    protected ?string $stripeSecret;
    protected ?string $platformAccountId;

    public function __construct()
    {
        $this->stripeKey = config('services.stripe.key');
        $this->stripeSecret = config('services.stripe.secret');
        $this->platformAccountId = config('services.stripe.platform_account_id');
    }

    public function processPayment(Order $order, string $paymentMethodId, ?string $idempotencyKey = null): Payment
    {
        if (!$this->stripeSecret) {
            throw new \Exception('Stripe is not configured. Set STRIPE_SECRET in .env');
        }

        $idempotencyKey = $idempotencyKey ?? $this->generateIdempotencyKey($order);

        $existingPayment = Payment::where('idempotency_key', $idempotencyKey)->first();
        if ($existingPayment) {
            Log::info('Duplicate payment attempt detected', [
                'order_id' => $order->id,
                'idempotency_key' => $idempotencyKey,
            ]);

            return $existingPayment;
        }

        return DB::transaction(function () use ($order, $paymentMethodId, $idempotencyKey) {
            $payment = Payment::create([
                'order_id' => $order->id,
                'user_id' => $order->customer_id,
                'amount' => $order->total,
                'platform_fee' => $order->platform_fee,
                'restaurant_amount' => $order->subtotal - $order->platform_fee,
                'rider_amount' => $order->delivery_fee,
                'status' => 'processing',
                'currency' => 'usd',
                'customer_email' => $order->customer->email,
                'idempotency_key' => $idempotencyKey,
            ]);

            try {
                $paymentIntent = $this->createPaymentIntent(
                    amount: (int) round($order->total * 100),
                    currency: 'usd',
                    customerEmail: $order->customer->email,
                    metadata: [
                        'order_id' => (string) $order->id,
                        'order_number' => $order->order_number,
                    ],
                    idempotencyKey: $idempotencyKey,
                    order: $order
                );

                $payment->stripe_payment_intent_id = $paymentIntent['id'];
                $payment->save();

                $this->confirmPaymentIntent($paymentIntent['id'], $paymentMethodId);

                $payment->update(['status' => 'succeeded']);

                Log::info('Payment successful', [
                    'payment_id' => $payment->id,
                    'order_id' => $order->id,
                    'amount' => $order->total,
                ]);

                return $payment->fresh();
            } catch (\Exception $e) {
                $payment->update([
                    'status' => 'failed',
                    'failure_reason' => $e->getMessage(),
                ]);

                Log::error('Payment failed', [
                    'payment_id' => $payment->id,
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);

                throw $e;
            }
        });
    }

    protected function createPaymentIntent(
        int $amount,
        string $currency,
        string $customerEmail,
        array $metadata,
        string $idempotencyKey,
        ?Order $order = null
    ): array {
        $body = [
            'amount' => $amount,
            'currency' => $currency,
            'automatic_payment_methods[enabled]' => 'true',
        ];

        foreach ($metadata as $key => $value) {
            $body['metadata['.$key.']'] = (string) $value;
        }

        $order?->loadMissing('restaurant');
        $destination = $order?->restaurant?->stripe_connect_account_id;
        if ($destination) {
            $restaurantNetCents = (int) round(((float) $order->subtotal - (float) $order->platform_fee) * 100);
            $applicationFee = max(0, $amount - $restaurantNetCents);
            $body['application_fee_amount'] = $applicationFee;
            $body['transfer_data[destination]'] = $destination;
        }

        $response = Http::withBasicAuth($this->stripeSecret, '')
            ->asForm()
            ->withHeaders([
                'Idempotency-Key' => $idempotencyKey,
            ])
            ->post('https://api.stripe.com/v1/payment_intents', $body);

        if (! $response->successful()) {
            throw new \Exception('Failed to create payment intent: '.$response->body());
        }

        return $response->json();
    }

    protected function confirmPaymentIntent(string $paymentIntentId, string $paymentMethodId): void
    {
        $response = Http::withBasicAuth($this->stripeSecret, '')
            ->asForm()
            ->post("https://api.stripe.com/v1/payment_intents/{$paymentIntentId}/confirm", [
                'payment_method' => $paymentMethodId,
            ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to confirm payment: ' . $response->body());
        }
    }

    public function createPayout(string $stripeAccountId, int $amount, string $currency = 'usd'): string
    {
        $response = Http::withBasicAuth($this->stripeSecret, '')
            ->post('https://api.stripe.com/v1/transfers', [
                'amount' => $amount,
                'currency' => $currency,
                'destination' => $stripeAccountId,
            ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to create payout: ' . $response->body());
        }

        return $response->json()['id'];
    }

    public function refundPayment(string $paymentIntentId, ?int $amount = null): array
    {
        $data = ['payment_intent' => $paymentIntentId];
        if ($amount) {
            $data['amount'] = $amount;
        }

        $response = Http::withBasicAuth($this->stripeSecret, '')
            ->post('https://api.stripe.com/v1/refunds', $data);

        if (!$response->successful()) {
            throw new \Exception('Failed to process refund: ' . $response->body());
        }

        return $response->json();
    }

    public function createConnectedAccount(string $email, string $businessType = 'individual'): string
    {
        $response = Http::withBasicAuth($this->stripeSecret, '')
            ->post('https://api.stripe.com/v1/accounts', [
                'type' => 'express',
                'email' => $email,
                'business_type' => $businessType,
            ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to create connected account: ' . $response->body());
        }

        return $response->json()['id'];
    }

    public function createAccountLink(string $accountId, string $refreshUrl, string $returnUrl): string
    {
        $response = Http::withBasicAuth($this->stripeSecret, '')
            ->post('https://api.stripe.com/v1/account_links', [
                'account' => $accountId,
                'refresh_url' => $refreshUrl,
                'return_url' => $returnUrl,
                'type' => 'account_onboarding',
            ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to create account link: ' . $response->body());
        }

        return $response->json()['url'];
    }

    protected function generateIdempotencyKey(Order $order): string
    {
        return sprintf('order_%d_%s', $order->id, Str::random(16));
    }

    public function handleWebhook(array $payload): void
    {
        $eventType = $payload['type'] ?? null;

        match ($eventType) {
            'payment_intent.succeeded' => $this->handlePaymentSuccess($payload['data']['object']),
            'payment_intent.payment_failed' => $this->handlePaymentFailure($payload['data']['object']),
            default => null,
        };
    }

    protected function handlePaymentSuccess(array $paymentIntent): void
    {
        $payment = Payment::where('stripe_payment_intent_id', $paymentIntent['id'])->first();
        if ($payment && $payment->status !== 'succeeded') {
            $payment->update(['status' => 'succeeded']);
            Log::info('Payment confirmed via webhook', ['payment_id' => $payment->id]);
        }
    }

    protected function handlePaymentFailure(array $paymentIntent): void
    {
        $payment = Payment::where('stripe_payment_intent_id', $paymentIntent['id'])->first();
        if ($payment) {
            $payment->update([
                'status' => 'failed',
                'failure_reason' => $paymentIntent['last_payment_error']['message'] ?? 'Payment failed',
            ]);
            Log::warning('Payment failed via webhook', ['payment_id' => $payment->id]);
        }
    }
}