<?php

declare(strict_types=1);

namespace Orders\Ports\Outbound;

interface EventPublisherInterface
{
    public function publish(object $event): void;
}
