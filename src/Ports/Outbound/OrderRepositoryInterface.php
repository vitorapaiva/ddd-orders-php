<?php

declare(strict_types=1);

namespace Orders\Ports\Outbound;

use Orders\Domain\Entities\Order;

interface OrderRepositoryInterface
{
    public function save(Order $order): void;
    public function findById(string $id): ?Order;
    public function update(Order $order): void;
    public function listAll(): array;
}
