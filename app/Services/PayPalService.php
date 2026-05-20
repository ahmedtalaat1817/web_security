<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayPalService
{
    protected ?string $clientId;
    protected ?string $clientSecret;
    protected string $mode;
    protected string $baseUrl;

    public function __construct()
    {
        $this->clientId = config('services.paypal.client_id');
        $this->clientSecret = config('services.paypal.client_secret');
        $this->mode = config('services.paypal.mode', 'sandbox');
        $this->baseUrl = $this->mode === 'sandbox'
            ? 'https://api.sandbox.paypal.com'
            : 'https://api.paypal.com';
    }

    protected function getAccessToken(): string
    {
        if (!$this->clientId || !$this->clientSecret) {
            throw new \Exception('PayPal is not configured. Set PAYPAL_CLIENT_ID and PAYPAL_CLIENT_SECRET in .env');
        }

        $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
            ->asForm()
            ->post("{$this->baseUrl}/v1/oauth2/token", [
                'grant_type' => 'client_credentials',
            ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to get PayPal access token: ' . $response->body());
        }

        return $response->json()['access_token'];
    }

    public function createOrder(float $amount, string $currency = 'USD', string $description = '', array $returnUrls = []): array
    {
        $accessToken = $this->getAccessToken();

        $data = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => $currency,
                        'value' => number_format($amount, 2, '.', ''),
                    ],
                    'description' => $description,
                ],
            ],
        ];

        if (!empty($returnUrls)) {
            $data['payment_source'] = [
                'paypal' => [
                    'experience_context' => [
                        'return_url' => $returnUrls['return_url'] ?? '',
                        'cancel_url' => $returnUrls['cancel_url'] ?? '',
                    ],
                ],
            ];
        }

        $response = Http::withToken($accessToken)
            ->post("{$this->baseUrl}/v2/checkout/orders", $data);

        if (!$response->successful()) {
            throw new \Exception('Failed to create PayPal order: ' . $response->body());
        }

        Log::info('PayPal order created', [
            'order_id' => $response->json()['id'],
            'amount' => $amount,
        ]);

        return $response->json();
    }

    public function captureOrder(string $orderId): array
    {
        $accessToken = $this->getAccessToken();

        $response = Http::withToken($accessToken)
            ->post("{$this->baseUrl}/v2/checkout/orders/{$orderId}/capture");

        if (!$response->successful()) {
            throw new \Exception('Failed to capture PayPal order: ' . $response->body());
        }

        $data = $response->json();

        Log::info('PayPal order captured', [
            'order_id' => $orderId,
            'status' => $data['status'],
        ]);

        return $data;
    }

    public function getOrderDetails(string $orderId): array
    {
        $accessToken = $this->getAccessToken();

        $response = Http::withToken($accessToken)
            ->get("{$this->baseUrl}/v2/checkout/orders/{$orderId}");

        if (!$response->successful()) {
            throw new \Exception('Failed to get PayPal order details: ' . $response->body());
        }

        return $response->json();
    }

    public function refundPayment(string $captureId, ?float $amount = null, string $reason = ''): array
    {
        $accessToken = $this->getAccessToken();

        $data = [];
        if ($amount !== null) {
            $data['amount'] = [
                'value' => number_format($amount, 2, '.', ''),
                'currency_code' => 'USD',
            ];
        }
        if ($reason) {
            $data['note_to_payer'] = $reason;
        }

        $response = Http::withToken($accessToken)
            ->post("{$this->baseUrl}/v2/payments/captures/{$captureId}/refund", $data);

        if (!$response->successful()) {
            throw new \Exception('Failed to refund PayPal payment: ' . $response->body());
        }

        Log::info('PayPal payment refunded', [
            'capture_id' => $captureId,
            'amount' => $amount,
        ]);

        return $response->json();
    }
}