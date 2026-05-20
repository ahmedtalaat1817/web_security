# Contributing to Quickbite

Thank you for your interest in contributing to Quickbite! This guide will help you get started with development and submitting contributions.

---

## Code of Conduct

We are committed to providing a welcoming and inclusive experience for everyone. By participating in this project, you agree to abide by our [Code of Conduct](https://github.com/quickbite/quickbite/blob/main/CODE_OF_CONDUCT.md).

---

## Getting Started

### Development Environment Setup

```bash
# Clone the repository
git clone https://github.com/quickbite/quickbite.git
cd quickbite

# Install dependencies
composer install
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations (SQLite for development)
touch database/database.sqlite
php artisan migrate

# Seed the database (optional)
php artisan db:seed

# Start development server
php artisan serve

# In another terminal, start Vite
npm run dev
```

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run tests with coverage
php artisan test --coverage
```

---

## Ways to Contribute

### 🐛 Bug Reports
If you find a bug, please create an issue with:
- Clear title and description
- Steps to reproduce
- Expected vs actual behavior
- Environment details
- Screenshots if applicable

### 💡 Feature Requests
For new features:
- Describe the feature in detail
- Explain the use case
- Provide mockups if possible
- Consider backward compatibility

### 🔧 Pull Requests

#### Pull Request Process

1. **Fork** the repository
2. **Create** a feature branch (`git checkout -b feature/amazing-feature`)
3. **Make** your changes
4. **Add** tests for new functionality
5. **Run** the test suite
6. **Commit** with descriptive messages
7. **Push** to your fork
8. **Submit** a Pull Request

#### Pull Request Guidelines

- Follow the [Laravel Coding Style](https://laravel.com/docs/contribution-guide#coding-style)
- Write meaningful commit messages
- Include tests for new features
- Update documentation if needed
- Ensure all tests pass
- Use semantic commit messages:
  - `feat:` New feature
  - `fix:` Bug fix
  - `docs:` Documentation
  - `refactor:` Code refactoring
  - `test:` Test updates

---

## Development Standards

### PHP Coding Standards

```php
// PSR-12 compliant
// Use type hints
public function processOrder(Order $order): array
{
    // ...
}

// Use return types
public function calculateTotal(): float
{
    // ...
}
```

### Blade Template Standards

```blade
{{-- Use proper escaping --}}
{{ $variable }}

{{-- Use Blade directives --}}
@if($condition)
    // ...
@endif

{{-- Keep templates clean --}}
@component('components.card')
    @slot('title')
        Title
    @endslot
    Content
@endcomponent
```

### JavaScript Standards

```javascript
// Use const/let instead of var
const API_URL = '/api';

// Use arrow functions
const getOrders = async (userId) => {
    // ...
};

// Use async/await
async function handleSubmit() {
    try {
        const response = await fetch('/api/orders');
        // ...
    } catch (error) {
        // Handle error
    }
}
```

### CSS Standards

```css
/* Use BEM naming convention */
.block__element--modifier { }

/* Use CSS custom properties */
:root {
    --primary-color: #FF6B35;
    --spacing-unit: 8px;
}

/* Keep specificity low */
.btn { }
.btn--primary { }
```

---

## Project Structure

```
quickbite/
├── app/
│   ├── Console/           # Artisan commands
│   ├── Events/            # Event classes
│   ├── Http/
│   │   ├── Controllers/   # Controllers
│   │   └── Middleware/    # Middleware
│   ├── Jobs/              # Queue jobs
│   ├── Models/            # Eloquent models
│   ├── Providers/         # Service providers
│   └── Services/          # Business logic
├── config/                # Configuration
├── database/
│   ├── migrations/        # Migrations
│   ├── factories/         # Factories
│   └── seeders/           # Seeders
├── resources/
│   ├── css/              # Styles
│   ├── js/               # Scripts
│   └── views/            # Blade templates
├── routes/               # Route definitions
├── tests/                # Test suite
└── docs/                 # Documentation
```

---

## Testing Guidelines

### Writing Tests

```php
// Feature test example
<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_place_order()
    {
        $user = User::factory()->customer()->create();
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/orders', [
                'restaurant_id' => $restaurant->id,
                'items' => [
                    ['menu_item_id' => 1, 'quantity' => 2]
                ]
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'order' => ['id', 'order_number', 'total']
            ]);
    }
}
```

### Running Specific Tests

```bash
# Run tests in a specific file
php artisan test --filter=OrderTest

# Run tests by group
php artisan test --group=unit

# Run tests with verbose output
php artisan test --verbose
```

---

## Documentation

### API Documentation
- Update `docs/openapi.yaml` for API changes
- Include request/response examples

### Code Documentation
- Use PHP docblocks for classes and methods
- Document complex business logic
- Update README for major changes

---

## Git Workflow

### Branch Naming

```
feature/add-payment-method
bugfix/fix-order-cancellation
hotfix/security-patch
docs/update-api-reference
refactor/improve-dispatch-algorithm
```

### Commit Messages

```
feat: add Stripe Connect support for restaurants

- Implement Stripe Connect onboarding flow
- Add split payment logic for platform fees
- Include webhook handling for payment events

Closes #123
```

---

## Community

### Join the Discussion
- GitHub Issues: For bug reports and feature requests
- Discord: [Join our community](https://discord.gg/quickbite)

### Recognition
Contributors will be:
- Added to README contributors section
- Mentioned in release notes
- Given credit in documentation

---

## Questions?

If you have questions, feel free to:
- Open an issue
- Start a discussion
- Join our Discord community

---

## Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Packages](https://packagist.org)
- [PHP Documentation](https://www.php.net/docs.php)
- [Vue.js Guide](https://vuejs.org/guide/)

---

Thank you for contributing to Quickbite! 🎉