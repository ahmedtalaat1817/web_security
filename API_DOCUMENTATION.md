# API Documentation

## Overview

The Quickbite API provides a comprehensive set of RESTful endpoints for building client applications. The API follows REST conventions and uses JSON for request/response bodies.

### Base URL
```
http://localhost:8000/api
```

### Authentication
The API uses **Laravel Sanctum** for token-based authentication. Include the token in the `Authorization` header:
```
Authorization: Bearer <your_token>
```

### Rate Limiting
- **General API**: 60 requests per minute
- **GPS endpoints**: 30 requests per minute
- **Authentication**: 10 requests per minute

---

## Authentication Endpoints

### Register

Create a new user account.

**Endpoint:** `POST /auth/register`

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "user_type": "customer"
}
```

**Parameters:**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| name | string | Yes | Full name |
| email | string | Yes | Valid email address |
| password | string | Yes | Min 8 characters |
| password_confirmation | string | Yes | Must match password |
| user_type | string | Yes | customer, restaurant, or rider |

**Response (201):**
```json
{
  "token": "1|xJ3K7Ys9Lm2NpQr3StUv4Wx5Yz6AbCdEfGhIj",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "user_type": "customer",
    "created_at": "2024-01-15T10:30:00Z"
  }
}
```

---

### Login

Authenticate user and receive access token.

**Endpoint:** `POST /auth/login`

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response (200):**
```json
{
  "token": "1|xJ3K7Ys9Lm2NpQr3StUv4Wx5Yz6AbCdEfGhIj",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "user_type": "customer"
  }
}
```

**Error (401):**
```json
{
  "message": "Invalid credentials"
}
```

---

### Get Current User

Retrieve authenticated user information.

**Endpoint:** `GET /auth/me`

**Headers:**
```
Authorization: Bearer <token>
```

**Response (200):**
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "user_type": "customer",
  "phone": "+1234567890",
  "created_at": "2024-01-15T10:30:00Z"
}
```

---

### Logout

Invalidate the current access token.

**Endpoint:** `POST /auth/logout`

**Headers:**
```
Authorization: Bearer <token>
```

**Response (200):**
```json
{
  "message": "Logged out successfully"
}
```

---

### Update Location

Update user's delivery location (customers).

**Endpoint:** `POST /auth/location`

**Headers:**
```
Authorization: Bearer <token>
```

**Request Body:**
```json
{
  "latitude": 40.7128,
  "longitude": -74.0060,
  "address": "123 Main Street, New York"
}
```

**Response (200):**
```json
{
  "message": "Location updated",
  "location": {
    "latitude": 40.7128,
    "longitude": -74.0060
  }
}
```

---

### Update Status

Update user status (riders only).

**Endpoint:** `POST /auth/status`

**Headers:**
```
Authorization: Bearer <token>
```

**Request Body:**
```json
{
  "status": "online"
}
```

**Response (200):**
```json
{
  "message": "Status updated",
  "status": "online"
}
```

---

## Order Endpoints

### List Orders

Get authenticated user's orders.

**Endpoint:** `GET /orders`

**Headers:**
```
Authorization: Bearer <token>
```

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| status | string | Filter by status |
| limit | int | Number of results (default 20) |

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "order_number": "ORD-2024-0001",
      "restaurant": {
        "id": 1,
        "name": "Pizza Palace"
      },
      "status": "delivered",
      "total": 34.97,
      "created_at": "2024-01-15T12:00:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "total": 15,
    "per_page": 20
  }
}
```

---

### Create Order

Create a new order.

**Endpoint:** `POST /orders`

**Headers:**
```
Authorization: Bearer <token>
Content-Type: application/json
```

**Request Body:**
```json
{
  "restaurant_id": 1,
  "delivery_address": "123 Main Street",
  "delivery_lat": 40.7128,
  "delivery_lng": -74.0060,
  "items": [
    {
      "menu_item_id": 1,
      "quantity": 2,
      "price": 12.99
    },
    {
      "menu_item_id": 2,
      "quantity": 1,
      "variant_id": 5,
      "price": 8.99
    }
  ],
  "payment_method": "stripe",
  "notes": "No onions please"
}
```

**Response (201):**
```json
{
  "order": {
    "id": 1,
    "order_number": "ORD-2024-0002",
    "status": "pending",
    "subtotal": 34.97,
    "delivery_fee": 2.99,
    "tax": 2.80,
    "total": 40.76,
    "restaurant": {
      "id": 1,
      "name": "Pizza Palace"
    }
  },
  "client_secret": "pi_3abc1234567890_secret_xyz123"
}
```

---

### Get Order

Retrieve order details.

**Endpoint:** `GET /orders/{order_id}`

**Headers:**
```
Authorization: Bearer <token>
```

**Response (200):**
```json
{
  "id": 1,
  "order_number": "ORD-2024-0001",
  "status": "preparing",
  "rider": {
    "id": 5,
    "name": "Mike R.",
    "phone": "+1234567890",
    "photo": "https://..."
  },
  "items": [
    {
      "id": 1,
      "name": "Margherita Pizza",
      "quantity": 2,
      "price": 12.99,
      "subtotal": 25.98
    }
  ],
  "timeline": [
    {
      "status": "confirmed",
      "timestamp": "2024-01-15T12:00:00Z"
    },
    {
      "status": "preparing",
      "timestamp": "2024-01-15T12:15:00Z"
    }
  ],
  "estimated_delivery": "2024-01-15T12:45:00Z"
}
```

---

### Update Order Status (Restaurant)

Confirm or update order status.

**Endpoint:** `POST /orders/{order_id}/confirm`

**Headers:**
```
Authorization: Bearer <token>
```

**Response (200):**
```json
{
  "order": {
    "id": 1,
    "status": "confirmed"
  }
}
```

---

### Start Preparing

Mark order as being prepared.

**Endpoint:** `POST /orders/{order_id}/preparing`

**Response (200):**
```json
{
  "order": {
    "id": 1,
    "status": "preparing"
  }
}
```

---

### Mark On The Way

Update status to out for delivery.

**Endpoint:** `POST /orders/{order_id}/on-the-way`

**Response (200):**
```json
{
  "order": {
    "id": 1,
    "status": "on_the_way"
  }
}
```

---

### Mark Delivered

Complete the order delivery.

**Endpoint:** `POST /orders/{order_id}/deliver`

**Response (200):**
```json
{
  "order": {
    "id": 1,
    "status": "delivered"
  }
}
```

---

### Cancel Order

Cancel an order (if allowed).

**Endpoint:** `POST /orders/{order_id}/cancel`

**Request Body:**
```json
{
  "reason": "Changed my mind"
}
```

**Response (200):**
```json
{
  "order": {
    "id": 1,
    "status": "cancelled"
  }
}
```

---

### Restaurant Orders

Get orders for restaurant.

**Endpoint:** `GET /orders/restaurant/list`

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| status | string | Filter by status |
| date | date | Filter by date |

**Response (200):**
```json
{
  "orders": [
    {
      "id": 1,
      "order_number": "ORD-2024-0001",
      "customer": {
        "name": "John D.",
        "phone": "+1234567890"
      },
      "status": "confirmed",
      "total": 40.76,
      "created_at": "2024-01-15T12:00:00Z"
    }
  ]
}
```

---

### Rider Orders

Get assigned orders for rider.

**Endpoint:** `GET /orders/rider/list`

**Response (200):**
```json
{
  "orders": [
    {
      "id": 1,
      "order_number": "ORD-2024-0001",
      "status": "confirmed",
      "restaurant": {
        "name": "Pizza Palace",
        "address": "456 Oak Ave"
      },
      "delivery_address": "123 Main St",
      "total": 40.76
    }
  ]
}
```

---

## Restaurant Endpoints

### List Restaurants

Get list of available restaurants.

**Endpoint:** `GET /restaurants`

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| category | string | Filter by category |
| search | string | Search by name |
| lat | float | User latitude |
| lng | float | User longitude |
| open | bool | Only open restaurants |

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Pizza Palace",
      "slug": "pizza-palace",
      "description": "Best pizza in town",
      "rating": 4.5,
      "review_count": 230,
      "delivery_time": "25-35 min",
      "delivery_fee": 2.99,
      "is_open": true,
      "logo": "https://...",
      "cover_image": "https://..."
    }
  ]
}
```

---

### Get Restaurant

Get restaurant details.

**Endpoint:** `GET /restaurants/{restaurant_id}`

**Response (200):**
```json
{
  "id": 1,
  "name": "Pizza Palace",
  "description": "Best pizza in town",
  "address": "456 Oak Ave",
  "phone": "+1234567890",
  "rating": 4.5,
  "is_open": true,
  "opening_hours": {
    "monday": "09:00 - 22:00",
    "tuesday": "09:00 - 22:00"
  },
  "categories": [
    {
      "id": 1,
      "name": "Pizzas",
      "items": [...]
    }
  ]
}
```

---

### Get Restaurant Menu

Get restaurant menu with items.

**Endpoint:** `GET /restaurants/{restaurant_id}/menu`

**Response (200):**
```json
{
  "restaurant": {
    "id": 1,
    "name": "Pizza Palace"
  },
  "categories": [
    {
      "id": 1,
      "name": "Pizzas",
      "items": [
        {
          "id": 1,
          "name": "Margherita",
          "description": "Classic tomato and mozzarella",
          "price": 12.99,
          "image": "https://...",
          "variants": [
            {
              "id": 1,
              "name": "Medium",
              "price": 12.99
            },
            {
              "id": 2,
              "name": "Large",
              "price": 16.99
            }
          ]
        }
      ]
    }
  ]
}
```

---

### Update Restaurant Profile

Update restaurant information.

**Endpoint:** `PUT /restaurants/profile`

**Headers:**
```
Authorization: Bearer <token>
```

**Request Body:**
```json
{
  "name": "New Restaurant Name",
  "description": "Updated description",
  "address": "789 New Street",
  "phone": "+1987654321",
  "opening_hours": {
    "monday": "10:00 - 23:00"
  }
}
```

---

### Create Category

Create menu category.

**Endpoint:** `POST /restaurants/categories`

**Request Body:**
```json
{
  "name": "Desserts",
  "description": "Sweet treats"
}
```

---

### Create Menu Item

Create menu item.

**Endpoint:** `POST /restaurants/menu-items`

**Request Body:**
```json
{
  "category_id": 1,
  "name": "Tiramisu",
  "description": "Classic Italian dessert",
  "price": 7.99,
  "is_available": true,
  "image": "https://..."
}
```

---

## Rider Endpoints

### List Riders

List all riders (admin only).

**Endpoint:** `GET /riders`

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Mike R.",
      "email": "mike@example.com",
      "phone": "+1234567890",
      "status": "online",
      "rating": 4.8,
      "total_deliveries": 450
    }
  ]
}
```

---

### Get Rider

Get rider details.

**Endpoint:** `GET /riders/{rider_id}`

**Response (200):**
```json
{
  "id": 1,
  "name": "Mike R.",
  "phone": "+1234567890",
  "status": "online",
  "rating": 4.8,
  "total_deliveries": 450,
  "current_location": {
    "lat": 40.7128,
    "lng": -74.0060
  }
}
```

---

### Available Riders

Get list of available riders near restaurant.

**Endpoint:** `GET /riders/available/list`

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| lat | float | Restaurant latitude |
| lng | float | Restaurant longitude |
| radius | int | Search radius in km |

**Response (200):**
```json
{
  "riders": [
    {
      "id": 1,
      "name": "Mike R.",
      "distance_km": 1.2,
      "rating": 4.8,
      "current_orders": 0,
      "current_location": {
        "lat": 40.71,
        "lng": -74.00
      }
    }
  ]
}
```

---

### Update Rider Location

Update rider's current GPS location.

**Endpoint:** `POST /riders/location`

**Headers:**
```
Authorization: Bearer <token>
```

**Request Body:**
```json
{
  "latitude": 40.7128,
  "longitude": -74.0060
}
```

**Response (200):**
```json
{
  "message": "Location updated"
}
```

---

### Get Rider Location

Get rider's current location.

**Endpoint:** `GET /riders/{rider_id}/location`

**Response (200):**
```json
{
  "rider_id": 1,
  "latitude": 40.7128,
  "longitude": -74.0060,
  "updated_at": "2024-01-15T12:30:00Z"
}
```

---

### Update Rider Status

Update rider availability status.

**Endpoint:** `POST /riders/status`

**Request Body:**
```json
{
  "status": "online"  // online, offline, busy
}
```

**Response (200):**
```json
{
  "status": "online"
}
```

---

## Payment Endpoints

### Get Payment

Get payment details for order.

**Endpoint:** `GET /payments/{order_id}`

**Headers:**
```
Authorization: Bearer <token>
```

**Response (200):**
```json
{
  "order_id": 1,
  "amount": 40.76,
  "currency": "usd",
  "status": "succeeded",
  "stripe_payment_intent": "pi_abc123",
  "created_at": "2024-01-15T12:00:00Z"
}
```

---

### Create Payment Intent

Create Stripe payment intent.

**Endpoint:** `POST /payments/create-intent`

**Request Body:**
```json
{
  "order_id": 1,
  "amount": 40.76,
  "currency": "usd"
}
```

**Response (200):**
```json
{
  "client_secret": "pi_abc123_secret_xyz",
  "payment_intent_id": "pi_abc123"
}
```

---

### Refund Payment

Process a refund.

**Endpoint:** `POST /payments/refund`

**Request Body:**
```json
{
  "order_id": 1,
  "amount": 40.76,
  "reason": "Customer request"
}
```

**Response (200):**
```json
{
  "refund_id": "re_abc123",
  "amount": 40.76,
  "status": "succeeded"
}
```

---

## Geocoding Endpoints

### Geocode Address

Convert address to coordinates.

**Endpoint:** `POST /geocode/address`

**Request Body:**
```json
{
  "address": "123 Main Street, New York, NY"
}
```

**Response (200):**
```json
{
  "latitude": 40.7128,
  "longitude": -74.0060,
  "formatted_address": "123 Main St, New York, NY 10001"
}
```

---

### Validate Address

Validate delivery address.

**Endpoint:** `POST /geocode/validate`

**Request Body:**
```json
{
  "address": "123 Main Street",
  "latitude": 40.7128,
  "longitude": -74.0060
}
```

**Response (200):**
```json
{
  "valid": true,
  "suggestions": []
}
```

---

### Calculate Distance

Calculate distance between two points.

**Endpoint:** `POST /geocode/distance`

**Request Body:**
```json
{
  "from_lat": 40.7128,
  "from_lng": -74.0060,
  "to_lat": 40.7580,
  "to_lng": -73.9855
}
```

**Response (200):**
```json
{
  "distance_km": 5.2,
  "estimated_time_min": 15
}
```

---

## Webhook Endpoints

### Stripe Webhook

Handle Stripe events.

**Endpoint:** `POST /webhooks/stripe`

**Headers:**
```
Content-Type: application/json
Stripe-Signature: sig_xxx
```

**Note:** This endpoint requires rate limiting configuration.

---

## Error Responses

### 400 Bad Request
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
  "message": "This action is unauthorized."
}
```

### 404 Not Found
```json
{
  "message": "Resource not found."
}
```

### 422 Validation Error
```json
{
  "message": "Validation failed",
  "errors": {
    "items": ["At least one item is required"]
  }
}
```

### 429 Too Many Requests
```json
{
  "message": "Too many attempts. Please try again later."
}
```

### 500 Server Error
```json
{
  "message": "Server error. Please try again later."
}
```

---

## Postman Collection

Import the collection from `docs/openapi.yaml` into Postman or Insomnia for testing.

---

## SDKs & Libraries

### Official
- PHP SDK: Built-in Laravel Sanctum

### Community
- JavaScript: Use standard `fetch` or Axios
- Mobile: Use Flutter, React Native, or Swift with HTTP client

---

## Support

For API support, contact: api-support@quickbite.com