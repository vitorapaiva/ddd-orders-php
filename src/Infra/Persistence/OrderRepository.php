<?php

declare(strict_types=1);

namespace Orders\Infra\Persistence;

use Orders\Domain\Entities\Order;
use Orders\Ports\Outbound\OrderRepositoryInterface;
use Orders\Adapters\Inbound\OrderAdapter as InboundAdapter;
use Orders\Adapters\Outbound\OrderAdapter as OutboundAdapter;

class OrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private readonly \PDO $pdo
    ) {}

    public function save(Order $order): void
    {
        $data = OutboundAdapter::orderToDb($order);

        $sql = "
            INSERT INTO orders 
            (id, customer_id, shipping_address, billing_address, items, total, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt = $this->pdo->prepare($sql);
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
        $sql = "SELECT * FROM orders WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);

        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        return InboundAdapter::dbToOrder($row);
    }

    public function update(Order $order): void
    {
        $data = OutboundAdapter::orderToDb($order);

        $sql = "UPDATE orders SET status = ?, updated_at = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['status'],
            $data['updated_at'],
            $data['id'],
        ]);
    }

    public function listAll(): array
    {
        $sql = "SELECT * FROM orders ORDER BY created_at DESC";
        $stmt = $this->pdo->query($sql);

        $orders = [];
        while ($row = $stmt->fetch()) {
            $orders[] = InboundAdapter::dbToOrder($row);
        }

        return $orders;
    }
}
