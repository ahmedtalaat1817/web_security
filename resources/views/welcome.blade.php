@extends('layouts.app')

@push('styles')
<style>
    .hero-section {
        background: linear-gradient(135deg, rgba(26, 26, 46, 0.95), rgba(22, 33, 62, 0.9)),
                    url('https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=1920&q=80');
        background-size: cover;
        background-position: center;
        padding: 120px 0 100px;
        position: relative;
        overflow: hidden;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255, 107, 53, 0.1), transparent);
    }

    .hero-content {
        position: relative;
        z-index: 2;
    }

    .hero-title {
        font-family: 'Playfair Display', serif;
        font-size: 56px;
        font-weight: 700;
        color: white;
        line-height: 1.2;
        margin-bottom: 20px;
    }

    .hero-title span {
        color: var(--primary-orange);
    }

    .hero-subtitle {
        font-size: 20px;
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: 40px;
        max-width: 500px;
    }

    .search-box {
        background: white;
        border-radius: var(--radius-lg);
        padding: 8px;
        display: flex;
        gap: 8px;
        box-shadow: var(--shadow-lg);
        max-width: 600px;
    }

    .search-box input {
        border: none;
        padding: 16px 20px;
        font-size: 16px;
        flex: 1;
        outline: none;
    }

    .search-box input::placeholder {
        color: var(--text-muted);
    }

    .search-box button {
        padding: 16px 32px;
        white-space: nowrap;
    }

    .floating-food {
        position: absolute;
        animation: float 6s ease-in-out infinite;
    }

    .floating-food:nth-child(1) { top: 20%; left: 5%; animation-delay: 0s; }
    .floating-food:nth-child(2) { top: 60%; left: 8%; animation-delay: 1s; }
    .floating-food:nth-child(3) { top: 30%; right: 10%; animation-delay: 2s; }
    .floating-food:nth-child(4) { top: 70%; right: 5%; animation-delay: 3s; }

    @keyframes float {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(5deg); }
    }

    .category-card {
        background: var(--bg-card);
        border-radius: var(--radius-lg);
        padding: 30px 20px;
        text-align: center;
        transition: var(--transition-normal);
        border: 2px solid transparent;
        cursor: pointer;
    }

    .category-card:hover {
        transform: translateY(-8px);
        border-color: var(--primary-orange);
        box-shadow: var(--shadow-lg);
    }

    .category-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, rgba(255, 107, 53, 0.1), rgba(255, 107, 53, 0.05));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        font-size: 32px;
        color: var(--primary-orange);
    }

    .category-name {
        font-weight: 600;
        font-size: 16px;
        color: var(--text-primary);
    }

    .restaurant-card {
        background: var(--bg-card);
        border-radius: var(--radius-lg);
        overflow: hidden;
        transition: var(--transition-normal);
        cursor: pointer;
    }

    .restaurant-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-lg);
    }

    .restaurant-image {
        height: 200px;
        background-size: cover;
        background-position: center;
        position: relative;
    }

    .restaurant-badge {
        position: absolute;
        top: 16px;
        right: 16px;
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .badge-open {
        background: var(--success-light);
        color: var(--success);
    }

    .badge-closed {
        background: var(--danger-light);
        color: var(--danger);
    }

    .restaurant-info {
        padding: 20px;
    }

    .restaurant-name {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 8px;
        color: var(--text-primary);
    }

    .restaurant-meta {
        display: flex;
        align-items: center;
        gap: 16px;
        color: var(--text-muted);
        font-size: 14px;
    }

    .restaurant-meta i {
        color: var(--primary-orange);
    }

    .restaurant-tags {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 12px;
    }

    .tag {
        background: var(--bg-off);
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 12px;
        color: var(--text-muted);
    }

    .stats-section {
        background: linear-gradient(135deg, var(--dark-charcoal), var(--dark-secondary));
        padding: 80px 0;
        position: relative;
        overflow: hidden;
    }

    .stats-section::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(255, 107, 53, 0.3), transparent 70%);
    }

    .stat-item {
        text-align: center;
    }

    .stat-number {
        font-family: 'Playfair Display', serif;
        font-size: 56px;
        font-weight: 700;
        color: var(--primary-orange);
        line-height: 1;
    }

    .stat-label {
        color: rgba(255, 255, 255, 0.8);
        font-size: 16px;
        margin-top: 8px;
    }

    .cta-section {
        background: linear-gradient(135deg, var(--primary-orange), var(--primary-orange-dark));
        padding: 80px 0;
        text-align: center;
    }

    .cta-title {
        font-family: 'Playfair Display', serif;
        font-size: 40px;
        font-weight: 700;
        color: white;
        margin-bottom: 16px;
    }

    .cta-subtitle {
        color: rgba(255, 255, 255, 0.9);
        font-size: 18px;
        margin-bottom: 32px;
    }

    @media (max-width: 991px) {
        .hero-title {
            font-size: 40px;
        }

        .search-box {
            flex-direction: column;
        }

        .search-box button {
            width: 100%;
        }
    }

    @media (max-width: 576px) {
        .hero-title {
            font-size: 32px;
        }

        .hero-subtitle {
            font-size: 16px;
        }

        .stat-number {
            font-size: 40px;
        }
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="floating-food"><i class="bi bi-cup-hot" style="font-size: 40px; color: rgba(255,255,255,0.3);"></i></div>
    <div class="floating-food"><i class="bi bi-burger" style="font-size: 35px; color: rgba(255,255,255,0.3);"></i></div>
    <div class="floating-food"><i class="bi bi-cup-straw" style="font-size: 38px; color: rgba(255,255,255,0.3);"></i></div>
    <div class="floating-food"><i class="bi bi-pizza" style="font-size: 36px; color: rgba(255,255,255,0.3);"></i></div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center hero-content">
                <h1 class="hero-title fade-in">Delicious Food <span>Delivered</span> Fast</h1>
                <p class="hero-subtitle fade-in fade-in-delay-1">Order from your favorite restaurants and get it delivered to your doorstep in minutes.</p>

                <form action="{{ route('restaurants.index') }}" method="GET" class="search-box fade-in fade-in-delay-2 mx-auto">
                    <input type="text" name="search" placeholder="Search for restaurants or cuisines...">
                    <button type="submit" class="btn btn-primary-custom">Find Food</button>
                </form>

                <div class="d-flex justify-content-center gap-4 mt-5 fade-in fade-in-delay-3">
                    <div class="d-flex align-items-center gap-2 text-white-50">
                        <i class="bi bi-check-circle-fill text-success"></i>
                        <span>Free Delivery</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 text-white-50">
                        <i class="bi bi-check-circle-fill text-success"></i>
                        <span>Best Restaurants</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 text-white-50">
                        <i class="bi bi-check-circle-fill text-success"></i>
                        <span>24/7 Support</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="section-padding">
    <div class="container-fluid-custom">
        <div class="text-center mb-5">
            <h2 class="section-title">Popular Categories</h2>
            <p class="section-subtitle">Explore your favorite cuisines</p>
        </div>

        <div class="row g-4">
            <div class="col-6 col-md-4 col-lg-2 stagger-item" style="animation-delay: 0.1s;">
                <div class="category-card">
                    <div class="category-icon"><i class="bi bi-burger"></i></div>
                    <div class="category-name">Burgers</div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 stagger-item" style="animation-delay: 0.2s;">
                <div class="category-card">
                    <div class="category-icon"><i class="bi bi-cup-hot"></i></div>
                    <div class="category-name">Pizza</div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 stagger-item" style="animation-delay: 0.3s;">
                <div class="category-card">
                    <div class="category-icon"><i class="bi bi-egg-fried"></i></div>
                    <div class="category-name">Asian</div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 stagger-item" style="animation-delay: 0.4s;">
                <div class="category-card">
                    <div class="category-icon"><i class="bi bi-cone"></i></div>
                    <div class="category-name">Desserts</div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 stagger-item" style="animation-delay: 0.5s;">
                <div class="category-card">
                    <div class="category-icon"><i class="bi bi-cup-straw"></i></div>
                    <div class="category-name">Drinks</div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 stagger-item" style="animation-delay: 0.6s;">
                <div class="category-card">
                    <div class="category-icon"><i class="bi bi-grid"></i></div>
                    <div class="category-name">View All</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">Restaurants</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <div class="stat-number">50K+</div>
                    <div class="stat-label">Orders Delivered</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <div class="stat-number">1000+</div>
                    <div class="stat-label">Delivery Partners</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <div class="stat-number">4.8</div>
                    <div class="stat-label">Average Rating</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Restaurants -->
<section class="section-padding" style="background:var(--bg-off);">
    <div class="container-fluid-custom">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="section-title mb-0">Featured Restaurants</h2>
                <p class="section-subtitle mb-0">Discover top-rated restaurants near you</p>
            </div>
            <a href="{{ route('restaurants.index') }}" class="btn btn-outline-custom">
                View All <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>

        <div class="row g-4">
            @forelse($restaurants as $restaurant)
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('restaurants.show', $restaurant) }}" class="text-decoration-none">
                    <div class="restaurant-card">
                        <div class="restaurant-image" style="background-image: url('{{ $restaurant->cover_image ?? 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=600&q=80' }}')">
                            <span class="restaurant-badge {{ $restaurant->is_open ? 'badge-open' : 'badge-closed' }}">
                                {{ $restaurant->is_open ? 'Open' : 'Closed' }}
                            </span>
                        </div>
                        <div class="restaurant-info">
                            <h3 class="restaurant-name">{{ $restaurant->name }}</h3>
                            <div class="restaurant-meta">
                                <span><i class="bi bi-star-fill"></i> {{ number_format($restaurant->rating ?? 4.5, 1) }}</span>
                                <span><i class="bi bi-clock"></i> {{ $restaurant->delivery_time_minutes ?? 30 }} min</span>
                                <span><i class="bi bi-currency-dollar"></i> {{ number_format($restaurant->delivery_fee ?? 2.99, 2) }}</span>
                            </div>
                            <div class="restaurant-tags">
                                <span class="tag">Fast Food</span>
                                <span class="tag">Burgers</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-shop display-1 text-muted"></i>
                <h4 class="mt-3 text-muted">No restaurants available yet</h4>
                <p class="text-muted">Check back soon for new restaurants!</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="cta-title">Become a Restaurant Partner</h2>
                <p class="cta-subtitle">Join our growing network of restaurants and reach more customers.</p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="{{ route('partner.pricing') }}" class="btn btn-light btn-lg px-4">Learn More</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection