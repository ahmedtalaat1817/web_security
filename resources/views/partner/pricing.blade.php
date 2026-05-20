@extends('layouts.app')

@push('styles')
<style>
    .pricing-hero {
        background: linear-gradient(135deg, var(--dark-charcoal), var(--dark-secondary));
        padding: 80px 0;
        position: relative;
        overflow: hidden;
    }

    .pricing-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(255, 107, 53, 0.2), transparent 70%);
    }

    .pricing-card {
        background: var(--bg-card);
        border-radius: var(--radius-lg);
        padding: 40px 30px;
        transition: var(--transition-normal);
        border: 2px solid transparent;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .pricing-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-lg);
    }

    .pricing-card.featured {
        border-color: var(--primary-orange);
        position: relative;
    }

    .pricing-card.featured::before {
        content: 'Most Popular';
        position: absolute;
        top: -12px;
        left: 50%;
        transform: translateX(-50%);
        background: var(--primary-orange);
        color: white;
        padding: 4px 16px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
    }

    .pricing-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, rgba(255, 107, 53, 0.1), rgba(255, 107, 53, 0.05));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 28px;
        color: var(--primary-orange);
    }

    .pricing-name {
        font-family: 'Playfair Display', serif;
        font-size: 24px;
        font-weight: 700;
        color: var(--dark-charcoal);
        margin-bottom: 8px;
    }

    .pricing-price {
        font-size: 42px;
        font-weight: 700;
        color: var(--primary-orange);
        margin-bottom: 8px;
    }

    .pricing-price span {
        font-size: 16px;
        color: var(--muted-gray);
        font-weight: 400;
    }

    .pricing-desc {
        color: var(--muted-gray);
        font-size: 14px;
        margin-bottom: 24px;
        flex-grow: 1;
    }

    .pricing-features {
        list-style: none;
        padding: 0;
        margin: 0 0 24px 0;
    }

    .pricing-features li {
        padding: 10px 0;
        border-bottom: 1px solid var(--border-light);
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        color: var(--text-primary);
    }

    .pricing-features li:last-child {
        border-bottom: none;
    }

    .pricing-features i {
        color: var(--primary-orange);
    }

    .pricing-btn {
        width: 100%;
        padding: 14px;
        font-size: 16px;
    }

    .billing-toggle {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-bottom: 50px;
    }

    .billing-option {
        padding: 10px 24px;
        border-radius: 50px;
        cursor: pointer;
        font-weight: 600;
        transition: var(--transition-fast);
    }

    .billing-option.active {
        background: var(--primary-orange);
        color: white;
    }

    .billing-option:not(.active) {
        background: var(--bg-off);
        color: var(--text-muted);
    }

    .section-title {
        font-family: 'Playfair Display', serif;
        font-size: 36px;
        font-weight: 700;
        color: var(--dark-charcoal);
        margin-bottom: 12px;
    }

    .section-subtitle {
        font-size: 18px;
        color: var(--muted-gray);
        margin-bottom: 40px;
    }

    .hero-title {
        font-family: 'Playfair Display', serif;
        font-size: 56px;
        font-weight: 700;
        color: white;
        line-height: 1.2;
        margin-bottom: 20px;
    }
    .hero-title span {
        color: var(--primary);
    }
    .hero-subtitle {
        font-size: 20px;
        color: rgba(255,255,255,0.8);
        margin-bottom: 40px;
    }
</style>
@endpush

@section('content')
<section class="pricing-hero">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h1 class="hero-title">Join Our <span>Restaurant Network</span></h1>
                <p class="hero-subtitle" style="color: rgba(255,255,255,0.8);">Choose the perfect plan for your business and start growing</p>
            </div>
        </div>
    </div>
</section>

<section class="section-padding" style="background:var(--bg-off);">
    <div class="container-fluid-custom">
        <div class="text-center mb-5">
            <h2 class="section-title">Select Your Plan</h2>
            <p class="section-subtitle">Flexible pricing for restaurants of all sizes</p>
        </div>

        <div class="billing-toggle">
            <div class="billing-option active" onclick="filterPackages(this, 'monthly')">Monthly</div>
            <div class="billing-option" onclick="filterPackages(this, 'yearly')">Yearly (Save 20%)</div>
        </div>

        <div class="row g-4">
            @foreach($monthly as $package)
            <div class="col-md-6 col-lg-4 package-card" data-cycle="monthly">
                <div class="pricing-card {{ $package->is_featured ? 'featured' : '' }}">
                    <div class="pricing-icon">
                        <i class="bi {{ $package->includes_ads ? 'bi-megaphone' : 'bi-shop' }}"></i>
                    </div>
                    <h3 class="pricing-name">{{ $package->name }}</h3>
                    <div class="pricing-price">${{ number_format($package->price, 2) }}<span>/{{ $package->billing_cycle }}</span></div>
                    <p class="pricing-desc">{{ $package->description }}</p>

                    <ul class="pricing-features">
                        <li><i class="bi bi-check-circle-fill"></i> Up to {{ $package->max_menu_items == -1 ? 'unlimited' : $package->max_menu_items }} menu items</li>
                        <li><i class="bi bi-check-circle-fill"></i> Online ordering system</li>
                        <li><i class="bi bi-check-circle-fill"></i> Order management dashboard</li>
                        @if($package->includes_ads)
                        <li><i class="bi bi-check-circle-fill"></i> Featured in search results</li>
                        <li><i class="bi bi-check-circle-fill"></i> Promotional badge</li>
                        @else
                        <li><i class="bi bi-x-circle"></i> Basic listing (upgrade for ads)</li>
                        @endif
                        <li><i class="bi bi-check-circle-fill"></i> 24/7 customer support</li>
                    </ul>

                    <a href="{{ route('partner.register', ['package' => $package->id]) }}" class="btn btn-primary-custom pricing-btn">
                        Get Started
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <div class="row g-4 mt-4" style="display: none;" id="yearly-packages">
            @foreach($yearly as $package)
            <div class="col-md-6 col-lg-4 package-card" data-cycle="yearly">
                <div class="pricing-card">
                    <div class="pricing-icon">
                        <i class="bi {{ $package->includes_ads ? 'bi-megaphone' : 'bi-calendar-check' }}"></i>
                    </div>
                    <h3 class="pricing-name">{{ $package->name }}</h3>
                    <div class="pricing-price">${{ number_format($package->price, 2) }}<span>/year</span></div>
                    <p class="pricing-desc">{{ $package->description }}</p>

                    <ul class="pricing-features">
                        <li><i class="bi bi-gift"></i> Save 20% with yearly billing</li>
                        <li><i class="bi bi-check-circle-fill"></i> Up to {{ $package->max_menu_items == -1 ? 'unlimited' : $package->max_menu_items }} menu items</li>
                        <li><i class="bi bi-check-circle-fill"></i> Online ordering system</li>
                        <li><i class="bi bi-check-circle-fill"></i> Order management dashboard</li>
                        @if($package->includes_ads)
                        <li><i class="bi bi-check-circle-fill"></i> Featured in search results</li>
                        <li><i class="bi bi-check-circle-fill"></i> Promotional badge</li>
                        @endif
                        <li><i class="bi bi-check-circle-fill"></i> 24/7 customer support</li>
                    </ul>

                    <a href="{{ route('partner.register', ['package' => $package->id]) }}" class="btn btn-primary-custom pricing-btn">
                        Get Started
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-5">
            <p class="text-muted">All plans include a 14-day free trial. No credit card required to start.</p>
        </div>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="section-title">Frequently Asked Questions</h2>
                <div class="text-start mt-4">
                    <div class="faq-item mb-4">
                        <h5 class="fw-bold">How long does setup take?</h5>
                        <p class="text-muted">Once you complete payment, your restaurant will be set up within minutes. You'll have access to your dashboard immediately.</p>
                    </div>
                    <div class="faq-item mb-4">
                        <h5 class="fw-bold">Can I change my plan later?</h5>
                        <p class="text-muted">Yes! You can upgrade or downgrade your plan at any time from your restaurant dashboard.</p>
                    </div>
                    <div class="faq-item mb-4">
                        <h5 class="fw-bold">What payment methods do you accept?</h5>
                        <p class="text-muted">We accept all major credit cards and PayPal for secure payments.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    function filterPackages(el, cycle) {
        document.querySelectorAll('.billing-option').forEach(opt => opt.classList.remove('active'));
        el.classList.add('active');

        if (cycle === 'monthly') {
            document.querySelectorAll('.package-card[data-cycle="monthly"]').forEach(el => el.style.display = 'block');
            document.getElementById('yearly-packages').style.display = 'none';
        } else {
            document.querySelectorAll('.package-card[data-cycle="monthly"]').forEach(el => el.style.display = 'none');
            document.getElementById('yearly-packages').style.display = 'flex';
        }
    }
</script>
@endpush