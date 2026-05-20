@extends('layouts.dashboard')
@section('page_title', 'Create Package')

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
    .form-card {
        background: white;
        border-radius: var(--radius-lg);
        padding: 40px;
        box-shadow: var(--shadow-sm);
    }
    .form-label {
        font-weight: 600;
        color: var(--dark-charcoal);
        margin-bottom: 8px;
    }
    .form-control, .form-select {
        border: 2px solid var(--off-white);
        border-radius: var(--radius-md);
        padding: 12px 16px;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--primary-orange);
        box-shadow: 0 0 0 4px rgba(255, 107, 53, 0.1);
    }
</style>
@endpush

@section('content')
<section class="page-header">
    <div class="container-fluid-custom">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="text-white" style="font-family: 'Playfair Display', serif; font-size: 36px;">Create Package</h1>
                <p class="text-white-50 mb-0">Add a new subscription plan</p>
            </div>
            <a href="{{ route('admin.packages.index') }}" class="btn btn-outline-light">
                <i class="bi bi-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>
</section>

<div class="container-fluid-custom">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="form-card">
                <form method="POST" action="{{ route('admin.packages.store') }}">
                    @csrf

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Package Name *</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Basic Pro" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Billing Cycle *</label>
                            <select name="billing_cycle" class="form-select" required>
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Price *</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" placeholder="e.g. 29.99" required>
                            @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Describe what this package includes...">{{ old('description') }}</textarea>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Max Menu Items</label>
                            <input type="number" name="max_menu_items" class="form-control" value="{{ old('max_menu_items', 50) }}" placeholder="-1 for unlimited">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" name="includes_ads" class="form-check-input" id="includes_ads" value="1">
                                <label class="form-check-label" for="includes_ads">Includes Ads</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" name="is_featured" class="form-check-input" id="is_featured" value="1">
                                <label class="form-check-label" for="is_featured">Featured</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" checked>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-check-lg me-2"></i>Create Package
                        </button>
                        <a href="{{ route('admin.packages.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection