<?php

declare(strict_types=1);

namespace Formation;

interface EventHandler
{
    public function handle(Event $event);
}
