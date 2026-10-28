<?php

declare(strict_types=1);

namespace App\Support;

use Throwable;

/** Appends one line per event to storage/logs/app-YYYY-MM-DD.log. */
final class Logger
{
    public function __construct(private string $dir)
    {
    }

    public function error(string $channel, Throwable|string $error): void
    {
        $message = $error instanceof Throwable
            ? sprintf('%s: %s in %s:%d', $error::class, $error->getMessage(), basename($error->getFile()), $error->getLine())
            : $error;
        $this->write('ERROR', $channel, $message);
    }

    public function info(string $channel, string $message): void
    {
        $this->write('INFO', $channel, $message);
    }

    private function write(string $level, string $channel, string $message): void
    {
        if (!is_dir($this->dir)) {
            @mkdir($this->dir, 0700, true);
        }
        $line = sprintf("[%s] %s.%s %s\n", date('c'), $channel, $level, str_replace(["\r", "\n"], ' ', $message));
        @file_put_contents($this->dir . '/app-' . date('Y-m-d') . '.log', $line, FILE_APPEND | LOCK_EX);
    }
}
