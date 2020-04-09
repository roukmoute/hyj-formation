<?php

declare(strict_types=1);

namespace Formation\Query;

class OrderId
{
    /** @var string */
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function id()
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return spl_object_hash($this);
    }
}
