<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\RiderLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiderController extends Controller
{
    public function dashboard()
    {
        $rider = Auth::user()->rider;

        if (!$rider) {
            return redirect()->route('home')->with('error', 'Rider profile not found');
        }

        $todayDeliveries = $rider->orders()
            ->whereDate('delivered_at', today())
            ->count();

        $todayEarnings = $rider->orders()
            ->whereDate('delivered_at', today())
            ->sum('delivery_fee');

        $currentOrders = $rider->orders()
            ->whereIn('status', ['confirmed', 'preparing', 'on_the_way'])
            ->with(['restaurant', 'customer'])
            ->orderBy('created_at', 'asc')
            ->get();

        $currentOrder = $currentOrders->first();

        $availableOrders = Order::where('status', 'confirmed')
            ->whereNull('rider_id')
            ->with(['restaurant'])
            ->orderBy('created_at', 'asc')
            ->take(20)
            ->get();

        $latestLocation = $rider->locations()->latest()->first();
        $riderLat = $latestLocation ? $latestLocation->latitude : 30.0444;
        $riderLng = $latestLocation ? $latestLocation->longitude : 31.2357;

        return view('rider.dashboard', compact(
            'rider', 
            'todayDeliveries', 
            'todayEarnings', 
            'currentOrder',
            'currentOrders',
            'availableOrders',
            'riderLat',
            'riderLng'
        ));
    }

    public function orders()
    {
        $rider = Auth::user()->rider;

        $orders = $rider->orders()
            ->with(['restaurant', 'customer'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('rider.orders', compact('orders'));
    }

    public function updateStatus(Request $request)
    {
        $rider = Auth::user()->rider;

        $request->validate([
            'status' => 'required|in:available,busy,offline',
        ]);

        $rider->update(['status' => $request->status]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'status' => $request->status]);
        }

        return back()->with('success', 'Status updated');
    }

    public function updateLocation(Request $request)
    {
        $rider = Auth::user()->rider;

        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $rider->locations()->create([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'recorded_at' => now(),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Location updated');
    }

    public function acceptOrder(Order $order)
    {
        $rider = Auth::user()->rider;

        if ($order->rider_id) {
            return back()->with('error', 'Order already assigned');
        }

        $activeCount = $rider->orders()
            ->whereIn('status', ['confirmed', 'preparing', 'on_the_way'])
            ->count();

        if ($activeCount >= 3) {
            return back()->with('error', 'You already have 3 active deliveries. Complete one first.');
        }

        $order->update(['rider_id' => $rider->id]);

        $newActiveCount = $activeCount + 1;
        if ($newActiveCount >= 3) {
            $rider->update(['status' => 'busy']);
        } else {
            $rider->update(['status' => 'available']);
        }

        return back()->with('success', 'Order accepted');
    }

    public function pickupAllOrders()
    {
        $rider = Auth::user()->rider;

        $orders = $rider->orders()
            ->whereIn('status', ['confirmed', 'preparing'])
            ->get();

        $count = 0;
        foreach ($orders as $order) {
            $order->update(['status' => 'on_the_way', 'picked_up_at' => now()]);
            $count++;
        }

        $rider->update(['status' => 'busy']);

        return back()->with('success', "{$count} order(s) picked up. Route started!");
    }

    public function pickupOrder(Order $order)
    {
        $rider = Auth::user()->rider;

        if ($order->rider_id !== $rider->id) {
            abort(403);
        }

        $order->update(['status' => 'on_the_way', 'picked_up_at' => now()]);

        return back()->with('success', 'Order picked up');
    }

    public function deliverOrder(Order $order)
    {
        $rider = Auth::user()->rider;

        if ($order->rider_id !== $rider->id) {
            abort(403);
        }

        $order->update(['status' => 'delivered', 'delivered_at' => now()]);
        $rider->increment('total_deliveries');

        $activeOrders = $rider->orders()
            ->whereIn('status', ['confirmed', 'preparing', 'on_the_way'])
            ->count();

        if ($activeOrders < 3) {
            $rider->update(['status' => 'available']);
        }

        return back()->with('success', 'Order delivered');
    }
}
