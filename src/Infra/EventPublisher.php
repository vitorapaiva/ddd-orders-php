<?php

declare(strict_types=1);

namespace Orders\Infra;

use Orders\Ports\Outbound\EventPublisherInterface;

class EventPublisher implements EventPublisherInterface
{
    public function publish(object $event): void
    {
        $eventArray = method_exists($event, 'toArray')
            ? $event->toArray()
            : (array) $event;

        echo ">>> Event published: {$eventArray['type']}\n";
        echo "    Data: " . json_encode($eventArray['data'], JSON_PRETTY_PRINT) . "\n";
        echo "    Timestamp: {$eventArray['timestamp']}\n";
    }
}
