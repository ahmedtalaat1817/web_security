<?php

namespace App\Http\Controllers;

use App\Models\MenuVariant;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreMenuVariantRequest;

class MenuVariantController extends Controller
{
    /**
     * Display variants.
     */
    public function index(): View|RedirectResponse
    {
        $restaurant = Auth::user()->restaurant;

        if (!$restaurant) {
            return redirect()->route('home');
        }

        $variants = MenuVariant::with('menuItem')
            ->whereHas('menuItem', function ($query) use ($restaurant) {
                $query->where('restaurant_id', $restaurant->id);
            })
            ->latest()
            ->get();

        return view('variants.index', compact('variants'));
    }

    /**
     * Show create form.
     */
    public function create(): View|RedirectResponse
    {
        $restaurant = Auth::user()->restaurant;

        if (!$restaurant) {
            return redirect()->route('home');
        }

        $menuItems = $restaurant->menuItems()->get();

        return view('variants.create', compact('menuItems'));
    }

    /**
     * Store variant.
     */
    public function store(
        StoreMenuVariantRequest $request
    ): RedirectResponse {

        MenuVariant::create([
            'menu_item_id' => $request->menu_item_id,
            'name' => $request->name,
            'price_adjustment' => $request->price_adjustment,
            'is_available' => $request->has('is_available'),
        ]);

        return redirect()
            ->route('variants.index')
            ->with('success', 'Variant created successfully.');
    }
}