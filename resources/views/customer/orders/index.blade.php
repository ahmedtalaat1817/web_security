@extends('layouts.app')
@section('title', 'My Orders')

@section('styles')
<style>
    .orders-header {
        background: linear-gradient(135deg, var(--dark-charcoal), var(--dark-secondary));
        padding: 40px 0;
        margin-bottom: 32px;
    }

    .orders-title {
        font-family: 'Playfair Display', serif;
        font-size: 36px;
        font-weight: 700;
        color: white;
    }

    .order-tabs {
        background: white;
        border-radius: var(--radius-lg);
        padding: 8px;
        box-shadow: var(--shadow-sm);
        margin-bottom: 32px;
    }

    .order-tab {
        padding: 12px 24px;
        border-radius: var(--radius-md);
        font-weight: 600;
        color: var(--muted-gray);
        text-decoration: none;
        transition: var(--transition-fast);
    }

    .order-tab:hover {
        color: var(--primary-orange);
    }

    .order-tab.active {
        background: var(--primary-orange);
        color: white;
    }

    .order-card {
        background: white;
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        transition: var(--transition-normal);
        margin-bottom: 20px;
    }

    .order-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-4px);
    }

    .order-header {
        padding: 20px 24px;
        background: var(--off-white);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .order-number {
        font-weight: 700;
        color: var(--dark-charcoal);
        font-size: 16px;
    }

    .order-date {
        color: var(--muted-gray);
        font-size: 14px;
    }

    .order-body {
        padding: 24px;
    }

    .order-restaurant {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 20px;
    }

    .restaurant-thumb {
        width: 60px;
        height: 60px;
        border-radius: var(--radius-md);
        object-fit: cover;
    }

    .restaurant-info h4 {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .order-meta {
        display: flex;
        gap: 24px;
        color: var(--muted-gray);
        font-size: 14px;
    }

    .order-meta i {
        color: var(--primary-orange);
    }

    .order-total {
        font-size: 20px;
        font-weight: 700;
        color: var(--primary-orange);
    }

    .order-footer {
        padding: 16px 24px;
        border-top: 1px solid var(--light-gray);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .status-badge {
        padding: 8px 16px;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-placed {
        background: rgba(243, 156, 18, 0.15);
        color: var(--warning-yellow);
    }

    .status-confirmed {
        background: rgba(52, 152, 219, 0.15);
        color: var(--info);
    }

    .status-preparing {
        background: rgba(255, 107, 53, 0.15);
        color: var(--primary-orange);
    }

    .status-on_the_way {
        background: rgba(155, 89, 182, 0.15);
        color: var(--secondary);
    }

    .status-delivered {
        background: rgba(46, 204, 113, 0.15);
        color: var(--success-green);
    }

    .status-cancelled {
        background: rgba(231, 76, 60, 0.15);
        color: var(--danger-red);
    }

    .empty-state {
        text-align: center;
        padding: 80px 20px;
    }

    .empty-icon {
        font-size: 80px;
        color: var(--light-gray);
    }
</style>
@endsection

@section('content')
<!-- Orders Header -->
<section class="orders-header">
    <div class="container-fluid-custom">
        <h1 class="orders-title">My Orders</h1>
    </div>
</section>

<!-- Orders Content -->
<div class="container-fluid-custom">
    <!-- Order Tabs -->
    <div class="order-tabs d-flex gap-2">
        <a class="order-tab {{ !request('status') ? 'active' : '' }}" href="{{ route('customer.orders.index') }}">
            All Orders
        </a>
        <a class="order-tab {{ request('status') === 'confirmed' || request('status') === 'preparing' || request('status') === 'on_the_way' || request('status') === 'delivered' ? 'active' : '' }}" href="{{ route('customer.orders.index', ['status' => 'active']) }}">
            Active
        </a>
        <a class="order-tab {{ request('status') === 'delivered' ? 'active' : '' }}" href="{{ route('customer.orders.index', ['status' => 'delivered']) }}">
            Completed
        </a>
    </div>

    <!-- Orders List -->
    <div class="row">
        @forelse($orders as $order)
        <div class="col-lg-6">
            <div class="order-card">
                <div class="order-header">
                    <div>
                        <span class="order-number">{{ $order->order_number }}</span>
                        <span class="status-badge status-{{ $order->status }}">{{ ucwords(str_replace('_', ' ', $order->status)) }}</span>
                    </div>
                    <span class="order-date">{{ $order->created_at->format('M d, Y • h:i A') }}</span>
                </div>

                <div class="order-body">
                    <div class="order-restaurant">
                        <img src="{{ $order->restaurant->cover_image ?? 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=100&q=80' }}" alt="{{ $order->restaurant->name }}" class="restaurant-thumb">
                        <div class="restaurant-info">
                            <h4>{{ $order->restaurant->name }}</h4>
                            <div class="order-meta">
                                <span><i class="bi bi-bag"></i> {{ $order->items->count() }} items</span>
                                <span><i class="bi bi-clock"></i> {{ $order->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="order-total">${{ number_format($order->total, 2) }}</span>
                        <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-primary-custom">
                            Track Order <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="empty-state">
                <i class="bi bi-bag-x empty-icon"></i>
                <h3 class="mt-4 text-muted">No orders yet</h3>
                <p class="text-muted mb-4">Start exploring restaurants to place your first order</p>
                <a href="{{ route('restaurants.index') }}" class="btn btn-primary-custom">Browse Restaurants</a>
            </div>
        </div>
        @endforelse
    </div>

    @if($orders->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $orders->links() }}
    </div>
    @endif
</div>
@endsection