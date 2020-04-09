<?php

declare(strict_types=1);

namespace Formation;

class MarchandisePartiallyReceived implements Event
{
    /** @var int|null */
    private $nbPackagesLeft;

    /** @var int|null */
    private ?int $nbPackagesTaken;

    public function __construct(?int $nbPackagesLeft = null, ?int $nbPackagesTaken = null)
    {
        $this->nbPackagesLeft = $nbPackagesLeft;
        $this->nbPackagesTaken = $nbPackagesTaken;
    }

    public function nbPackagesLeft(): ?int
    {
        return $this->nbPackagesLeft;
    }

    public function nbPackagesTaken(): ?int
    {
        return $this->nbPackagesTaken;
    }
}
