@extends('layouts.app')

@push('styles')
<style>
    .payment-hero {
        background: linear-gradient(135deg, var(--dark-charcoal), var(--dark-secondary));
        padding: 60px 0;
    }

    .payment-card {
        background: white;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg);
        padding: 40px;
        margin-top: -40px;
        margin-bottom: 60px;
    }

    .payment-summary {
        background: linear-gradient(135deg, rgba(255, 107, 53, 0.1), rgba(255, 107, 53, 0.05));
        border-radius: var(--radius-md);
        padding: 24px;
        margin-bottom: 24px;
        border-left: 4px solid var(--primary-orange);
    }

    .payment-amount {
        font-size: 36px;
        font-weight: 700;
        color: var(--primary-orange);
    }

    .payment-methods {
        display: flex;
        gap: 16px;
        margin-bottom: 24px;
    }

    .payment-method {
        flex: 1;
        padding: 20px;
        border: 2px solid var(--off-white);
        border-radius: var(--radius-md);
        text-align: center;
        cursor: pointer;
        transition: var(--transition-fast);
    }

    .payment-method:hover {
        border-color: var(--primary-orange);
    }

    .payment-method.selected {
        border-color: var(--primary-orange);
        background: rgba(255, 107, 53, 0.05);
    }

    .payment-method i {
        font-size: 32px;
        color: var(--primary-orange);
    }

    .paypal-btn {
        background: #0070ba;
        color: white;
        border: none;
        padding: 16px 32px;
        border-radius: var(--radius-md);
        font-size: 18px;
        font-weight: 600;
        width: 100%;
        transition: var(--transition-fast);
    }

    .paypal-btn:hover {
        background: #005ea6;
    }

    .secure-badge {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        color: var(--muted-gray);
        font-size: 14px;
    }

    .info-icon {
        color: var(--primary-orange);
    }
</style>
@endpush

@section('content')
<section class="payment-hero">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h1 class="text-white">Complete Payment</h1>
                <p class="text-white-50">You're almost there! Complete your registration</p>
            </div>
        </div>
    </div>
</section>

<div class="container-fluid-custom">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="payment-card">
                <div class="payment-summary">
                    <h5 class="mb-3"><i class="bi bi-receipt me-2"></i>Order Summary</h5>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Package</span>
                        <strong>{{ $package->name }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Billing</span>
                        <span>{{ ucfirst($package->billing_cycle) }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Total</span>
                        <div class="payment-amount">${{ number_format($package->price, 2) }}</div>
                    </div>
                </div>

                <div class="mb-4">
                    <h5 class="mb-3"><i class="bi bi-shield-lock me-2"></i>Payment Method</h5>
                    <div class="payment-methods">
                        <div class="payment-method selected">
                            <i class="bi bi-paypal"></i>
                            <div class="mt-2 fw-bold">PayPal</div>
                            <small class="text-muted">Fast & Secure</small>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-3">
                    <a href="{{ $approveUrl }}" class="paypal-btn">
                        <i class="bi bi-paypal me-2"></i>Pay with PayPal
                    </a>

                    <div class="text-center">
                        <a href="{{ route('partner.payment.cancel', ['user' => $user->id]) }}" class="text-muted text-decoration-none">
                            Cancel and go back
                        </a>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="secure-badge">
                        <i class="bi bi-shield-check"></i>
                        <span>Secure payment powered by PayPal</span>
                    </div>
                    <p class="text-muted small text-center mt-2">
                        Your payment is secured with 256-bit SSL encryption.<br>
                        We don't store your payment details.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection