<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreMenuItemRequest;

class MenuItemController extends Controller
{
    /**
     * Display menu items.
     */
    public function index(): View|RedirectResponse
    {
        $restaurant = Auth::user()->restaurant;

        if (!$restaurant) {
            return redirect()->route('home');
        }

        $menuItems = $restaurant->menuItems()
            ->with('category')
            ->latest()
            ->get();

        return view('menu-items.index', compact('menuItems'));
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

        $categories = $restaurant->categories()
            ->where('is_active', true)
            ->get();

        return view('menu-items.create', compact('categories'));
    }

    /**
     * Store menu item.
     */
    public function store(StoreMenuItemRequest $request): RedirectResponse
    {
        $restaurant = Auth::user()->restaurant;

        if (!$restaurant) {
            return redirect()->route('home');
        }

        $restaurant->menuItems()->create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'is_available' => $request->has('is_available'),
        ]);

        return redirect()
            ->route('menu-items.index')
            ->with('success', 'Menu item created successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit(MenuItem $menuItem): View|RedirectResponse
    {
        $restaurant = Auth::user()->restaurant;

        if (!$restaurant) {
            return redirect()->route('home');
        }

        $categories = $restaurant->categories()
            ->where('is_active', true)
            ->get();

        return view('menu-items.edit', compact(
            'menuItem',
            'categories'
        ));
    }

    /**
     * Update menu item.
     */
    public function update(
        StoreMenuItemRequest $request,
        MenuItem $menuItem
    ): RedirectResponse {

        $menuItem->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'is_available' => $request->has('is_available'),
        ]);

        return redirect()
            ->route('menu-items.index')
            ->with('success', 'Menu item updated successfully.');
    }

    /**
     * Delete menu item.
     */
    public function destroy(MenuItem $menuItem): RedirectResponse
    {
        $menuItem->delete();

        return redirect()
            ->route('menu-items.index')
            ->with('success', 'Menu item deleted successfully.');
    }
}