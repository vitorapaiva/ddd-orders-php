# DDD Orders - PHP

Order service implemented in PHP following Domain-Driven Design and Hexagonal Architecture.

## Hexagonal Architecture

### Core (Domain)
The core contains business rules, isolated from technical details:
- **Entities**: Classes with identity and behavior (Order)
- **Value Objects**: Immutable classes defined by attributes (Address, Item)
- **Events**: Facts that occurred in the domain

### Ports
Interfaces that define how the core communicates with the outside world:
- **Inbound**: Use cases (CloseOrderUseCase, etc.)
- **Outbound**: PHP interfaces (OrderRepositoryInterface, etc.)

### Adapters
Concrete implementations of the ports:
- **HTTP**: Slim Framework controllers
- **MySQL**: Repository with PDO
- **HTTP Client**: Guzzle for Products service

## API

### POST /orders/close
Closes a new order.

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
  "billing_address": {
    "street_type": "Street",
    "street_name": "Main",
    "number": "123",
    "complement": "Apt 45",
    "district": "Center",
    "city": "New York",
    "state": "NY",
    "zip_code": "01234-567"
  },
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
# Install dependencies
composer install

# Start server
composer start
# or
php -S localhost:3000 -t public
```

## Database

The service automatically creates the `orders` table in MySQL.

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
