@extends('layouts.dashboard')
@section('page_title', 'Admin Dashboard')

@section('sidebar_menu')
    @include('admin.partials.sidebar')
@endsection

@section('styles')
<style>
    .admin-header {
        background: linear-gradient(135deg, var(--primary) 0%, #1D4ED8 100%);
        padding: 48px 0;
        margin-bottom: var(--space-8);
        position: relative;
        overflow: hidden;
    }

    .admin-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        opacity: 0.5;
    }

    .admin-title {
        font-family: var(--font-display);
        font-size: var(--text-3xl);
        font-weight: 700;
        color: white;
        position: relative;
    }

    .admin-header p {
        color: rgba(255, 255, 255, 0.8);
        font-size: var(--text-base);
        position: relative;
        margin-top: var(--space-2);
    }

    .admin-header .badge {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        font-size: var(--text-sm);
        padding: var(--space-2) var(--space-4);
    }

    .stat-card {
        background: var(--bg-secondary);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-subtle);
        padding: var(--space-6);
        box-shadow: var(--shadow-sm);
        height: 100%;
        transition: all var(--transition-base);
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--border-default);
    }

    .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        margin-bottom: var(--space-4);
    }

    .stat-value {
        font-size: var(--text-3xl);
        font-weight: 700;
        color: var(--text-primary);
        line-height: 1;
    }

    .stat-label {
        font-size: var(--text-sm);
        color: var(--text-tertiary);
        font-weight: 500;
        margin-top: var(--space-2);
    }

    .stat-trend {
        font-size: var(--text-xs);
        font-weight: 600;
        margin-top: var(--space-2);
    }

    .chart-container {
        background: var(--bg-secondary);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-subtle);
        padding: var(--space-6);
        box-shadow: var(--shadow-sm);
        height: 100%;
    }

    .chart-title {
        font-size: var(--text-lg);
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: var(--space-5);
        display: flex;
        align-items: center;
        gap: var(--space-2);
    }

    .chart-box {
        position: relative;
        height: 280px;
    }

    .control-tower-card {
        background: var(--bg-secondary);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-subtle);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }

    .card-header-custom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: var(--space-5) var(--space-6);
        border-bottom: 1px solid var(--border-subtle);
    }

    .card-header-title {
        font-size: var(--text-lg);
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }

    .table-card-admin {
        background: var(--bg-secondary);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-subtle);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }

    .table-header-admin {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: var(--space-5) var(--space-6);
        border-bottom: 1px solid var(--border-subtle);
    }

    .table-header-title {
        font-size: var(--text-lg);
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }

    .table th {
        background: var(--bg-tertiary);
        padding: var(--space-4);
        font-weight: 600;
        color: var(--text-primary);
        font-size: var(--text-xs);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid var(--border-default);
    }

    .table td {
        padding: var(--space-4);
        vertical-align: middle;
        border-bottom: 1px solid var(--border-subtle);
        color: var(--text-secondary);
    }

    .table tbody tr:hover {
        background: var(--surface-hover);
    }

    .table .badge {
        font-size: var(--text-xs);
    }

    .dispatch-btn {
        background: var(--primary);
        color: white;
        border: none;
        padding: var(--space-2) var(--space-4);
        border-radius: var(--radius-md);
        font-weight: 600;
        font-size: var(--text-sm);
        cursor: pointer;
        transition: all var(--transition-fast);
    }

    .dispatch-btn:hover {
        background: var(--primary-dark);
        color: white;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: var(--space-2);
    }

    .status-dot.placed { background: var(--info); }
    .status-dot.confirmed { background: var(--warning); }
    .status-dot.preparing { background: var(--secondary); }
    .status-dot.on_the_way { background: var(--success); }
    .status-dot.delivered { background: #10B981; }
    .status-dot.cancelled { background: var(--danger); }

    .quick-link-btn {
        display: flex;
        align-items: center;
        gap: var(--space-3);
        width: 100%;
        padding: var(--space-4);
        background: var(--bg-tertiary);
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-md);
        color: var(--text-secondary);
        font-weight: 500;
        font-size: var(--text-sm);
        transition: all var(--transition-fast);
        cursor: pointer;
    }

    .quick-link-btn:hover {
        background: var(--surface-hover);
        border-color: var(--border-default);
        color: var(--text-primary);
    }

    .quick-link-btn i {
        font-size: 18px;
        color: var(--primary);
    }

    .status-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: var(--space-3) 0;
        border-bottom: 1px solid var(--border-subtle);
    }

    .status-row:last-child {
        border-bottom: none;
    }

    .status-row-label {
        display: flex;
        align-items: center;
        gap: var(--space-2);
        color: var(--text-secondary);
        font-size: var(--text-sm);
    }

    .status-row-value {
        font-weight: 600;
        color: var(--text-primary);
    }

    @media (max-width: 991px) {
        .admin-header {
            padding: 32px 0;
        }

        .admin-title {
            font-size: var(--text-2xl);
        }
    }
</style>
@endsection

@section('content')
<section class="admin-header">
    <div class="container-fluid-custom">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="admin-title">Admin Dashboard</h1>
                <p class="mb-0">Platform overview and insights</p>
            </div>
            <div class="text-end">
                <span class="badge">
                    <i class="bi bi-calendar3 me-2"></i>{{ now()->format('M d, Y') }}
                </span>
            </div>
        </div>
    </div>
</section>

<div class="container-fluid-custom mb-4">
    <div class="row g-4">
        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(59, 130, 246, 0.12); color: var(--primary);">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-value">{{ $stats['customers'] }}</div>
                <div class="stat-label">Total Customers</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--success-light); color: var(--success);">
                    <i class="bi bi-shop"></i>
                </div>
                <div class="stat-value">{{ $stats['restaurants'] }}</div>
                <div class="stat-label">Restaurants</div>
                <div class="stat-trend text-warning">
                    {{ $pendingRestaurants->count() }} pending
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--warning-light); color: var(--warning);">
                    <i class="bi bi-motorcycle"></i>
                </div>
                <div class="stat-value">{{ $stats['riders'] }}</div>
                <div class="stat-label">Riders</div>
                <div class="stat-trend text-success">
                    {{ $riders->where('status', 'available')->count() }} available
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(139, 92, 246, 0.12); color: var(--secondary);">
                    <i class="bi bi-bag"></i>
                </div>
                <div class="stat-value">{{ $stats['today_orders'] }}</div>
                <div class="stat-label">Orders Today</div>
                <div class="stat-trend text-danger">
                    {{ $stats['pending_orders'] }} pending
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(16, 185, 129, 0.12); color: var(--success);">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="stat-value">${{ number_format($stats['today_revenue'], 0) }}</div>
                <div class="stat-label">Revenue Today</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--info-light); color: var(--info);">
                    <i class="bi bi-graph-up"></i>
                </div>
                <div class="stat-value">${{ number_format($stats['total_revenue'], 0) }}</div>
                <div class="stat-label">Total Revenue</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--bg-tertiary); color: var(--text-tertiary);">
                    <i class="bi bi-boxes"></i>
                </div>
                <div class="stat-value">{{ $stats['total_orders'] }}</div>
                <div class="stat-label">Total Orders</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--danger-light); color: var(--danger);">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div class="stat-value">${{ number_format($stats['avg_order_value'], 2) }}</div>
                <div class="stat-label">Avg Order Value</div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid-custom mb-4">
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="chart-container">
                <h6 class="chart-title">
                    <i class="bi bi-graph-up text-primary"></i>
                    Orders & Revenue — Last 7 Days
                </h6>
                <div class="chart-box">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="chart-container">
                <h6 class="chart-title">
                    <i class="bi bi-pie-chart text-primary"></i>
                    Order Status Distribution
                </h6>
                <div class="chart-box">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid-custom mb-4">
    <div class="row g-4">
        <div class="col-lg-8" id="mapCard">
            <div class="control-tower-card">
                <div class="card-header-custom">
                    <h4 class="card-header-title">
                        <i class="bi bi-map text-primary me-2"></i>Live Rider Map
                    </h4>
                    <button class="btn btn-sm btn-secondary" onclick="toggleMapFullscreen()">
                        <i class="bi bi-arrows-fullscreen me-1"></i>
                        <span id="expandText">Expand</span>
                    </button>
                </div>
                <div id="riderMap" style="height:350px; background: var(--bg-tertiary);"></div>
            </div>
        </div>
        <div class="col-lg-4" id="mapSidebar">
            <div class="control-tower-card mb-3">
                <div class="card-header-custom">
                    <h6 class="card-header-title mb-0">
                        <i class="bi bi-gear text-primary me-2"></i>Quick Links
                    </h6>
                </div>
                <div class="p-3">
                    <a href="{{ route('admin.partners.index') }}" class="quick-link-btn mb-2">
                        <i class="bi bi-people"></i>
                        Manage Partners
                    </a>
                    <a href="{{ route('admin.packages.index') }}" class="quick-link-btn">
                        <i class="bi bi-box-seam"></i>
                        Subscription Plans
                    </a>
                </div>
            </div>
            <div class="control-tower-card">
                <div class="card-header-custom">
                    <h6 class="card-header-title mb-0">Orders by Status</h6>
                </div>
                <div class="p-4">
                    @forelse($orderStatusCounts as $status => $count)
                    <div class="status-row">
                        <span class="status-row-label">
                            <span class="status-dot {{ $status }}"></span>
                            {{ ucwords(str_replace('_', ' ', $status)) }}
                        </span>
                        <span class="status-row-value">{{ $count }}</span>
                    </div>
                    @empty
                    <div class="text-muted text-center py-4">No active orders</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid-custom mb-4">
    <div class="table-card-admin">
        <div class="table-header-admin">
            <h4 class="table-header-title">
                <i class="bi bi-list-ul text-primary me-2"></i>Active Orders
            </h4>
            <span class="badge badge-primary">{{ $activeOrders->count() }} orders</span>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Restaurant</th>
                        <th>Rider</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activeOrders as $order)
                    <tr>
                        <td><strong class="text-primary">{{ $order->order_number }}</strong></td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ $order->restaurant->name }}</td>
                        <td>
                            @if($order->rider)
                            <span class="text-success"><i class="bi bi-motorcycle me-1"></i>{{ $order->rider->user->name }}</span>
                            @else
                            <span class="text-muted">Unassigned</span>
                            @endif
                        </td>
                        <td>
                            <span class="status-badge {{ $order->status }}">
                                <span class="status-dot {{ $order->status }}"></span>
                                {{ ucwords(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </td>
                        <td class="fw-bold">${{ number_format($order->total, 2) }}</td>
                        <td class="text-muted">{{ $order->created_at->diffForHumans() }}</td>
                        <td>
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-secondary me-1">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if(!$order->rider)
                            <form method="POST" action="{{ route('admin.orders.assign', $order->id) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="dispatch-btn">Assign</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="bi bi-check-circle display-4 d-block mb-3 text-success"></i>
                            <p class="text-muted mb-0">No active orders</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="container-fluid-custom">
    <div class="table-card-admin">
        <div class="table-header-admin">
            <div>
                <h4 class="table-header-title">
                    <i class="bi bi-shop text-primary me-2"></i>Restaurants
                </h4>
                <small class="text-muted">{{ $restaurants->count() }} total
                    @if($pendingRestaurants->count() > 0)
                    · <span class="text-warning">{{ $pendingRestaurants->count() }} pending</span>
                    @endif
                </small>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table mb-0" id="restaurants-table">
                <thead>
                    <tr>
                        <th>Restaurant</th>
                        <th>Owner</th>
                        <th>Status</th>
                        <th>Orders</th>
                        <th>Revenue</th>
                        <th>Rating</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($restaurants as $restaurant)
                    <tr class="{{ $restaurant->status !== 'active' || !$restaurant->is_open ? '' : '' }}">
                        <td>
                            <strong>{{ $restaurant->name }}</strong>
                            @if(!$restaurant->is_open)
                            <span class="badge badge-warning ms-1">Closed</span>
                            @endif
                        </td>
                        <td>
                            <div>{{ $restaurant->owner_name }}</div>
                            <small class="text-muted">{{ $restaurant->owner_email }}</small>
                        </td>
                        <td>
                            @if($restaurant->status === 'active' && $restaurant->is_open)
                            <span class="badge badge-success">Active</span>
                            @elseif($restaurant->status !== 'active')
                            <span class="badge badge-danger">{{ ucfirst($restaurant->status) }}</span>
                            @else
                            <span class="badge badge-warning">Pending</span>
                            @endif
                        </td>
                        <td class="fw-bold">{{ $restaurant->total_orders }}</td>
                        <td class="fw-bold text-success">${{ number_format($restaurant->total_revenue, 2) }}</td>
                        <td>
                            @if($restaurant->rating > 0)
                            <span class="rating-star"><i class="bi bi-star-fill"></i> {{ number_format($restaurant->rating, 1) }}</span>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-muted">{{ $restaurant->created_at->format('M d, Y') }}</td>
                        <td>
                            @if($restaurant->status !== 'active' || !$restaurant->is_open)
                            <form method="POST" action="{{ route('admin.restaurants.approve', $restaurant->id) }}" class="d-inline">
                                @csrf @method('PUT')
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="bi bi-check-lg"></i> Approve
                                </button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('admin.restaurants.toggle', $restaurant->id) }}" class="d-inline">
                                @csrf @method('PUT')
                                <button type="submit" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-power"></i> {{ $restaurant->is_open ? 'Close' : 'Open' }}
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="bi bi-shop display-4 d-block mb-3 text-muted" style="opacity: 0.5;"></i>
                            <p class="text-muted mb-0">No restaurants yet</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let map, mapInitialized = false;

function initRiderMap() {
    if (mapInitialized) return;
    mapInitialized = true;
    var container = document.getElementById('riderMap');
    if (!container) return;
    map = L.map('riderMap').setView([30.0444, 31.2357], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);
    var riders = @json($riders);
    var bounds = [];
    riders.forEach(function(rider) {
        if (rider.location && rider.location.latitude && rider.location.longitude) {
            var color = rider.status === 'available' ? '#10B981' : '#3B82F6';
            var marker = L.marker([rider.location.latitude, rider.location.longitude], {
                icon: L.divIcon({
                    html: '<div style="background:' + color + ';width:28px;height:28px;border-radius:50%;border:3px solid white;box-shadow:0 2px 8px rgba(0,0,0,0.3);display:flex;align-items:center;justify-content:center;color:white;font-size:14px;"><i class="bi bi-motorcycle"></i></div>',
                    className: '', iconSize: [28, 28], iconAnchor: [14, 14]
                })
            }).addTo(map);
            marker.bindPopup('<strong>' + rider.name + '</strong><br>Status: ' + rider.status);
            bounds.push([rider.location.latitude, rider.location.longitude]);
        }
    });
    if (bounds.length > 0) map.fitBounds(bounds, { padding: [50, 50] });

    setInterval(refreshRiderMarkers, 30000);
}

var riderMarkers = {};

function refreshRiderMarkers() {
    fetch("{{ route('admin.riders.locations') }}")
        .then(function (r) { return r.json() })
        .then(function (data) {
            data.forEach(function(rider) {
                if (rider.latitude && rider.longitude) {
                    if (riderMarkers[rider.id]) {
                        riderMarkers[rider.id].setLatLng([rider.latitude, rider.longitude]);
                    } else {
                        var color = rider.status === 'available' ? '#10B981' : '#3B82F6';
                        var marker = L.marker([rider.latitude, rider.longitude], {
                            icon: L.divIcon({
                                html: '<div style="background:' + color + ';width:28px;height:28px;border-radius:50%;border:3px solid white;box-shadow:0 2px 8px rgba(0,0,0,0.3);display:flex;align-items:center;justify-content:center;color:white;font-size:14px;"><i class="bi bi-motorcycle"></i></div>',
                                className: '', iconSize: [28, 28], iconAnchor: [14, 14]
                            })
                        }).addTo(map);
                        marker.bindPopup('<strong>' + rider.name + '</strong><br>Status: ' + rider.status);
                        riderMarkers[rider.id] = marker;
                    }
                }
            });
        })
        .catch(function () {});
}

function toggleMapFullscreen() {
    var card = document.getElementById('mapCard');
    var sidebar = document.getElementById('mapSidebar');
    var text = document.getElementById('expandText');
    if (!card || !sidebar) return;
    if (card.classList.contains('col-lg-8')) {
        card.classList.remove('col-lg-8');
        card.classList.add('col-12');
        text.textContent = 'Shrink';
        sidebar.style.display = 'none';
    } else {
        card.classList.remove('col-12');
        card.classList.add('col-lg-8');
        text.textContent = 'Expand';
        sidebar.style.display = '';
    }
    if (map) setTimeout(function() { map.invalidateSize() }, 300);
}

(function autoRefreshRestaurants() {
    if (typeof window._restaurantsRefreshStarted !== 'undefined') return;
    window._restaurantsRefreshStarted = true;

    setInterval(function () {
        fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.text() })
            .then(function (html) {
                var parser = new DOMParser();
                var doc = parser.parseFromString(html, 'text/html');
                var oldBody = document.querySelector('#restaurants-table tbody');
                var newBody = doc.querySelector('#restaurants-table tbody');
                if (oldBody && newBody && oldBody.innerHTML !== newBody.innerHTML) {
                    oldBody.innerHTML = newBody.innerHTML;
                }
            })
            .catch(function () {});
    }, 30000);
})();

function initCharts() {
    var trendCtx = document.getElementById('trendChart');
    if (trendCtx) {
        var days = @json($ordersByDay->pluck('date'));
        var counts = @json($ordersByDay->pluck('count'));
        var revenues = @json($ordersByDay->pluck('revenue'));
        new Chart(trendCtx, {
            type: 'bar',
            data: {
                labels: days.map(function(d) { return new Date(d).toLocaleDateString('en', { weekday: 'short', month: 'short', day: 'numeric' }); }),
                datasets: [{
                    label: 'Orders',
                    data: counts,
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: '#3B82F6',
                    borderWidth: 0,
                    borderRadius: 6,
                    order: 2
                }, {
                    label: 'Revenue ($)',
                    data: revenues,
                    type: 'line',
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#10B981',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    borderWidth: 2,
                    order: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { family: "'DM Sans', sans-serif", size: 12 }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { font: { family: "'DM Sans', sans-serif", size: 11 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: "'DM Sans', sans-serif", size: 11 } }
                    }
                }
            }
        });
    }
    var statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        var statusData = @json($orderStatusAll);
        var labels = Object.keys(statusData).map(function(s) { return s.replace(/_/g, ' '); });
        var values = Object.values(statusData);
        var colors = { placed: '#06B6D4', confirmed: '#F59E0B', preparing: '#8B5CF6', on_the_way: '#10B981', delivered: '#10B981', cancelled: '#EF4444' };
        var bgColors = labels.map(function(l) { return colors[l.replace(/ /g, '_')] || '#64748B'; });
        new Chart(statusCtx, {
            type: 'doughnut',
            data: { labels: labels, datasets: [{ data: values, backgroundColor: bgColors, borderWidth: 2, borderColor: 'white' }] },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            padding: 12,
                            boxWidth: 12,
                            font: { family: "'DM Sans', sans-serif", size: 12 },
                            usePointStyle: true
                        }
                    }
                },
                cutout: '65%'
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    initRiderMap();
    initCharts();
});
</script>
@endsection