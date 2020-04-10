<?php

declare(strict_types=1);

namespace Formation;

use Formation\Command\MarchandiseReceived;
use Formation\Command\Order;
use Formation\Command\OrderStarted;
use Formation\PubSub\PubSub;
use Formation\Query\OrderId;
use Formation\Query\WaitingOrdersProjection;
use PHPUnit\Framework\TestCase;

class PubSubTest extends TestCase
{
    public function test_Should_store_events_when_publish_event()
    {
        $pubsub = new PubSub([]);
        $pubsub->publish(new OrderStarted(), new OrderId('foo'));

        $this->assertContainsEquals(new OrderStarted(), $pubsub->events());
    }

    public function test_Should_call_handlers_when_publish_event()
    {
        $projection = new FakeProjection();

        $event = new OrderStarted();
        $orderId = new OrderId('foo');

        $pubsub = new PubSub([$projection]);
        $pubsub->publish($event, $orderId);

        $this->assertContainsEquals([$event, $orderId], $projection->events);
    }

    public function test_Should_display_updated_projection_when_send_command()
    {
        $order = new Order();
        $order->start();

        $projection = new WaitingOrdersProjection();
        $orderId = new OrderId('foo');
        $pubsub = new PubSub([$projection]);

        foreach ($order->uncommitedEvents() as $event) {
            $pubsub->publish($event, $orderId);
        }

        $this->assertContainsEquals($orderId, $projection->orderIds());
    }
}
