<?php

declare(strict_types=1);

namespace App\Support;

/** Injectable time source (tests pass a fixed clock). */
class Clock
{
    public function now(): int
    {
        return time();
    }
}
