@extends('layouts.app')

@section('styles')
<style>
    .hero-mini {
        background: linear-gradient(135deg, var(--dark-charcoal), var(--dark-secondary)),
                    url('https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=1920&q=80');
        background-size: cover;
        background-position: center;
        padding: 60px 0;
        position: relative;
    }

    .hero-mini::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(26, 26, 46, 0.85);
    }

    .hero-mini .container {
        position: relative;
        z-index: 2;
    }

    .hero-title-mini {
        font-family: 'Playfair Display', serif;
        font-size: 36px;
        font-weight: 700;
        color: white;
    }

    .mini-search-box {
        background: white;
        border-radius: var(--radius-lg);
        padding: 6px;
        display: flex;
        gap: 8px;
        max-width: 500px;
        margin: 24px auto 0;
    }

    .mini-search-box input {
        border: none;
        padding: 14px 18px;
        font-size: 15px;
        flex: 1;
        outline: none;
    }

    .mini-search-box button {
        padding: 14px 24px;
    }

    .restaurant-card-compact {
        background: white;
        border-radius: var(--radius-lg);
        overflow: hidden;
        transition: var(--transition-normal);
        cursor: pointer;
        height: 100%;
    }

    .restaurant-card-compact:hover {
        transform: translateY(-6px);
        box-shadow: var(--shadow-lg);
    }

    .restaurant-img-compact {
        height: 160px;
        background-size: cover;
        background-position: center;
    }

    .restaurant-info-compact {
        padding: 16px;
    }

    .restaurant-name-compact {
        font-size: 16px;
        font-weight: 700;
        color: var(--dark-charcoal);
        margin-bottom: 8px;
    }

    .restaurant-meta-compact {
        display: flex;
        align-items: center;
        gap: 16px;
        color: var(--muted-gray);
        font-size: 13px;
    }

    .restaurant-meta-compact i {
        color: var(--primary-orange);
    }

    .quick-features {
        background: var(--off-white);
        padding: 40px 0;
    }

    .feature-item {
        text-align: center;
        padding: 20px;
    }

    .feature-icon {
        width: 70px;
        height: 70px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        font-size: 28px;
        color: var(--primary-orange);
        box-shadow: var(--shadow-sm);
    }

    .feature-title {
        font-weight: 600;
        margin-bottom: 8px;
    }

    .feature-desc {
        font-size: 14px;
        color: var(--muted-gray);
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<section class="hero-mini">
    <div class="container text-center">
        <h1 class="hero-title-mini">What would you like to eat?</h1>
        <form action="{{ route('restaurants.index') }}" method="GET" class="mini-search-box">
            <input type="text" name="search" placeholder="Search for restaurants or dishes...">
            <button type="submit" class="btn btn-primary-custom">Search</button>
        </form>
    </div>
</section>

<!-- Quick Features -->
<section class="quick-features">
    <div class="container-fluid-custom">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-item">
                    <div class="feature-icon"><i class="bi bi-shop-window"></i></div>
                    <h5 class="feature-title">100+ Restaurants</h5>
                    <p class="feature-desc">Choose from a wide variety of cuisines</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-item">
                    <div class="feature-icon"><i class="bi bi-lightning-charge"></i></div>
                    <h5 class="feature-title">Fast Delivery</h5>
                    <p class="feature-desc">Get your food delivered in minutes</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-item">
                    <div class="feature-icon"><i class="bi bi-shield-check"></i></div>
                    <h5 class="feature-title">Secure Payments</h5>
                    <p class="feature-desc">Pay securely online or cash on delivery</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Popular Restaurants -->
<section class="section-padding">
    <div class="container-fluid-custom">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="section-title mb-0" style="font-size: 28px;">Popular Restaurants</h2>
                <p class="text-muted mb-0">Top rated restaurants near you</p>
            </div>
            <a href="{{ route('restaurants.index') }}" class="btn btn-outline-custom">View All</a>
        </div>

        <div class="row g-4">
            @forelse($restaurants->take(6) as $restaurant)
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('restaurants.show', $restaurant) }}" class="text-decoration-none">
                    <div class="restaurant-card-compact">
                        <div class="restaurant-img-compact" style="background-image: url('{{ $restaurant->cover_image ?? 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=400&q=80' }}')"></div>
                        <div class="restaurant-info-compact">
                            <h3 class="restaurant-name-compact">{{ $restaurant->name }}</h3>
                            <div class="restaurant-meta-compact">
                                <span><i class="bi bi-star-fill"></i> {{ number_format($restaurant->rating ?? 4.5, 1) }}</span>
                                <span><i class="bi bi-clock"></i> {{ $restaurant->delivery_time_minutes ?? 30 }} min</span>
                                <span><i class="bi bi-currency-dollar"></i> {{ number_format($restaurant->delivery_fee ?? 2.99, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-shop display-1 text-muted"></i>
                <h4 class="mt-3 text-muted">No restaurants yet</h4>
            </div>
            @endforelse
        </div>
    </div>
</section>
@endsection