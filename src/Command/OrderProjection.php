<?php

declare(strict_types=1);

namespace Formation\Command;

class OrderProjection
{
    private bool $isStarted;
    private int $numberPackagesLeft;

    public function __construct()
    {
        $this->isStarted = false;
        $this->numberPackagesLeft = 0;
    }

    /** @todo: Mettre en place un visitor */
    public function apply(Event $event)
    {
        if ($event instanceOf OrderStarted) {
            $this->isStarted = true;
            if ($event->packages()) {
                $this->numberPackagesLeft = $event->packages();
            }
        } elseif ($event instanceOf MarchandiseReceived) {
            $this->numberPackagesLeft = 0;
        } elseif ($event instanceof MarchandisePartiallyReceived) {
            $this->numberPackagesLeft -= $event->nbPackagesTaken();
        }
    }

    public function isStarted(): bool
    {
        return $this->isStarted;
    }

    public function nbPackagesLeft(): int
    {
        return $this->numberPackagesLeft;
    }
}
