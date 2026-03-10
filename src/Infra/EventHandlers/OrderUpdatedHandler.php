<?php

declare(strict_types=1);

namespace Orders\Infra\EventHandlers;

use Orders\Domain\Events\OrderUpdated;

class OrderUpdatedHandler implements EventHandlerInterface
{
    public function supports(object $event): bool
    {
        return $event instanceof OrderUpdated;
    }

    public function handle(object $event): void
    {
        if (!$event instanceof OrderUpdated) {
            return;
        }

        // Future: notify other services about status changes
    }
}
