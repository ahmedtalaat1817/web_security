@extends('layouts.app')

@section('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, var(--dark-charcoal), var(--dark-secondary));
        padding: 60px 0;
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1920&q=80') center/cover;
        opacity: 0.25;
    }

    .page-title {
        font-family: 'Playfair Display', serif;
        font-size: 42px;
        font-weight: 700;
        color: white;
        position: relative;
    }

    .search-bar {
        background: white;
        border-radius: var(--radius-lg);
        padding: 8px;
        display: flex;
        gap: 8px;
        box-shadow: var(--shadow-lg);
        max-width: 600px;
        margin-top: 24px;
        position: relative;
        z-index: 2;
    }

    .search-bar input {
        border: none;
        padding: 14px 18px;
        font-size: 16px;
        flex: 1;
        outline: none;
    }

    .search-bar input::placeholder {
        color: var(--muted-gray);
    }

    .search-bar button {
        padding: 14px 28px;
        white-space: nowrap;
    }

    .filter-section {
        background: var(--bg-secondary);
        border-radius: var(--radius-lg);
        padding: 20px 24px;
        box-shadow: var(--shadow-sm);
        margin-top: -30px;
        position: relative;
        z-index: 10;
    }

    .filter-group label {
        font-weight: 600;
        color: var(--dark-charcoal);
        margin-bottom: 8px;
        display: block;
        font-size: 13px;
    }

    .filter-select, .filter-input {
        border: 2px solid var(--light-gray);
        border-radius: var(--radius-md);
        padding: 10px 14px;
        font-size: 14px;
        transition: var(--transition-fast);
        width: 100%;
    }

    .filter-select:focus, .filter-input:focus {
        border-color: var(--primary-orange);
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
    }

    .restaurant-card-large {
        background: var(--bg-secondary);
        border-radius: var(--radius-lg);
        overflow: hidden;
        transition: var(--transition-normal);
        cursor: pointer;
        height: 100%;
    }

    .restaurant-card-large:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-lg);
    }

    .restaurant-image-lg {
        height: 220px;
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

    .restaurant-content {
        padding: 24px;
    }

    .restaurant-title {
        font-size: 20px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 12px;
    }

    .restaurant-description {
        color: var(--muted-gray);
        font-size: 14px;
        margin-bottom: 16px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .restaurant-meta-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 16px;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--text-tertiary);
    }

    .meta-item i {
        color: var(--primary);
    }

    .empty-state {
        text-align: center;
        padding: 80px 20px;
    }

    .empty-icon {
        font-size: 80px;
        color: var(--light-gray);
        margin-bottom: 20px;
    }

    .active-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 20px;
    }

    .active-filter-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--bg-tertiary);
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 13px;
        color: var(--text-primary);
    }

    .active-filter-tag a {
        color: var(--muted-gray);
        text-decoration: none;
    }

    .active-filter-tag a:hover {
        color: var(--danger-red);
    }

    .results-count {
        color: var(--muted-gray);
        font-size: 14px;
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        .page-title {
            font-size: 32px;
        }

        .filter-section {
            margin-top: 20px;
        }

        .restaurant-meta-grid {
            grid-template-columns: 1fr;
        }

        .search-bar {
            flex-direction: column;
        }

        .search-bar button {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')
<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="page-title">Restaurants</h1>
                <p class="mt-2" style="color: rgba(255,255,255,0.75);">Discover the best food delivery near you</p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <form method="GET" action="{{ route('restaurants.index') }}" class="search-bar">
                    <input type="text" name="search" placeholder="Search restaurants or cuisines..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="bi bi-search me-1"></i> Search
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Filter Section -->
<section class="filter-section">
    <form method="GET" action="{{ route('restaurants.index') }}" id="filterForm">
        @if(request('search'))
        <input type="hidden" name="search" value="{{ request('search') }}">
        @endif

        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <div class="filter-group">
                    <label>Rating</label>
                    <select name="rating" class="form-select filter-select" onchange="this.form.submit()">
                        <option value="">Any Rating</option>
                        <option value="4.5" {{ request('rating') == '4.5' ? 'selected' : '' }}>4.5+ Stars</option>
                        <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4+ Stars</option>
                        <option value="3.5" {{ request('rating') == '3.5' ? 'selected' : '' }}>3.5+ Stars</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="filter-group">
                    <label>Delivery Time</label>
                    <select name="delivery_time" class="form-select filter-select" onchange="this.form.submit()">
                        <option value="">Any Time</option>
                        <option value="20" {{ request('delivery_time') == '20' ? 'selected' : '' }}>Under 20 min</option>
                        <option value="30" {{ request('delivery_time') == '30' ? 'selected' : '' }}>Under 30 min</option>
                        <option value="45" {{ request('delivery_time') == '45' ? 'selected' : '' }}>Under 45 min</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="filter-group">
                    <label>Sort By</label>
                    <select name="sort_by" class="form-select filter-select" onchange="this.form.submit()">
                        <option value="rating" {{ request('sort_by') == 'rating' || !request('sort_by') ? 'selected' : '' }}>Top Rated</option>
                        <option value="time" {{ request('sort_by') == 'time' ? 'selected' : '' }}>Fastest Delivery</option>
                        <option value="delivery" {{ request('sort_by') == 'delivery' ? 'selected' : '' }}>Lowest Delivery Fee</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <a href="{{ route('restaurants.index') }}" class="btn btn-outline-custom w-100 {{ request()->anyFilled(['search', 'rating', 'delivery_time', 'sort_by']) ? '' : 'invisible' }}">
                    <i class="bi bi-x-circle me-1"></i> Clear Filters
                </a>
            </div>
        </div>
    </form>
</section>

<!-- Restaurant List -->
<section class="section-padding">
    <div class="container-fluid-custom">
        <div class="results-count">
            @if($restaurants->total() > 0)
                {{ $restaurants->total() }} restaurant{{ $restaurants->total() != 1 ? 's' : '' }} found
            @endif
        </div>

        <div class="row g-4">
            @forelse($restaurants as $restaurant)
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('restaurants.show', $restaurant) }}" class="text-decoration-none">
                    <div class="restaurant-card-large">
                        <div class="restaurant-image-lg" style="background-image: url('{{ $restaurant->cover_image ?? 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=600&q=80' }}')">
                            <span class="restaurant-badge {{ $restaurant->is_open ? 'badge-open' : 'badge-closed' }}">
                                {{ $restaurant->is_open ? 'Open' : 'Closed' }}
                            </span>
                        </div>
                        <div class="restaurant-content">
                            <h3 class="restaurant-title">{{ $restaurant->name }}</h3>
                            <p class="restaurant-description">{{ $restaurant->description ?? 'Delicious food served fresh daily. Order now and enjoy!' }}</p>

                            <div class="restaurant-meta-grid">
                                <div class="meta-item">
                                    <i class="bi bi-star-fill"></i>
                                    <span>{{ number_format($restaurant->rating ?? 4.5, 1) }}</span>
                                </div>
                                <div class="meta-item">
                                    <i class="bi bi-clock"></i>
                                    <span>{{ $restaurant->delivery_time_minutes ?? 30 }} min</span>
                                </div>
                                <div class="meta-item">
                                    <i class="bi bi-currency-dollar"></i>
                                    <span>{{ number_format($restaurant->delivery_fee ?? 2.99, 2) }} delivery</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @empty
            <div class="col-12">
                <div class="empty-state">
                    <i class="bi bi-shop empty-icon"></i>
                    <h3>No Restaurants Found</h3>
                    <p class="text-muted">Try adjusting your filters or check back later.</p>
                    <a href="{{ route('restaurants.index') }}" class="btn btn-primary-custom">Clear Filters</a>
                </div>
            </div>
            @endforelse
        </div>

        @if($restaurants->hasPages())
        <div class="d-flex justify-content-center mt-5">
            {{ $restaurants->links() }}
        </div>
        @endif
    </div>
</section>
@endsection