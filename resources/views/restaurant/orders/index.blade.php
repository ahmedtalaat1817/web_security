@extends('layouts.app')
@section('title', 'Restaurant Orders')

@section('styles')
<style>
    .orders-page-header {
        background: linear-gradient(135deg, var(--dark-charcoal), var(--dark-secondary));
        padding: 40px 0;
        margin-bottom: 32px;
    }

    .orders-page-title {
        font-family: 'Playfair Display', serif;
        font-size: 36px;
        font-weight: 700;
        color: white;
    }

    .filter-pills {
        background: white;
        border-radius: var(--radius-lg);
        padding: 12px 16px;
        box-shadow: var(--shadow-sm);
        margin-bottom: 24px;
        overflow-x: auto;
        white-space: nowrap;
    }

    .filter-pill {
        display: inline-flex;
        align-items: center;
        padding: 8px 18px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 13px;
        text-decoration: none;
        transition: var(--transition-fast);
        margin: 2px 4px;
    }

    .filter-pill:hover {
        background: var(--primary);
        color: white;
    }

    .filter-pill.active {
        background: var(--primary);
        color: white;
    }

    .new-order-badge {
        background: var(--danger);
        color: white;
        padding: 2px 8px;
        border-radius: 50px;
        font-size: 11px;
        margin-left: 6px;
    }

    .order-table-card {
        background: white;
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }

    .order-table-card .table th {
        background: var(--bg-tertiary);
        padding: 16px 20px;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none;
    }

    .order-table-card .table td {
        padding: 16px 20px;
        vertical-align: middle;
        border-color: var(--light-gray);
    }

    .auto-refresh-indicator {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: var(--muted-gray);
    }

    .auto-refresh-indicator .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--success-green);
        animation: pulse-dot 2s ease-in-out infinite;
    }

    @keyframes pulse-dot {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.4; }
    }
</style>
@endsection

@section('content')
<!-- Page Header -->
<section class="orders-page-header">
    <div class="container-fluid-custom">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="orders-page-title">Orders</h1>
                <p class="text-white-75 mt-2" style="color: rgba(255,255,255,0.75) !important;">Manage incoming orders from customers</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('restaurant.dashboard') }}" class="btn btn-light">Dashboard</a>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<div class="container-fluid-custom">
    <!-- Auto-refresh indicator -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="auto-refresh-indicator">
            <span class="dot"></span>
            <span>Auto-refreshing every 30s</span>
        </div>
        <span class="text-muted small">{{ $orders->total() }} total orders</span>
    </div>

    <!-- Filter Pills -->
    @php
        $filters = [
            'placed' => 'New',
            'confirmed' => 'Confirmed',
            'preparing' => 'Preparing',
            'on_the_way' => 'On the way',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
        ];
        $currentStatus = request('status');
    @endphp

    <div class="filter-pills">
        <a href="{{ route('restaurant.orders') }}" class="filter-pill {{ $currentStatus === null || $currentStatus === '' ? 'active' : '' }}">All</a>
        @foreach($filters as $value => $label)
            <a href="{{ route('restaurant.orders', ['status' => $value]) }}" class="filter-pill {{ (string) $currentStatus === (string) $value ? 'active' : '' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <!-- Orders Table -->
    <div class="order-table-card">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
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
                        <td class="text-end">
                            <a href="{{ route('restaurant.orders.show', $order) }}" class="btn btn-primary-custom btn-sm">
                                View <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox display-4 d-block mb-3"></i>
                            No orders yet
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
        <div class="p-3 border-top">{{ $orders->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
(function() {
    let refreshInterval = 30000;

    function autoRefresh() {
        const url = new URL(window.location.href);
        const currentPath = url.pathname + url.search;

        fetch(currentPath, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const oldTable = document.querySelector('.order-table-card .table tbody');
            const newTable = doc.querySelector('.order-table-card .table tbody');

            if (oldTable && newTable && oldTable.innerHTML !== newTable.innerHTML) {
                const oldCount = document.querySelectorAll('.order-table-card .table tbody tr').length;
                const newCount = newTable.querySelectorAll('tr').length;

                if (newCount > oldCount) {
                    showNewOrderToast(newCount - oldCount);
                }

                document.querySelector('.order-table-card').innerHTML = doc.querySelector('.order-table-card').innerHTML;

                const totalEl = document.querySelector('.text-muted.small');
                if (totalEl && doc.querySelector('.text-muted.small')) {
                    totalEl.textContent = doc.querySelector('.text-muted.small').textContent;
                }
            }
        })
        .catch(() => {});
    }

    function showNewOrderToast(count) {
        const existing = document.querySelector('.toast-notification');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.className = 'toast-notification';
        toast.innerHTML = '<i class="bi bi-bell-fill me-2"></i>' + count + ' new order' + (count > 1 ? 's' : '') + ' received!';
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
            border-left: 4px solid var(--primary-orange);
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
        }, 5000);
    }

    if (!document.querySelector('meta[name="auto-refresh"]')) {
        setInterval(autoRefresh, refreshInterval);
    }
})();
</script>
@endsection