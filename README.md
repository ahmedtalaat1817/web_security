# Quickbite - Food Delivery Platform

<p align="center">

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-4.x-4A5568?style=for-the-badge)
![Stripe](https://img.shields.io/badge/Stripe-Connect-635BFF?style=for-the-badge&logo=stripe&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)

</p>

> **Enterprise-grade food delivery ecosystem** | Inspired by Talabat, Uber Eats, and Stripe Dashboard

---

## Overview

**Quickbite** is a full-stack food delivery platform built with Laravel 11, featuring real-time order tracking, AI-powered dispatch engine, Stripe Connect payments, and a modern responsive UI. The platform connects customers, restaurants, riders, and administrators in a seamless ecosystem.

<img width="1920" height="925" alt="image" src="https://github.com/user-attachments/assets/e6f36b4c-884a-4318-80f8-dc8d8ad16c27" />

---
<img width="1920" height="926" alt="image" src="https://github.com/user-attachments/assets/ab2c5181-b24b-41a7-88f2-abc223915442" />

---
<img width="1920" height="928" alt="image" src="https://github.com/user-attachments/assets/d544c919-6f60-4696-8bc5-0776c908d86e" />

---
<img width="1920" height="928" alt="image" src="https://github.com/user-attachments/assets/8d9251a9-8d88-4be6-b1a8-1bad935d201f" />

---
<img width="1920" height="928" alt="image" src="https://github.com/user-attachments/assets/d0399b8f-2137-43bc-8cd1-356e9b87f9b3" />

---
<img width="1917" height="920" alt="image" src="https://github.com/user-attachments/assets/d846d4cc-bb94-4c4f-b4dc-2164a860b84f" />

---
<img width="1920" height="927" alt="image" src="https://github.com/user-attachments/assets/8664a2a0-9ebe-4512-a012-321cdc1bb00b" />

---
<img width="1920" height="928" alt="image" src="https://github.com/user-attachments/assets/48d2f0af-8dae-4d1b-b30b-a6d53f4496ad" />

---
<img width="1920" height="928" alt="image" src="https://github.com/user-attachments/assets/f3346aa5-acc9-4ca8-b140-3e6834d5246f" />

---
<img width="1920" height="924" alt="image" src="https://github.com/user-attachments/assets/82de7587-b63a-4007-8620-4db4e5b456e5" />

---
<img width="1920" height="928" alt="image" src="https://github.com/user-attachments/assets/dc849ea5-20e7-45af-9a49-a8c01b4f28c6" />

---

## Architecture Highlights

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                              CLIENT LAYER                                   │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐         │
│  │  Customer   │  │ Restaurant  │  │    Rider    │  │   Admin     │         │
│  │   Mobile    │  │  Dashboard  │  │   Mobile    │  │   Control   │         │
│  │   App/Web   │  │   (Blade)   │  │   App/Web   │  │    Tower    │         │
│  └──────┬──────┘  └──────┬──────┘  └──────┬──────┘  └──────┬──────┘         │
└─────────┼────────────────┼────────────────┼────────────────┼────────────────┘
          │                │                │                │
          ▼                ▼                ▼                ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                           API GATEWAY (Laravel)                             │
│  ┌────────────────────────────────────────────────────────────────────────┐ │
│  │                    Laravel 12 + Sanctum + Horizon                      │ │
│  └────────────────────────────────────────────────────────────────────────┘ │
│                                                                             │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐     │
│  │  REST API    │  │  Real-Time   │  │   Queue      │  │   Stripe     │     │
│  │  Endpoints   │  │  Events      │  │  Processing  │  │   Connect    │     │
│  └──────────────┘  └──────────────┘  └──────────────┘  └──────────────┘     │
└─────────────────────────────────────────────────────────────────────────────┘
          │                │                │                │
          ▼                ▼                ▼                ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                        MICROSERVICES LAYER                                  │
│  ┌──────────────────────┐  ┌──────────────────────┐                         │
│  │  FastAPI Dispatch    │  │   External APIs      │                         │
│  │  Engine (Python)     │  │   (Stripe, Pusher,   │                         │
│  │  - Haversine Algo    │  │    Google Maps)      │                         │
│  │  - Rider Matching    │  │                      │                         │
│  │  - Surge Pricing     │  │                      │                         │
│  └──────────────────────┘  └──────────────────────┘                         │
│                                                                             │
│  ┌──────────────────────┐  ┌──────────────────────┐                         │
│  │  Redis Queue         │  │  MySQL Database      │                         │
│  │  - Async Jobs        │  │  - Orders, Payments  │                         │
│  │  - Event Sourcing    │  │  - Users, Riders     │                         │
│  └──────────────────────┘  └──────────────────────┘                         │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## Features

### 👤 Customer System
- Browse restaurants by category, rating, delivery time
- Real-time order tracking with live map
- Multiple payment methods (Stripe, PayPal)
- Order history and re-ordering
- Restaurant reviews and ratings

### 🍽️ Restaurant Dashboard
- Menu management with categories and variants
- Order management and status updates
- Sales analytics and insights
- Stripe Connect onboarding for payouts
- Availability toggles and operating hours

### 🛵 Rider Dashboard
- GPS-based location tracking
- Order acceptance and delivery workflow
- Earnings dashboard and payout history
- Order status management (pickup, deliver)
- Real-time order notifications

### ⚙️ Admin Control Tower
- Platform-wide order monitoring
- Rider and restaurant management
- Partner package configuration
- Financial reports and analytics
- Manual order assignment

### 🔄 Real-Time Features
- Live order status updates via Pusher
- Rider location tracking on map
- Push notifications for all stakeholders
- WebSocket-based instant updates
- Event sourcing for audit trails

### 💳 Payment System
- Stripe Checkout integration
- Stripe Connect for restaurant/rider payouts
- Split payments (platform commission + restaurant)
- Idempotent transactions
- Refund handling

### 📈 Surge Pricing Engine
- Multi-factor surge calculation
- Demand-based pricing multiplier
- Maximum cap configuration
- Time-window based rules
- Real-time price display

### 🚀 Dispatch Engine
- Haversine formula for distance calculation
- Nearest rider matching algorithm
- Caching strategy for performance
- Automatic and manual dispatch
- Delivery time estimation

---

## Tech Stack

| Category | Technology |
|----------|------------|
| **Backend** | Laravel 12, PHP 8.2+ |
| **Frontend** | Blade Templates, Livewire 4, Bootstrap 5 |
| **Authentication** | Laravel Sanctum |
| **Real-Time** | Pusher, WebSockets |
| **Queue** | Laravel Horizon, Redis |
| **Payments** | Stripe Connect, PayPal |
| **Maps** | Google Maps API, TomTom |
| **Microservices** | FastAPI (Python) |
| **Database** | MySQL 8.0, SQLite (dev) |
| **DevOps** | Docker, Docker Compose |

---

## Project Structure

```
quickbite/
├── app/
│   ├── Console/                    # Artisan commands
│   ├── Events/                     # Event classes
│   │   ├── OrderStatusUpdated.php
│   │   ├── RiderAssigned.php
│   │   ├── RiderLocationUpdated.php
│   │   └── OrderPickedUp.php
│   ├── Exceptions/                # Custom exceptions
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/               # REST API controllers
│   │   │   └── Web/               # Web controllers
│   │   └── Middleware/            # Custom middleware
│   ├── Jobs/                      # Queue jobs
│   ├── Livewire/                  # Livewire components
│   ├── Models/                    # Eloquent models
│   │   ├── User.php               # User with role support
│   │   ├── Restaurant.php         # Restaurant entity
│   │   ├── Rider.php              # Rider with location
│   │   ├── Order.php              # Order with FSM
│   │   ├── Payment.php            # Payment tracking
│   │   └── ...
│   ├── Observers/                 # Eloquent observers
│   ├── Policies/                  # Authorization policies
│   ├── Providers/                 # Service providers
│   ├── Services/                  # Business logic
│   │   ├── OrderService.php       # Order state machine
│   │   ├── DispatchService.php   # Rider dispatch
│   │   ├── PaymentService.php    # Stripe integration
│   │   ├── GeocodingService.php   # Location services
│   │   └── SurgePricingService.php
│   └── Enums/                     # Enum classes
├── config/                        # Configuration files
│   ├── horizon.php               # Queue monitoring
│   ├── surge.php                 # Surge pricing config
│   └── ...
├── database/
│   ├── migrations/               # Database migrations
│   ├── factories/               # Model factories
│   └── seeders/                 # Database seeders
├── docker/                      # Docker configuration
├── docs/                        # Documentation
│   └── openapi.yaml            # OpenAPI spec
├── resources/
│   ├── css/                    # Stylesheets
│   ├── js/                    # JavaScript
│   └── views/                 # Blade templates
│       ├── layouts/          # Base layouts
│       ├── customer/         # Customer views
│       ├── restaurant/       # Restaurant views
│       ├── rider/           # Rider views
│       └── admin/           # Admin views
├── routes/
│   ├── api.php               # REST API routes
│   ├── web.php               # Web routes
│   ├── channels.php          # Broadcast channels
│   └── console.php           # Console routes
├── services/                  # External service integrations
├── microservices/           # FastAPI services
├── storage/                  # Logs, cache, sessions
├── tests/                   # Test suite
│   ├── Feature/            # Feature tests
│   └── Unit/               # Unit tests
└── vendor/                  # Dependencies
```

---

## Installation Guide

### Prerequisites

- **PHP 8.2+** with extensions: `pdo_sqlite`, `mbstring`, `openssl`, `redis`
- **Composer** (PHP package manager)
- **Node.js 18+** and npm
- **Redis** (for queues and caching)
- **MySQL 8.0+** (for production)
- **Docker** and **Docker Compose** (optional)

### Quick Start (Local Development)

```bash
# 1. Clone the repository
git clone https://github.com/your-org/quickbite.git
cd quickbite

# 2. Install PHP dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Install frontend dependencies
npm install

# 6. Build frontend assets
npm run build

# 7. Run migrations (SQLite for dev)
php artisan migrate

# 8. Start the development server
php artisan serve
```

### Database Setup

```bash
# Using SQLite (Development)
DB_CONNECTION=sqlite
# Create the database file
touch database/database.sqlite
php artisan migrate

# Using MySQL (Production)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=quickbite
DB_USERNAME=root
DB_PASSWORD=secret
php artisan migrate
```

### Redis Setup

```bash
# Install Redis
# Ubuntu
sudo apt-get install redis-server

# macOS
brew install redis

# Start Redis
redis-server
```

### Queue Worker Setup

```bash
# Start the queue worker
php artisan queue:work

# Or use Horizon for monitoring
php artisan horizon
```

### Stripe Configuration

```bash
# In .env file
STRIPE_KEY=pk_test_xxx
STRIPE_SECRET=sk_test_xxx
STRIPE_WEBHOOK_SECRET=whsec_xxx
STRIPE_PLATFORM_ACCOUNT_ID=acct_xxx
```

### Pusher Configuration

```bash
# In .env file
BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1
```

### Google Maps API

```bash
# In .env file
GOOGLE_MAPS_API_KEY=your_api_key
TOMTOM_API_KEY=your_tomtom_key
```

### FastAPI Microservice (Optional)

```bash
# Navigate to microservices directory
cd microservices

# Install Python dependencies
pip install -r requirements.txt

# Start the FastAPI server
uvicorn main:app --host 0.0.0.0 --port 8088
```

### Docker Setup (Alternative)

```bash
# Start all services with Docker
docker compose up -d

# View logs
docker compose logs -f

# Stop services
docker compose down
```

---

## Running the Project

### Development Commands

```bash
# Start Laravel development server
php artisan serve

# Start queue worker
php artisan queue:listen --tries=1

# Start Vite dev server
npm run dev

# Run all dev services concurrently
composer run dev
```

### Production Commands

```bash
# Build assets for production
npm run build

# Clear and rebuild caches
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start queue workers (production)
php artisan queue:work --daemon --tries=3 --timeout=60
```

---

## API Documentation

### Authentication

#### Register User
```
POST /api/auth/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "user_type": "customer" // customer, restaurant, rider
}

Response: 201 Created
{
  "token": "laravel_sanctum_token...",
  "user": { ... }
}
```

#### Login
```
POST /api/auth/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}

Response: 200 OK
{
  "token": "laravel_sanctum_token...",
  "user": { ... }
}
```

#### Get Current User
```
GET /api/auth/me
Authorization: Bearer <token>

Response: 200 OK
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "user_type": "customer",
  ...
}
```

### Orders

#### Create Order
```
POST /api/orders
Authorization: Bearer <token>
Content-Type: application/json

{
  "restaurant_id": 1,
  "delivery_address": "123 Main St",
  "delivery_lat": 40.7128,
  "delivery_lng": -74.0060,
  "items": [
    {"menu_item_id": 1, "quantity": 2, "price": 12.99},
    {"menu_item_id": 2, "quantity": 1, "price": 8.99}
  ],
  "payment_method": "stripe"
}

Response: 201 Created
{
  "order_id": "ORD-2024-001",
  "total": 34.97,
  "status": "pending",
  "payment_intent": "pi_xxx"
}
```

#### Get Order Status
```
GET /api/orders/{order_id}
Authorization: Bearer <token>

Response: 200 OK
{
  "order_id": "ORD-2024-001",
  "status": "preparing",
  "rider": { "name": "Mike", "phone": "+1234567890" },
  "estimated_delivery": "2024-01-15T12:30:00Z"
}
```

### Restaurants

#### List Restaurants
```
GET /api/restaurants
Authorization: Bearer <token>

Response: 200 OK
{
  "data": [
    {
      "id": 1,
      "name": "Pizza Palace",
      "rating": 4.5,
      "delivery_time": "25-35 min",
      "is_open": true
    }
  ]
}
```

#### Restaurant Menu
```
GET /api/restaurants/{id}/menu
Authorization: Bearer <token>

Response: 200 OK
{
  "categories": [
    {
      "name": "Pizzas",
      "items": [
        { "id": 1, "name": "Margherita", "price": 12.99 }
      ]
    }
  ]
}
```

### Rider Location

#### Update Location
```
POST /api/riders/location
Authorization: Bearer <token>
Content-Type: application/json

{
  "latitude": 40.7128,
  "longitude": -74.0060
}

Response: 200 OK
{
  "status": "location_updated"
}
```

#### Get Available Riders
```
GET /api/riders/available/list
Authorization: Bearer <token>

Response: 200 OK
{
  "riders": [
    {
      "id": 1,
      "name": "Mike R.",
      "distance_km": 1.2,
      "current_location": { "lat": 40.71, "lng": -74.00 }
    }
  ]
}
```

### Payments

#### Create Payment Intent
```
POST /api/payments/create-intent
Authorization: Bearer <token>
Content-Type: application/json

{
  "order_id": "ORD-2024-001",
  "amount": 34.97,
  "currency": "usd"
}

Response: 200 OK
{
  "client_secret": "pi_xxx_secret_xxx"
}
```

### Partner Onboarding

#### Register Restaurant Partner
```
POST /partner/store
Content-Type: application/json

{
  "name": "Restaurant Owner",
  "email": "owner@restaurant.com",
  "restaurant_name": "My Restaurant",
  "address": "123 Main St",
  "package_id": 1
}

Response: 200 OK
{
  "checkout_url": "https://checkout.stripe.com/..."
}
```

---

## Database Schema

### Users Table
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| name | string | Full name |
| email | string | Unique email |
| password | string | Hashed password |
| user_type | enum | customer, restaurant, rider, admin |
| phone | string | Contact number |
| stripe_customer_id | string | Stripe customer ID |
| created_at | timestamp | Creation time |

### Restaurants Table
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| user_id | bigint | Owner user FK |
| name | string | Restaurant name |
| slug | string | URL-friendly slug |
| description | text | About restaurant |
| address | string | Physical address |
| latitude | decimal | GPS latitude |
| longitude | decimal | GPS longitude |
| phone | string | Contact number |
| logo | text | Logo URL |
| cover_image | text | Cover image URL |
| rating | decimal | Average rating |
| is_open | boolean | Open for orders |
| stripe_connect_id | string | Stripe Connect account |
| created_at | timestamp | Creation time |

### Orders Table
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| order_number | string | Unique order number |
| user_id | bigint | Customer FK |
| restaurant_id | bigint | Restaurant FK |
| rider_id | bigint | Assigned rider FK |
| status | enum | Order status |
| subtotal | decimal | Items total |
| delivery_fee | decimal | Delivery charge |
| surge_fee | decimal | Surge pricing |
| tax | decimal | Tax amount |
| total | decimal | Total amount |
| delivery_address | string | Delivery location |
| delivery_lat | decimal | Delivery latitude |
| delivery_lng | decimal | Delivery longitude |
| notes | text | Order notes |
| created_at | timestamp | Creation time |

### Payments Table
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| order_id | bigint | Order FK |
| stripe_payment_intent | string | Stripe payment ID |
| amount | decimal | Payment amount |
| currency | string | Currency code |
| status | enum | Payment status |
| created_at | timestamp | Creation time |

### Payouts Table
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| restaurant_id / rider_id | bigint | Recipient FK |
| amount | decimal | Payout amount |
| stripe_payout_id | string | Stripe payout ID |
| status | enum | Payout status |
| created_at | timestamp | Creation time |

---

## Authentication & Authorization

### Sanctum Integration
- Token-based authentication for mobile apps
- Session-based auth for web
- Scoped tokens for API access

### Roles & Permissions
| Role | Access Level |
|------|---------------|
| customer | Browse, order, review |
| restaurant | Manage menu, orders, profile |
| rider | Accept, pickup, deliver orders |
| admin | Full platform access |

### Middleware
- `auth:sanctum` - API authentication
- `auth` - Web authentication
- `role:admin` - Admin access

---

## Payment Flow

```
┌─────────┐     ┌─────────────┐     ┌─────────┐     ┌──────────────┐
│Customer │────▶│   Stripe   │────▶│ Platform│────▶│ Restaurant   │
│         │     │   Checkout │     │   Fees  │     │   Payout     │
└─────────┘     └─────────────┘     └─────────┘     └──────────────┘
     │               │                    │                    │
     │               │                    │                    │
     ▼               ▼                    ▼                    ▼
 1. Order       2. Payment         3. Split        4. Auto-transfer
  Created       Intent Created     Payment         to Connect Account
```

### Stripe Connect Flow
1. Customer completes payment via Stripe Checkout
2. Platform receives payment (minus commission)
3. Automatic transfer to restaurant's Stripe Connect account
4. Payout to bank account (scheduled)

---

## Dispatch Algorithm

### Haversine Formula
```php
// Calculate distance between two coordinates
function haversine($lat1, $lng1, $lat2, $lng2) {
    $earthRadius = 6371; // km
    
    $dLat = deg2rad($lat2 - $lat1);
    $dLng = deg2rad($lng2 - $lng1);
    
    $a = sin($dLat/2) * sin($dLat/2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLng/2) * sin($dLng/2);
         
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    
    return $earthRadius * $c;
}
```

### Rider Selection Criteria
1. Within 5km radius of restaurant
2. Status: online/available
3. Not currently assigned to active order
4. Sorted by: distance → rating → total deliveries

---

## Queue & Real-Time Systems

### Redis Queue Jobs
- `ProcessOrderPayment` - Handle payment processing
- `DispatchOrder` - Find and assign rider
- `SendNotification` - Push notification dispatch
- `UpdateRiderLocation` - Location tracking
- `ProcessPayout` - Restaurant/rider payouts

### Pusher Events
- `order.created` - New order notification
- `order.status.updated` - Status change
- `rider.location.updated` - Live tracking
- `order.assigned` - Rider assignment

---

## UI/UX Documentation

### Dark/Light Mode
- CSS custom properties for theming
- `data-theme` attribute on `<html>`
- LocalStorage persistence
- Toggle button in navbar

### Responsive Design
- Mobile-first approach
- Breakpoints: 576px, 768px, 992px, 1400px
- Touch-friendly interactions

### Dashboard UX
- Card-based layout for stats
- Data tables with sorting/filtering
- Real-time charts and graphs

### Accessibility
- WCAG 2.1 AA compliant contrast
- Keyboard navigation support
- Screen reader compatible

---

## Security Practices

### Application Security
- CSRF protection on all forms
- XSS sanitization with Blade
- SQL injection prevention via Eloquent
- Rate limiting on API endpoints

### Payment Security
- Stripe PCI compliance
- No sensitive data stored locally
- Webhook signature verification

### Environment Security
- All secrets in `.env` file
- No credentials in version control
- Environment-specific configurations

---

## Testing

### Feature Tests
```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=OrderTest
```

### Test Coverage
- Order state machine transitions
- Payment processing
- Dispatch algorithm
- Queue job execution

---

## Deployment

### Production Checklist
- [ ] Set `APP_ENV=production`
- [ ] Disable debug mode
- [ ] Generate optimized autoloader
- [ ] Cache configurations
- [ ] Set up queue workers
- [ ] Configure SSL certificate
- [ ] Set up monitoring

### Queue Workers (Production)
```bash
# Supervisor config example
[program:quickbite-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/quickbite/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/quickbite-worker.log
stopwaitsecs=3600
```

---

## Troubleshooting

### Common Issues

#### Queue Not Working
```bash
# Check queue configuration
php artisan config:clear
php artisan queue:restart

# Monitor Horizon
php artisan horizon:list
```

#### WebSocket Connection Failed
```bash
# Check Pusher credentials in .env
# Verify Pusher app settings
# Check firewall rules for Pusher ports
```

#### Stripe Webhook Issues
```bash
# Test webhook locally
stripe listen --forward-to localhost:8000/api/webhooks/stripe

# Verify webhook signature
# Check Stripe dashboard logs
```

#### Migration Errors
```bash
# Rollback and re-migrate
php artisan migrate:rollback
php artisan migrate

# Force migrate in production
php artisan migrate --force
```

#### Redis Connection Issues
```bash
# Check Redis is running
redis-cli ping

# Verify .env configuration
# Check Redis service status
```

---

## Contributors

<p align="center">

<!-- Add your team members here -->

| Role | Name | Email |
|------|------|-------|
| Lead Developer | Your Name | dev@example.com |
| Backend Engineer | Contributor | backend@example.com |
| Frontend Engineer | Contributor | frontend@example.com |
| DevOps Engineer | Contributor | devops@example.com |

</p>

---

## License

This project is licensed under the **MIT License** - see the [LICENSE](LICENSE) file for details.

---

<p align="center">

**Built with ❤️ using Laravel & Livewire**

*Star us on GitHub if you find this project useful!*

</p>
