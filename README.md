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
- **Inbound**: Use cases (FecharPedidoUseCase, etc.)
- **Outbound**: PHP interfaces (PedidoRepositoryInterface, etc.)

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
  "cliente_id": "uuid-do-cliente",
  "endereco_entrega": {
    "tipo_logradouro": "Rua",
    "nome_logradouro": "das Flores",
    "numero": "123",
    "complemento": "Apto 45",
    "bairro": "Centro",
    "cidade": "São Paulo",
    "estado": "SP",
    "cep": "01234-567"
  },
  "endereco_cobranca": {
    "tipo_logradouro": "Rua",
    "nome_logradouro": "das Flores",
    "numero": "123",
    "complemento": "Apto 45",
    "bairro": "Centro",
    "cidade": "São Paulo",
    "estado": "SP",
    "cep": "01234-567"
  },
  "itens": [
    {"produto_id": "uuid-produto", "quantidade": 2, "preco": 29.90}
  ]
}
```

### GET /pedidos
Lists all orders.

### GET /pedidos/{id}
Gets an order by ID.

### PUT /pedidos/{id}/status
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

The service automatically creates the `pedidos` table in MySQL.

```sql
CREATE TABLE IF NOT EXISTS pedidos (
  id VARCHAR(36) PRIMARY KEY,
  cliente_id VARCHAR(36) NOT NULL,
  endereco_entrega JSON NOT NULL,
  endereco_cobranca JSON NOT NULL,
  itens JSON NOT NULL,
  valor_total DECIMAL(10, 2) NOT NULL,
  status VARCHAR(50) NOT NULL,
  criado_em TIMESTAMP NOT NULL,
  atualizado_em TIMESTAMP NOT NULL
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
