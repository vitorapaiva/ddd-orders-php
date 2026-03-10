<?php

declare(strict_types=1);

namespace Orders\Adapters\Outbound;

use Orders\Domain\Entities\Order;

interface OrderDbAdapterInterface
{
    public function toDb(Order $order): array;
}
