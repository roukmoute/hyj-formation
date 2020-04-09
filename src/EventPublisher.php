<?php

declare(strict_types=1);

namespace Formation;

class EventPublisher
{
    /** @var array */
    private array $handlers;

    public function __construct()
    {
        $this->handlers = [];
    }

    public function subscribe(EventHandler $event)
    {
        $this->handlers[get_class($event)] = $event;
    }

    public function publish(Event $event)
    {
        foreach ($this->handlers as $handler) {
            $handler->handle($event);
        }
    }
}
