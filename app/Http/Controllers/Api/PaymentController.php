<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    public function show(Order $order): JsonResponse
    {
        if ($order->customer_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $payment = $order->payment;

        return response()->json(['payment' => $payment]);
    }

    public function createPaymentIntent(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::findOrFail($request->order_id);

        if ($order->customer_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($order->payment && $order->payment->status === 'succeeded') {
            return response()->json(['message' => 'Payment already processed'], 400);
        }

        $idempotencyKey = "order_{$order->id}_" . time();

        try {
            $payment = $this->paymentService->processPayment(
                $order,
                $request->payment_method_id,
                $idempotencyKey
            );

            return response()->json([
                'message' => 'Payment initiated',
                'payment' => $payment,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Payment failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function refund(Request $request): JsonResponse
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'amount' => 'nullable|numeric|min:0',
        ]);

        $payment = Payment::findOrFail($request->payment_id);

        if (!$payment->isSuccessful()) {
            return response()->json(['message' => 'Cannot refund unsuccessful payment'], 400);
        }

        try {
            $result = $this->paymentService->refundPayment(
                $payment->stripe_payment_intent_id,
                $request->amount ? (int) ($request->amount * 100) : null
            );

            $payment->update(['status' => 'refunded']);

            return response()->json([
                'message' => 'Refund processed',
                'refund_id' => $result['id'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Refund failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function webhook(Request $request): JsonResponse
    {
        $payload = $request->all();

        $this->paymentService->handleWebhook($payload);

        return response()->json(['received' => true]);
    }
}