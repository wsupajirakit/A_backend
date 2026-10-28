<?php

declare(strict_types=1);

// Shared bootstrap for the API, the public site and the admin panel:
// autoload, .env, error handling, security headers.
require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/helpers.php';

use App\Config\Env;
use App\Security\SecurityHeaders;
use App\Support\Logger;
use App\Support\Paths;

Env::load(dirname(__DIR__) . '/.env');
date_default_timezone_set((string) Env::get('APP_TIMEZONE', 'Asia/Bangkok'));

if (!defined('APP_BOOTSTRAPPED')) {
    define('APP_BOOTSTRAPPED', true);

    $debug = Env::bool('APP_DEBUG', false);
    ini_set('display_errors', $debug ? '1' : '0');
    ini_set('log_errors', '1');
    error_reporting(E_ALL);

    $logger = new Logger(Paths::storage() . '/logs');
    set_error_handler(static function (int $no, string $msg, string $file, int $line) use ($logger): bool {
        if (!(error_reporting() & $no)) {
            return false;
        }
        $logger->error('php', sprintf('%s in %s:%d', $msg, basename($file), $line));
        return true; // handled: never echo to the client
    });
    set_exception_handler(static function (Throwable $e) use ($logger): void {
        $logger->error('uncaught', $e);
        if (!headers_sent()) {
            http_response_code(500);
        }
        $wantsJson = str_contains((string) ($_SERVER['HTTP_ACCEPT'] ?? ''), 'json')
            || str_contains((string) ($_SERVER['REQUEST_URI'] ?? ''), '/api/')
            || str_contains((string) ($_SERVER['REQUEST_URI'] ?? ''), '/webservice/');
        if ($wantsJson) {
            if (!headers_sent()) {
                header('Content-Type: application/json; charset=utf-8');
            }
            echo '{"ok":false,"error":{"code":"server_error","message":"Internal server error."}}';
        } else {
            echo 'Something went wrong. Please try again later.';
        }
    });

    if (PHP_SAPI !== 'cli') {
        SecurityHeaders::send();
    }
}
