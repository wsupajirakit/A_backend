<?php

declare(strict_types=1);

namespace App\Admin;

/** Per-session synchronizer token. */
final class Csrf
{
    private const KEY = '_csrf';

    /** @param array<string, mixed> $session */
    public function __construct(private array &$session)
    {
    }

    public function token(): string
    {
        if (!isset($this->session[self::KEY]) || !is_string($this->session[self::KEY])) {
            $this->session[self::KEY] = bin2hex(random_bytes(32));
        }
        return $this->session[self::KEY];
    }

    public function isValid(?string $given): bool
    {
        $expected = $this->session[self::KEY] ?? null;
        return is_string($expected) && is_string($given) && $given !== '' && hash_equals($expected, $given);
    }

    public function rotate(): void
    {
        unset($this->session[self::KEY]);
    }
}
