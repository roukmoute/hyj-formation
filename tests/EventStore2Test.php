<?php

declare(strict_types=1);

namespace Formation;

use EventStore\Exception\WrongExpectedVersionException;
use Formation\Command\MarchandisePartiallyReceived;
use Formation\Command\MarchandiseReceived;
use Formation\Command\OrderStarted;
use Formation\Query\OrderId;
use Formation\StoreEvent\StoreEventStore;
use PHPUnit\Framework\TestCase;

class EventStoreTest extends TestCase
{
    public function test_Should_return_all_events_when_get_all_events_of_aggregate_instance_after_store_events_of_an_aggregate_instance()
    {
        $aggregateId = new OrderId('foo');
        $event1 = new OrderStarted();
        $event2 = new MarchandiseReceived();

        $storeEvent = new StoreEventStore('mySuperStream');
        $storeEvent->storeEvents([$event1, $event2], $aggregateId);

        $eventsOfAggregateId = $storeEvent->allEventsOfAggregateId($aggregateId);

        $this->assertContainsEquals($event1, $eventsOfAggregateId);
        $this->assertContainsEquals($event2, $eventsOfAggregateId);
    }

    public function test_Should_return_only_events_of_aggregate_instance_when_get_all_events_of_aggregate_instance_after_store_events_of_several_aggregate_instances(
    )
    {
        $aggregateId1 = new OrderId('foo');
        $aggregateId2 = new OrderId('bar');
        $event1 = new OrderStarted();
        $event2 = new MarchandiseReceived();
        $event3 = new MarchandisePartiallyReceived(1, 2);

        $storeEvent = new StoreEventStore('mySuperStream');
        $storeEvent->storeEvents([$event1, $event2], $aggregateId1);
        $storeEvent->storeEvents([$event2, $event3], $aggregateId2);

        $eventsOfAggregateId = $storeEvent->allEventsOfAggregateId($aggregateId1);

        $this->assertContainsEquals($event1, $eventsOfAggregateId);
        $this->assertContainsEquals($event2, $eventsOfAggregateId);
        $this->assertNotContainsEquals($event3, $eventsOfAggregateId);
    }

    public function test_Should_throw_when_store_event_with_sequence_event_already_stored()
    {
        $this->expectException(WrongExpectedVersionException::class);

        $aggregateId = new OrderId('baz');
        $event1 = new OrderStarted();
        $event2 = new MarchandiseReceived();

        $storeEvent = new StoreEventStore('mySuperStream');

        $storeEvent->storeEvents([$event1, $event2], $aggregateId, -1);
        $storeEvent->storeEvents([$event1, $event2], $aggregateId, 2);
    }
}
