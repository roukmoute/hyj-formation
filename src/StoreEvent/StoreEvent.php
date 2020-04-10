<?php

namespace Formation\StoreEvent;

use Formation\Query\OrderId;

interface StoreEvent
{
    public function storeEvents(array $events, OrderId $aggregateId, int $sequence = 0): void;

    public function allEventsOfAggregateId(OrderId $aggregateId): array;
}
