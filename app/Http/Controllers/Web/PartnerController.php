<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PartnerPackage;
use App\Models\User;
use App\Models\Restaurant;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PartnerController extends Controller
{
    public function __construct(
        protected PayPalService $paypalService
    ) {}

    public function pricing()
    {
        $packages = PartnerPackage::active()->orderBy('price')->get();
        $monthly = $packages->where('billing_cycle', 'monthly');
        $yearly = $packages->where('billing_cycle', 'yearly');

        return view('partner.pricing', compact('monthly', 'yearly'));
    }

    public function register(Request $request)
    {
        $packageId = $request->get('package');
        $package = PartnerPackage::findOrFail($packageId);

        return view('partner.register', compact('package'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'owner_name' => 'required|string|max:255',
            'national_id' => 'required|string|max:50|unique:users,national_id',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'restaurant_name' => 'required|string|max:255',
            'restaurant_address' => 'required|string',
            'commercial_registration_number' => 'nullable|string|max:50',
            'tax_id' => 'nullable|string|max:50',
            'package_id' => 'required|exists:partner_packages,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $package = PartnerPackage::findOrFail($validated['package_id']);

        $user = User::create([
            'name' => $validated['restaurant_name'] . ' - ' . $validated['owner_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'user_type' => 'restaurant',
            'owner_name' => $validated['owner_name'],
            'national_id' => $validated['national_id'],
            'commercial_registration_number' => $validated['commercial_registration_number'] ?? null,
            'tax_id' => $validated['tax_id'] ?? null,
            'restaurant_name' => $validated['restaurant_name'],
            'restaurant_address' => $validated['restaurant_address'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'partner_package_id' => $package->id,
            'partner_status' => 'pending_payment',
        ]);

        return redirect()->route('partner.payment', ['user' => $user->id, 'package' => $package->id]);
    }

    public function payment(Request $request)
    {
        $userId = $request->get('user');
        $packageId = $request->get('package');

        $user = User::findOrFail($userId);
        $package = PartnerPackage::findOrFail($packageId);

        if ($user->partner_status !== 'pending_payment') {
            return redirect()->route('partner.pricing')->with('error', 'Invalid payment request.');
        }

        try {
            $returnUrls = [
                'return_url' => route('partner.payment.success') . '?user=' . $user->id,
                'cancel_url' => route('partner.payment.cancel') . '?user=' . $user->id,
            ];

            $order = $this->paypalService->createOrder(
                (float) $package->price,
                'USD',
                "Foodie Partner Package: {$package->name}",
                $returnUrls
            );

            $user->update(['payment_id' => $order['id']]);
            $user->save();

            $approveUrl = collect($order['links'])->firstWhere('rel', 'approve')['href'];

            return view('partner.payment', compact('user', 'package', 'approveUrl', 'order'));
        } catch (\Exception $e) {
            Log::error('PayPal payment creation failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Payment initialization failed. Please try again.');
        }
    }

    public function paymentSuccess(Request $request)
    {
        $userId = $request->get('user');
        $token = $request->get('token');

        $user = User::findOrFail($userId);

        try {
            $captureData = $this->paypalService->captureOrder($token);

            if ($captureData['status'] === 'COMPLETED') {
                $user->update([
                    'partner_status' => 'active',
                    'partner_since' => now(),
                    'email_verified_at' => now(),
                ]);

                $restaurant = Restaurant::create([
                    'user_id' => $user->id,
                    'name' => $user->restaurant_name,
                    'address' => $user->restaurant_address,
                    'latitude' => $user->latitude ?? 0,
                    'longitude' => $user->longitude ?? 0,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'status' => 'inactive',
                    'is_open' => false,
                    'delivery_time_minutes' => 30,
                    'delivery_fee' => 2.99,
                    'minimum_order' => 10.00,
                ]);

                Log::info('Restaurant partner registration completed', [
                    'user_id' => $user->id,
                    'restaurant_id' => $restaurant->id,
                    'package' => $user->partner_package_id,
                ]);

                Auth::login($user);

                return redirect()->route('restaurant.dashboard')->with('success', 'Welcome! Your restaurant partner account is now active.');
            }

            return redirect()->route('partner.pricing')->with('error', 'Payment was not completed.');
        } catch (\Exception $e) {
            Log::error('PayPal payment capture failed', ['error' => $e->getMessage()]);
            return redirect()->route('partner.pricing')->with('error', 'Payment verification failed. Please contact support.');
        }
    }

    public function paymentCancel(Request $request)
    {
        $userId = $request->get('user');
        $user = User::findOrFail($userId);

        $user->update(['partner_status' => 'pending', 'payment_id' => null]);

        return redirect()->route('partner.pricing')->with('info', 'Payment was cancelled. You can try again.');
    }
}