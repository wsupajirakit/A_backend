<?php

declare(strict_types=1);

namespace App\Security;

use App\Config\Env;

final class SecurityHeaders
{
    /**
     * The legacy pages use inline scripts and AngularJS, hence 'unsafe-inline'/'unsafe-eval'.
     * Tighten once inline scripts are moved to files.
     */
    public static function csp(): string
    {
        $stream = (string) Env::get('STREAM_URL', '');
        $streamOrigin = '';
        if ($stream !== '' && ($p = parse_url($stream)) && isset($p['scheme'], $p['host'])) {
            $streamOrigin = ' ' . $p['scheme'] . '://' . $p['host'] . (isset($p['port']) ? ':' . $p['port'] : '');
        }
        return implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com https://connect.facebook.net",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com",
            "font-src 'self' data: https://fonts.gstatic.com",
            "img-src 'self' data: blob: https:",
            "media-src 'self' blob:" . $streamOrigin,
            "connect-src 'self'",
            "frame-src 'self' https://www.facebook.com https://web.facebook.com",
            "frame-ancestors 'self'",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ]);
    }

    public static function isHttps(): bool
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (int) ($_SERVER['SERVER_PORT'] ?? 0) === 443
            || (Env::bool('TRUST_PROXY') && ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    }

    public static function send(): void
    {
        if (headers_sent()) {
            return;
        }
        header_remove('X-Powered-By');
        header('Content-Security-Policy: ' . self::csp());
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
        if (self::isHttps()) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
    }
}
