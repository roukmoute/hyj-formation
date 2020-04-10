<?php

declare(strict_types=1);

namespace Formation\PubSub;

use Formation\Command\Event;
use Formation\Query\OrderId;

interface Handler
{
    public function handle(Event $event, OrderId $orderId);
}
