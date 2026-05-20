@extends('layouts.dashboard')
@section('page_title', 'Restaurant Dashboard')
@section('sidebar_menu')
<div class="nav-item">
    <a href="{{ route('restaurant.dashboard') }}" class="nav-link active">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
</div>
<div class="nav-item">
    <a href="{{ route('restaurant.orders') }}" class="nav-link">
        <i class="bi bi-bag"></i> Orders
    </a>
</div>
<div class="nav-item">
    <a href="{{ route('restaurant.menu.index') }}" class="nav-link">
        <i class="bi bi-menu-button-wide"></i> Menu
    </a>
</div>
<div class="nav-item">
    <a href="{{ route('restaurant.profile') }}" class="nav-link">
        <i class="bi bi-shop"></i> Restaurant Profile
    </a>
</div>
<div class="nav-item mt-3">
    <a href="{{ route('home') }}" class="nav-link">
        <i class="bi bi-arrow-left"></i> Back to Home
    </a>
</div>
@endsection

@section('styles')
<style>
    .stat-card-modern {
        background: white;
        border-radius: var(--radius-lg);
        padding: 28px;
        box-shadow: var(--shadow-sm);
        transition: var(--transition-normal);
        height: 100%;
    }

    .stat-card-modern:hover {
        transform: translateY(-6px);
        box-shadow: var(--shadow-lg);
    }

    .stat-icon-lg {
        width: 64px;
        height: 64px;
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        margin-bottom: 20px;
    }

    .stat-value-lg {
        font-size: 36px;
        font-weight: 700;
        color: var(--text-primary);
        line-height: 1;
    }

    .stat-label-lg {
        color: var(--muted-gray);
        font-size: 15px;
        margin-top: 8px;
    }

    .table-card {
        background: white;
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }

    .table-header-modern {
        padding: 24px;
        border-bottom: 1px solid var(--light-gray);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .table-title-modern {
        font-size: 20px;
        font-weight: 700;
        color: var(--text-primary);
    }

    .table thead th {
        background: var(--bg-tertiary);
        padding: 16px 20px;
        font-weight: 600;
        color: var(--text-primary);
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none;
    }

    .table td {
        padding: 16px 20px;
        vertical-align: middle;
        border-color: var(--light-gray);
    }

    .quick-action-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        padding: 16px 24px;
        background: white;
        border-radius: var(--radius-lg);
        text-decoration: none;
        color: var(--text-primary);
        font-weight: 600;
        transition: var(--transition-normal);
        box-shadow: var(--shadow-sm);
    }

    .quick-action-btn:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        color: var(--primary-orange);
    }

    .quick-action-btn i {
        font-size: 20px;
    }

    .category-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid var(--light-gray);
    }

    .category-item:last-child {
        border-bottom: none;
    }

    .category-count {
        background: var(--primary-orange);
        color: white;
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
    }
</style>
@endsection

@section('content')
@if(session('status'))
<div class="alert alert-success border-0 rounded-3 mb-4" style="background: var(--success-light);">
    <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
</div>
@endif

@if(!$restaurant->is_open)
<div class="alert alert-warning border-0 rounded-3 mb-4 d-flex align-items-center gap-2" style="background: var(--warning-light);">
    <i class="bi bi-shield-exclamation fs-4"></i>
    <div>
        <strong>Restaurant Not Visible to Customers</strong><br>
        <span>Your restaurant is currently closed. An admin needs to approve it before customers can see it, or you can toggle it open from your
        <a href="{{ route('restaurant.profile') }}" class="text-decoration-underline fw-semibold">Profile Settings</a>.</span>
    </div>
</div>
@elseif($restaurant->status === 'active' && $restaurant->is_open)
<div class="alert alert-success border-0 rounded-3 mb-4 d-flex align-items-center gap-2" style="background: var(--success-light);">
    <i class="bi bi-check-circle-fill fs-4"></i>
    <div>
        <strong>Your Restaurant is Active</strong><br>
        <span>Your restaurant is visible to customers and accepting orders.</span>
    </div>
</div>
@endif

<!-- Stats Row -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card-modern">
            <div class="stat-icon-lg" style="background: var(--primary); color: white;">
                <i class="bi bi-bag"></i>
            </div>
            <div class="stat-value-lg">{{ $todayOrders }}</div>
            <div class="stat-label-lg">Today's Orders</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card-modern">
            <div class="stat-icon-lg" style="background: var(--success-light); color: var(--success);">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <div class="stat-value-lg">${{ number_format($todayRevenue, 2) }}</div>
            <div class="stat-label-lg">Today's Revenue</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card-modern">
            <div class="stat-icon-lg" style="background: var(--warning-light); color: var(--warning);">
                <i class="bi bi-star-fill"></i>
            </div>
            <div class="stat-value-lg">{{ number_format($restaurant->rating, 1) }}</div>
            <div class="stat-label-lg">Rating</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card-modern">
            <div class="stat-icon-lg" style="background: var(--info-light); color: var(--info);">
                <i class="bi bi-chat-square-text"></i>
            </div>
            <div class="stat-value-lg">{{ $restaurant->total_reviews }}</div>
            <div class="stat-label-lg">Total Reviews</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Orders -->
    <div class="col-lg-8">
        <div class="table-card">
            <div class="table-header-modern">
                <h3 class="table-title-modern">Recent Orders</h3>
                <a href="{{ route('restaurant.orders') }}" class="btn btn-primary-custom btn-sm">
                    View All <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                        <tr>
                            <td>
                                <strong>{{ $order->order_number }}</strong>
                                <div class="small text-muted">{{ $order->created_at->diffForHumans() }}</div>
                            </td>
                            <td>{{ $order->customer_name }}</td>
                            <td>
                                <span class="status-badge status-{{ $order->status }}">
                                    {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </td>
                            <td class="fw-bold">${{ number_format($order->total, 2) }}</td>
                            <td>
                                <a href="{{ route('restaurant.orders.show', $order->id) }}" class="btn btn-sm btn-outline-dark">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-bag-x display-4 mb-3 d-block"></i>
                                No orders yet
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card-custom p-4 mb-4">
            <h5 class="fw-bold mb-4"><i class="bi bi-lightning me-2 text-orange"></i>Quick Actions</h5>
            <div class="d-grid gap-3">
                <a href="{{ route('restaurant.menu.create') }}" class="quick-action-btn">
                    <i class="bi bi-plus-circle text-orange"></i>
                    Add Menu Item
                </a>
                <a href="{{ route('restaurant.orders', ['status' => 'placed']) }}" class="quick-action-btn">
                    <i class="bi bi-bell text-warning"></i>
                    <span>New Orders</span>
                    <span class="badge bg-danger">{{ $pendingOrders }}</span>
                </a>
            </div>
        </div>

        <!-- Categories -->
        <div class="card-custom p-4">
            <h5 class="fw-bold mb-4"><i class="bi bi-grid me-2 text-orange"></i>Menu Items</h5>
            @forelse($restaurant->categories as $category)
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <strong>{{ $category->name }}</strong>
                    <span class="badge bg-secondary">{{ $category->menuItems->count() }}</span>
                </div>
                @forelse($category->menuItems as $item)
                <div class="d-flex justify-content-between align-items-center py-2 ps-3 border-bottom">
                    <div class="small">
                        <span>{{ $item->name }}</span>
                        <span class="text-muted ms-2">${{ number_format($item->price, 2) }}</span>
                        @if(!$item->is_available)
                        <span class="badge bg-danger ms-1">Unavailable</span>
                        @endif
                    </div>
                    <div class="d-flex gap-1">
                        <a href="{{ route('restaurant.menu.edit', $item->id) }}" class="btn btn-sm btn-outline-custom" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('restaurant.menu.destroy', $item->id) }}" class="d-inline" onsubmit="return confirm('Delete this item?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="ps-3 small text-muted py-1">No items</div>
                @endforelse
            </div>
            @empty
            <p class="text-muted">No categories yet</p>
            @endforelse
            <div class="d-flex gap-2 mt-3">
                <a href="{{ route('restaurant.menu.create') }}" class="btn btn-primary-custom w-100">
                    <i class="bi bi-plus me-2"></i>Add Item
                </a>
                <button class="btn btn-outline-dark w-100" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="bi bi-plus me-2"></i>Category
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-charcoal text-white">
                <h5 class="modal-title"><i class="bi bi-grid me-2"></i>Add Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('restaurant.categories.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category Name</label>
                        <input type="text" name="name" class="form-control form-control-custom" placeholder="e.g. Appetizers" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Brief description of this category"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-custom">Create Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function() {
    let refreshInterval = 30000;

    function autoRefresh() {
        const url = new URL(window.location.href);

        fetch(url.pathname, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const oldTbody = document.querySelector('.table-card .table tbody');
            const newTbody = doc.querySelector('.table-card .table tbody');

            if (oldTbody && newTbody && oldTbody.innerHTML !== newTbody.innerHTML) {
                const oldCount = oldTbody.querySelectorAll('tr').length;
                const newCount = newTbody.querySelectorAll('tr').length;

                if (newCount > oldCount) {
                    const diff = newCount - oldCount;
                    const toast = document.createElement('div');
                    toast.style.cssText = 'position:fixed;bottom:30px;right:30px;background:white;color:var(--text-primary);padding:16px 28px;border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,0.15);z-index:99999;font-weight:600;border-left:4px solid var(--primary-orange);';
                    toast.innerHTML = '<i class="bi bi-bell-fill me-2"></i>' + diff + ' new order' + (diff > 1 ? 's' : '') + '!';
                    document.body.appendChild(toast);
                    setTimeout(() => toast.remove(), 4000);
                }

                oldTbody.innerHTML = newTbody.innerHTML;
            }
        })
        .catch(() => {});
    }

    if (!document.querySelector('meta[name="auto-refresh"]')) {
        setInterval(autoRefresh, refreshInterval);
    }
})();
</script>
@endsection