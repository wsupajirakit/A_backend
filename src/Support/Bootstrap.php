<?php

declare(strict_types=1);

namespace App\Support;

use App\Config\Env;
use App\Security\SecurityHeaders;
use Throwable;

/**
 * One-time start-up shared by the API, the public site and the admin panel:
 * .env, timezone, error handling that logs instead of printing, security headers.
 */
final class Bootstrap
{
    private static bool $booted = false;

    public static function boot(string $root): void
    {
        if (self::$booted) {
            return;
        }
        self::$booted = true;

        Env::load($root . '/.env');
        date_default_timezone_set((string) Env::get('APP_TIMEZONE', 'Asia/Bangkok'));

        ini_set('display_errors', Env::bool('APP_DEBUG', false) ? '1' : '0');
        ini_set('log_errors', '1');
        error_reporting(E_ALL);

        $logger = new Logger(Paths::storage() . '/logs');
        set_error_handler(static function (int $no, string $msg, string $file, int $line) use ($logger): bool {
            if ((error_reporting() & $no) === 0) {
                return false;
            }
            $logger->error('php', sprintf('%s in %s:%d', $msg, basename($file), $line));

            return true; // handled: never echoed to the client
        });
        set_exception_handler(static function (Throwable $e) use ($logger): void {
            $logger->error('uncaught', $e);
            self::renderServerError();
        });

        if (PHP_SAPI !== 'cli') {
            SecurityHeaders::send();
        }
    }

    private static function renderServerError(): void
    {
        if (!headers_sent()) {
            http_response_code(500);
        }
        $uri = (string) ($_SERVER['REQUEST_URI'] ?? '');
        $wantsJson = str_contains((string) ($_SERVER['HTTP_ACCEPT'] ?? ''), 'json')
            || str_contains($uri, '/api/');
        if (!$wantsJson) {
            echo 'Something went wrong. Please try again later.';

            return;
        }
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
        }
        echo '{"ok":false,"error":{"code":"server_error","message":"Internal server error."}}';
    }
}
