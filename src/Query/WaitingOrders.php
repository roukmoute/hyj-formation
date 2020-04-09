<?php

declare(strict_types=1);

namespace Formation\Query;

use Formation\Command\Event;
use Formation\Command\MarchandiseReceived;
use Formation\Command\OrderStarted;

class WaitingOrders
{
    /** @var Event */
    private Event $event;

    private $listIds;

    public function handle(Event $event, OrderId $id)
    {
        if ($event instanceOf OrderStarted) {
            $this->listIds[(string) $id] = $id;
        } elseif ($event instanceof MarchandiseReceived) {
            unset($this->listIds[(string) $id]);
        }
    }

    public function orderIds(): array
    {
        return $this->listIds;
    }
}
