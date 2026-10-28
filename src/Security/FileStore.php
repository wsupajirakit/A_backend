<?php

declare(strict_types=1);

namespace App\Security;

use RuntimeException;

/**
 * Directory-backed store used by NonceStore and RateLimiter.
 * File names are hashes, so user input never reaches the filesystem path.
 */
abstract class FileStore
{
    public function __construct(protected string $dir)
    {
        if (!is_dir($dir) && !@mkdir($dir, 0700, true) && !is_dir($dir)) {
            throw new RuntimeException('Storage directory is not writable.');
        }
    }

    protected function pathFor(string $key): string
    {
        return rtrim($this->dir, '/') . '/' . hash('sha256', $key);
    }

    /** Occasionally delete expired entries (expiry stored as file mtime). */
    protected function maybeCollect(int $now, int $probability = 100): void
    {
        if (random_int(1, $probability) !== 1) {
            return;
        }
        foreach (glob(rtrim($this->dir, '/') . '/*') ?: [] as $file) {
            if (is_file($file) && @filemtime($file) < $now) {
                @unlink($file);
            }
        }
    }
}
