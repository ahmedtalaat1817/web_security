<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PartnerPackage;
use App\Models\Rider;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'customers' => Order::distinct('customer_id')->count('customer_id'),
            'restaurants' => Restaurant::count(),
            'riders' => Rider::count(),
            'today_orders' => Order::whereDate('created_at', today())->count(),
            'today_revenue' => Order::whereDate('created_at', today())->where('status', 'delivered')->sum('total'),
            'pending_orders' => Order::whereIn('status', ['placed', 'confirmed', 'preparing'])->count(),
            'total_revenue' => Order::where('status', 'delivered')->sum('total'),
            'total_orders' => Order::count(),
            'avg_order_value' => Order::where('status', 'delivered')->avg('total') ?? 0,
        ];

        $orderStatusCounts = Order::select('status', DB::raw('count(*) as count'))
            ->whereIn('status', ['placed', 'confirmed', 'preparing', 'on_the_way'])
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $activeOrders = Order::whereIn('status', ['placed', 'confirmed', 'preparing', 'on_the_way'])
            ->with(['restaurant', 'customer', 'rider.user'])
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        $riders = Rider::with(['user', 'location'])->get();

        $restaurants = Restaurant::with('user')
            ->withCount('orders')
            ->get()
            ->map(function ($r) {
                $revenue = Order::where('restaurant_id', $r->id)
                    ->where('status', 'delivered')
                    ->sum('total');
                return (object) [
                    'id' => $r->id,
                    'name' => $r->name,
                    'status' => $r->status,
                    'is_open' => $r->is_open,
                    'rating' => $r->rating,
                    'total_orders' => $r->orders_count,
                    'total_revenue' => $revenue,
                    'owner_name' => $r->user?->owner_name ?? $r->user?->name ?? 'N/A',
                    'owner_email' => $r->user?->email ?? 'N/A',
                    'owner_phone' => $r->user?->phone ?? 'N/A',
                    'partner_status' => $r->user?->partner_status ?? null,
                    'created_at' => $r->created_at,
                ];
            })->sortByDesc('total_orders')->values();

        $pendingRestaurants = $restaurants->filter(function ($r) {
            return $r->status !== 'active' || !$r->is_open;
        })->values();

        $ordersByDay = Order::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'), DB::raw('COALESCE(SUM(CASE WHEN status = "delivered" THEN total ELSE 0 END), 0) as revenue'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $orderStatusAll = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('admin.dashboard', compact(
            'stats', 'orderStatusCounts', 'activeOrders', 'riders',
            'restaurants', 'pendingRestaurants', 'ordersByDay', 'orderStatusAll'
        ));
    }

    public function showOrder(Order $order)
    {
        $order->load(['restaurant', 'customer', 'rider.user', 'items', 'statusLogs']);
        return view('admin.orders.show', compact('order'));
    }

    public function assignOrder(Request $request, Order $order)
    {
        if ($request->has('rider_id')) {
            $rider = Rider::findOrFail($request->rider_id);

            $order->update([
                'rider_id' => $rider->id,
                'status' => 'confirmed',
            ]);

            $rider->update(['status' => 'busy']);

            return redirect()->route('admin.dashboard')->with('success', "Rider {$rider->user->name} assigned to order {$order->order_number}.");
        }

        $availableRiders = Rider::where('status', 'available')->with('user', 'location')->get();
        return view('admin.orders.assign', compact('order', 'availableRiders'));
    }

    public function riderLocations()
    {
        $riders = Rider::with(['user', 'location'])->get()->map(function ($rider) {
            return [
                'id' => $rider->id,
                'name' => $rider->user->name,
                'status' => $rider->status,
                'latitude' => $rider->location?->latitude,
                'longitude' => $rider->location?->longitude,
            ];
        });
        return response()->json($riders);
    }

    public function partners()
    {
        $partners = User::where('user_type', 'restaurant')
            ->with('partnerPackage')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total_partners' => User::where('user_type', 'restaurant')->count(),
            'active_partners' => User::where('user_type', 'restaurant')->where('partner_status', 'active')->count(),
            'pending_partners' => User::where('user_type', 'restaurant')->where(function ($q) {
                $q->where('partner_status', 'pending_payment')->orWhereNull('partner_status');
            })->count(),
            'suspended_partners' => User::where('user_type', 'restaurant')->where('partner_status', 'suspended')->count(),
        ];

        return view('admin.partners.index', compact('partners', 'stats'));
    }

    public function showPartner(User $user)
    {
        $user->load('partnerPackage', 'restaurant');
        return view('admin.partners.show', compact('user'));
    }

    public function updatePartnerStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:active,suspended,pending_payment',
        ]);

        $user->update(['partner_status' => $request->status]);

        return back()->with('success', 'Partner status updated successfully.');
    }

    public function packages()
    {
        $packages = PartnerPackage::orderBy('price')->get();
        return view('admin.packages.index', compact('packages'));
    }

    public function createPackage()
    {
        return view('admin.packages.create');
    }

    public function storePackage(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly',
            'includes_ads' => 'sometimes|boolean',
            'max_menu_items' => 'required|integer|min:-1',
            'is_featured' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
        ]);

        $validated['includes_ads'] = $request->boolean('includes_ads');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active'] = $request->boolean('is_active');

        PartnerPackage::create($validated);

        return redirect()->route('admin.packages.index')->with('success', 'Package created successfully.');
    }

    public function editPackage(PartnerPackage $package)
    {
        return view('admin.packages.edit', compact('package'));
    }

    public function updatePackage(Request $request, PartnerPackage $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly',
            'includes_ads' => 'sometimes|boolean',
            'max_menu_items' => 'required|integer|min:-1',
            'is_featured' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
        ]);

        $validated['includes_ads'] = $request->boolean('includes_ads');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active'] = $request->boolean('is_active');

        $package->update($validated);

        return redirect()->route('admin.packages.index')->with('success', 'Package updated successfully.');
    }

    public function destroyPackage(PartnerPackage $package)
    {
        $package->update(['is_active' => false]);
        return redirect()->route('admin.packages.index')->with('success', 'Package deactivated successfully.');
    }

    public function approveRestaurant($id)
    {
        $restaurant = Restaurant::withTrashed()->findOrFail($id);
        $restaurant->update([
            'status' => 'active',
            'is_open' => true,
        ]);
        return back()->with('success', "{$restaurant->name} has been approved and is now open.");
    }

    public function toggleRestaurant($id)
    {
        $restaurant = Restaurant::withTrashed()->findOrFail($id);
        $restaurant->update(['is_open' => !$restaurant->is_open]);
        $state = $restaurant->fresh()->is_open ? 'opened' : 'closed';
        return back()->with('success', "{$restaurant->name} has been {$state}.");
    }
}