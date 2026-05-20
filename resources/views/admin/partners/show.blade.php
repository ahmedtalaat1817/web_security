@extends('layouts.dashboard')
@section('page_title', 'Partner Details')

@section('sidebar_menu')
    @include('admin.partials.sidebar')
@endsection

@push('styles')
<style>
    .detail-card {
        background: white;
        border-radius: var(--radius-lg);
        padding: 24px;
        box-shadow: var(--shadow-sm);
        margin-bottom: 24px;
    }

    .detail-card h5 {
        font-family: 'Playfair Display', serif;
        color: var(--dark-charcoal);
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid var(--off-white);
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid var(--off-white);
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        color: var(--muted-gray);
        font-weight: 500;
    }

    .info-value {
        color: var(--dark-charcoal);
        font-weight: 600;
        text-align: right;
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

    .restaurant-status {
        padding: 8px 16px;
        border-radius: var(--radius-md);
        font-weight: 600;
    }

    .restaurant-active {
        background: rgba(40, 167, 69, 0.1);
        color: var(--success);
    }

    .restaurant-pending {
        background: rgba(255, 193, 7, 0.1);
        color: var(--warning);
    }
</style>
@endpush

@section('content')
<div class="section-padding bg-light">
    <div class="container-fluid-custom">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="section-title mb-0">Partner Details</h2>
                <p class="text-muted mb-0">View restaurant partner information</p>
            </div>
            <a href="{{ route('admin.partners.index') }}" class="btn btn-outline-custom">
                <i class="bi bi-arrow-left me-2"></i>Back to Partners
            </a>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="detail-card">
                    <h5><i class="bi bi-shop-window me-2"></i>Restaurant Information</h5>
                    <div class="info-row">
                        <span class="info-label">Restaurant Name</span>
                        <span class="info-value">{{ $user->restaurant_name ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Address</span>
                        <span class="info-value">{{ $user->restaurant_address ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Phone</span>
                        <span class="info-value">{{ $user->phone ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email</span>
                        <span class="info-value">{{ $user->email }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Commercial Reg.#</span>
                        <span class="info-value">{{ $user->commercial_registration_number ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Tax ID</span>
                        <span class="info-value">{{ $user->tax_id ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Restaurant Status</span>
                        <span class="info-value">
                            @if($user->restaurant)
                            <span class="restaurant-status {{ $user->restaurant->status === 'active' ? 'restaurant-active' : 'restaurant-pending' }}">
                                {{ ucfirst($user->restaurant->status) }}
                            </span>
                            @else
                            <span class="text-muted">Not created</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="detail-card">
                    <h5><i class="bi bi-person-badge me-2"></i>Owner Information</h5>
                    <div class="info-row">
                        <span class="info-label">Owner Name</span>
                        <span class="info-value">{{ $user->owner_name ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">National ID</span>
                        <span class="info-value">{{ $user->national_id ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email Verified</span>
                        <span class="info-value">
                            @if($user->email_verified_at)
                            <span class="text-success"><i class="bi bi-check-circle me-1"></i>Yes</span>
                            @else
                            <span class="text-muted">No</span>
                            @endif
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Account Created</span>
                        <span class="info-value">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                </div>

                <div class="detail-card">
                    <h5><i class="bi bi-box-seam me-2"></i>Subscription Details</h5>
                    <div class="info-row">
                        <span class="info-label">Package</span>
                        <span class="info-value">{{ $user->partnerPackage?->name ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Price</span>
                        <span class="info-value">
                            ${{ number_format($user->partnerPackage?->price ?? 0, 2) }}
                            /{{ $user->partnerPackage?->billing_cycle }}
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Includes Ads</span>
                        <span class="info-value">
                            @if($user->partnerPackage?->includes_ads)
                            <span class="text-success"><i class="bi bi-check-circle me-1"></i>Yes</span>
                            @else
                            <span class="text-muted">No</span>
                            @endif
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Partner Status</span>
                        <span class="info-value">
                            @if($user->partner_status === 'active')
                            <span class="status-badge status-active">
                                <i class="bi bi-check-circle me-1"></i>Active
                            </span>
                            @elseif($user->partner_status === 'pending_payment')
                            <span class="status-badge status-pending">
                                <i class="bi bi-clock me-1"></i>Pending Payment
                            </span>
                            @else
                            <span class="status-badge status-suspended">
                                <i class="bi bi-x-circle me-1"></i>{{ ucfirst($user->partner_status) }}
                            </span>
                            @endif
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Partner Since</span>
                        <span class="info-value">
                            @if($user->partner_since)
                            {{ $user->partner_since->format('M d, Y') }}
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-card">
            <h5><i class="bi bi-gear me-2"></i>Actions</h5>
            <form method="POST" action="{{ route('admin.partners.update-status', $user->id) }}" class="d-flex align-items-center gap-3">
                @csrf
                @method('PUT')
                <label class="mb-0">Change Status:</label>
                <select name="status" class="form-select" style="width: auto;">
                    <option value="active" {{ $user->partner_status === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="pending_payment" {{ $user->partner_status === 'pending_payment' ? 'selected' : '' }}>Pending Payment</option>
                    <option value="suspended" {{ $user->partner_status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
                <button type="submit" class="btn btn-primary-custom">
                    <i class="bi bi-check2 me-2"></i>Update Status
                </button>
            </form>
        </div>
    </div>
</div>
@endsection