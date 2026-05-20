<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Events\OrderPickedUp;
use App\Events\OrderStatusUpdated;
use App\Jobs\DispatchRider;
use App\Jobs\ProcessPayment;
use App\Jobs\RecalculateSurge;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderService
{
    public function createOrder(array $data, User $customer): Order
    {
        return DB::transaction(function () use ($data, $customer) {
            $restaurant = Restaurant::findOrFail($data['restaurant_id']);
            $items = $this->calculateOrderItems($data['items']);
            $subtotal = array_sum(array_column($items, 'subtotal'));
            $deliveryFee = (float) $restaurant->delivery_fee;
            $platformFee = round($subtotal * 0.10, 2);
            $discount = $data['discount'] ?? 0;
            $surgeAmount = $data['surge_amount'] ?? 0;

            $total = $subtotal + $deliveryFee + $platformFee + $surgeAmount - $discount;

            $order = Order::create([
                'customer_id' => $customer->id,
                'restaurant_id' => $restaurant->id,
                'status' => OrderStatus::CONFIRMED->value,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'platform_fee' => $platformFee,
                'surge_amount' => $surgeAmount,
                'discount' => $discount,
                'total' => $total,
                'delivery_address' => $data['delivery_address'],
                'delivery_latitude' => $data['delivery_latitude'],
                'delivery_longitude' => $data['delivery_longitude'],
                'delivery_instructions' => $data['delivery_instructions'] ?? null,
                'customer_phone' => $data['customer_phone'],
                'customer_name' => $data['customer_name'],
                'estimated_delivery_time' => now()->addMinutes($restaurant->delivery_time_minutes),
            ]);

            foreach ($items as $item) {
                $order->items()->create($item);
            }

            $order->statusLogs()->create([
                'old_state' => null,
                'new_state' => OrderStatus::CONFIRMED->value,
                'actor_id' => $customer->id,
                'actor_type' => get_class($customer),
                'notes' => 'Order placed and auto-confirmed',
                'timestamp' => now(),
            ]);

            $order->confirmed_at = now();
            $order->save();

            event(new OrderStatusUpdated($order->fresh()));

            Log::info('Order created and auto-confirmed', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'customer_id' => $customer->id,
                'restaurant_id' => $restaurant->id,
                'total' => $total,
            ]);

            $paymentMethodId = $data['payment_method_id'] ?? null;
            ProcessPayment::dispatch($order, $paymentMethodId)->onQueue('payments');
            DispatchRider::dispatch($order->fresh())->onQueue('dispatch');

            return $order;
        });
    }

    public function confirmOrder(Order $order, User $actor): bool
    {
        return $this->transitionOrder($order, OrderStatus::CONFIRMED, $actor, 'Order confirmed by restaurant');
    }

    public function startPreparing(Order $order, User $actor): bool
    {
        return $this->transitionOrder($order, OrderStatus::PREPARING, $actor, 'Order being prepared');
    }

    public function markOnTheWay(Order $order, User $actor): bool
    {
        return $this->transitionOrder($order, OrderStatus::ON_THE_WAY, $actor, 'Order picked up by rider');
    }

    public function deliverOrder(Order $order, User $actor): bool
    {
        $result = $this->transitionOrder($order, OrderStatus::DELIVERED, $actor, 'Order delivered successfully');

        if ($result) {
            $this->processPayouts($order);
        }

        return $result;
    }

    public function cancelOrder(Order $order, User $actor, string $reason): bool
    {
        return $this->transitionOrder($order, OrderStatus::CANCELLED, $actor, $reason, [
            'cancellation_reason' => $reason,
            'cancelled_by' => $actor->id,
        ]);
    }

    public function assignRider(Order $order, int $riderId): bool
    {
        $order->rider_id = $riderId;
        $order->save();

        Log::info('Rider assigned to order', [
            'order_id' => $order->id,
            'rider_id' => $riderId,
        ]);

        return true;
    }

    public function transitionOrder(
        Order $order,
        OrderStatus $newStatus,
        User $actor,
        ?string $notes = null,
        array $additionalData = []
    ): bool {
        $currentStatus = OrderStatus::fromString($order->status);

        if (! $currentStatus->canTransitionTo($newStatus)) {
            Log::warning('Invalid order state transition attempted', [
                'order_id' => $order->id,
                'current_status' => $order->status,
                'attempted_status' => $newStatus->value,
                'actor_id' => $actor->id,
            ]);

            return false;
        }

        $order->fill($additionalData);
        $order->status = $newStatus->value;
        $order->save();

        $order->statusLogs()->create([
            'old_state' => $currentStatus->value,
            'new_state' => $newStatus->value,
            'actor_id' => $actor->id,
            'actor_type' => get_class($actor),
            'notes' => $notes,
            'timestamp' => now(),
        ]);

        $this->handleStatusTransitionSideEffects($order->fresh(), $newStatus, $actor, $currentStatus);

        $this->syncStatusTimestamp($order->fresh(), $newStatus);

        event(new OrderStatusUpdated($order->fresh()));

        Log::info('Order status transitioned', [
            'order_id' => $order->id,
            'previous_status' => $currentStatus->value,
            'new_status' => $newStatus->value,
            'actor_id' => $actor->id,
        ]);

        return true;
    }

    protected function syncStatusTimestamp(Order $order, OrderStatus $status): void
    {
        $field = match ($status) {
            OrderStatus::CONFIRMED => 'confirmed_at',
            OrderStatus::PREPARING => 'preparing_at',
            OrderStatus::ON_THE_WAY => 'picked_up_at',
            OrderStatus::DELIVERED => 'delivered_at',
            OrderStatus::CANCELLED => 'cancelled_at',
            default => null,
        };

        if ($field) {
            $order->{$field} = now();
            $order->save();
        }
    }

    protected function handleStatusTransitionSideEffects(Order $order, OrderStatus $newStatus, User $actor, OrderStatus $from): void
    {
        $surge = app(SurgePricingService::class);

        switch ($newStatus) {
            case OrderStatus::CONFIRMED:
                $surge->registerDemandSignal();
                RecalculateSurge::dispatch($order)->onQueue('surge');
                break;
            case OrderStatus::PREPARING:
                DispatchRider::dispatch($order)->onQueue('dispatch');
                break;
            case OrderStatus::ON_THE_WAY:
                event(new OrderPickedUp($order));
                break;
            case OrderStatus::DELIVERED:
                $surge->releaseDemandSignal();
                if ($order->rider) {
                    $order->rider->increment('total_deliveries');
                }
                break;
            case OrderStatus::CANCELLED:
                if ($from !== OrderStatus::PLACED) {
                    $surge->releaseDemandSignal();
                }
                break;
        }
    }

    protected function processPayouts(Order $order): void
    {
        $restaurantPayout = $order->subtotal - $order->platform_fee;
        $riderPayout = $order->delivery_fee;

        $order->payouts()->createMany([
            [
                'recipient_type' => 'restaurant',
                'recipient_id' => $order->restaurant_id,
                'amount' => $restaurantPayout,
                'platform_commission' => $order->platform_fee,
                'status' => 'pending',
            ],
            [
                'recipient_type' => 'rider',
                'recipient_id' => $order->rider_id,
                'amount' => $riderPayout,
                'platform_commission' => 0,
                'status' => 'pending',
            ],
        ]);

        Log::info('Payouts created for order', [
            'order_id' => $order->id,
            'restaurant_amount' => $restaurantPayout,
            'rider_amount' => $riderPayout,
        ]);
    }

    protected function calculateOrderItems(array $items): array
    {
        $calculatedItems = [];

        foreach ($items as $item) {
            $menuItem = MenuItem::findOrFail($item['menu_item_id']);
            $unitPrice = (float) $menuItem->price;
            $variantName = null;

            if (! empty($item['variant_id'])) {
                $variant = $menuItem->variants()->findOrFail($item['variant_id']);
                $unitPrice += (float) $variant->price_modifier;
                $variantName = $variant->name;
            }

            $quantity = $item['quantity'];
            $subtotal = $unitPrice * $quantity;

            $calculatedItems[] = [
                'menu_item_id' => $menuItem->id,
                'variant_id' => $item['variant_id'] ?? null,
                'item_name' => $menuItem->name,
                'variant_name' => $variantName,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
                'special_instructions' => $item['special_instructions'] ?? null,
            ];
        }

        return $calculatedItems;
    }

    public function getOrderHistory(User $customer, int $perPage = 15)
    {
        return $customer->customerOrders()
            ->with(['restaurant', 'rider', 'items'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getRestaurantOrders(Restaurant $restaurant, ?string $status = null)
    {
        $query = $restaurant->orders()->with(['customer', 'items', 'rider']);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    public function getRiderOrders($rider)
    {
        return $rider->orders()
            ->with(['restaurant', 'customer', 'items'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
    }
}
