<?php

declare(strict_types=1);

namespace Tests;

use RuntimeException;

/** Minimal assertion helpers for the plain-PHP test runner. */
abstract class TestCase
{
    private int $assertions = 0;

    public function assertions(): int
    {
        return $this->assertions;
    }

    protected function assertSame(mixed $expected, mixed $actual, string $message = ''): void
    {
        $this->assertions++;
        if ($expected !== $actual) {
            throw new RuntimeException(sprintf(
                "%s\n  expected: %s\n  actual:   %s",
                $message !== '' ? $message : 'Values differ',
                var_export($expected, true),
                var_export($actual, true)
            ));
        }
    }

    protected function assertTrue(bool $condition, string $message = 'Expected true'): void
    {
        $this->assertSame(true, $condition, $message);
    }

    protected function assertFalse(bool $condition, string $message = 'Expected false'): void
    {
        $this->assertSame(false, $condition, $message);
    }

    protected static function tempDir(string $prefix): string
    {
        $dir = sys_get_temp_dir() . '/' . $prefix . '-' . bin2hex(random_bytes(6));
        mkdir($dir, 0700, true);
        return $dir;
    }
}
