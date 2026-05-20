@extends('layouts.app')

@section('title', 'Profile')
@section('styles')
<style>
    .profile-header {
        background: linear-gradient(135deg, var(--dark-charcoal), var(--dark-secondary));
        padding: 60px 0;
        margin-bottom: 40px;
    }

    .profile-title {
        font-family: 'Playfair Display', serif;
        font-size: 40px;
        font-weight: 700;
        color: white;
    }

    .profile-card {
        background: white;
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-lg);
        overflow: hidden;
    }

    .profile-avatar-section {
        background: linear-gradient(135deg, var(--primary-orange), var(--primary-orange-dark));
        padding: 40px;
        text-align: center;
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 48px;
        color: var(--primary-orange);
    }

    .profile-name {
        font-size: 24px;
        font-weight: 700;
        color: white;
    }

    .profile-role {
        color: rgba(255,255,255,0.8);
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .profile-body {
        padding: 40px;
    }

    .form-label {
        font-weight: 600;
        color: var(--dark-charcoal);
        margin-bottom: 8px;
    }

    .form-control {
        border: 2px solid var(--off-white);
        border-radius: var(--radius-md);
        padding: 12px 16px;
        transition: var(--transition-fast);
    }

    .form-control:focus {
        border-color: var(--primary-orange);
        box-shadow: 0 0 0 4px rgba(255, 107, 53, 0.1);
    }

    .dashboard-link-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 14px 28px;
        border-radius: var(--radius-md);
        font-weight: 600;
        text-decoration: none;
        transition: var(--transition-normal);
    }

    .dashboard-link-btn:hover {
        transform: translateY(-2px);
    }

    .logout-btn {
        padding: 14px 28px;
        border-radius: var(--radius-md);
        font-weight: 600;
        transition: var(--transition-normal);
    }

    @media (max-width: 768px) {
        .profile-info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<!-- Profile Header -->
<section class="profile-header">
    <div class="container-fluid-custom">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h1 class="profile-title">My Profile</h1>
                        <p class="text-white-50 mt-2">Update your account details</p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        @if($user?->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="dashboard-link-btn bg-white text-dark">
                            <i class="bi bi-gear"></i> Admin Dashboard
                        </a>
                        @elseif($user?->isRestaurant())
                        <a href="{{ route('restaurant.dashboard') }}" class="dashboard-link-btn bg-white text-dark">
                            <i class="bi bi-shop"></i> Restaurant Dashboard
                        </a>
                        @elseif($user?->isRider())
                        <a href="{{ route('rider.dashboard') }}" class="dashboard-link-btn bg-white text-dark">
                            <i class="bi bi-motorcycle"></i> Rider Dashboard
                        </a>
                        @else
                        <a href="{{ route('restaurants.index') }}" class="dashboard-link-btn bg-white text-dark">
                            <i class="bi bi-shop-window"></i> Browse Restaurants
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Profile Content -->
<div class="container-fluid-custom">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            @if(session('status'))
            <div class="alert alert-success border-0 rounded-3 mb-4">
                <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
            </div>
            @endif

            <div class="profile-card">
                <div class="profile-avatar-section">
                    <div class="profile-avatar">
                        <i class="bi bi-person"></i>
                    </div>
                    <h3 class="profile-name">{{ $user->name ?? 'User' }}</h3>
                    <span class="profile-role">
                        @if($user?->isAdmin())
                            Administrator
                        @elseif($user?->isRestaurant())
                            Restaurant Owner
                        @elseif($user?->isRider())
                            Delivery Rider
                        @else
                            Customer
                        @endif
                    </span>
                </div>

                <div class="profile-body">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" name="name" id="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" name="email" id="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" name="phone" id="phone"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone', $user->phone) }}"
                                       placeholder="e.g. +1 (555) 123-4567">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Member Since</label>
                                <input type="text" class="form-control" value="{{ optional($user->created_at)->format('M d, Y') ?? '—' }}" disabled>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary-custom">
                                    <i class="bi bi-check-lg me-2"></i>Save Changes
                                </button>
                                <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>

                    <div class="d-flex justify-content-end pt-3">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="logout-btn btn btn-outline-danger">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection