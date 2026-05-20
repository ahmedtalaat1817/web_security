<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreCategoryRequest;

class CategoryController extends Controller
{
    /**
     * Display categories list.
     */
    public function index(): View|RedirectResponse
    {
        $restaurant = Auth::user()->restaurant;

        if (!$restaurant) {
            return redirect()->route('home');
        }

        $categories = $restaurant->categories()
            ->latest()
            ->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Show create form.
     */
    public function create(): View|RedirectResponse
    {
        if (!Auth::user()->restaurant) {
            return redirect()->route('home');
        }

        return view('categories.create');
    }

    /**
     * Store category.
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $restaurant = Auth::user()->restaurant;

        if (!$restaurant) {
            return redirect()->route('home');
        }

        $restaurant->categories()->create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit(Category $category): View|RedirectResponse
    {
        if (!Auth::user()->restaurant) {
            return redirect()->route('home');
        }

        return view('categories.edit', compact('category'));
    }

    /**
     * Update category.
     */
    public function update(
        StoreCategoryRequest $request,
        Category $category
    ): RedirectResponse {

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Delete category.
     */
    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}