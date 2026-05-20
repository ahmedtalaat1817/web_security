@extends('layouts.app')
@section('title', 'Order ' . $order->order_number)

@section('styles')
<style>
    .review-star {
        cursor: pointer;
        font-size: 32px;
        color: var(--text-tertiary);
        transition: color 0.15s ease;
    }
    .star-rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: center;
        gap: 4px;
        margin-bottom: 16px;
    }
    .star-rating input { display: none; }
    .star-rating label {
        cursor: pointer;
        font-size: 32px;
        color: var(--text-tertiary);
        transition: color 0.15s ease;
    }
    .star-rating label:hover,
    .star-rating label:hover ~ label,
    .star-rating input:checked ~ label {
        color: var(--warning);
    }
    .rating-card {
        background: white;
        border-radius: 16px;
        border: 2px solid var(--light-gray);
        padding: 24px;
    }
    .rating-card.submitted {
        border-color: var(--success-green);
        background: rgba(46,204,113,0.03);
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 rounded-3" style="background:rgba(46,204,113,0.1);">
        <i class="bi bi-check-circle-fill text-success me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3" style="background:rgba(231,76,60,0.1);">
        <i class="bi bi-exclamation-circle-fill text-danger me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Order {{ $order->order_number }}</h2>
            <span class="status-badge status-{{ $order->status }}">{{ ucwords(str_replace('_', ' ', $order->status)) }}</span>
        </div>
        <a href="{{ route('customer.orders.index') }}" class="btn btn-outline-dark">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card-custom p-4 mb-4">
                <h5 class="fw-bold mb-4"><i class="bi bi-route me-2 text-orange"></i>Order Progress</h5>
                @php
                    $timeline = [
                        'placed'    => ['Order Placed', $order->created_at, ['placed','confirmed','preparing','on_the_way','delivered']],
                        'confirmed' => ['Confirmed', $order->confirmed_at, ['confirmed','preparing','on_the_way','delivered']],
                        'preparing' => ['Preparing', $order->preparing_at, ['preparing','on_the_way','delivered']],
                        'on_the_way'=> ['On The Way', $order->picked_up_at, ['on_the_way','delivered']],
                        'delivered' => ['Delivered', $order->delivered_at, ['delivered']],
                    ];
                @endphp
                @foreach($timeline as $key => $step)
                <div class="d-flex align-items-start mb-3">
                    <div class="me-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;{{ in_array($order->status, $step[2]) ? 'background:var(--primary-orange);color:white;' : 'background:var(--light-gray);color:var(--muted-gray);' }}">
                            <i class="bi {{ $loop->last ? 'bi-house' : 'bi-check-lg' }}"></i>
                        </div>
                    </div>
                    <div>
                        <strong>{{ $step[0] }}</strong>
                        <div class="text-muted small">{{ $step[1] ? $step[1]->format('M d, h:i A') : 'Pending' }}</div>
                    </div>
                </div>
                @endforeach
            </div>

            @if($order->rider && in_array($order->status, ['on_the_way', 'delivered']))
            <div class="card-custom p-4 mb-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-motorcycle me-2 text-orange"></i>Delivery Partner</h5>
                <div class="d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width:64px;height:64px;background:var(--off-white);">
                        <i class="bi bi-person-fill" style="font-size:28px;color:var(--muted-gray);"></i>
                    </div>
                    <div>
                        <h5 class="mb-1">{{ $order->rider->user->name }}</h5>
                        <p class="text-muted mb-0">
                            <i class="bi bi-star-fill text-warning"></i> {{ number_format($order->rider->rating, 1) }} &bull;
                            {{ $order->rider->total_deliveries }} deliveries
                        </p>
                    </div>
                    <div class="ms-auto">
                        <a href="tel:{{ $order->rider->phone }}" class="btn btn-primary-custom"><i class="bi bi-telephone"></i></a>
                    </div>
                </div>
            </div>
            @endif

            <div class="card-custom p-4 mb-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-geo-alt me-2 text-orange"></i>Delivery Address</h5>
                <p class="mb-0">{{ $order->delivery_address }}</p>
                @if($order->delivery_instructions)
                <p class="text-muted small mb-0 mt-2"><i class="bi bi-info-circle"></i> {{ $order->delivery_instructions }}</p>
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-custom p-4 mb-4">
                <h5 class="fw-bold mb-4"><i class="bi bi-receipt me-2 text-orange"></i>Order Details</h5>
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-shop text-orange me-2"></i>
                    <div>
                        <strong>{{ $order->restaurant->name }}</strong>
                        <div class="small text-muted">{{ $order->restaurant->address }}</div>
                    </div>
                </div>
                <hr>
                <h6 class="fw-bold mb-3">Items</h6>
                @foreach($order->items as $item)
                <div class="d-flex justify-content-between mb-2">
                    <div>
                        <span class="fw-semibold">{{ $item->quantity }}x</span> {{ $item->item_name }}
                        @if($item->variant_name)
                        <small class="text-muted">({{ $item->variant_name }})</small>
                        @endif
                    </div>
                    <span>${{ number_format($item->subtotal, 2) }}</span>
                </div>
                @endforeach
                <hr>
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">Subtotal</span><span>${{ number_format($order->subtotal, 2) }}</span></div>
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">Delivery Fee</span><span>${{ number_format($order->delivery_fee, 2) }}</span></div>
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">Platform Fee</span><span>${{ number_format($order->platform_fee, 2) }}</span></div>
                @if($order->surge_amount > 0)<div class="d-flex justify-content-between mb-2"><span class="text-muted">Surge</span><span>${{ number_format($order->surge_amount, 2) }}</span></div>@endif
                @if($order->discount > 0)<div class="d-flex justify-content-between mb-2 text-success"><span>Discount</span><span>-${{ number_format($order->discount, 2) }}</span></div>@endif
                <hr>
                <div class="d-flex justify-content-between fw-bold fs-5"><span>Total</span><span>${{ number_format($order->total, 2) }}</span></div>
                @if($order->relationLoaded('payment') && $order->payment)
                <div class="mt-2 pt-2 border-top d-flex justify-content-between">
                    <span class="text-muted">Payment</span>
                    <span class="{{ $order->payment->isSuccessful() ? 'text-success' : ($order->payment->isFailed() ? 'text-danger' : 'text-warning') }}">
                        <i class="bi {{ $order->payment->isSuccessful() ? 'bi-check-circle' : ($order->payment->isFailed() ? 'bi-x-circle' : 'bi-clock') }} me-1"></i>
                        {{ $order->payment->isSuccessful() ? 'Paid' : ($order->payment->isFailed() ? 'Failed' : 'Pending') }}
                    </span>
                </div>
                @endif
            </div>

            @if(!in_array($order->status, ['on_the_way', 'delivered', 'cancelled']))
            <div class="card-custom p-4 mb-4 border-danger">
                <div class="text-center">
                    <h6 class="fw-bold text-danger mb-2"><i class="bi bi-exclamation-circle me-2"></i>Cancel Order</h6>
                    <p class="text-muted small mb-3">You can cancel this order before the delivery partner picks it up.</p>
                    <form method="POST" action="{{ route('customer.orders.cancel', $order->id) }}" onsubmit="return confirm('Are you sure you want to cancel this order?')">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-x-circle me-2"></i>Cancel Order
                        </button>
                    </form>
                </div>
            </div>
            @endif

            @if($order->status === 'delivered')
            <div class="rating-card {{ $order->review ? 'submitted' : '' }}">
                @if($order->review)
                <div class="text-center">
                    <h6 class="fw-bold mb-2">Your Review</h6>
                    <div class="mb-2">
                        @for($i = 1; $i <= 5; $i++)
                        <i class="bi {{ $i <= $order->review->rating ? 'bi-star-fill' : 'bi-star' }} text-warning fs-4"></i>
                        @endfor
                    </div>
                    @if($order->review->comment)
                    <p class="text-muted small mb-0">{{ $order->review->comment }}</p>
                    @endif
                </div>
                @else
                <form method="POST" action="{{ route('customer.orders.review', $order) }}">
                    @csrf
                    <h6 class="fw-bold mb-3 text-center">Rate your experience</h6>
                    <div class="star-rating">
                        @for($i = 5; $i >= 1; $i--)
                        <input type="radio" name="rating" value="{{ $i }}" id="star{{ $i }}" {{ old('rating')==$i ? 'checked' : '' }} required>
                        <label for="star{{ $i }}" title="{{ $i }} star{{ $i>1 ? 's' : '' }}"><i class="bi bi-star-fill"></i></label>
                        @endfor
                    </div>
                    <textarea name="comment" class="form-control mb-3" rows="2" placeholder="Write a comment (optional)..." style="border-radius:12px;border:2px solid var(--light-gray);">{{ old('comment') }}</textarea>
                    <button type="submit" class="btn btn-primary-custom w-100"><i class="bi bi-star me-2"></i>Submit Review</button>
                </form>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
if (typeof Pusher !== 'undefined') {
    try {
        var ch = pusher.subscribe('order.{{ $order->id }}');
        ch.bind('App\\Events\\OrderStatusUpdated', function(){ location.reload(); });
        ch.bind('App\\Events\\RiderAssigned', function(){ location.reload(); });
    } catch(e) {}
}
</script>
@endsection