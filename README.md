# DDD Orders - PHP

Serviço de Pedidos implementado em PHP seguindo Domain-Driven Design e Arquitetura Hexagonal.

## Estrutura do Projeto

```
src/
├── Domain/                         # Núcleo do Domínio
│   ├── Entities/
│   │   └── Pedido.php              # Entidade Pedido (raiz do agregado)
│   ├── ValueObjects/
│   │   ├── Endereco.php            # Objeto de Valor Endereço
│   │   ├── Item.php                # Objeto de Valor Item
│   │   └── StatusPedido.php        # Enum de Status
│   └── Events/
│       ├── PedidoCriado.php        # Evento de domínio
│       └── PedidoAtualizado.php    # Evento de domínio
├── Ports/                          # Portas (Interfaces)
│   ├── Inbound/                    # Casos de Uso
│   │   ├── FecharPedidoUseCase.php
│   │   ├── AtualizarStatusPedidoUseCase.php
│   │   ├── ConsultarPedidoUseCase.php
│   │   └── ListarPedidosUseCase.php
│   └── Outbound/                   # Interfaces para mundo externo
│       ├── PedidoRepositoryInterface.php
│       ├── ProdutosServiceInterface.php
│       └── EventPublisherInterface.php
└── Adapters/                       # Adaptadores (Implementações)
    ├── Inbound/
    │   └── Http/                   # Controllers REST
    │       ├── FecharPedidoController.php
    │       ├── ListarPedidosController.php
    │       ├── ConsultarPedidoController.php
    │       └── AtualizarStatusController.php
    └── Outbound/
        ├── MySQLPedidoRepository.php
        ├── HttpProdutosClient.php
        └── ConsoleEventPublisher.php
```

## Arquitetura Hexagonal

### Núcleo (Domain)
O núcleo contém as regras de negócio, isolado de detalhes técnicos:
- **Entidades**: Classes com identidade e comportamento (Pedido)
- **Objetos de Valor**: Classes imutáveis definidas por atributos (Endereco, Item)
- **Eventos**: Fatos que ocorreram no domínio

### Portas (Ports)
Interfaces que definem como o núcleo se comunica com o mundo externo:
- **Inbound**: Casos de uso (FecharPedidoUseCase, etc.)
- **Outbound**: Interfaces PHP (PedidoRepositoryInterface, etc.)

### Adaptadores (Adapters)
Implementações concretas das portas:
- **HTTP**: Controllers Slim Framework
- **MySQL**: Repositório com PDO
- **HTTP Client**: Guzzle para serviço de Produtos

## API

### POST /order/close
Fecha um novo pedido.

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
Lista todos os pedidos.

### GET /pedidos/{id}
Consulta um pedido por ID.

### PUT /pedidos/{id}/status
Atualiza o status de um pedido.

## Executando

```bash
# Instalar dependências
composer install

# Iniciar servidor
composer start
# ou
php -S localhost:3000 -t public
```

## Banco de Dados

O serviço cria automaticamente a tabela `pedidos` no MySQL.

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

## Variáveis de Ambiente

```env
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=orders_db
DB_USER=root
DB_PASSWORD=root
PRODUCTS_SERVICE_URL=http://localhost:3001
```
