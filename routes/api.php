<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GeocodingController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\RestaurantController;
use App\Http\Controllers\Api\RiderController;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/stripe', [PaymentController::class, 'webhook'])
    ->middleware('throttle:60,1');

Route::get('/health', fn () => response()->json(['status' => 'ok']));

Route::prefix('geocode')->name('geocode.')->group(function () {
    Route::post('/address', [GeocodingController::class, 'geocode'])->name('address');
    Route::post('/validate', [GeocodingController::class, 'validateAddress'])->name('validate');
    Route::post('/distance', [GeocodingController::class, 'calculateDistance'])->name('distance');
});

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('me', [AuthController::class, 'me'])->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('location', [AuthController::class, 'updateLocation'])->middleware('throttle:gps');
        Route::post('status', [AuthController::class, 'updateStatus']);
    });

    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::post('/', [OrderController::class, 'store']);
        Route::get('/{order}', [OrderController::class, 'show']);
        Route::post('/{order}/confirm', [OrderController::class, 'confirm']);
        Route::post('/{order}/preparing', [OrderController::class, 'startPreparing']);
        Route::post('/{order}/on-the-way', [OrderController::class, 'markOnTheWay']);
        Route::post('/{order}/deliver', [OrderController::class, 'deliver']);
        Route::post('/{order}/cancel', [OrderController::class, 'cancel']);
        Route::get('/restaurant/list', [OrderController::class, 'restaurantOrders']);
        Route::get('/rider/list', [OrderController::class, 'riderOrders']);
    });

    Route::prefix('restaurants')->group(function () {
        Route::get('/', [RestaurantController::class, 'index']);
        Route::get('/{restaurant}', [RestaurantController::class, 'show']);
        Route::get('/{restaurant}/menu', [RestaurantController::class, 'menu']);
        Route::put('/profile', [RestaurantController::class, 'updateProfile']);
        Route::post('/categories', [RestaurantController::class, 'createCategory']);
        Route::put('/categories/{category}', [RestaurantController::class, 'updateCategory']);
        Route::delete('/categories/{category}', [RestaurantController::class, 'deleteCategory']);
        Route::post('/menu-items', [RestaurantController::class, 'createMenuItem']);
        Route::put('/menu-items/{menuItem}', [RestaurantController::class, 'updateMenuItem']);
        Route::delete('/menu-items/{menuItem}', [RestaurantController::class, 'deleteMenuItem']);
    });

    Route::prefix('riders')->group(function () {
        Route::get('/', [RiderController::class, 'index']);
        Route::get('/{rider}', [RiderController::class, 'show']);
        Route::get('/available/list', [RiderController::class, 'availableRiders']);
        Route::post('/location', [RiderController::class, 'updateLocation'])->middleware('throttle:gps');
        Route::get('/{rider}/location', [RiderController::class, 'getLocation']);
        Route::post('/status', [RiderController::class, 'updateStatus']);
    });

    Route::prefix('payments')->group(function () {
        Route::get('/{order}', [PaymentController::class, 'show']);
        Route::post('/create-intent', [PaymentController::class, 'createPaymentIntent']);
        Route::post('/refund', [PaymentController::class, 'refund']);
    });
});
