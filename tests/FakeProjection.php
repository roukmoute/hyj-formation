<?php

declare(strict_types=1);

namespace Formation;

use Formation\Command\Event;
use Formation\PubSub\Handler;
use Formation\Query\OrderId;

class FakeProjection implements Handler
{
    public array $events;

    public function __construct()
    {
        $this->events = [];
    }

    public function handle(Event $event, OrderId $orderId)
    {
        $this->events[] = [$event, $orderId];
    }
}
