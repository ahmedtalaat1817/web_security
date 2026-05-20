<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    /**
     * Display reviews dashboard.
     */
    public function index(): View|RedirectResponse
    {
        $restaurant = Auth::user()->restaurant;

        if (!$restaurant) {
            return redirect()->route('home');
        }

        $reviews = $restaurant->reviews()
            ->with('user')
            ->latest()
            ->get();

        return view('reviews.index', compact('reviews'));
    }
}