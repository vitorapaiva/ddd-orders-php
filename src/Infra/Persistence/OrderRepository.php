<?php

declare(strict_types=1);

namespace Orders\Infra\Persistence;

use Orders\Domain\Entities\Order;
use Orders\Ports\Outbound\OrderRepositoryInterface;
use Orders\Adapters\Inbound\OrderDbAdapterInterface as InboundDbAdapter;
use Orders\Adapters\Outbound\OrderDbAdapterInterface as OutboundDbAdapter;

class OrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private readonly \PDO $pdo,
        private readonly InboundDbAdapter $inboundDbAdapter,
        private readonly OutboundDbAdapter $outboundDbAdapter
    ) {}

    public function save(Order $order): void
    {
        $data = $this->outboundDbAdapter->toDb($order);
        $stmt = $this->pdo->prepare(OrderQueries::INSERT);
        $stmt->execute([
            $data['id'],
            $data['customer_id'],
            $data['shipping_address'],
            $data['billing_address'],
            $data['items'],
            $data['total'],
            $data['status'],
            $data['created_at'],
            $data['updated_at'],
        ]);
    }

    public function findById(string $id): ?Order
    {
        $stmt = $this->pdo->prepare(OrderQueries::FIND_BY_ID);
        $stmt->execute([$id]);

        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        return $this->inboundDbAdapter->toOrder($row);
    }

    public function update(Order $order): void
    {
        $data = $this->outboundDbAdapter->toDb($order);
        $stmt = $this->pdo->prepare(OrderQueries::UPDATE_STATUS);
        $stmt->execute([
            $data['status'],
            $data['updated_at'],
            $data['id'],
        ]);
    }

    public function listAll(): array
    {
        $stmt = $this->pdo->query(OrderQueries::LIST_ALL);

        $orders = [];
        while ($row = $stmt->fetch()) {
            $orders[] = $this->inboundDbAdapter->toOrder($row);
        }

        return $orders;
    }
}
