<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        if ($user->isCustomer()) {
            $orders = $this->orderService->getOrderHistory($user, $request->per_page ?? 15);
        } elseif ($user->isRider() && $user->rider) {
            $orders = $this->orderService->getRiderOrders($user->rider);
        } else {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($orders);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.variant_id' => 'nullable|exists:item_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.special_instructions' => 'nullable|string',
            'delivery_address' => 'required|string',
            'delivery_latitude' => 'required|numeric',
            'delivery_longitude' => 'required|numeric',
            'delivery_instructions' => 'nullable|string',
            'customer_phone' => 'required|string',
            'customer_name' => 'required|string',
        ]);

        $order = $this->orderService->createOrder($request->all(), Auth::user());

        return response()->json([
            'message' => 'Order placed successfully',
            'order' => $order->load(['items', 'restaurant']),
        ], 201);
    }

    public function show(Order $order): JsonResponse
    {
        $user = Auth::user();

        if ($order->customer_id !== $user->id &&
            $order->restaurant_id !== $user->restaurant?->id &&
            $order->rider_id !== $user->rider?->id &&
            !$user->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'order' => $order->load(['items', 'restaurant', 'rider', 'statusLogs']),
        ]);
    }

    public function confirm(Order $order): JsonResponse
    {
        if ($order->restaurant_id !== Auth::user()->restaurant?->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $result = $this->orderService->confirmOrder($order, Auth::user());

        if (!$result) {
            return response()->json(['message' => 'Cannot confirm order'], 400);
        }

        return response()->json(['message' => 'Order confirmed', 'order' => $order->fresh()]);
    }

    public function startPreparing(Order $order): JsonResponse
    {
        if ($order->restaurant_id !== Auth::user()->restaurant?->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $result = $this->orderService->startPreparing($order, Auth::user());

        if (!$result) {
            return response()->json(['message' => 'Cannot start preparing'], 400);
        }

        return response()->json(['message' => 'Order being prepared', 'order' => $order->fresh()]);
    }

    public function markOnTheWay(Order $order): JsonResponse
    {
        if ($order->rider_id !== Auth::user()->rider?->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $result = $this->orderService->markOnTheWay($order, Auth::user());

        if (!$result) {
            return response()->json(['message' => 'Cannot mark as on the way'], 400);
        }

        return response()->json(['message' => 'Order picked up', 'order' => $order->fresh()]);
    }

    public function deliver(Order $order): JsonResponse
    {
        if ($order->rider_id !== Auth::user()->rider?->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $result = $this->orderService->deliverOrder($order, Auth::user());

        if (!$result) {
            return response()->json(['message' => 'Cannot deliver order'], 400);
        }

        return response()->json(['message' => 'Order delivered', 'order' => $order->fresh()]);
    }

    public function cancel(Request $request, Order $order): JsonResponse
    {
        $request->validate(['reason' => 'required|string']);

        $user = Auth::user();

        if ($order->customer_id !== $user->id &&
            $order->restaurant_id !== $user->restaurant?->id &&
            !$user->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $result = $this->orderService->cancelOrder($order, $user, $request->reason);

        if (!$result) {
            return response()->json(['message' => 'Cannot cancel order'], 400);
        }

        return response()->json(['message' => 'Order cancelled', 'order' => $order->fresh()]);
    }

    public function restaurantOrders(Request $request): JsonResponse
    {
        $restaurant = Auth::user()->restaurant;

        if (!$restaurant) {
            return response()->json(['message' => 'Restaurant profile not found'], 403);
        }

        $orders = $this->orderService->getRestaurantOrders(
            $restaurant,
            $request->status
        );

        return response()->json($orders);
    }

    public function riderOrders(): JsonResponse
    {
        $rider = Auth::user()->rider;

        if (!$rider) {
            return response()->json(['message' => 'Rider profile not found'], 403);
        }

        $orders = $this->orderService->getRiderOrders($rider);

        return response()->json($orders);
    }
}