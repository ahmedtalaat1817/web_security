# System Architecture

## Overview

Quickbite follows a **microservices-ready** modular architecture built on Laravel 12. The system is designed to scale horizontally while maintaining code simplicity and maintainability.

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                              CLIENT APPLICATIONS                            │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐             │
│  │  Customer Web   │  │  Restaurant     │  │  Rider Mobile   │             │
│  │  (Blade/Live)   │  │  Dashboard      │  │  (API + Blade)  │             │
│  └────────┬────────┘  └────────┬────────┘  └────────┬────────┘             │
│           │                    │                    │                       │
└───────────┼────────────────────┼────────────────────┼───────────────────────┘
            │                    │                    │
            ▼                    ▼                    ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                         API GATEWAY (Laravel 12)                           │
│                                                                              │
│  ┌──────────────────────────────────────────────────────────────────────┐   │
│  │                        HTTP Layer                                    │   │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐              │   │
│  │  │   REST API    │  │   Web Routes │  │   Websocket  │              │   │
│  │  │  (Sanctum)    │  │   (Blade)    │  │   (Pusher)   │              │   │
│  │  └──────────────┘  └──────────────┘  └──────────────┘              │   │
│  └──────────────────────────────────────────────────────────────────────┘   │
│  ┌──────────────────────────────────────────────────────────────────────┐   │
│  │                     Application Layer                               │   │
│  │  ┌─────────────────────────────────────────────────────────────────┐│   │
│  │  │                    Service Container                            ││   │
│  │  │  • OrderService  • DispatchService  • PaymentService           ││   │
│  │  │  • GeocodingService  • SurgePricingService                    ││   │
│  │  └─────────────────────────────────────────────────────────────────┘│   │
│  │  ┌─────────────────────────────────────────────────────────────────┐│   │
│  │  │                  Event-Driven Architecture                     ││   │
│  │  │  • OrderStatusUpdated  • RiderAssigned  • OrderPickedUp        ││   │
│  │  └─────────────────────────────────────────────────────────────────┘│   │
│  │  ┌─────────────────────────────────────────────────────────────────┐│   │
│  │  │                    Queue Processing                            ││   │
│  │  │  • Laravel Horizon  • Redis Driver  • Async Jobs              ││   │
│  │  └─────────────────────────────────────────────────────────────────┘│   │
│  └──────────────────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────────────┘
            │                    │                    │
            ▼                    ▼                    ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                           EXTERNAL SERVICES                                │
│  ┌──────────────────┐  ┌──────────────────┐  ┌──────────────────┐          │
│  │  Stripe Connect │  │   Pusher Events  │  │   Google Maps    │          │
│  │  (Payments)     │  │  (Real-Time)     │  │   (Geocoding)    │          │
│  └──────────────────┘  └──────────────────┘  └──────────────────┘          │
│                                                                              │
│  ┌──────────────────┐  ┌──────────────────┐  ┌──────────────────┐          │
│  │   PayPal API    │  │   FastAPI ML     │  │   TomTom Maps    │          │
│  │  (Partner Pay)  │  │  (Dispatch AI)   │  │   (Alternative)  │          │
│  └──────────────────┘  └──────────────────┘  └──────────────────┘          │
└─────────────────────────────────────────────────────────────────────────────┘
            │                    │                    │
            ▼                    ▼                    ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                         DATA LAYER                                         │
│  ┌────────────────────────────────────┐  ┌─────────────────────────────┐   │
│  │         MySQL Database             │  │        Redis Cache         │   │
│  │  • Orders, Payments, Users        │  │  • Session Store           │   │
│  │  • Restaurants, Riders, Menu      │  │  • Queue Backend           │   │
│  │  • Reviews, Payouts                │  │  • Caching Layer           │   │
│  └────────────────────────────────────┘  └─────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## Core Components

### 1. Laravel Application Layer

#### HTTP Handling
- **REST API** (`routes/api.php`): Token-based API using Laravel Sanctum
- **Web Routes** (`routes/web.php`): Session-based authentication for Blade views
- **WebSocket** (`routes/channels.php`): Pusher channel subscriptions

#### Service Container
| Service | Responsibility |
|---------|----------------|
| `OrderService` | Order state machine, lifecycle management |
| `DispatchService` | Rider matching, delivery assignment |
| `PaymentService` | Stripe integration, payment processing |
| `GeocodingService` | Address geocoding, distance calculation |
| `SurgePricingService` | Dynamic pricing based on demand |

#### Event System
```php
// Events are dispatched when significant actions occur
event(new OrderStatusUpdated($order, $oldStatus, $newStatus));
event(new RiderAssigned($order, $rider));
event(new RiderLocationUpdated($rider, $lat, $lng));
event(new OrderPickedUp($order, $rider));
```

---

### 2. Data Flow Architecture

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                           ORDER LIFECYCLE                                  │
│                                                                              │
│   ┌─────────┐    ┌──────────┐    ┌───────────┐    ┌──────────┐            │
│   │Customer │───▶│  Create  │───▶│  Confirm  │───▶│Preparing │            │
│   │  App    │    │  Order   │    │  Payment  │    │ Kitchen  │            │
│   └─────────┘    └──────────┘    └───────────┘    └──────────┘            │
│                                                             │                │
│                           ┌────────────────────────────────┘                │
│                           ▼                                                     │
│   ┌─────────┐    ┌──────────┐    ┌───────────┐    ┌──────────┐            │
│   │Customer │◀───│ Delivered│◀───│ On The    │◀───│  Picked  │            │
│   │  App    │    │ Complete │    │    Way    │    │   Up     │            │
│   └─────────┘    └──────────┘    └───────────┘    └──────────┘            │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

### 3. Real-Time Communication

#### Pusher Integration
```
┌─────────────┐         ┌─────────────┐         ┌─────────────┐
│   Laravel   │────────▶│   Pusher    │────────▶│   Client    │
│   Backend   │  Event  │   Server    │  WebSocket│   Apps     │
└─────────────┘         └─────────────┘         └─────────────┘
     │                                              ▲
     │        ┌─────────────────────────────────────┘
     │        │
     ▼        │
┌─────────────────────┐
│   Event Channel     │
│  • order-updates    │
│  • rider-tracking   │
│  • notifications    │
└─────────────────────┘
```

#### Event Broadcasting
```php
// Channel: order.{orderId}
event(new OrderStatusUpdated($order, 'preparing', 'ready_for_pickup'));

// Listeners receive:
// - Old status, new status
// - Timestamp
// - Additional metadata
```

---

### 4. Queue System Architecture

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                         QUEUE PROCESSING FLOW                              │
│                                                                              │
│   ┌──────────────┐      ┌────────────────┐      ┌──────────────────┐      │
│   │   Trigger    │─────▶│  Queue Job     │─────▶│   Worker         │      │
│   │   Event      │      │  Dispatched    │      │   Processing    │      │
│   └──────────────┘      └────────────────┘      └──────────────────┘      │
│         │                        │                        │                 │
│         ▼                        ▼                        ▼                 │
│   ┌──────────────┐      ┌────────────────┐      ┌──────────────────┐      │
│   │ Order Created│      │ ProcessPayment │      │ Database         │      │
│   │              │      │ DispatchOrder  │      │ Update            │      │
│   │              │      │ SendNotification│      │ Pusher Broadcast  │      │
│   └──────────────┘      └────────────────┘      └──────────────────┘      │
└─────────────────────────────────────────────────────────────────────────────┘
```

#### Redis Queue Jobs
| Job | Purpose | Priority |
|-----|---------|-----------|
| `ProcessOrderPayment` | Handle payment intent, confirm order | High |
| `DispatchOrder` | Find nearest available rider | High |
| `SendNotification` | Push notifications to users | Medium |
| `UpdateRiderLocation` | Cache rider GPS location | Low |
| `ProcessPayout` | Calculate and transfer restaurant earnings | Low |

---

### 5. FastAPI Microservice

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                      DISPATCH MICROSERVICE (FastAPI)                       │
│                                                                              │
│  ┌─────────────────────────────────────────────────────────────────────┐    │
│  │                         Endpoints                                   │    │
│  │  POST /dispatch - Find and assign rider                           │    │
│  │  GET  /surge     - Calculate surge pricing                        │    │
│  │  GET  /eta       - Estimate delivery time                          │    │
│  │  POST /route     - Optimize delivery route                         │    │
│  └─────────────────────────────────────────────────────────────────────┘    │
│                                    │                                        │
│  ┌─────────────────────────────────────────────────────────────────────┐    │
│  │                      Business Logic                                │    │
│  │  • Haversine distance algorithm                                   │    │
│  │  • Rider availability scoring                                     │    │
│  │  • Demand-based surge calculation                                 │    │
│  │  • Traffic pattern analysis                                       │    │
│  └─────────────────────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────────────────────┘
         │
         ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                    DISPATCH ALGORITHM                                      │
│                                                                              │
│   Input: Restaurant location, Delivery location, Order details              │
│                                                                              │
│   Step 1: Query available riders within 5km radius                       │
│   Step 2: Calculate distance using Haversine formula                     │
│   Step 3: Score riders: (distance * 0.4) + (rating * 0.3) + (0.3 / orders)│
│   Step 4: Select highest scoring rider                                    │
│   Step 5: Calculate ETA based on distance + traffic factor               │
│   Step 6: Return assignment to Laravel                                     │
│                                                                              │
│   Output: Rider ID, ETA, Dispatch confirmation                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

### 6. Payment Processing Architecture

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                       PAYMENT FLOW                                         │
│                                                                              │
│   ┌─────────┐     ┌─────────────┐     ┌──────────────┐                  │
│   │Customer │────▶│   Stripe    │────▶│   Platform   │                  │
│   │  App    │     │   Checkout  │     │   Account    │                  │
│   └─────────┘     └─────────────┘     └──────────────┘                  │
│                                              │                             │
│                                              ▼                             │
│   ┌─────────────────────────────────────────────────────────────────────┐  │
│   │                    SPLIT PAYMENT LOGIC                             │  │
│   │                                                                     │  │
│   │  Total: $34.97                                                      │  │
│   │    ├── Platform Fee: $3.50 (10%)                                  │  │
│   │    ├── Payment Fee: $1.05 (3%)                                   │  │
│   │    └── Restaurant Net: $30.42                                    │  │
│   │                                                                     │  │
│   │  → Auto-transfer to Restaurant's Stripe Connect account           │  │
│   └─────────────────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

### 7. Database Schema Relationships

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                        DATABASE RELATIONSHIPS                              │
│                                                                              │
│    ┌────────────┐          ┌────────────┐          ┌────────────┐        │
│    │    User    │          │  Restaurant │          │   Rider    │        │
│    │ (customer) │◀────────▶│             │◀────────▶│            │        │
│    └────────────┘   orders  └────────────┘  orders  └────────────┘        │
│         │                  │                  │                             │
│         │                  │                  │                             │
│         ▼                  ▼                  ▼                             │
│    ┌────────────┐    ┌────────────┐    ┌────────────┐                     │
│    │   Order    │◀───│  Order     │───▶│  Payment   │                     │
│    │   (user)   │    │  (restaurant)│    │            │                     │
│    └────────────┘    └────────────┘    └────────────┘                     │
│         │                  │                  │                             │
│         ▼                  ▼                  ▼                             │
│    ┌────────────┐    ┌────────────┐    ┌────────────┐                     │
│    │Order Items │    │Menu Items  │    │  Payout    │                     │
│    │            │◀──▶│            │    │            │                     │
│    └────────────┘    └────────────┘    └────────────┘                     │
│                                                                              │
│    ┌────────────┐          ┌────────────┐                                  │
│    │  Review    │          │   Payout   │                                  │
│    │            │          │            │                                  │
│    └────────────┘          └────────────┘                                  │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## Service Layer Architecture

### Dependency Injection

```php
// Example: OrderService injection
class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private PaymentService $paymentService
    ) {}

    public function store(Request $request)
    {
        $order = $this->orderService->createOrder($request->all());
        // ...
    }
}
```

### Service Responsibilities

| Service | Public API | Dependencies |
|---------|------------|---------------|
| `OrderService` | `createOrder()`, `updateStatus()`, `cancelOrder()` | `DispatchService`, `PaymentService` |
| `DispatchService` | `dispatch()`, `findRider()`, `reassignRider()` | `GeocodingService` |
| `PaymentService` | `createIntent()`, `processWebhook()`, `refund()` | Stripe SDK |
| `GeocodingService` | `geocode()`, `calculateDistance()`, `validateAddress()` | Google Maps API |
| `SurgePricingService` | `calculate()`, `getMultiplier()` | Config, Time factors |

---

## Scalability Considerations

### Horizontal Scaling
- API servers can be replicated behind load balancer
- Stateless session handling
- Database read replicas for queries
- Redis cache shared across instances

### Caching Strategy
- **Route-level caching**: `Route::cache('api')`
- **Query caching**: Eloquent query cache
- **View caching**: Compiled Blade templates
- **Redis caching**: Rider locations, surge prices

### Performance Optimizations
- Database indexing on foreign keys
- Pagination for large datasets
- Lazy loading for relationships
- Queue long-running operations

---

## Security Architecture

### Authentication Flow
```
┌─────────────────────────────────────────────────────────────────────────────┐
│                         AUTHENTICATION FLOW                                 │
│                                                                              │
│   Web (Session)                     API (Token)                             │
│        │                                 │                                   │
│        ▼                                 ▼                                   │
│   ┌──────────────┐                 ┌──────────────┐                        │
│   │ Login Form   │                 │ POST /login  │                        │
│   │   (Blade)    │                 │  (JSON)      │                        │
│   └──────┬───────┘                 └──────┬───────┘                        │
│          │                                 │                                 │
│          ▼                                 ▼                                 │
│   ┌───────────────────────────────────────────────────────────────────┐    │
│   │                  Laravel Session                                 │    │
│   │  • Cookie-based session                                          │    │
│   │  • CSRF token protection                                         │    │
│   └───────────────────────────────────────────────────────────────────┘    │
│                                      │                                      │
│                                      ▼                                      │
│   ┌───────────────────────────────────────────────────────────────────┐    │
│   │              Laravel Sanctum                                     │    │
│   │  • Token generation (hashed SHA-256)                           │    │
│   │  • Token abilities/scopes                                        │    │
│   │  • Expiration handling                                          │    │
│   └───────────────────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────────────────────┘
```

### Authorization Model
- **Policies**: Resource-based authorization
- **Roles**: Customer, Restaurant, Rider, Admin
- **Middleware**: Route-level access control

---

## Monitoring & Observability

### Laravel Horizon
- Queue job monitoring
- Job execution time tracking
- Failed job alerting
- throughput metrics

### Logging
- Channel-based logging (stack, single, slack)
- Contextual logging with correlation IDs
- Exception tracking

---

## File Structure Summary

```
quickbite/
├── app/
│   ├── Services/           # Business logic layer
│   │   ├── OrderService.php
│   │   ├── DispatchService.php
│   │   ├── PaymentService.php
│   │   └── ...
│   ├── Events/            # Event classes for decoupling
│   ├── Jobs/              # Queue job classes
│   ├── Models/            # Eloquent models
│   ├── Http/
│   │   ├── Controllers/   # API and Web controllers
│   │   └── Middleware/   # Custom middleware
│   └── Policies/         # Authorization policies
├── config/               # Configuration files
├── database/
│   ├── migrations/       # Schema definitions
│   └── seeders/         # Test data
├── routes/
│   ├── api.php          # REST API routes
│   ├── web.php          # Web routes
│   └── channels.php     # WebSocket channels
├── microservices/       # FastAPI dispatch engine
└── docker/             # Container configurations
```

---

## Next Steps

- Review [API Documentation](API_DOCUMENTATION.md)
- See [Deployment Guide](DEPLOYMENT_GUIDE.md)
- Check [Security Guidelines](SECURITY.md)
- See [Contributing Guide](CONTRIBUTING.md)