<?php

declare(strict_types=1);

namespace App\Admin;

/**
 * Decides access for legacy admin endpoints by script name:
 * public (login, password reset) -> CSRF on POST only;
 * reads (*List, *Ini) -> authenticated session;
 * everything else (writes) -> POST + authenticated session + CSRF.
 */
final class AccessPolicy
{
    public const ALLOW = 0;

    private const PUBLIC_SCRIPTS = ['CheckLogin', 'ResetPassword', 'ResetPassword_Submit'];
    private const SESSION_SCRIPTS = ['CheckRefresh'];

    public function decide(string $script, string $method, bool $authenticated, bool $csrfValid): int
    {
        $name = basename($script, '.php');
        $isPost = strtoupper($method) === 'POST';

        if (in_array($name, self::PUBLIC_SCRIPTS, true)) {
            return ($isPost && !$csrfValid) ? 403 : self::ALLOW;
        }
        if (!$authenticated) {
            return 401;
        }
        if (in_array($name, self::SESSION_SCRIPTS, true) || $this->isRead($name)) {
            return self::ALLOW;
        }
        if (!$isPost) {
            return 405;
        }
        return $csrfValid ? self::ALLOW : 403;
    }

    private function isRead(string $name): bool
    {
        return preg_match('/(List|Ini)$/', $name) === 1;
    }
}
