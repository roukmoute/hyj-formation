<?php

declare(strict_types=1);

namespace Formation\PubSub;

use Formation\Command\Event;
use Formation\Query\OrderId;

class PubSub
{
    /** @var Handler[] */
    private array $handlers;

    /** @var array */
    private $events;

    public function __construct(array $handlers)
    {
        $this->handlers = $handlers;
    }

    public function handlers(): array
    {
        return $this->handlers;
    }

    public function publish(Event $event, OrderId $orderId): void
    {
        $this->events[] = $event;

        /** @var Handler $handler */
        foreach ($this->handlers as $handler) {
            $handler->handle($event, $orderId);
        }
    }

    public function events(): array
    {
        return $this->events;
    }
}
