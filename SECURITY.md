# Security Policy

## Overview

Security is a top priority for Quickbite. This document outlines our security practices, vulnerability reporting procedures, and guidelines for contributing to the security of our platform.

---

## Supported Versions

| Version | Supported | Notes |
|---------|-----------|-------|
| 1.x | ✅ Yes | Current stable release |
| 0.x | ❌ No | Legacy version |

---

## Reporting a Vulnerability

If you discover a security vulnerability, please report it responsibly.

### How to Report

1. **DO NOT** create a public GitHub issue
2. Email security details to: security@quickbite.com
3. Include in your report:
   - Description of the vulnerability
   - Steps to reproduce
   - Potential impact
   - Any suggested fixes (optional)

### Response Timeline

- **Initial Response**: Within 48 hours
- **Status Update**: Within 7 days
- **Resolution**: Target 30 days for critical issues

---

## Security Architecture

### Authentication

```php
// Laravel Sanctum configuration
// config/sanctum.php
return [
    'stateful' => explode(',', config('app.frontend_url')),
    'guard' => ['web'],
    'expiration' => null, // Token doesn't expire by default
    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),
];
```

### Authorization

```php
// Policy-based authorization example
// app/Policies/OrderPolicy.php
public function update(User $user, Order $order)
{
    return $user->id === $order->user_id
        || $user->isAdmin()
        || $user->isRestaurantOwner($order->restaurant_id);
}
```

---

## Security Measures

### Application Security

| Measure | Implementation |
|---------|---------------|
| **SQL Injection** | Eloquent ORM with parameter binding |
| **XSS Protection** | Blade automatic escaping |
| **CSRF Protection** | Laravel CSRF tokens |
| **Clickjacking** | X-Frame-Options header |
| **Content Sniffing** | X-Content-Type-Options header |
| **SSL/TLS** | HTTPS enforcement |

### Configuration Security

```env
# Production security settings
APP_ENV=production
APP_DEBUG=false
SESSION_SECURE_COOKIE=true
SANCTUM_STATEFUL_DOMAINS=app.quickbite.com
CORS_ALLOWED_ORIGINS=https://app.quickbite.com
```

### Database Security

- Use parameterized queries (Eloquent)
- Implement proper escaping
- Regular security audits
- Principle of least privilege

---

## Payment Security

### Stripe Integration

```php
// Payment processing with proper validation
public function processPayment(Order $order, string $paymentMethodId)
{
    // Verify payment method
    $paymentMethod = $this->stripe->paymentMethods->retrieve($paymentMethodId);

    if ($paymentMethod->customer !== $order->user->stripe_customer_id) {
        throw new UnauthorizedException('Invalid payment method');
    }

    // Create payment intent with proper metadata
    $paymentIntent = $this->stripe->paymentIntents->create([
        'amount' => $order->total * 100, // Convert to cents
        'currency' => 'usd',
        'customer' => $order->user->stripe_customer_id,
        'payment_method' => $paymentMethodId,
        'metadata' => [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
        ],
        'automatic_payment_methods' => [
            'enabled' => true,
            'allow_redirects' => 'never'
        ],
    ]);

    return $paymentIntent;
}
```

### Webhook Signature Verification

```php
public function handleWebhook(Request $request)
{
    $sigHeader = $request->header('Stripe-Signature');
    $payload = $request->getContent();

    try {
        $event = \Stripe\Webhook::constructEvent(
            $payload,
            $sigHeader,
            config('services.stripe.webhook_secret')
        );
    } catch (\UnexpectedValueException $e) {
        return response()->json(['error' => 'Invalid payload'], 400);
    }

    // Handle the event
    switch ($event->type) {
        case 'payment_intent.succeeded':
            $this->handlePaymentSuccess($event->data->object);
            break;
        case 'payment_intent.payment_failed':
            $this->handlePaymentFailure($event->data->object);
            break;
    }

    return response()->json(['received' => true]);
}
```

---

## API Security

### Rate Limiting

```php
// Rate limiter configuration
// app/Providers/RouteServiceProvider.php
protected function configureRateLimiting(): void
{
    RateLimiter::for('api', function (Request $request) {
        return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
    });

    RateLimiter::for('gps', function (Request $request) {
        return Limit::perMinute(30)->by($request->user()?->id ?: $request->ip());
    });
}
```

### Request Validation

```php
// Form request validation example
class CreateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'restaurant_id' => ['required', 'integer', 'exists:restaurants,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_item_id' => ['required', 'integer', 'exists:menu_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'delivery_address' => ['required', 'string', 'max:500'],
            'delivery_lat' => ['required', 'numeric', 'between:-90,90'],
            'delivery_lng' => ['required', 'numeric', 'between:-180,180'],
        ];
    }
}
```

---

## Data Protection

### PII Handling

We minimize Personal Identifiable Information (PII) storage:
- Encrypt sensitive data at rest
- Use tokenization for payment data
- Regular data retention reviews

### Encryption

```php
// Using Laravel's encryption
// Encrypt sensitive data
$encrypted = encrypt($sensitiveData);

// Decrypt when needed
$decrypted = decrypt($encrypted);
```

---

## Security Headers

```php
// App/Http/Middleware/SecurityHeaders.php
public function handle($request, Closure $next)
{
    $response = $next($request);

    $response->headers->set('X-Content-Type-Options', 'nosniff');
    $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
    $response->headers->set('X-XSS-Protection', '1; mode=block');
    $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
    $response->headers->set(
        'Content-Security-Policy',
        "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline';"
    );

    return $response;
}
```

---

## Security Checklist for Deployments

### Pre-Deployment

- [ ] Set `APP_ENV=production`
- [ ] Disable `APP_DEBUG`
- [ ] Use strong `APP_KEY`
- [ ] Configure SSL/TLS
- [ ] Set secure session config

### Infrastructure

- [ ] Configure firewall rules
- [ ] Set up database backups
- [ ] Enable Redis authentication
- [ ] Configure queue authentication

### Monitoring

- [ ] Set up error tracking (Sentry)
- [ ] Configure log aggregation
- [ ] Enable uptime monitoring
- [ ] Set up security alerts

---

## Vulnerability Disclosure Timeline

```
Day 0:   Vulnerability discovered
Day 1-2: Initial response to reporter
Day 3-7: Investigation and confirmation
Day 8-14: Development of fix
Day 15-21: Security testing of fix
Day 22-30: Release patch and notification
```

---

## Security Resources

### Documentation
- [Laravel Security](https://laravel.com/docs/10.x/security)
- [Stripe Security](https://stripe.com/docs/security)
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)

### Tools
- [Laravel Audit](https://github.com/stechstudio/laravel-audit)
- [PHPStan](https://phpstan.org/)
- [SonarQube](https://www.sonarqube.org/)

---

## Contact

For security-related questions:
- **Email**: security@quickbite.com
- **PGP Key**: Available on request

---

Thank you for helping keep Quickbite secure! 🛡️