<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Restaurant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestaurantController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Restaurant::where('status', 'active');

        if ($request->has('latitude') && $request->has('longitude')) {
            $lat = $request->latitude;
            $lng = $request->longitude;
            $query->selectRaw(
                '*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) * 1000 AS distance',
                [$lat, $lng, $lat]
            );
        }

        if ($request->has('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $restaurants = $query->with('categories')->paginate(20);

        return response()->json($restaurants);
    }

    public function show(Restaurant $restaurant): JsonResponse
    {
        $restaurant->load(['categories.menuItems.variants', 'reviews']);

        return response()->json(['restaurant' => $restaurant]);
    }

    public function menu(Restaurant $restaurant): JsonResponse
    {
        $categories = $restaurant->categories()
            ->with(['menuItems.variants' => function ($query) {
                $query->where('is_available', true);
            }])
            ->where('is_active', true)
            ->get();

        return response()->json(['categories' => $categories]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $restaurant = Auth::user()->restaurant;

        if (!$restaurant) {
            return response()->json(['message' => 'Restaurant profile not found'], 403);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'address' => 'sometimes|string',
            'phone' => 'sometimes|string',
            'email' => 'sometimes|email',
            'delivery_fee' => 'sometimes|numeric|min:0',
            'delivery_time_minutes' => 'sometimes|integer|min:1',
            'minimum_order' => 'sometimes|numeric|min:0',
        ]);

        $restaurant->update($request->only([
            'name', 'description', 'address', 'phone', 'email',
            'delivery_fee', 'delivery_time_minutes', 'minimum_order'
        ]));

        return response()->json(['message' => 'Profile updated', 'restaurant' => $restaurant]);
    }

    public function createCategory(Request $request): JsonResponse
    {
        $restaurant = Auth::user()->restaurant;

        if (!$restaurant) {
            return response()->json(['message' => 'Restaurant profile not found'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        $category = $restaurant->categories()->create($request->all());

        return response()->json(['message' => 'Category created', 'category' => $category], 201);
    }

    public function updateCategory(Request $request, MenuCategory $category): JsonResponse
    {
        if ($category->restaurant_id !== Auth::user()->restaurant?->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $category->update($request->all());

        return response()->json(['message' => 'Category updated', 'category' => $category]);
    }

    public function deleteCategory(MenuCategory $category): JsonResponse
    {
        if ($category->restaurant_id !== Auth::user()->restaurant?->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted']);
    }

    public function createMenuItem(Request $request): JsonResponse
    {
        $restaurant = Auth::user()->restaurant;

        if (!$restaurant) {
            return response()->json(['message' => 'Restaurant profile not found'], 403);
        }

        $request->validate([
            'category_id' => 'required|exists:menu_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $menuItem = $restaurant->menuItems()->create($request->all());

        return response()->json(['message' => 'Menu item created', 'menu_item' => $menuItem], 201);
    }

    public function updateMenuItem(Request $request, MenuItem $menuItem): JsonResponse
    {
        if ($menuItem->restaurant_id !== Auth::user()->restaurant?->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'image' => 'nullable|string',
            'is_available' => 'sometimes|boolean',
            'is_featured' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $menuItem->update($request->all());

        return response()->json(['message' => 'Menu item updated', 'menu_item' => $menuItem]);
    }

    public function deleteMenuItem(MenuItem $menuItem): JsonResponse
    {
        if ($menuItem->restaurant_id !== Auth::user()->restaurant?->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $menuItem->delete();

        return response()->json(['message' => 'Menu item deleted']);
    }
}