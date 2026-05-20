@extends('layouts.dashboard')
@section('page_title', 'Subscription Plans')

@section('sidebar_menu')
    @include('admin.partials.sidebar')
@endsection

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, var(--dark-charcoal), var(--dark-secondary));
        padding: 40px 0;
        margin-bottom: 32px;
    }
    .package-table {
        background: white;
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }
    .package-table th {
        background: var(--off-white);
        font-weight: 600;
        padding: 16px;
        border-bottom: 2px solid var(--border-default);
    }
    .package-table td {
        padding: 16px;
        vertical-align: middle;
        border-bottom: 1px solid var(--border-subtle);
    }
</style>
@endpush

@section('content')
<section class="page-header">
    <div class="container-fluid-custom">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="text-white" style="font-family: 'Playfair Display', serif; font-size: 36px;">Subscription Plans</h1>
                <p class="text-white-50 mb-0">Manage restaurant partner packages</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.packages.create') }}" class="btn btn-primary-custom">
                    <i class="bi bi-plus-lg me-2"></i>New Package
                </a>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light">Dashboard</a>
            </div>
        </div>
    </div>
</section>

<div class="container-fluid-custom">
    @if(session('success'))
    <div class="alert alert-success border-0 rounded-3 mb-4">{{ session('success') }}</div>
    @endif

    <div class="package-table">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Billing</th>
                    <th>Ads</th>
                    <th>Max Items</th>
                    <th>Featured</th>
                    <th>Active</th>
                    <th>Subscribers</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($packages as $p)
                <tr>
                    <td><strong>{{ $p->name }}</strong></td>
                    <td>${{ number_format($p->price, 2) }}</td>
                    <td>{{ ucfirst($p->billing_cycle) }}</td>
                    <td>
                        @if($p->includes_ads)
                        <span class="badge bg-success"><i class="bi bi-check"></i></span>
                        @else
                        <span class="badge bg-secondary"><i class="bi bi-x"></i></span>
                        @endif
                    </td>
                    <td>{{ $p->max_menu_items == -1 ? 'Unlimited' : $p->max_menu_items }}</td>
                    <td>
                        @if($p->is_featured)
                        <span class="badge bg-warning text-dark">Featured</span>
                        @else
                        <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if($p->is_active)
                        <span class="badge bg-success">Active</span>
                        @else
                        <span class="badge bg-danger">Inactive</span>
                        @endif
                    </td>
                    <td>{{ $p->users->count() }}</td>
                    <td>
                        <a href="{{ route('admin.packages.edit', $p->id) }}" class="btn btn-sm btn-outline-custom">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @if($p->is_active)
                        <form method="POST" action="{{ route('admin.packages.destroy', $p->id) }}" class="d-inline" onsubmit="return confirm('Deactivate this package?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-x-circle"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-5 text-muted">
                        <i class="bi bi-box display-4 d-block mb-3"></i>
                        No packages created yet
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection