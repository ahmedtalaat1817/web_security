<footer class="footer-section">
    <div class="container-fluid px-4">
        <div class="row g-5">
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center mb-4">
                    <div class="footer-brand-icon">
                        <i class="bi bi-lightning-charge-fill"></i>
                    </div>
                    <span class="footer-brand-text">Quickbite!</span>
                </div>
                <p class="footer-description mb-4">
                    Your favorite restaurants, delivered fast. Lightning-fast food delivery at your fingertips.
                </p>
                <div class="d-flex gap-3">
                    <a href="#" class="social-icon" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="social-icon" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="social-icon" aria-label="X"><i class="bi bi-twitter-x"></i></a>
                </div>
            </div>

            <div class="col-lg-2 col-md-6">
                <h6 class="footer-title">Quick Links</h6>
                <ul class="footer-links">
                    <li><a href="{{ route('restaurants.index') }}">Browse Restaurants</a></li>
                    <li><a href="{{ route('partner.pricing') }}">Partner with Us</a></li>
                    @auth
                    <li><a href="{{ route('dashboard') }}">My Dashboard</a></li>
                    <li><a href="{{ route('profile') }}">My Profile</a></li>
                    @endauth
                </ul>
            </div>

            <div class="col-lg-2 col-md-6">
                <h6 class="footer-title">Support</h6>
                <ul class="footer-links">
                    <li><a href="mailto:support@quickbite.local">Contact Us</a></li>
                    <li><a href="#">Help Center</a></li>
                    <li><a href="#">FAQ</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-6">
                <h6 class="footer-title">Legal</h6>
                <ul class="footer-links">
                    <li><a href="#">Terms of Service</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                </ul>
            </div>
        </div>

        <hr class="footer-divider">

        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <p class="footer-copyright mb-0">&copy; {{ date('Y') }} Quickbite!. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/Visa_Inc._logo.svg/200px-Visa_Inc._logo.svg.png" alt="Visa" height="24" class="me-2" loading="lazy">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2a/Mastercard-logo.svg/200px-Mastercard-logo.svg.png" alt="Mastercard" height="24" loading="lazy">
            </div>
        </div>
    </div>
</footer>

<style>
    .footer-section {
        background: var(--bg-secondary);
        border-top: 1px solid var(--border-default);
        padding: 64px 0 32px;
    }

    [data-theme="dark"] .footer-section {
        background: var(--bg-tertiary);
    }

    .footer-brand-icon {
        width: 44px;
        height: 44px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        box-shadow: var(--shadow-md), 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .footer-brand-text {
        font-family: var(--font-display);
        font-size: 22px;
        font-weight: 700;
        color: var(--text-primary);
    }

    .footer-description {
        color: var(--text-tertiary);
        font-size: var(--text-sm);
        line-height: 1.7;
    }

    .footer-title {
        font-size: var(--text-sm);
        font-weight: 700;
        color: var(--text-primary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 20px;
    }

    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-links li {
        margin-bottom: 12px;
    }

    .footer-links a {
        color: var(--text-tertiary);
        font-size: var(--text-sm);
        transition: all var(--transition-fast);
        text-decoration: none;
        display: inline-block;
    }

    .footer-links a:hover {
        color: var(--primary);
        transform: translateX(4px);
    }

    .social-icon {
        width: 40px;
        height: 40px;
        background: var(--bg-tertiary);
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all var(--transition-base);
        text-decoration: none;
        color: var(--text-tertiary);
    }

    .social-icon:hover {
        background: var(--primary);
        color: white;
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
    }

    .footer-divider {
        border: none;
        border-top: 1px solid var(--border-subtle);
        margin: 40px 0 24px;
    }

    .footer-copyright {
        color: var(--text-muted);
        font-size: var(--text-sm);
    }

    @media (max-width: 768px) {
        .footer-section {
            padding: 48px 0 24px;
        }

        .footer-copyright {
            text-align: center !important;
            margin-bottom: 16px;
        }

        .footer-links a:hover {
            transform: none;
        }
    }
</style>