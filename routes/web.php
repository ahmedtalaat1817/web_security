<?php

use App\Http\Controllers\Web\CustomerController;
use App\Http\Controllers\Web\OrderController as WebOrderController;
use App\Http\Controllers\Web\RestaurantController as WebRestaurantController;
use App\Http\Controllers\Web\RiderController as WebRiderController;
use App\Http\Controllers\Web\AdminController;
use App\Http\Controllers\Web\PartnerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\MenuVariantController;
use App\Http\Controllers\GeocodeController;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $restaurants = \App\Models\Restaurant::query()
        ->orderByDesc('rating')
        ->limit(6)
        ->get();
    return view('welcome', compact('restaurants'));
})->name('home');

Route::get('/partner/pricing', [PartnerController::class, 'pricing'])->name('partner.pricing');
Route::get('/partner/register', [PartnerController::class, 'register'])->name('partner.register');
Route::post('/partner/store', [PartnerController::class, 'store'])->name('partner.store');
Route::get('/partner/payment', [PartnerController::class, 'payment'])->name('partner.payment');
Route::get('/partner/payment/success', [PartnerController::class, 'paymentSuccess'])->name('partner.payment.success');
Route::get('/partner/payment/cancel', [PartnerController::class, 'paymentCancel'])->name('partner.payment.cancel');

Route::get('/geocode/search', [GeocodeController::class, 'search'])->name('geocode.search');
Route::get('/geocode/reverse', [GeocodeController::class, 'reverse'])->name('geocode.reverse');

Route::get('/restaurants', [CustomerController::class, 'restaurantsIndex'])->name('restaurants.index');
Route::get('/restaurants/{restaurant}', [CustomerController::class, 'restaurantsShow'])->name('restaurants.show');

Route::middleware(['auth'])->group(function () {
    Route::post('/orders', [WebOrderController::class, 'store'])->name('orders.store');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->isAdmin()) return redirect()->route('admin.dashboard');
        if ($user->isRestaurant()) return redirect()->route('restaurant.dashboard');
        if ($user->isRider()) return redirect()->route('rider.dashboard');
        return redirect()->route('restaurants.index');
    })->name('dashboard');

    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');

    Route::resource('categories', CategoryController::class);
    Route::resource('variants', MenuVariantController::class);
    Route::resource('menu-items', MenuItemController::class);

    Route::prefix('customer')->name('customer.')->group(function () {
        Route::get('/orders', [CustomerController::class, 'orders'])->name('orders.index');
        Route::get('/orders/{order}', [CustomerController::class, 'showOrder'])->name('orders.show');
        Route::post('/orders/{order}/review', [CustomerController::class, 'storeReview'])->name('orders.review');
        Route::post('/orders/{order}/cancel', [CustomerController::class, 'cancelOrder'])->name('orders.cancel');
    });

    Route::prefix('restaurant')->name('restaurant.')->group(function () {
        Route::get('/dashboard', [WebRestaurantController::class, 'dashboard'])->name('dashboard');
        Route::get('/orders', [WebRestaurantController::class, 'orders'])->name('orders');
        Route::get('/orders/{order}', [WebRestaurantController::class, 'showOrder'])->name('orders.show');
        Route::get('/profile', [WebRestaurantController::class, 'profile'])->name('profile');
        Route::put('/profile', [WebRestaurantController::class, 'updateProfileWeb'])->name('profile.update');
        Route::get('/menu', [WebRestaurantController::class, 'menuIndex'])->name('menu.index');
        Route::get('/menu/create', [WebRestaurantController::class, 'createMenuItem'])->name('menu.create');
        Route::post('/menu/items', [WebRestaurantController::class, 'storeMenuItem'])->name('menu.store');
        Route::get('/menu/{menuItem}/edit', [WebRestaurantController::class, 'editMenuItem'])->name('menu.edit');
        Route::put('/menu/{menuItem}', [WebRestaurantController::class, 'updateMenuItem'])->name('menu.update');
        Route::delete('/menu/{menuItem}', [WebRestaurantController::class, 'destroyMenuItem'])->name('menu.destroy');
        Route::post('/categories', [WebRestaurantController::class, 'storeCategory'])->name('categories.store');
        Route::post('/orders/{order}/confirm', [WebRestaurantController::class, 'confirmOrder'])->name('orders.confirm');
        Route::post('/orders/{order}/preparing', [WebRestaurantController::class, 'startPreparing'])->name('orders.preparing');
    });

    Route::prefix('rider')->name('rider.')->group(function () {
        Route::get('/dashboard', [WebRiderController::class, 'dashboard'])->name('dashboard');
        Route::get('/orders', [WebRiderController::class, 'orders'])->name('orders');
        Route::post('/status', [WebRiderController::class, 'updateStatus'])->name('status');
        Route::post('/location', [WebRiderController::class, 'updateLocation'])->name('location');
        Route::post('/orders/{order}/accept', [WebRiderController::class, 'acceptOrder'])->name('order.accept');
        Route::post('/orders/{order}/pickup', [WebRiderController::class, 'pickupOrder'])->name('order.pickup');
        Route::post('/orders/pickup-all', [WebRiderController::class, 'pickupAllOrders'])->name('order.pickup-all');
        Route::post('/orders/{order}/deliver', [WebRiderController::class, 'deliverOrder'])->name('order.deliver');
    });

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/orders/{order}', [AdminController::class, 'showOrder'])->name('orders.show');
        Route::get('/orders/{order}/assign', [AdminController::class, 'assignOrder'])->name('orders.assign');
        Route::post('/orders/{order}/assign', [AdminController::class, 'assignOrder']);
        Route::get('/riders/locations', [AdminController::class, 'riderLocations'])->name('riders.locations');
        Route::get('/partners', [AdminController::class, 'partners'])->name('partners.index');
        Route::get('/partners/{user}', [AdminController::class, 'showPartner'])->name('partners.show');
        Route::put('/partners/{user}/status', [AdminController::class, 'updatePartnerStatus'])->name('partners.update-status');
        Route::get('/packages', [AdminController::class, 'packages'])->name('packages.index');
        Route::get('/packages/create', [AdminController::class, 'createPackage'])->name('packages.create');
        Route::post('/packages', [AdminController::class, 'storePackage'])->name('packages.store');
        Route::get('/packages/{package}/edit', [AdminController::class, 'editPackage'])->name('packages.edit');
        Route::put('/packages/{package}', [AdminController::class, 'updatePackage'])->name('packages.update');
        Route::delete('/packages/{package}', [AdminController::class, 'destroyPackage'])->name('packages.destroy');
        Route::put('/restaurants/{id}/approve', [AdminController::class, 'approveRestaurant'])->name('restaurants.approve');
        Route::put('/restaurants/{id}/toggle', [AdminController::class, 'toggleRestaurant'])->name('restaurants.toggle');
    });
});

require __DIR__.'/auth.php';