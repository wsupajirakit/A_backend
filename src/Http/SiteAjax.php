<?php

declare(strict_types=1);

namespace App\Http;

use App\Config\Env;
use App\Database\Connection;
use App\Security\RateLimiter;
use App\Support\Logger;
use App\Support\Paths;
use Throwable;

/**
 * Same-origin JSON endpoints used by the public website (legacy {status, result} shape).
 * Rate limited per IP; not signed, as they only expose public site data.
 */
final class SiteAjax
{
    /** @param callable(Connection): (array<mixed>|null) $loader */
    public static function respond(callable $loader): never
    {
        $limiter = new RateLimiter(Paths::storage() . '/rate-site', Env::int('RATE_LIMIT_PER_MINUTE', 120), 60);
        if ($limiter->hit('ip:' . (string) ($_SERVER['REMOTE_ADDR'] ?? '')) > 0) {
            json_out(['status' => 0, 'result' => null], 429);
        }
        try {
            $result = $loader(Connection::fromEnv());
        } catch (Throwable $e) {
            (new Logger(Paths::storage() . '/logs'))->error('site-ajax', $e);
            json_out(['status' => 0, 'result' => null], 500);
        }
        json_out($result === null ? ['status' => 2, 'result' => null] : ['status' => 1, 'result' => $result]);
    }
}
