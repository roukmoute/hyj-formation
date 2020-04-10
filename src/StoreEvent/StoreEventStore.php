<?php

declare(strict_types=1);

namespace Formation\StoreEvent;

use EventStore\EventStore;
use EventStore\Exception\StreamDeletedException;
use EventStore\Exception\StreamNotFoundException;
use EventStore\Http\GuzzleHttpClient;
use EventStore\StreamFeed\Entry;
use EventStore\WritableEvent;
use EventStore\WritableEventCollection;
use Exception;
use Formation\Command\Order;
use Formation\Query\OrderId;
use GuzzleHttp\Client;

class StoreEventStore implements StoreEvent
{
    private EventStore $eventStore;
    private array $sequence;
    private string $streamName;

    public function __construct(string $streamName)
    {
        $this->eventStore = new EventStore(
            'http://127.0.0.1:2113',
            new GuzzleHttpClient(new Client(['auth' => ['admin', 'changeit']]))
        );
        $this->streamName = $streamName;
    }

    public function storeEvents(array $events, OrderId $aggregateId, int $sequence = null): void
    {
        foreach ($events as $event) {
            $eventCollection[] = WritableEvent::newInstance(
                sprintf('%s', get_class($event)),
                [serialize($event)]
            );
        }

        if ($sequence) {
            $this->sequence[$aggregateId->id()] = $sequence;
        }
        try {
            if (!isset($this->sequence[$aggregateId->id()])) {
                $feed = $this->eventStore->openStreamFeed($aggregateId->id());
                $this->sequence[$aggregateId->id()] = count($feed->getEntries()) - 1;
            }
        } catch (StreamNotFoundException $e) {
            $this->sequence[$aggregateId->id()] = -1;
        }

        $this->eventStore->writeToStream(
            $aggregateId->id(),
            new WritableEventCollection($eventCollection),
            $this->sequence[$aggregateId->id()]
        );

        $this->sequence[$aggregateId->id()] += count($eventCollection);
    }

    public function allEventsOfAggregateId(OrderId $aggregateId): array
    {
        $events = [];

        /** @var Entry $entry */
        foreach ($this->eventStore->openStreamFeed($aggregateId->id())->getEntries() as $entry) {
            $eventUrl = $entry->getEventUrl();
            $event = $this->eventStore->readEvent($eventUrl);
            $events[] = unserialize($event->getData()[0]);
        }

        return $events;
    }
}
