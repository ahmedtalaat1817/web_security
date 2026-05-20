@php $stripeConfigured = config('services.stripe.key') && config('services.stripe.secret'); @endphp
@extends('layouts.app')
@section('title', $restaurant->name)

@section('styles')
<style>
    .restaurant-hero {
        background: linear-gradient(135deg, var(--dark-charcoal), var(--dark-secondary)),
                    url('{{ $restaurant->cover_image ?? 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1920&q=80' }}');
        background-size: cover;
        background-position: center;
        padding: 80px 0;
        position: relative;
    }

    .restaurant-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to top, rgba(26, 26, 46, 0.95), rgba(26, 26, 46, 0.5));
    }

    .restaurant-hero .container {
        position: relative;
        z-index: 2;
    }

    .restaurant-logo {
        width: 100px;
        height: 100px;
        border-radius: var(--radius-lg);
        object-fit: cover;
        border: 4px solid white;
        box-shadow: var(--shadow-lg);
    }

    .restaurant-title-main {
        font-family: 'Playfair Display', serif;
        font-size: 42px;
        font-weight: 700;
        color: white;
        margin-bottom: 8px;
    }

    .restaurant-address {
        color: rgba(255, 255, 255, 0.8);
        font-size: 16px;
        margin-bottom: 16px;
    }

    .restaurant-stats {
        display: flex;
        gap: 24px;
        flex-wrap: wrap;
    }

    .stat-badge {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        padding: 10px 20px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .stat-badge i {
        color: var(--primary-orange);
        font-size: 18px;
    }

    .stat-badge span {
        color: white;
        font-weight: 600;
    }

    .favorite-btn {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: white;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: var(--muted-gray);
        cursor: pointer;
        box-shadow: var(--shadow-md);
        transition: var(--transition-normal);
    }

    .favorite-btn:hover {
        color: var(--danger-red);
        transform: scale(1.1);
    }

    .menu-category {
        margin-bottom: 48px;
    }

    .category-title {
        font-family: 'Playfair Display', serif;
        font-size: 28px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 8px;
        padding-bottom: 12px;
        border-bottom: 3px solid var(--primary);
        display: inline-block;
    }

    .category-desc {
        color: var(--muted-gray);
        margin-bottom: 24px;
    }

    .menu-item-card {
        background: white;
        border-radius: var(--radius-lg);
        padding: 20px;
        margin-bottom: 16px;
        display: flex;
        gap: 16px;
        transition: var(--transition-normal);
        cursor: pointer;
        border: 2px solid transparent;
        position: relative;
    }

    .menu-item-card:hover {
        border-color: var(--primary-orange);
        box-shadow: var(--shadow-md);
        transform: translateY(-4px);
    }

    .menu-item-card:active {
        transform: translateY(-2px);
    }

    .menu-item-image {
        width: 100px;
        height: 100px;
        border-radius: var(--radius-md);
        object-fit: cover;
        flex-shrink: 0;
    }

    .menu-item-info {
        flex: 1;
    }

    .menu-item-name {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 8px;
    }

    .menu-item-desc {
        color: var(--muted-gray);
        font-size: 14px;
        margin-bottom: 12px;
    }

    .menu-item-price {
        font-size: 18px;
        font-weight: 700;
        color: var(--primary);
    }

    .add-to-cart-btn {
        background: var(--primary-orange);
        color: white;
        border: none;
        padding: 8px 18px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        transition: var(--transition-fast);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .add-to-cart-btn:hover {
        background: var(--primary-orange-dark);
        transform: scale(1.05);
    }

    .add-to-cart-btn i {
        font-size: 14px;
    }

    .cart-sidebar {
        position: sticky;
        top: 100px;
        background: white;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg);
        overflow: hidden;
    }

    .cart-header {
        background: linear-gradient(135deg, var(--dark-charcoal), var(--dark-secondary));
        color: white;
        padding: 20px 24px;
    }

    .cart-header h5 {
        margin: 0;
        font-weight: 700;
    }

    .cart-body {
        padding: 20px;
        min-height: 200px;
    }

    .cart-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 12px 0;
        border-bottom: 1px solid var(--light-gray);
    }

    .cart-item:last-child {
        border-bottom: none;
    }

    .cart-item-info strong {
        display: block;
        font-size: 14px;
        color: var(--dark-charcoal);
    }

    .cart-item-info span {
        font-size: 13px;
        color: var(--muted-gray);
    }

    .cart-item-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .qty-control {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .qty-btn {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 2px solid var(--light-gray);
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: var(--transition-fast);
    }

    .qty-btn:hover {
        border-color: var(--primary-orange);
        color: var(--primary-orange);
    }

    .remove-btn {
        color: var(--danger);
        background: none;
        border: none;
        cursor: pointer;
    }

    .cart-footer {
        padding: 20px;
        border-top: 2px solid var(--light-gray);
    }

    .cart-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .cart-row span:first-child {
        color: var(--muted-gray);
    }

    .cart-row.total {
        font-size: 18px;
        font-weight: 700;
        color: var(--dark-charcoal);
        margin-top: 12px;
        padding-top: 12px;
        border-top: 2px solid var(--light-gray);
    }

    .checkout-btn {
        width: 100%;
        padding: 16px;
        font-size: 16px;
        margin-top: 16px;
    }

    .empty-cart {
        text-align: center;
        padding: 40px 20px;
    }

    .empty-cart i {
        font-size: 60px;
        color: var(--light-gray);
        margin-bottom: 16px;
    }

    @media (max-width: 991px) {
        .cart-sidebar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            border-radius: var(--radius-lg) var(--radius-lg) 0 0;
            max-height: 60vh;
            overflow-y: auto;
        }
    }
</style>
@endsection

@section('content')
<!-- Restaurant Hero -->
<section class="restaurant-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-2 text-center text-md-start mb-4 mb-md-0">
                <img src="{{ $restaurant->logo ?? 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=200&q=80' }}" alt="{{ $restaurant->name }}" class="restaurant-logo">
            </div>
            <div class="col-md-8">
                <h1 class="restaurant-title-main">{{ $restaurant->name }}</h1>
                <p class="restaurant-address"><i class="bi bi-geo-alt me-2"></i>{{ $restaurant->address }}</p>
                <div class="restaurant-stats">
                    <div class="stat-badge">
                        <i class="bi bi-star-fill"></i>
                        <span>{{ number_format($restaurant->rating ?? 4.5, 1) }}</span>
                    </div>
                    <div class="stat-badge">
                        <i class="bi bi-clock"></i>
                        <span>{{ $restaurant->delivery_time_minutes ?? 30 }} min</span>
                    </div>
                    <div class="stat-badge">
                        <i class="bi bi-truck"></i>
                        <span>${{ number_format($restaurant->delivery_fee ?? 2.99, 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-2 text-end">
                <button class="favorite-btn" onclick="toggleFavorite()">
                    <i class="bi bi-heart"></i>
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Menu Section -->
<section class="section-padding pt-5">
    <div class="container-fluid-custom">
        <div class="row">
            <div class="col-lg-8">
                @forelse($categories as $category)
                <div class="menu-category" id="category-{{ $category->id }}">
                    <h2 class="category-title">{{ $category->name }}</h2>
                    @if($category->description)
                    <p class="category-desc">{{ $category->description }}</p>
                    @endif

                    <div class="row">
                        @forelse($category->menuItems as $item)
                        <div class="col-md-6">
                            <div class="menu-item-card" data-id="{{ $item->id }}" data-name="{{ $item->name }}" data-price="{{ $item->price }}">
                                @if($item->image)
                                <img src="{{ $item->image }}" alt="{{ $item->name }}" class="menu-item-image">
                                @else
                                <div class="menu-item-image d-flex align-items-center justify-content-center bg-light">
                                    <i class="bi bi-image text-muted" style="font-size: 32px;"></i>
                                </div>
                                @endif
                                <div class="menu-item-info">
                                    <h4 class="menu-item-name">{{ $item->name }}</h4>
                                    <p class="menu-item-desc">{{ Str::limit($item->description, 80) }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="menu-item-price">${{ number_format($item->price, 2) }}</span>
                                        <button type="button" class="add-to-cart-btn" onclick="event.stopPropagation(); addToCart({{ json_encode($item->id) }}, {{ json_encode($item->name) }}, {{ json_encode((float) $item->price) }})">
                                            <i class="bi bi-plus-lg"></i> Add
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <p class="text-muted">No items in this category</p>
                        </div>
                        @endforelse
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <i class="bi bi-menu-button display-1 text-muted"></i>
                    <h3 class="mt-3 text-muted">Menu not available</h3>
                </div>
                @endforelse
            </div>

            <!-- Cart Sidebar -->
            <div class="col-lg-4">
                <div class="cart-sidebar">
                    <div class="cart-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-cart3 me-2"></i><span id="cart-title">Your Order</span></h5>
                            <span class="badge bg-orange rounded-pill d-none" id="cart-count-badge">0</span>
                        </div>
                    </div>
                    <div class="cart-body" id="cart-items">
                        <div class="empty-cart">
                            <i class="bi bi-cart"></i>
                            <p class="text-muted">Your cart is empty</p>
                            <small class="text-muted">Add items from the menu</small>
                        </div>
                    </div>
                    <div class="cart-footer">
                        <div class="cart-row">
                            <span>Subtotal</span>
                            <span id="cart-subtotal">$0.00</span>
                        </div>
                        <div class="cart-row">
                            <span>Delivery Fee</span>
                            <span>${{ number_format($restaurant->delivery_fee ?? 2.99, 2) }}</span>
                        </div>
                        <div class="cart-row">
                            <span>Platform Fee</span>
                            <span id="cart-platform-fee">$0.00</span>
                        </div>
                        <div class="cart-row total">
                            <span>Total</span>
                            <span id="cart-total">$0.00</span>
                        </div>
                        @auth
                        <button type="button" class="btn btn-primary-custom checkout-btn" data-bs-toggle="modal" data-bs-target="#checkoutModal">
                            <i class="bi bi-lock me-2"></i>Checkout
                        </button>
                        @else
                        <a href="{{ route('login') }}" class="btn btn-primary-custom w-100 text-center d-block">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Log in to order
                        </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@auth
<!-- Checkout Modal -->
<div class="modal fade" id="checkoutModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-charcoal text-white">
                <h5 class="modal-title"><i class="bi bi-cart-check me-2"></i>Checkout</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('orders.store') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">
                    <input type="hidden" name="items" id="cart-items-input">
                    <input type="hidden" name="delivery_latitude" id="delivery_latitude" value="{{ $restaurant->latitude }}">
                    <input type="hidden" name="delivery_longitude" id="delivery_longitude" value="{{ $restaurant->longitude }}">
                    <input type="hidden" name="payment_method_id" id="payment_method_id" value="">

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Delivery Address</label>
                        <div class="input-group mb-2">
                            <input type="text" name="delivery_address" id="delivery_address" class="form-control form-control-custom" placeholder="Enter your delivery address" required>
                            <button type="button" class="btn btn-primary-custom" id="useLocationBtn" onclick="getUserLocation()" title="Use my current location">
                                <i class="bi bi-crosshair"></i>
                            </button>
                        </div>
                        <small class="text-muted" id="location-status"></small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Your Name</label>
                            <input type="text" name="customer_name" class="form-control form-control-custom" value="{{ auth()->user()?->name }}" placeholder="Your full name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <input type="text" name="customer_phone" class="form-control form-control-custom" placeholder="Your phone number" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Special Instructions</label>
                        <textarea name="delivery_instructions" class="form-control" rows="3" placeholder="Any special requests? (optional)"></textarea>
                    </div>

                    <div class="bg-light p-4 rounded-3 mt-4">
                        <h6 class="fw-semibold mb-3">Order Summary</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span id="checkout-subtotal">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Delivery Fee</span>
                            <span>${{ number_format($restaurant->delivery_fee ?? 2.99, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Platform Fee</span>
                            <span id="checkout-platform-fee">$0.00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total</span>
                            <span id="checkout-total">$0.00</span>
                        </div>
                    </div>

                    @if($stripeConfigured)
                    <div class="mt-4 p-3 border rounded-3" id="payment-section">
                        <h6 class="fw-semibold mb-3"><i class="bi bi-credit-card me-2"></i>Payment</h6>
                        <div id="card-element" class="form-control" style="padding: 12px;"></div>
                        <div id="card-errors" class="text-danger small mt-2" role="alert"></div>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="bi bi-check-circle me-2"></i>Place Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endauth
@endsection

@section('scripts')
<script>
let cart = [];

document.getElementById('checkoutModal')?.addEventListener('show.bs.modal', function () {
    if (navigator.geolocation) {
        setTimeout(() => getUserLocation(true), 500);
    }
});

function getUserLocation(silent) {
    const statusEl = document.getElementById('location-status');
    const btn = document.getElementById('useLocationBtn');
    const addrInput = document.getElementById('delivery_address');

    if (!btn) return;

    if (!navigator.geolocation) {
        if (!silent) showToast('Geolocation not supported by your browser');
        return;
    }

    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    btn.disabled = true;
    if (statusEl) statusEl.textContent = 'Getting your location...';

    navigator.geolocation.getCurrentPosition(
        function (pos) {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;

            document.getElementById('delivery_latitude').value = lat;
            document.getElementById('delivery_longitude').value = lng;

            if (statusEl) statusEl.textContent = 'Finding your address...';

            fetch("{{ route('geocode.reverse') }}?lat=" + lat + "&lng=" + lng)
                .then(res => res.json())
                .then(data => {
                    if (data.formatted_address) {
                        addrInput.value = data.formatted_address;
                        if (statusEl) statusEl.innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> Location found</span>';
                        setTimeout(() => { if (statusEl) statusEl.textContent = ''; }, 4000);
                    } else {
                        addrInput.value = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                        if (statusEl) statusEl.textContent = 'Could not find address. Please enter manually.';
                    }
                })
                .catch(() => {
                    addrInput.value = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                    if (statusEl) statusEl.textContent = 'Could not find address. Please enter manually.';
                })
                .finally(() => {
                    btn.innerHTML = '<i class="bi bi-crosshair"></i>';
                    btn.disabled = false;
                });
        },
        function (err) {
            if (!silent) {
                let msg = 'Could not get location';
                if (err.code === 1) msg = 'Location access denied. Please enter manually.';
                else if (err.code === 2) msg = 'Location unavailable. Please try again.';
                else if (err.code === 3) msg = 'Location request timed out.';
                if (statusEl) statusEl.innerHTML = '<span class="text-danger">' + msg + '</span>';
                showToast(msg);
            }
            btn.innerHTML = '<i class="bi bi-crosshair"></i>';
            btn.disabled = false;
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
}

function addToCart(id, name, price) {
    const existing = cart.find(item => item.menu_item_id === id);
    if (existing) {
        existing.quantity++;
    } else {
        cart.push({ menu_item_id: id, item_name: name, unit_price: price, quantity: 1 });
    }
    updateCartDisplay();
    showToast(name + ' added to cart!');
}

document.addEventListener('click', function (e) {
    const card = e.target.closest('.menu-item-card');
    if (card && !e.target.closest('.add-to-cart-btn')) {
        const id = parseInt(card.dataset.id);
        const name = card.dataset.name;
        const price = parseFloat(card.dataset.price);
        addToCart(id, name, price);
        card.style.borderColor = 'var(--success-green)';
        setTimeout(() => { card.style.borderColor = ''; }, 600);
    }
});

function removeFromCart(id) {
    cart = cart.filter(item => item.menu_item_id !== id);
    updateCartDisplay();
}

function updateQuantity(id, change) {
    const item = cart.find(item => item.menu_item_id === id);
    if (item) {
        item.quantity += change;
        if (item.quantity <= 0) {
            removeFromCart(id);
            return;
        }
        updateCartDisplay();
    }
}

function updateCartDisplay() {
    const container = document.getElementById('cart-items');
    const countBadge = document.getElementById('cart-count-badge');
    const cartTitle = document.getElementById('cart-title');
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);

    if (countBadge) {
        if (totalItems > 0) {
            countBadge.classList.remove('d-none');
            countBadge.textContent = totalItems;
        } else {
            countBadge.classList.add('d-none');
        }
    }
    if (cartTitle) {
        cartTitle.textContent = totalItems > 0 ? `Your Order (${totalItems})` : 'Your Order';
    }

    if (cart.length === 0) {
        container.innerHTML = `
            <div class="empty-cart">
                <i class="bi bi-cart"></i>
                <p class="text-muted">Your cart is empty</p>
                <small class="text-muted">Add items from the menu</small>
            </div>
        `;
        document.getElementById('cart-subtotal').textContent = '$0.00';
        document.getElementById('cart-platform-fee').textContent = '$0.00';
        document.getElementById('cart-total').textContent = '$0.00';
        return;
    }

    let html = '';
    let subtotal = 0;

    cart.forEach(item => {
        const itemTotal = item.unit_price * item.quantity;
        subtotal += itemTotal;

        html += `
            <div class="cart-item">
                <div class="cart-item-info">
                    <strong>${item.item_name}</strong>
                    <span>$${item.unit_price.toFixed(2)} x ${item.quantity}</span>
                </div>
                <div class="cart-item-actions">
                    <div class="qty-control">
                        <button class="qty-btn" onclick="updateQuantity(${item.menu_item_id}, -1)">-</button>
                        <span class="px-2">${item.quantity}</span>
                        <button class="qty-btn" onclick="updateQuantity(${item.menu_item_id}, 1)">+</button>
                    </div>
                    <button class="remove-btn" onclick="removeFromCart(${item.menu_item_id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;

    const platformFee = subtotal * 0.10;
    const deliveryFee = {{ $restaurant->delivery_fee ?? 2.99 }};
    const total = subtotal + platformFee + deliveryFee;

    document.getElementById('cart-subtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('cart-platform-fee').textContent = '$' + platformFee.toFixed(2);
    document.getElementById('cart-total').textContent = '$' + total.toFixed(2);

    document.getElementById('checkout-subtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('checkout-platform-fee').textContent = '$' + platformFee.toFixed(2);
    document.getElementById('checkout-total').textContent = '$' + total.toFixed(2);

    document.getElementById('cart-items-input').value = JSON.stringify(cart);
}

function toggleFavorite() {
    showToast('Feature coming soon!');
}

function showToast(message) {
    const existing = document.querySelector('.toast-notification');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.className = 'toast-notification';
    toast.innerHTML = `<i class="bi bi-check-circle-fill me-2"></i>${message}`;
    toast.style.cssText = `
        position: fixed;
        bottom: 30px;
        right: 30px;
        background: white;
        color: var(--text-primary);
        padding: 16px 28px;
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.15);
        z-index: 99999;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 4px;
        border-left: 4px solid #2ECC71;
        transform: translateY(20px);
        opacity: 0;
        transition: all 0.3s ease;
    `;
    document.body.appendChild(toast);
    requestAnimationFrame(() => {
        toast.style.transform = 'translateY(0)';
        toast.style.opacity = '1';
    });
    setTimeout(() => {
        toast.style.transform = 'translateY(20px)';
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 2500);
}

(function autoRefreshMenu() {
    if (typeof window._menuRefreshStarted !== 'undefined') return;
    window._menuRefreshStarted = true;

    try {
        const saved = sessionStorage.getItem('foodie_cart_' + {{ $restaurant->id }});
        if (saved) {
            const parsed = JSON.parse(saved);
            if (Array.isArray(parsed) && parsed.length > 0) {
                cart = parsed;
                setTimeout(updateCartDisplay, 100);
            }
        }
    } catch (e) {}

    window.addEventListener('beforeunload', function () {
        try {
            if (cart.length > 0) {
                sessionStorage.setItem('foodie_cart_' + {{ $restaurant->id }}, JSON.stringify(cart));
            }
        } catch (e) {}
    });

    setInterval(function () {
        if (cart.length === 0 && !document.querySelector('.modal.show')) {
            fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const oldMenu = document.querySelector('.menu-category');
                    const newMenu = doc.querySelector('.menu-category');
                    if (oldMenu && newMenu && oldMenu.innerHTML !== newMenu.innerHTML) {
                        showToast('Menu was updated! Refreshing...');
                        try {
                            sessionStorage.setItem('foodie_cart_' + {{ $restaurant->id }}, JSON.stringify(cart));
                        } catch (e) {}
                        setTimeout(() => location.reload(), 1500);
                    }
                })
                .catch(() => {});
        }
    }, 45000);
})();
</script>

@if($stripeConfigured)
<script src="https://js.stripe.com/v3/"></script>
<script>
try {
    var stripe = Stripe('{{ config("services.stripe.key") }}');
    var elements = stripe.elements();
    var card = elements.create('card', { style: {
        base: { fontSize: '16px', color: '#1A1A2E', '::placeholder': { color: '#6C757D' } }
    }});
    card.mount('#card-element');
    card.on('change', function (event) {
        document.getElementById('card-errors').textContent = event.error ? event.error.message : ''
    })

    var submitting = false;
    document.querySelector('#checkoutModal form')?.addEventListener('submit', async function (e) {
        e.preventDefault()
        if (submitting) return
        submitting = true

        var lat = document.getElementById('delivery_latitude').value
        var lng = document.getElementById('delivery_longitude').value
        var err = verifyLocation(lat, lng)
        if (err) {
            document.getElementById('location-status').innerHTML = '<span class="text-danger"><i class="bi bi-exclamation-circle me-1"></i>' + err + '</span>'
            submitting = false
            return
        }

        var submitBtn = this.querySelector('[type="submit"]')
        submitBtn.disabled = true
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing payment...'

        var result = await stripe.createPaymentMethod({
            type: 'card',
            card: card,
            billing_details: { name: document.querySelector('[name="customer_name"]').value }
        })

        if (result.error) {
            document.getElementById('card-errors').textContent = result.error.message
            submitBtn.disabled = false
            submitBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Place Order'
            submitting = false
            return
        }

        document.getElementById('payment_method_id').value = result.paymentMethod.id
        document.getElementById('cart-items-input').value = JSON.stringify(cart)
        this.submit()
    })
} catch (e) {
    console.warn('Stripe failed to load:', e)
    attachFallbackSubmit()
}
</script>
@else
<script>
function attachFallbackSubmit() {
    var form = document.querySelector('#checkoutModal form')
    if (!form) return
    form.addEventListener('submit', function (e) {
        var lat = document.getElementById('delivery_latitude').value
        var lng = document.getElementById('delivery_longitude').value
        var err = verifyLocation(lat, lng)
        if (err) {
            e.preventDefault()
            document.getElementById('location-status').innerHTML = '<span class="text-danger"><i class="bi bi-exclamation-circle me-1"></i>' + err + '</span>'
            return
        }
        if (!cart || cart.length === 0) {
            e.preventDefault()
            showToast('Your cart is empty')
            return
        }
        document.getElementById('cart-items-input').value = JSON.stringify(cart)
    })
}
attachFallbackSubmit()
</script>
@endif
@endsection