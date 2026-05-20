@extends('layouts.app')
@section('title', 'Login')

@section('styles')
<style>
    .auth-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-tertiary) 100%);
        padding: 40px 20px;
        position: relative;
        overflow: hidden;
    }

    [data-theme="dark"] .auth-page {
        background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%);
    }

    .auth-page::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.08) 0%, transparent 50%);
        animation: rotate 20s linear infinite;
    }

    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .auth-card {
        background: var(--bg-secondary);
        border-radius: var(--radius-xl);
        border: 1px solid var(--border-default);
        box-shadow: var(--shadow-xl);
        overflow: hidden;
        position: relative;
        z-index: 2;
        max-width: 460px;
        width: 100%;
    }

    .auth-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        padding: 40px 40px 56px;
        text-align: center;
        margin-bottom: -40px;
    }

    .auth-logo {
        width: 72px;
        height: 72px;
        background: white;
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        font-size: 32px;
        color: var(--primary);
        box-shadow: var(--shadow-lg);
    }

    .auth-title {
        font-family: var(--font-display);
        font-size: 26px;
        font-weight: 700;
        color: white;
        margin: 0;
    }

    .auth-subtitle {
        color: rgba(255, 255, 255, 0.85);
        margin-top: 6px;
        font-size: var(--text-sm);
    }

    .auth-body {
        padding: 40px;
    }

    .form-group-custom {
        margin-bottom: 24px;
    }

    .form-label-custom {
        display: block;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 8px;
        font-size: var(--text-sm);
    }

    .form-input-custom {
        width: 100%;
        padding: 14px 18px;
        border: 2px solid var(--border-default);
        border-radius: var(--radius-md);
        font-size: var(--text-sm);
        transition: all var(--transition-fast);
        background: var(--bg-secondary);
        color: var(--text-primary);
    }

    .form-input-custom::placeholder {
        color: var(--text-muted);
    }

    .form-input-custom:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
        outline: none;
    }

    .form-input-custom.is-invalid {
        border-color: var(--danger);
    }

    .remember-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .remember-check {
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--text-secondary);
        cursor: pointer;
    }

    .remember-check input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: var(--primary);
        cursor: pointer;
    }

    .forgot-link {
        color: var(--primary);
        font-weight: 600;
        font-size: var(--text-sm);
    }

    .forgot-link:hover {
        color: var(--primary-dark);
    }

    .auth-btn {
        width: 100%;
        padding: 16px;
        font-size: var(--text-base);
        font-weight: 600;
        border-radius: var(--radius-md);
    }

    .auth-divider {
        display: flex;
        align-items: center;
        gap: 16px;
        margin: 28px 0;
    }

    .auth-divider::before,
    .auth-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--border-default);
    }

    .auth-divider span {
        color: var(--text-muted);
        font-size: var(--text-sm);
    }

    .auth-footer {
        text-align: center;
        color: var(--text-secondary);
        font-size: var(--text-sm);
    }

    .auth-footer a {
        color: var(--primary);
        font-weight: 600;
    }

    .social-login {
        display: flex;
        gap: 12px;
    }

    .social-btn {
        flex: 1;
        padding: 14px;
        border: 2px solid var(--border-default);
        border-radius: var(--radius-md);
        background: var(--bg-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        cursor: pointer;
        transition: all var(--transition-fast);
    }

    .social-btn:hover {
        border-color: var(--primary);
        background: var(--surface-hover);
    }

    .alert-custom {
        display: flex;
        align-items: center;
        gap: var(--space-3);
        padding: var(--space-4);
        border-radius: var(--radius-md);
        margin-bottom: var(--space-5);
        font-size: var(--text-sm);
    }

    .alert-custom.success {
        background: var(--success-light);
        color: var(--success);
        border: 1px solid rgba(16, 185, 129, 0.2);
    }

    [data-theme="dark"] .alert-custom.success {
        background: var(--success-light);
    }

    @media (max-width: 576px) {
        .auth-card {
            max-width: 100%;
        }

        .auth-header {
            padding: 32px 24px 48px;
        }

        .auth-body {
            padding: 24px;
        }

        .auth-logo {
            width: 60px;
            height: 60px;
            font-size: 26px;
        }
    }
</style>
@endsection

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo">
                <i class="bi bi-lightning-charge-fill"></i>
            </div>
            <h2 class="auth-title">Welcome Back</h2>
            <p class="auth-subtitle">Sign in to continue to Quickbite</p>
        </div>

        <div class="auth-body">
            @if(session('status'))
            <div class="alert-custom success">
                <i class="bi bi-check-circle-fill"></i>{{ session('status') }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group-custom">
                    <label class="form-label-custom" for="email">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                           class="form-input-custom @error('email') is-invalid @enderror"
                           required autocomplete="email" autofocus placeholder="Enter your email">
                    @error('email')
                    <div class="text-danger small mt-2" style="color: var(--danger);">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group-custom">
                    <label class="form-label-custom" for="password">Password</label>
                    <input type="password" name="password" id="password"
                           class="form-input-custom @error('password') is-invalid @enderror"
                           required autocomplete="current-password" placeholder="Enter your password">
                    @error('password')
                    <div class="text-danger small mt-2" style="color: var(--danger);">{{ $message }}</div>
                    @enderror
                </div>

                <div class="remember-row">
                    <label class="remember-check">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span>Remember me</span>
                    </label>
                    @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                    @endif
                </div>

                <button type="submit" class="btn btn-primary-custom auth-btn">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                </button>
            </form>

            <div class="auth-divider"><span>or continue with</span></div>

            <div class="social-login">
                <button type="button" class="social-btn" title="Google">
                    <i class="bi bi-google" style="color: #DB4437;"></i>
                </button>
                <button type="button" class="social-btn" title="Facebook">
                    <i class="bi bi-facebook" style="color: #4267B2;"></i>
                </button>
                <button type="button" class="social-btn" title="Apple">
                    <i class="bi bi-apple"></i>
                </button>
            </div>

            <div class="auth-footer mt-4">
                <p class="mb-0">Don't have an account? <a href="{{ route('register') }}">Create one</a></p>
            </div>
        </div>
    </div>
</div>
@endsection