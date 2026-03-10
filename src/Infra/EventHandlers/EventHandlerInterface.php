<?php

declare(strict_types=1);

namespace Orders\Infra\EventHandlers;

interface EventHandlerInterface
{
    public function handle(object $event): void;
    
    public function supports(object $event): bool;
}
