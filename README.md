# DDD Orders - PHP

Order service implemented in PHP following Domain-Driven Design and Hexagonal Architecture. This project is part of a comparative study on applying DDD in different programming paradigms.

## About the Project

The service manages orders in an e-commerce context. It exposes an HTTP API to create orders, list them, retrieve by ID, and update their status. The domain model and business rules are isolated from infrastructure through Hexagonal Architecture (Ports and Adapters).

## Domain Model

The core domain represents an **Order** with the following concepts:

- **Order**: Aggregate root with identity, customer reference, addresses, items, total, and status. Orders progress through a defined status lifecycle.
- **Address**: Value object with street, number, district, city, state, and zip code. Used for both shipping and billing.
- **Item**: Value object with product reference, quantity, and price.
- **Order status**: Orders follow a state machine (e.g. pending_payment → products_reserved → payment_processed → products_picked → shipped → delivered; cancellation is allowed in early stages).

The system uses **domain events** to communicate facts (e.g. order created, status updated). Event handlers react to these events to perform side effects such as reserving products in an external service.

## Hexagonal Architecture

### Core (Domain)
Business rules isolated from technical details:
- **Entities**: Classes with identity and behavior (Order)
- **Value Objects**: Immutable classes (Address, Item)
- **Events**: Facts that occurred in the domain

### Ports
Interfaces defining how the core communicates with the outside world:
- **Inbound**: Use cases (close order, list orders, get order, update status)
- **Outbound**: Repository, ProductsService, EventPublisher

### Adapters
Concrete implementations:
- **HTTP**: Slim Framework
- **MySQL**: PDO
- **HTTP Client**: Guzzle (Products service)

## API

### POST /orders/close
Creates and closes a new order.

**Request:**
```json
{
  "customer_id": "customer-uuid",
  "shipping_address": {
    "street_type": "Street",
    "street_name": "Main",
    "number": "123",
    "complement": "Apt 45",
    "district": "Center",
    "city": "New York",
    "state": "NY",
    "zip_code": "01234-567"
  },
  "billing_address": { ... },
  "items": [
    {"product_id": "product-uuid", "quantity": 2, "price": 29.90}
  ]
}
```

### GET /orders
Lists all orders.

### GET /orders/{id}
Gets an order by ID.

### PUT /orders/{id}/status
Updates an order status.

## Running

```bash
composer install
composer start
# or: php -S localhost:3000 -t public
```

## Database

The service creates the `orders` table in MySQL on startup.

```sql
CREATE TABLE IF NOT EXISTS orders (
  id VARCHAR(36) PRIMARY KEY,
  customer_id VARCHAR(36) NOT NULL,
  shipping_address JSON NOT NULL,
  billing_address JSON NOT NULL,
  items JSON NOT NULL,
  total DECIMAL(10, 2) NOT NULL,
  status VARCHAR(50) NOT NULL,
  created_at TIMESTAMP NOT NULL,
  updated_at TIMESTAMP NOT NULL
);
```

## Environment Variables

```env
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=orders_db
DB_USER=root
DB_PASSWORD=root
PRODUCTS_SERVICE_URL=http://localhost:3001
```
