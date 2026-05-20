@extends('layouts.app')
@section('title', 'Rider Dashboard')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .rider-header {
        background: linear-gradient(135deg, var(--dark-charcoal), var(--dark-secondary));
        padding: 40px 0;
        margin-bottom: 32px;
    }

    .rider-title {
        font-family: 'Playfair Display', serif;
        font-size: 36px;
        font-weight: 700;
        color: white;
    }

    .status-selector {
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(10px);
        padding: 12px 20px;
        border-radius: var(--radius-lg);
    }

    .status-selector label {
        color: rgba(255,255,255,0.8);
        margin-right: 12px;
    }

    .status-selector select {
        background: white;
        border: none;
        padding: 8px 16px;
        border-radius: var(--radius-md);
        font-weight: 600;
    }

    .stat-card-rider {
        background: var(--bg-card);
        border-radius: var(--radius-lg);
        padding: 28px;
        box-shadow: var(--shadow-sm);
        text-align: center;
        transition: var(--transition-normal);
    }

    .stat-value-rider {
        font-size: 36px;
        font-weight: 700;
        line-height: 1;
    }

    .stat-label-rider {
        color: var(--text-muted);
        font-size: 14px;
        margin-top: 8px;
    }

    .stat-card-rider:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-md);
    }

    .stat-icon-rider {
        width: 64px;
        height: 64px;
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 28px;
    }

    .stat-value-rider {
        font-size: 36px;
        font-weight: 700;
        line-height: 1;
    }

    .stat-label-rider {
        color: var(--muted-gray);
        font-size: 14px;
        margin-top: 8px;
    }

    .current-delivery-card {
        background: linear-gradient(135deg, var(--primary-orange), var(--primary-orange-dark));
        border-radius: var(--radius-xl);
        padding: 32px;
        color: white;
        margin-bottom: 32px;
    }

    .delivery-timeline {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin: 32px 0;
    }

    .delivery-timeline::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        height: 4px;
        background: rgba(255,255,255,0.3);
        border-radius: 2px;
    }

    .timeline-step {
        text-align: center;
        position: relative;
        z-index: 2;
    }

    .timeline-icon {
        width: 44px;
        height: 44px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
        font-size: 18px;
        color: var(--muted-gray);
        transition: var(--transition-normal);
    }

    .timeline-step.active .timeline-icon {
        background: white;
        color: var(--primary-orange);
        box-shadow: 0 0 20px rgba(255,255,255,0.5);
    }

    .timeline-label {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .timeline-step.active .timeline-label {
        color: white;
    }

    .delivery-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .delivery-btn {
        padding: 14px 28px;
        border-radius: var(--radius-md);
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: var(--transition-normal);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-pickup {
        background: white;
        color: var(--primary-orange);
    }

    .btn-deliver {
        background: var(--success-green);
        color: white;
    }

    .btn-call {
        background: rgba(255,255,255,0.2);
        color: white;
    }

    .table-card-rider {
        background: var(--bg-card);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }

    .map-container-rider {
        height: 250px;
        border-radius: var(--radius-lg);
        overflow: hidden;
    }

    #map { border-radius: var(--radius-lg); height: 250px; }

    .location-marker {
        width: 28px;
        height: 28px;
        background: var(--primary-orange);
        border-radius: 50%;
        border: 4px solid white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }

    .location-update-btn {
        width: 100%;
        padding: 14px;
        background: var(--dark-charcoal);
        color: white;
        border: none;
        border-radius: var(--radius-md);
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition-normal);
    }

    .location-update-btn:hover {
        background: var(--dark-secondary);
    }

    .rider-marker, .restaurant-marker, .delivery-marker {
        background: none !important;
        border: none !important;
    }

    .rider-marker div, .restaurant-marker div, .delivery-marker div {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .leaflet-popup-content {
        font-family: 'DM Sans', sans-serif;
        font-size: 14px;
    }

    .route-info-popup .leaflet-popup-content-wrapper {
        background: var(--dark-charcoal);
        color: white;
        border-radius: 12px;
    }

    .route-info-popup .leaflet-popup-tip {
        background: var(--dark-charcoal);
    }
</style>
@endsection

@section('content')
<!-- Rider Header -->
<section class="rider-header">
    <div class="container-fluid-custom">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="rider-title">Welcome, {{ auth()->user()->name }}</h1>
                <p class="text-white-50 mt-2">
                    @php $assignedCount = $currentOrders->count(); @endphp
                    @if($assignedCount > 0)
                    <span class="badge bg-success fs-6 me-2 px-3 py-2">
                        <i class="bi bi-bicycle me-1"></i> {{ $assignedCount }} active
                    </span>
                    On delivery
                    @else
                    Ready to deliver
                    @endif
                </p>
            </div>
            <div class="status-selector">
                <label class="fw-semibold">Status:</label>
                <form method="POST" action="{{ route('rider.status') }}" class="d-inline">
                    @csrf
                    <select name="status" onchange="this.form.submit()">
                        <option value="available" {{ $rider->status === 'available' ? 'selected' : '' }}>Available</option>
                        <option value="busy" {{ $rider->status === 'busy' ? 'selected' : '' }}>Busy</option>
                        <option value="offline" {{ $rider->status === 'offline' ? 'selected' : '' }}>Offline</option>
                    </select>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Stats -->
<div class="container-fluid-custom mb-4">
    <div class="row g-4">
        <div class="col-md-3">
            <div class="stat-card-rider">
                <div class="stat-icon-rider" style="background: rgba(59, 130, 246, 0.12); color: var(--primary);">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="stat-value-rider" style="color: var(--primary);">{{ $todayDeliveries }}</div>
                <div class="stat-label-rider">Today's Deliveries</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-rider">
                <div class="stat-icon-rider" style="background: rgba(22, 163, 74, 0.12); color: var(--success);">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="stat-value-rider" style="color: var(--success);">${{ number_format($todayEarnings, 2) }}</div>
                <div class="stat-label-rider">Today's Earnings</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-rider">
                <div class="stat-icon-rider" style="background: rgba(217, 119, 6, 0.12); color: var(--warning);">
                    <i class="bi bi-star-fill"></i>
                </div>
                <div class="stat-value-rider" style="color: var(--warning);">{{ number_format($rider->rating, 1) }}</div>
                <div class="stat-label-rider">Rating</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-rider">
                <div class="stat-icon-rider" style="background: rgba(139, 92, 246, 0.12); color: var(--secondary);">
                    <i class="bi bi-trophy"></i>
                </div>
                <div class="stat-value-rider" style="color: var(--secondary);">{{ $rider->total_deliveries }}</div>
                <div class="stat-label-rider">Total Deliveries</div>
            </div>
        </div>
    </div>
</div>

<!-- Current Deliveries -->
@if($currentOrders->count() > 0)
<div class="container-fluid-custom mb-4">
    @if($currentOrders->count() > 1)
    <div class="alert alert-info border-0 rounded-3 mb-3">
        <i class="bi bi-diagram-3 me-2"></i>
        You have <strong>{{ $currentOrders->count() }}</strong> active deliveries
        <span class="badge bg-dark ms-2">{{ 3 - $currentOrders->count() }} slot{{ 3 - $currentOrders->count() != 1 ? 's' : '' }} remaining</span>
    </div>
    @endif

    @foreach($currentOrders as $co)
    <div class="current-delivery-card mb-3">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <span class="badge bg-white text-orange px-3 py-2">Delivery {{ $loop->iteration }}</span>
                <h3 class="mt-3 mb-1">Order {{ $co->order_number }}</h3>
                <p class="mb-0 opacity-75">{{ $co->restaurant->name }}</p>
            </div>
            <div class="text-end">
                <div class="h4 mb-1">${{ number_format($co->delivery_fee, 2) }}</div>
                <small class="opacity-75">Your Earning</small>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <p class="mb-2"><i class="bi bi-shop me-2"></i>{{ $co->restaurant->address }}</p>
                <p class="mb-0"><i class="bi bi-geo-alt me-2"></i>{{ $co->delivery_address }}</p>
            </div>
        </div>

        <div class="delivery-timeline">
            <div class="timeline-step {{ in_array($co->status, ['confirmed', 'preparing', 'on_the_way', 'delivered']) ? 'active' : '' }}">
                <div class="timeline-icon"><i class="bi bi-check2"></i></div>
                <div class="timeline-label">Confirmed</div>
            </div>
            <div class="timeline-step {{ in_array($co->status, ['preparing', 'on_the_way', 'delivered']) ? 'active' : '' }}">
                <div class="timeline-icon"><i class="bi bi-fire"></i></div>
                <div class="timeline-label">Preparing</div>
            </div>
            <div class="timeline-step {{ in_array($co->status, ['on_the_way', 'delivered']) ? 'active' : '' }}">
                <div class="timeline-icon"><i class="bi bi-bicycle"></i></div>
                <div class="timeline-label">Picked Up</div>
            </div>
            <div class="timeline-step {{ $co->status === 'delivered' ? 'active' : '' }}">
                <div class="timeline-icon"><i class="bi bi-house"></i></div>
                <div class="timeline-label">Delivered</div>
            </div>
        </div>

        <div class="delivery-actions">
            @if($co->status === 'on_the_way')
            <form method="POST" action="{{ route('rider.order.deliver', $co->id) }}" class="d-inline">
                @csrf
                <button type="submit" class="delivery-btn btn-deliver">
                    <i class="bi bi-check-circle"></i> Mark as Delivered
                </button>
            </form>
            @endif
            <a href="tel:{{ $co->customer_phone }}" class="delivery-btn btn-call">
                <i class="bi bi-telephone"></i> Call Customer
            </a>
        </div>
    </div>
    @endforeach

    @php $pickupReady = $currentOrders->whereIn('status', ['confirmed', 'preparing']); @endphp
    @if($pickupReady->count() > 0)
    <div class="d-flex gap-2 mt-3">
        <form method="POST" action="{{ route('rider.order.pickup-all') }}" class="flex-grow-1">
            @csrf
            <button type="submit" class="delivery-btn btn-pickup w-100" style="justify-content:center;">
                <i class="bi bi-box-seam"></i> Start Route — Pick Up All ({{ $pickupReady->count() }})
            </button>
        </form>
    </div>
    @endif

    @php $onWayCount = $currentOrders->where('status', 'on_the_way')->count(); $totalCount = $currentOrders->count(); @endphp
    @if($totalCount > 0)
    <div class="mt-3">
        <div class="d-flex justify-content-between small text-white-50 mb-1">
            <span>Route Progress</span>
            <span>{{ $onWayCount }}/{{ $totalCount }} delivered</span>
        </div>
        <div class="progress" style="height:6px;background:rgba(255,255,255,0.2);">
            <div class="progress-bar bg-white" style="width:{{ $totalCount > 0 ? ($onWayCount / $totalCount) * 100 : 0 }}%;"></div>
        </div>
    </div>
    @endif
</div>
@endif

<!-- Orders and Map -->
<div class="container-fluid-custom">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="table-card-rider p-0 overflow-hidden mb-4">
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1"><i class="bi bi-map me-2 text-orange"></i>Delivery Map</h5>
                        <small class="text-muted">Optimized route for all deliveries</small>
                    </div>
                </div>
                <div id="route-info" class="p-3 bg-light border-bottom" style="display:none;">
                    <div class="row g-3 text-center">
                        <div class="col-4">
                            <small class="text-muted d-block">Total Distance</small>
                            <span class="fw-bold fs-5" id="ri-distance">—</span>
                        </div>
                        <div class="col-4">
                            <small class="text-muted d-block">Est. Time</small>
                            <span class="fw-bold fs-5" id="ri-time">—</span>
                        </div>
                        <div class="col-4">
                            <small class="text-muted d-block">Stops</small>
                            <span class="fw-bold fs-5" id="ri-stops">—</span>
                        </div>
                    </div>
                </div>
                <div id="deliveryMap" class="w-100" style="height: 400px;"></div>
            </div>

            <div class="table-card-rider">
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0"><i class="bi bi-route me-2 text-orange"></i>My Route</h5>
                        <small class="text-muted">{{ 3 - $currentOrders->count() }} slot{{ 3 - $currentOrders->count() != 1 ? 's' : '' }} remaining</small>
                    </div>
                    @php $onWayOrders = $currentOrders->where('status', 'on_the_way'); @endphp
                    @if($onWayOrders->count() > 1)
                    <form method="POST" action="{{ route('rider.order.deliver', 0) }}" class="d-inline" id="completeAllForm">
                        @csrf
                        <button type="button" class="btn btn-success btn-sm" onclick="completeAllDeliveries()">
                            <i class="bi bi-check-all me-1"></i> Complete All ({{ $onWayOrders->count() }})
                        </button>
                    </form>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Restaurant</th>
                                <th>Status</th>
                                <th>Earning</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $onWayOrders = $currentOrders->where('status', 'on_the_way');
                                $pickupOrders = $currentOrders->whereIn('status', ['confirmed', 'preparing']);
                                $hasAny = $onWayOrders->count() > 0 || $pickupOrders->count() > 0 || $availableOrders->count() > 0;
                            @endphp
                            @forelse($onWayOrders as $order)
                            <tr class="table-success">
                                <td>
                                    <strong>{{ $order->order_number }}</strong>
                                    <div class="small text-muted">{{ $order->items->count() }} items</div>
                                </td>
                                <td>{{ $order->restaurant->name }}</td>
                                <td><span class="badge bg-success">Picked Up</span></td>
                                <td class="fw-bold" style="color:var(--success);">${{ number_format($order->delivery_fee, 2) }}</td>
                                <td>
                                    <form method="POST" action="{{ route('rider.order.deliver', $order->id) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Complete delivery for {{ $order->order_number }}?')">
                                            <i class="bi bi-check-circle me-1"></i> Complete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            @endforelse
                            @forelse($pickupOrders as $order)
                            <tr class="table-info">
                                <td>
                                    <strong>{{ $order->order_number }}</strong>
                                    <div class="small text-muted">{{ $order->items->count() }} items</div>
                                </td>
                                <td>{{ $order->restaurant->name }}</td>
                                <td><span class="badge bg-info text-dark">Ready for Pickup</span></td>
                                <td class="fw-bold" style="color:var(--primary);">${{ number_format($order->delivery_fee, 2) }}</td>
                                <td>
                                    <form method="POST" action="{{ route('rider.order.pickup', $order->id) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-primary-custom btn-sm">
                                            <i class="bi bi-box-seam me-1"></i> Pick Up
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            @endforelse
                            @forelse($availableOrders as $order)
                            <tr>
                                <td>
                                    <strong>{{ $order->order_number }}</strong>
                                    <div class="small text-muted">{{ $order->items->count() }} items</div>
                                </td>
                                <td>{{ $order->restaurant->name }}</td>
                                <td><span class="badge bg-warning text-dark">Available</span></td>
                                <td class="fw-bold" style="color:var(--success);">${{ number_format($order->delivery_fee, 2) }}</td>
                                <td>
                                    <form method="POST" action="{{ route('rider.order.accept', $order->id) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-primary-custom btn-sm">
                                            <i class="bi bi-plus-lg me-1"></i> Accept
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            @if(!$hasAny)
                            <tr>
                                <td colspan="5" class="text-center py-5" style="color:var(--text-muted);">
                                    <i class="bi bi-check2-all display-4 d-block mb-3" style="color:var(--text-muted);"></i>
                                    All caught up! No deliveries pending.
                                </td>
                            </tr>
                            @endif
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="table-card-rider p-4">
                <h5 class="mb-3"><i class="bi bi-geo me-2 text-orange"></i>My Location</h5>
                <div id="myLocationMap" class="map-container-rider mb-3" style="height: 180px;"></div>
                <form method="POST" action="{{ route('rider.location') }}" id="locationForm">
                    @csrf
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                    <button type="submit" class="location-update-btn">
                        <i class="bi bi-geo-alt me-2"></i>Update Location
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script id="order-locations" type="application/json">
{
    "rider": { "lat": {{ $riderLat ?? 30.0444 }}, "lng": {{ $riderLng ?? 31.2357 }} },
    "orders": [
        @foreach($currentOrders as $co)
        {
            "order_number": "{{ $co->order_number }}",
            "restaurant": {
                "lat": {{ $co->restaurant->latitude }},
                "lng": {{ $co->restaurant->longitude }},
                "name": "{{ $co->restaurant->name }}",
                "address": "{{ $co->restaurant->address }}"
            },
            "delivery": {
                "lat": {{ $co->delivery_latitude }},
                "lng": {{ $co->delivery_longitude }},
                "address": "{{ $co->delivery_address }}"
            },
            "status": "{{ $co->status }}"
        }@if(!$loop->last),@endif
        @endforeach
    ]
}
</script>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
let deliveryMap;
let locationMap;
let locationMarker;
let riderLat = {{ $riderLat ?? 30.0444 }};
let riderLng = {{ $riderLng ?? 31.2357 }};

const orderLocations = (function () {
    try {
        const el = document.getElementById('order-locations');
        return el ? JSON.parse(el.textContent) : null;
    } catch (e) {
        return null;
    }
})();

document.addEventListener('DOMContentLoaded', function () {
    initDeliveryMap();
    initLocationMap();
});

function fetchOSRMRoute(waypoints, callback) {
    var lnglat = waypoints.map(function (w) { return w[1] + ',' + w[0] }).join(';');
    var url = 'https://router.project-osrm.org/route/v1/driving/' + lnglat + '?overview=full&geometries=geojson&steps=false';
    fetch(url)
        .then(function (r) { return r.json() })
        .then(function (data) {
            if (data.code === 'Ok' && data.routes && data.routes[0]) {
                var route = data.routes[0];
                var coords = route.geometry.coordinates.map(function (c) { return [c[1], c[0]] });
                callback(null, {
                    coords: coords,
                    distance: route.distance,
                    duration: route.duration
                });
            } else {
                callback('Routing failed');
            }
        })
        .catch(function (err) { callback(err) });
}

function initDeliveryMap() {
    deliveryMap = L.map('deliveryMap').setView([riderLat, riderLng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(deliveryMap);

    var riderIcon = L.divIcon({
        className: 'rider-marker',
        html: '<div style="width:32px;height:32px;background:var(--primary-orange, #FF6B35);border:3px solid white;border-radius:50%;box-shadow:0 4px 12px rgba(0,0,0,0.3);display:flex;align-items:center;justify-content:center;color:white;font-size:14px;"><i class="bi bi-person"></i></div>',
        iconSize: [32, 32], iconAnchor: [16, 16]
    });

    var restaurantIcon = L.divIcon({
        className: 'restaurant-marker',
        html: '<div style="width:32px;height:32px;background:#E74C3C;border:3px solid white;border-radius:4px;box-shadow:0 4px 12px rgba(0,0,0,0.3);display:flex;align-items:center;justify-content:center;color:white;font-size:14px;"><i class="bi bi-shop"></i></div>',
        iconSize: [32, 32], iconAnchor: [16, 16]
    });

    var deliveryIcon = L.divIcon({
        className: 'delivery-marker',
        html: '<div style="width:32px;height:32px;background:#2ECC71;border:3px solid white;border-radius:50%;box-shadow:0 4px 12px rgba(0,0,0,0.3);display:flex;align-items:center;justify-content:center;color:white;font-size:14px;"><i class="bi bi-house"></i></div>',
        iconSize: [32, 32], iconAnchor: [16, 16]
    });

    L.marker([riderLat, riderLng], { icon: riderIcon })
        .addTo(deliveryMap)
        .bindPopup('<strong>You</strong>');

    if (!orderLocations || !orderLocations.orders || orderLocations.orders.length === 0) return;

    var allStops = [[riderLat, riderLng]];
    var bounds = L.latLngBounds([allStops[0]]);
    var colors = ['#FF6B35', '#3498DB', '#9B59B6'];
    var routeLines = [];

    orderLocations.orders.forEach(function (ord, idx) {
        var rest = ord.restaurant;
        var del = ord.delivery;
        var color = colors[idx % colors.length];

        if (rest && rest.lat && rest.lng) {
            L.marker([rest.lat, rest.lng], { icon: restaurantIcon })
                .addTo(deliveryMap)
                .bindPopup('<strong>' + rest.name + '</strong><br>Order ' + ord.order_number);
            bounds.extend([rest.lat, rest.lng]);
            allStops.push([rest.lat, rest.lng]);
        }
        if (del && del.lat && del.lng) {
            L.marker([del.lat, del.lng], { icon: deliveryIcon })
                .addTo(deliveryMap)
                .bindPopup('<strong>Delivery</strong><br>Order ' + ord.order_number + '<br>' + del.address);
            bounds.extend([del.lat, del.lng]);
            allStops.push([del.lat, del.lng]);
        }

        if (idx === 0) {
            var dashLen = '12, 8';
            var coords = [
                [riderLat, riderLng],
                [rest.lat, rest.lng],
                [del.lat, del.lng]
            ];
            var line = L.polyline(coords, { color: color, weight: 4, opacity: 0.5, dashArray: dashLen }).addTo(deliveryMap);
            routeLines.push(line);
        } else {
            var prevDel = orderLocations.orders[idx - 1].delivery;
            var prevRest = orderLocations.orders[idx - 1].restaurant;
            var coords = [
                [prevDel.lat, prevDel.lng],
                [rest.lat, rest.lng],
                [del.lat, del.lng]
            ];
            var line = L.polyline(coords, { color: color, weight: 4, opacity: 0.5, dashArray: dashLen }).addTo(deliveryMap);
            routeLines.push(line);
        }
    });

    deliveryMap.fitBounds(bounds, { padding: [50, 50] });

    if (allStops.length >= 2) {
        fetchOSRMRoute(allStops, function (err, result) {
            if (!err && result) {
                routeLines.forEach(function (l) { deliveryMap.removeLayer(l); });
                var fullRoute = L.polyline(result.coords, {
                    color: '#FF6B35', weight: 5, opacity: 0.85
                }).addTo(deliveryMap);

                var totalKm = (result.distance / 1000).toFixed(1);
                var totalMin = Math.round(result.duration / 60);
                var hours = Math.floor(totalMin / 60);
                var mins = totalMin % 60;
                var timeStr = hours > 0 ? hours + 'h ' + mins + 'min' : mins + 'min';

                document.getElementById('route-info').style.display = 'block';
                document.getElementById('ri-distance').textContent = totalKm + ' km';
                document.getElementById('ri-time').textContent = timeStr;
                document.getElementById('ri-stops').textContent = allStops.length - 1;

                L.popup({ closeButton: false, className: 'route-info-popup' })
                    .setLatLng(result.coords[Math.floor(result.coords.length / 2)])
                    .setContent('<div style="font-size:13px;font-weight:600;">' + totalKm + ' km &middot; ' + timeStr + '</div>')
                    .openOn(deliveryMap);
            }
        });
    }
}

function initLocationMap() {
    locationMap = L.map('myLocationMap').setView([riderLat, riderLng], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(locationMap);
    locationMarker = L.marker([riderLat, riderLng], {
        icon: L.divIcon({
            className: 'location-marker',
            html: '<div></div>',
            iconSize: [28, 28], iconAnchor: [14, 28]
        })
    }).addTo(locationMap);
}

function updateMarkerPosition(lat, lng) {
    if (locationMarker) locationMarker.setLatLng([lat, lng]);
    if (locationMap) locationMap.panTo([lat, lng]);
}

function calcDistance(lat1, lng1, lat2, lng2) {
    var R = 6371;
    var dLat = (lat2 - lat1) * Math.PI / 180;
    var dLng = (lng2 - lng1) * Math.PI / 180;
    var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLng / 2) * Math.sin(dLng / 2);
    return R * (2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a)));
}

if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function (position) {
        document.getElementById('latitude').value = position.coords.latitude;
        document.getElementById('longitude').value = position.coords.longitude;
        riderLat = position.coords.latitude;
        riderLng = position.coords.longitude;
        updateMarkerPosition(riderLat, riderLng);
        autoSubmitLocation();
    }, function (err) {
        console.log('Geolocation error:', err.message);
    });
}

function autoSubmitLocation() {
    setInterval(function () {
        navigator.geolocation.getCurrentPosition(function (position) {
            document.getElementById('latitude').value = position.coords.latitude;
            document.getElementById('longitude').value = position.coords.longitude;
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;
            if (Math.abs(lat - riderLat) > 0.0001 || Math.abs(lng - riderLng) > 0.0001) {
                riderLat = lat;
                riderLng = lng;
                updateMarkerPosition(lat, lng);
                fetch("{{ route('rider.location') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: new URLSearchParams({ latitude: lat, longitude: lng })
                }).catch(function () {});
            }
        }, function () {}, { enableHighAccuracy: true, timeout: 10000 });
    }, 30000);
}

document.getElementById('locationForm').addEventListener('submit', function (e) {
    e.preventDefault();
    var lat = document.getElementById('latitude').value;
    var lng = document.getElementById('longitude').value;
    var error = verifyLocation(lat, lng);
    if (!error) {
        fetch("{{ route('rider.location') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: new URLSearchParams({ latitude: lat, longitude: lng })
        })
        .then(function (r) { return r.json() })
        .then(function (data) {
            if (data.success) {
                updateMarkerPosition(lat, lng);
                showToast('Location updated!');
            }
        });
    }
});

function showToast(message) {
    var existing = document.querySelector('.toast-notification');
    if (existing) existing.remove();
    var toast = document.createElement('div');
    toast.className = 'toast-notification';
    toast.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>' + message;
    toast.style.cssText = 'position:fixed;bottom:30px;right:30px;background:white;color:var(--text-primary);padding:16px 28px;border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,0.15);z-index:99999;font-weight:600;display:flex;align-items:center;gap:4px;border-left:4px solid #2ECC71;transform:translateY(20px);opacity:0;transition:all 0.3s ease;';
    document.body.appendChild(toast);
    requestAnimationFrame(function () {
        toast.style.transform = 'translateY(0)';
        toast.style.opacity = '1';
    });
    setTimeout(function () {
        toast.style.transform = 'translateY(20px)';
        toast.style.opacity = '0';
        setTimeout(function () { toast.remove() }, 300);
    }, 2500);
}

function completeAllDeliveries() {
    if (!confirm('Complete all ' + {{ $currentOrders->where('status', 'on_the_way')->count() }} + ' deliveries?')) return;
    var forms = document.querySelectorAll('form[action*="deliver"]');
    var idx = 0;
    function submitNext() {
        while (idx < forms.length && forms[idx].id === 'completeAllForm') { idx++; }
        if (idx >= forms.length) { location.reload(); return; }
        var form = forms[idx];
        idx++;
        fetch(form.action, { method: 'POST', body: new FormData(form) })
            .then(function() { submitNext(); })
            .catch(function() { submitNext(); });
    }
    submitNext();
}
</script>
@endsection