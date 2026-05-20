<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RestaurantController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {
    }

    public function dashboard()
    {
        $restaurant = Auth::user()->restaurant;

        if (!$restaurant) {
            return redirect()->route('home')->with('error', 'Restaurant profile not found');
        }

        $todayOrders = $restaurant->orders()
            ->whereDate('created_at', today())
            ->count();

        $todayRevenue = $restaurant->orders()
            ->whereDate('created_at', today())
            ->where('status', 'delivered')
            ->sum('subtotal');

        $recentOrders = $restaurant->orders()
            ->with(['customer'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $pendingOrders = $restaurant->orders()
            ->whereIn('status', ['placed', 'confirmed'])
            ->count();

        return view('restaurant.dashboard', compact('restaurant', 'todayOrders', 'todayRevenue', 'recentOrders', 'pendingOrders'));
    }

    public function orders(Request $request)
    {
        $restaurant = Auth::user()->restaurant;

        $ordersQuery = $restaurant->orders()
            ->with(['customer', 'rider']);

        if ($request->filled('status')) {
            $ordersQuery->where('status', $request->string('status'));
        }

        $orders = $ordersQuery
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('restaurant.orders.index', compact('orders'));
    }

    public function showOrder(Order $order)
    {
        $restaurant = Auth::user()->restaurant;

        if ($order->restaurant_id !== $restaurant->id) {
            abort(403);
        }

        $order->load(['customer', 'items', 'statusLogs']);

        return view('restaurant.orders.show', compact('order'));
    }

    public function profile()
    {
        $restaurant = Auth::user()->restaurant;
        abort_unless($restaurant, 403);

        return view('restaurant.profile', compact('restaurant'));
    }

    public function updateProfileWeb(Request $request)
    {
        $restaurant = Auth::user()->restaurant;
        abort_unless($restaurant, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'delivery_fee' => ['nullable', 'numeric', 'min:0'],
            'delivery_time_minutes' => ['nullable', 'integer', 'min:1'],
            'minimum_order' => ['nullable', 'numeric', 'min:0'],
            'logo' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'string'],
            'is_open' => ['nullable', 'boolean'],
        ]);

        $restaurant->update($validated);

        return redirect()->route('restaurant.profile')->with('status', 'Profile updated successfully.');
    }

    public function menuIndex()
    {
        $restaurant = Auth::user()->restaurant;
        abort_unless($restaurant, 403);

        $categories = $restaurant->categories()
            ->with('menuItems')
            ->orderBy('sort_order')
            ->get();

        return view('restaurant.menu.index', compact('restaurant', 'categories'));
    }

    public function createMenuItem()
    {
        $restaurant = Auth::user()->restaurant;
        $categories = $restaurant->categories()->where('is_active', true)->get();

        return view('restaurant.menu.create', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $restaurant = Auth::user()->restaurant;
        abort_unless($restaurant, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $restaurant->categories()->create($validated);

        return redirect()->route('restaurant.dashboard')->with('status', 'Category added.');
    }

    public function storeMenuItem(Request $request)
    {
        $restaurant = Auth::user()->restaurant;
        abort_unless($restaurant, 403);

        $validated = $request->validate([
            'category_id' => [
                'required',
                Rule::exists('menu_categories', 'id')->where('restaurant_id', $restaurant->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'string'],
        ]);

        $restaurant->menuItems()->create([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'image' => $validated['image'] ?? null,
            'is_featured' => $request->boolean('is_featured'),
            'is_available' => true,
        ]);

        return redirect()->route('restaurant.menu.create')->with('status', 'Menu item created.');
    }

    public function editMenuItem(MenuItem $menuItem)
    {
        $restaurant = Auth::user()->restaurant;
        abort_if($menuItem->restaurant_id !== $restaurant->id, 403);

        $categories = $restaurant->categories()->where('is_active', true)->get();
        return view('restaurant.menu.edit', compact('menuItem', 'categories'));
    }

    public function updateMenuItem(Request $request, MenuItem $menuItem)
    {
        $restaurant = Auth::user()->restaurant;
        abort_if($menuItem->restaurant_id !== $restaurant->id, 403);

        $validated = $request->validate([
            'category_id' => [
                'required',
                Rule::exists('menu_categories', 'id')->where('restaurant_id', $restaurant->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'string'],
        ]);

        $menuItem->update([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'image' => $validated['image'] ?? null,
            'is_featured' => $request->boolean('is_featured'),
            'is_available' => $request->boolean('is_available', true),
        ]);

        return redirect()->route('restaurant.dashboard')->with('status', 'Menu item updated.');
    }

    public function destroyMenuItem(MenuItem $menuItem)
    {
        $restaurant = Auth::user()->restaurant;
        abort_if($menuItem->restaurant_id !== $restaurant->id, 403);

        $menuItem->delete();

        return redirect()->route('restaurant.dashboard')->with('status', 'Menu item deleted.');
    }

    public function confirmOrder(Order $order)
    {
        $this->authorizeRestaurantOrder($order);

        if (!$this->orderService->confirmOrder($order, Auth::user())) {
            return back()->withErrors(['order' => 'Cannot confirm this order.']);
        }

        return redirect()->route('restaurant.orders.show', $order)->with('status', 'Order confirmed.');
    }

    public function startPreparing(Order $order)
    {
        $this->authorizeRestaurantOrder($order);

        if (!$this->orderService->startPreparing($order, Auth::user())) {
            return back()->withErrors(['order' => 'Cannot start preparing this order.']);
        }

        return redirect()->route('restaurant.orders.show', $order)->with('status', 'Now preparing.');
    }

    private function authorizeRestaurantOrder(Order $order): void
    {
        $restaurant = Auth::user()->restaurant;
        abort_if(!$restaurant || $order->restaurant_id !== $restaurant->id, 403);
    }
}
