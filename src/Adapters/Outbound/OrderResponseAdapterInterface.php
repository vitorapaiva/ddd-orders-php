<?php

declare(strict_types=1);

namespace Orders\Adapters\Outbound;

use Orders\Domain\Entities\Order;

interface OrderResponseAdapterInterface
{
    public function toJson(Order $order): array;
    
    public function toJsonList(array $orders): array;
}
