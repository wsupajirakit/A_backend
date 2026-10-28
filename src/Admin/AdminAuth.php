<?php

declare(strict_types=1);

namespace App\Admin;

use App\Security\RateLimiter;

/**
 * Session-based admin authentication with login throttling and idle timeout.
 * The session array is injected so the class is testable without a real session.
 */
final class AdminAuth
{
    private const UID = 'admin_uid';
    private const LAST_SEEN = 'admin_last_seen';

    /** @var array<string, mixed>|null */
    private ?array $currentUser = null;

    /**
     * @param array<string, mixed> $session
     * @param \Closure(): void $regenerateId
     */
    public function __construct(
        private array &$session,
        private UserRepository $users,
        private PasswordHasher $hasher,
        private RateLimiter $loginLimiter,
        private \Closure $regenerateId,
        private int $idleTimeout = 1800,
    ) {
    }

    public function attempt(string $username, string $password, string $ip, ?int $now = null): LoginResult
    {
        $now ??= time();
        if ($this->loginLimiter->hit('login:' . $ip . ':' . strtolower($username), $now) > 0) {
            return LoginResult::Throttled;
        }
        $user = $this->users->findByUsername($username);
        if ($user === null || !$this->hasher->verify($password, (string) $user['password'])) {
            return LoginResult::InvalidCredentials;
        }
        if ((int) $user['active_status'] !== 1) {
            return LoginResult::Suspended;
        }
        if ($this->hasher->needsRehash((string) $user['password'])) {
            $this->users->updatePasswordHash((int) $user['user_id'], $this->hasher->hash($password));
        }
        ($this->regenerateId)();
        $this->session[self::UID] = (int) $user['user_id'];
        $this->session[self::LAST_SEEN] = $now;
        $this->currentUser = $user;
        return LoginResult::Success;
    }

    /**
     * Current active user or null. $touch=false checks without extending the idle window.
     *
     * @return array<string, mixed>|null
     */
    public function user(bool $touch = true, ?int $now = null): ?array
    {
        $now ??= time();
        $uid = $this->session[self::UID] ?? null;
        $lastSeen = (int) ($this->session[self::LAST_SEEN] ?? 0);
        if (!is_int($uid) || $now - $lastSeen > $this->idleTimeout) {
            $this->clear();
            return null;
        }
        $this->currentUser ??= $this->users->findActiveById($uid);
        if ($this->currentUser === null) {
            $this->clear();
            return null;
        }
        if ($touch) {
            $this->session[self::LAST_SEEN] = $now;
        }
        return $this->currentUser;
    }

    public function secondsUntilIdle(?int $now = null): int
    {
        $lastSeen = (int) ($this->session[self::LAST_SEEN] ?? 0);
        return max(0, $lastSeen + $this->idleTimeout - ($now ?? time()));
    }

    public function clear(): void
    {
        unset($this->session[self::UID], $this->session[self::LAST_SEEN]);
        $this->currentUser = null;
    }
}
