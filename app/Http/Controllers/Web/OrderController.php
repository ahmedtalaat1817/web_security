<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    public function store(Request $request): RedirectResponse
    {
        if (isset($request['items']) && is_string($request['items'])) {
            $decoded = json_decode($request['items'], true);
            $request->merge(['items' => is_array($decoded) ? $decoded : []]);
        }

        $validated = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.variant_id' => 'nullable|exists:item_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'delivery_address' => 'required|string',
            'delivery_instructions' => 'nullable|string',
            'customer_phone' => 'required|string',
            'customer_name' => 'required|string',
            'delivery_latitude' => 'nullable|numeric',
            'delivery_longitude' => 'nullable|numeric',
            'payment_method_id' => 'nullable|string',
        ]);

        $restaurant = \App\Models\Restaurant::find($validated['restaurant_id']);

        $validated['delivery_latitude'] = (float) ($request->input('delivery_latitude', $restaurant?->latitude ?? 0));
        $validated['delivery_longitude'] = (float) ($request->input('delivery_longitude', $restaurant?->longitude ?? 0));

        $order = $this->orderService->createOrder($validated, $request->user());

        return redirect()
            ->route('customer.orders.show', $order)
            ->with('success', 'Order placed successfully.');
    }
}