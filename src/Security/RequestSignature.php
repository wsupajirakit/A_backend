<?php

declare(strict_types=1);

namespace App\Security;

use App\Http\Request;

/**
 * HMAC-SHA256 request signing as defined in the API contract.
 */
final class RequestSignature
{
    public const MAX_SKEW = 300;
    public const NONCE_TTL = 600;
    private const NONCE_PATTERN = '/^[A-Za-z0-9_-]{16,64}$/';

    /** @param array<string, string> $appKeys app id => secret */
    public function __construct(
        private array $appKeys,
        private NonceStore $nonces,
    ) {
    }

    /**
     * Parses MOBILE_APP_KEYS (JSON object). Invalid JSON yields no keys.
     *
     * @return array<string, string>
     */
    public static function keysFromJson(?string $json): array
    {
        $keys = json_decode((string) $json, true);
        if (!is_array($keys)) {
            return [];
        }
        return array_filter($keys, static fn ($v, $k) => is_string($k) && is_string($v) && $v !== '', ARRAY_FILTER_USE_BOTH);
    }

    public static function canonicalQuery(string $rawQuery): string
    {
        $pairs = Request::parseQuery($rawQuery);
        usort($pairs, static fn (array $a, array $b) => [$a[0], $a[1]] <=> [$b[0], $b[1]]);
        return implode('&', array_map(static fn (array $p) => rawurlencode($p[0]) . '=' . rawurlencode($p[1]), $pairs));
    }

    public static function canonical(string $method, string $path, string $rawQuery, string $timestamp, string $nonce, string $body): string
    {
        return implode("\n", [
            strtoupper($method),
            $path,
            self::canonicalQuery($rawQuery),
            $timestamp,
            $nonce,
            hash('sha256', $body),
        ]);
    }

    public static function sign(string $secret, string $canonical): string
    {
        return base64_encode(hash_hmac('sha256', $canonical, $secret, true));
    }

    /** True only when every check passes; callers must not reveal which one failed. */
    public function verify(Request $request, ?int $now = null): bool
    {
        $now ??= time();
        $appId = (string) $request->header('X-App-Id');
        $timestamp = (string) $request->header('X-Timestamp');
        $nonce = (string) $request->header('X-Nonce');
        $given = (string) $request->header('X-Signature');

        // 1. app id known
        if ($appId === '' || !isset($this->appKeys[$appId])) {
            return false;
        }
        // 2. timestamp within the allowed skew
        if (!ctype_digit($timestamp) || abs($now - (int) $timestamp) > self::MAX_SKEW) {
            return false;
        }
        // 3. nonce well-formed and not seen
        if (!preg_match(self::NONCE_PATTERN, $nonce) || $this->nonces->seen($appId, $nonce, $now)) {
            return false;
        }
        // 4. signature
        $expected = self::sign(
            $this->appKeys[$appId],
            self::canonical($request->method(), $request->path(), $request->rawQuery(), $timestamp, $nonce, $request->body())
        );
        if (!hash_equals($expected, $given)) {
            return false;
        }
        // Claim the nonce atomically; a parallel replay loses here.
        return $this->nonces->claim($appId, $nonce, $now, self::NONCE_TTL);
    }
}
