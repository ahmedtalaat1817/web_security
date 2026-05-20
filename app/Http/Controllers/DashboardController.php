<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    /**
     * Display restaurant dashboard.
     */
    public function index(): View|RedirectResponse
    {
        $restaurant = Auth::user()->restaurant;

        if (!$restaurant) {
            return redirect()->route('home');
        }

        return view('dashboard', [
            'restaurant' => $restaurant,
            'categoriesCount' => $restaurant->categories()->count(),
            'menuItemsCount' => $restaurant->menuItems()->count(),
            'reviewsCount' => $restaurant->reviews()->count(),
        ]);
    }
}