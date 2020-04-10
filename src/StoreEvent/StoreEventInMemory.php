<?php

declare(strict_types=1);

namespace Formation\StoreEvent;

use Exception;
use Formation\Command\Order;
use Formation\Query\OrderId;

class StoreEventInMemory implements StoreEvent
{
    /** @var array[aggregateId] = [events] */
    private $events;

    /** @var array[aggregateId] = sequence_number */
    private $sequence;

    public function storeEvents(array $events, OrderId $aggregateId, int $sequence = 0): void
    {
        if (!isset($this->events[$aggregateId->id()])) {
            $this->sequence[$aggregateId->id()] = 0;
            $this->events[$aggregateId->id()] = [];
        }

        if ($this->sequence[$aggregateId->id()] !== $sequence) {
            throw new Exception(sprintf('Sequence number failed, expected %d and given %d', $this->sequence[$aggregateId->id()], $sequence));
        }

        $this->events[$aggregateId->id()] = array_merge($this->events[$aggregateId->id()], $events);
        $this->sequence[$aggregateId->id()] = count($this->events[$aggregateId->id()]);
    }

    public function allEventsOfAggregateId(OrderId $aggregateId): array
    {
        return $this->events[$aggregateId->id()];
    }
}
