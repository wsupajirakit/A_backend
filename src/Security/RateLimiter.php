<?php

declare(strict_types=1);

namespace App\Security;

/**
 * Fixed-window counter per key (e.g. client IP), guarded by flock.
 */
final class RateLimiter extends FileStore
{
    public function __construct(string $dir, private int $limit = 120, private int $window = 60)
    {
        parent::__construct($dir);
    }

    /** Counts one hit; returns seconds to wait when over the limit, else 0. */
    public function hit(string $key, ?int $now = null): int
    {
        $now ??= time();
        $this->maybeCollect($now, 500);
        $file = $this->pathFor($key);
        $handle = fopen($file, 'c+');
        if ($handle === false) {
            return 0; // fail open rather than block everyone on a storage error
        }
        try {
            flock($handle, LOCK_EX);
            $data = json_decode((string) stream_get_contents($handle), true);
            $start = is_array($data) ? (int) ($data['start'] ?? 0) : 0;
            $count = is_array($data) ? (int) ($data['count'] ?? 0) : 0;
            if ($now - $start >= $this->window) {
                $start = $now;
                $count = 0;
            }
            $count++;
            ftruncate($handle, 0);
            rewind($handle);
            fwrite($handle, (string) json_encode(['start' => $start, 'count' => $count]));
            fflush($handle);
            flock($handle, LOCK_UN);
        } finally {
            fclose($handle);
        }
        touch($file, $start + $this->window);
        return $count > $this->limit ? max(1, $start + $this->window - $now) : 0;
    }
}
