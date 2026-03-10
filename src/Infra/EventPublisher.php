<?php

declare(strict_types=1);

namespace Orders\Infra;

use Orders\Infra\EventHandlers\EventHandlerInterface;
use Orders\Ports\Outbound\EventPublisherInterface;

class EventPublisher implements EventPublisherInterface
{
    /** @var EventHandlerInterface[] */
    private array $handlers;

    public function __construct(EventHandlerInterface ...$handlers)
    {
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

        echo ">>> Event published: {$eventArray['type']}\n";
        echo "    Data: " . json_encode($eventArray['data'], JSON_PRETTY_PRINT) . "\n";
        echo "    Timestamp: {$eventArray['timestamp']}\n";
    }
}
