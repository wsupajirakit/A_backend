<?php

declare(strict_types=1);

namespace App\Admin;

/**
 * Verifies passwords and tells the caller when the stored hash must be upgraded.
 * Legacy md5/sha1/plain values are accepted once and then replaced by a modern hash.
 */
final class PasswordHasher
{
    public function hash(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function verify(string $password, string $stored): bool
    {
        if ($this->isModern($stored)) {
            return password_verify($password, $stored);
        }
        return $this->verifyLegacy($password, $stored);
    }

    public function needsRehash(string $stored): bool
    {
        return !$this->isModern($stored) || password_needs_rehash($stored, PASSWORD_DEFAULT);
    }

    private function isModern(string $stored): bool
    {
        return (password_get_info($stored)['algo'] ?? null) !== null;
    }

    private function verifyLegacy(string $password, string $stored): bool
    {
        if ($stored === '') {
            return false;
        }
        $lower = strtolower($stored);
        if (preg_match('/^[a-f0-9]{32}$/', $lower) === 1) {
            return hash_equals($lower, md5($password));
        }
        if (preg_match('/^[a-f0-9]{40}$/', $lower) === 1) {
            return hash_equals($lower, sha1($password));
        }
        return hash_equals($stored, $password);
    }
}
