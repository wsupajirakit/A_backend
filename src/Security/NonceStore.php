<?php

declare(strict_types=1);

namespace App\Security;

/**
 * Remembers nonces per app id until they expire (file mtime = expiry).
 */
final class NonceStore extends FileStore
{
    public function seen(string $appId, string $nonce, int $now): bool
    {
        $file = $this->pathFor($appId . "\0" . $nonce);
        clearstatcache(true, $file);
        return is_file($file) && (int) @filemtime($file) >= $now;
    }

    /** Atomically records the nonce. Returns false if it is already live. */
    public function claim(string $appId, string $nonce, int $now, int $ttl): bool
    {
        $this->maybeCollect($now);
        $file = $this->pathFor($appId . "\0" . $nonce);
        $handle = @fopen($file, 'x');
        if ($handle === false) {
            if ($this->seen($appId, $nonce, $now)) {
                return false;
            }
            // Expired leftover: replace it.
            @unlink($file);
            $handle = @fopen($file, 'x');
            if ($handle === false) {
                return false;
            }
        }
        fclose($handle);
        touch($file, $now + $ttl);
        return true;
    }
}
