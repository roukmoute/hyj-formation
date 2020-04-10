<?php

declare(strict_types=1);

namespace Formation\Command;

class Order
{
    /** @var array */
    private $events;

    /** @var array */
    private $uncommittedEvents;

    /**@var OrderProjection */
    private OrderProjection $projection;

    public function __construct(array $events = [])
    {
        $this->projection = new OrderProjection();
        $this->events = [];
        $this->uncommittedEvents = [];

        foreach ($events as $event) {
            $this->raise($event);
        }

        $this->uncommittedEvents = [];
    }

    public function start(int $nbPackages = null)
    {
        $this->raise(new OrderStarted($nbPackages));
    }

    public function takeMarchandise(int $nbPackagesTaken = null)
    {
        if (in_array(
            MarchandiseReceived::class,
            array_map(
                function (Event $event) {
                    return get_class($event);
                },
                $this->events()
            )
        )) {
            return;
        }

        $numberPackagesLeft = null;
        $event = new MarchandiseReceived($nbPackagesTaken);

        if ($nbPackagesTaken) {
            $numberPackagesLeft = $this->projection->nbPackagesLeft() - $nbPackagesTaken;
            if ($numberPackagesLeft > 0) {
                $event = new MarchandisePartiallyReceived($this->projection->nbPackagesLeft(), $nbPackagesTaken);
            }
        }

        $this->raise($event);
    }

    private function raise(Event $event): void
    {
        $this->projection->apply($event);

        if (!$this->projection->isStarted() && !$event instanceof OrderStarted) {
            return;
        }

        $this->addEvent($event);
    }

    public function events(): array
    {
        return $this->events;
    }

    // Les évènements après le constructeur
    public function uncommitedEvents(): array
    {
        return $this->uncommittedEvents;
    }

    private function addEvent(Event $event): void
    {
        $this->uncommittedEvents[] = $event;
        $this->events[] = $event;
    }
}
