<?php

declare(strict_types=1);

namespace Orders\Infra;

use Orders\Infra\EventHandlers\EventHandlerInterface;
use Orders\Ports\Outbound\EventPublisherInterface;
use Psr\Log\LoggerInterface;

class EventPublisher implements EventPublisherInterface
{
    /** @var EventHandlerInterface[] */
    private array $handlers;

    public function __construct(
        private readonly LoggerInterface $logger,
        EventHandlerInterface ...$handlers
    ) {
        $this->handlers = $handlers;
    }

    public function publish(object $event): void
    {
        foreach ($this->handlers as $handler) {
            if ($handler->supports($event)) {
                $handler->handle($event);
            }
        }

        $this->logEvent($event);
    }

    private function logEvent(object $event): void
    {
        $eventArray = method_exists($event, 'toArray')
            ? $event->toArray()
            : (array) $event;

        $this->logger->info('Event published', [
            'type' => $eventArray['type'] ?? 'unknown',
            'data' => $eventArray['data'] ?? [],
            'timestamp' => $eventArray['timestamp'] ?? null,
        ]);
    }
}
