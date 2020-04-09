<?php

declare(strict_types=1);

namespace Formation\Command;

class OrderStarted implements Event
{
    /** @var int|null */
    private $packages;

    public function __construct(?int $packages = null)
    {
        $this->packages = $packages;
    }

    public function packages(): ?int
    {
        return $this->packages;
    }
}
