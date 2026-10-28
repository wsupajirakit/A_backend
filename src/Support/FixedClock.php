<?php

declare(strict_types=1);

namespace App\Support;

final class FixedClock extends Clock
{
    public function __construct(private int $time)
    {
    }

    public function now(): int
    {
        return $this->time;
    }
}
