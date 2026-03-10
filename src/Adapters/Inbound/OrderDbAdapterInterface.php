<?php

declare(strict_types=1);

namespace Orders\Adapters\Inbound;

use Orders\Domain\Entities\Order;

interface OrderDbAdapterInterface
{
    public function toOrder(array $row): Order;
}
