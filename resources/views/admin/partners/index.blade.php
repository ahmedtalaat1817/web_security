@extends('layouts.dashboard')
@section('page_title', 'Restaurant Partners')

@section('sidebar_menu')
    @include('admin.partials.sidebar')
@endsection

@push('styles')
<style>
    .stat-card {
        background: white;
        border-radius: var(--radius-lg);
        padding: 24px;
        box-shadow: var(--shadow-sm);
        transition: var(--transition-fast);
    }

    .stat-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .stat-number {
        font-size: 32px;
        font-weight: 700;
        color: var(--dark-charcoal);
    }

    .stat-label {
        color: var(--muted-gray);
        font-size: 14px;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .stat-icon.primary {
        background: rgba(255, 107, 53, 0.1);
        color: var(--primary-orange);
    }

    .stat-icon.success {
        background: rgba(40, 167, 69, 0.1);
        color: var(--success);
    }

    .stat-icon.warning {
        background: rgba(255, 193, 7, 0.1);
        color: var(--warning);
    }

    .stat-icon.danger {
        background: rgba(220, 53, 69, 0.1);
        color: var(--danger);
    }

    .partner-table {
        background: white;
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }

    .partner-table th {
        background: var(--off-white);
        font-weight: 600;
        padding: 16px;
        border-bottom: 2px solid var(--border-default);
    }

    .partner-table td {
        padding: 16px;
        vertical-align: middle;
        border-bottom: 1px solid var(--border-subtle);
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-active {
        background: rgba(40, 167, 69, 0.1);
        color: var(--success);
    }

    .status-pending {
        background: rgba(255, 193, 7, 0.1);
        color: var(--warning);
    }

    .status-suspended {
        background: rgba(220, 53, 69, 0.1);
        color: var(--danger);
    }

    .partner-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .partner-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-orange), var(--primary-orange-dark));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="section-padding bg-light">
    <div class="container-fluid-custom">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="section-title mb-0">Restaurant Partners</h2>
                <p class="text-muted mb-0">Manage restaurant partner registrations</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-custom">
                <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-number">{{ $stats['total_partners'] }}</div>
                            <div class="stat-label">Total Partners</div>
                        </div>
                        <div class="stat-icon primary">
                            <i class="bi bi-shop"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-number">{{ $stats['active_partners'] }}</div>
                            <div class="stat-label">Active Partners</div>
                        </div>
                        <div class="stat-icon success">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-number">{{ $stats['pending_partners'] }}</div>
                            <div class="stat-label">Pending Payment</div>
                        </div>
                        <div class="stat-icon warning">
                            <i class="bi bi-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-number">{{ $stats['suspended_partners'] }}</div>
                            <div class="stat-label">Suspended</div>
                        </div>
                        <div class="stat-icon danger">
                            <i class="bi bi-x-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="partner-table">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Restaurant</th>
                        <th>Owner</th>
                        <th>Package</th>
                        <th>Status</th>
                        <th>Since</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($partners as $partner)
                    <tr>
                        <td>
                            <div class="partner-info">
                                <div class="partner-avatar">
                                    {{ substr($partner->restaurant_name ?? 'R', 0, 1) }}
                                </div>
                                <div>
                                    <strong>{{ $partner->restaurant_name ?? 'N/A' }}</strong>
                                    <div class="text-muted small">{{ $partner->restaurant_address ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>{{ $partner->owner_name ?? 'N/A' }}</div>
                            <div class="text-muted small">{{ $partner->email }}</div>
                            <div class="text-muted small">{{ $partner->phone }}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                {{ $partner->partnerPackage?->name ?? 'N/A' }}
                            </span>
                            <div class="text-muted small">
                                ${{ number_format($partner->partnerPackage?->price ?? 0, 2) }}
                                /{{ $partner->partnerPackage?->billing_cycle }}
                            </div>
                        </td>
                        <td>
                            @if($partner->partner_status === 'active')
                            <span class="status-badge status-active">
                                <i class="bi bi-check-circle me-1"></i>Active
                            </span>
                            @elseif($partner->partner_status === 'pending_payment')
                            <span class="status-badge status-pending">
                                <i class="bi bi-clock me-1"></i>Pending Payment
                            </span>
                            @else
                            <span class="status-badge status-pending">
                                <i class="bi bi-clock me-1"></i>{{ $partner->partner_status ? ucfirst($partner->partner_status) : 'N/A' }}
                            </span>
                            @endif
                        </td>
                        <td>
                            @if($partner->partner_since)
                            {{ $partner->partner_since->format('M d, Y') }}
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.partners.show', $partner->id) }}" class="btn btn-sm btn-outline-custom">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-shop display-1 text-muted"></i>
                            <h4 class="mt-3 text-muted">No restaurant partners yet</h4>
                            <p class="text-muted">Partner registrations will appear here</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $partners->links() }}
        </div>
    </div>
</div>
@endsection