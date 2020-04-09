<?php

declare(strict_types=1);

namespace Formation\Command;

class Order
{
    /** @var array */
    private $raised;

    /**@var OrderProjection */
    private OrderProjection $projection;

    public function __construct(array $events = [])
    {
        $this->projection = new OrderProjection();
        $this->raised = [];

        foreach ($events as $event) {
            $this->raise($event);
        }
    }

    public function start(int $nbPackages = null)
    {
        $this->raise(new OrderStarted($nbPackages));
    }

    public function takeMarchandise(int $nbPackagesTaken = null)
    {
        if (in_array(MarchandiseReceived::class, array_keys($this->events()))) {
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

        $this->raised[get_class($event)] = $event;
    }

    public function events(): array
    {
        return $this->raised;
    }

    // Les évènements après le constructeur
    public function uncommitedEvents(): array
    {
        return $this->raised;
    }
}
