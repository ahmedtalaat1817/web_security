<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review;
use App\Models\Restaurant;
use App\Services\OrderService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    public function restaurantsIndex(Request $request)
    {
        $query = Restaurant::query()->where('is_open', true);

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if ($request->filled('rating')) {
            $minRating = (float) $request->string('rating')->toString();
            $query->where('rating', '>=', $minRating);
        }

        if ($request->filled('delivery_time')) {
            $maxTime = (int) $request->string('delivery_time')->toString();
            $query->where('delivery_time_minutes', '<=', $maxTime);
        }

        $sortField = 'rating';
        $sortDir = 'desc';

        if ($request->filled('sort_by')) {
            $sortBy = $request->string('sort_by');
            match ($sortBy) {
                'time' => $sortField = 'delivery_time_minutes',
                'delivery' => [$sortField, $sortDir] = ['delivery_fee', 'asc'],
                default => null,
            };
        }

        $restaurants = $query->orderBy($sortField, $sortDir)->paginate(12)->withQueryString();

        return view('customer.restaurants.index', compact('restaurants'));
    }

    public function restaurantsShow(Restaurant $restaurant)
    {
        $categories = $restaurant->activeCategories()
            ->with(['activeMenuItems' => function ($q) {
                $q->select([
                    'id',
                    'category_id',
                    'name',
                    'description',
                    'price',
                    'image',
                    'is_available',
                    'sort_order',
                ]);
            }])
            ->get();

        $categories->each(function ($category) {
            $category->setRelation('menuItems', $category->activeMenuItems);
        });

        return view('customer.restaurants.show', compact('restaurant', 'categories'));
    }

    public function orders(Request $request)
    {
        $query = Auth::user()->customerOrders()->with(['restaurant']);

        if ($request->filled('status')) {
            $status = $request->string('status');
            if ($status === 'active') {
                $query->whereIn('status', ['confirmed', 'preparing', 'on_the_way', 'delivered']);
            } else {
                $query->where('status', $status);
            }
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('customer.orders.index', compact('orders'));
    }

    public function showOrder(Order $order)
    {
        if ($order->customer_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['restaurant', 'rider.user', 'items', 'statusLogs', 'payment']);

        return view('customer.orders.show', compact('order'));
    }

    public function cancelOrder(Request $request, Order $order)
    {
        if ($order->customer_id !== Auth::id()) {
            abort(403);
        }

        if (in_array($order->status, ['on_the_way', 'delivered', 'cancelled'])) {
            return back()->with('error', 'This order cannot be cancelled at this stage.');
        }

        $reason = $request->input('reason', 'Cancelled by customer');

        if ($this->orderService->cancelOrder($order, Auth::user(), $reason)) {
            return redirect()->route('customer.orders.show', $order)->with('success', 'Order has been cancelled.');
        }

        return back()->with('error', 'Unable to cancel this order.');
    }

    public function storeReview(Request $request, Order $order)
    {
        if ($order->customer_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status !== 'delivered') {
            return back()->with('error', 'You can only review delivered orders.');
        }

        if ($order->review()->exists()) {
            return back()->with('error', 'You have already reviewed this order.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        $review = $order->review()->create([
            'customer_id' => Auth::id(),
            'restaurant_id' => $order->restaurant_id,
            'rider_id' => $order->rider_id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'type' => 'restaurant',
        ]);

        $order->restaurant->recalculateRating();

        return back()->with('success', 'Thank you for your review!');
    }
}