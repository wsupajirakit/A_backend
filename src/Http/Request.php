<?php

declare(strict_types=1);

namespace App\Http;

final class Request
{
    /** @var array<string, string> */
    private array $query;
    /** @var array<string, string> lower-case header names */
    private array $headers;

    /**
     * @param array<string, string> $headers
     */
    public function __construct(
        private string $method,
        private string $path,
        private string $rawQuery = '',
        array $headers = [],
        private string $body = '',
        private string $ip = '0.0.0.0',
    ) {
        $this->method = strtoupper($method);
        $this->headers = array_change_key_case($headers, CASE_LOWER);
        $this->query = [];
        foreach (self::parseQuery($rawQuery) as [$k, $v]) {
            $this->query[$k] = $v;
        }
    }

    public static function fromGlobals(): self
    {
        $uri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
        $path = (string) (parse_url($uri, PHP_URL_PATH) ?? '/');
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with((string) $key, 'HTTP_')) {
                $headers[str_replace('_', '-', substr((string) $key, 5))] = (string) $value;
            }
        }
        return new self(
            (string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'),
            $path,
            (string) ($_SERVER['QUERY_STRING'] ?? ''),
            $headers,
            (string) file_get_contents('php://input'),
            (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'),
        );
    }

    /**
     * Raw query parsing (no PHP name mangling). Returns decoded [key, value] pairs.
     *
     * @return list<array{0: string, 1: string}>
     */
    public static function parseQuery(string $rawQuery): array
    {
        $pairs = [];
        foreach (explode('&', $rawQuery) as $part) {
            if ($part === '') {
                continue;
            }
            [$k, $v] = array_pad(explode('=', $part, 2), 2, '');
            $pairs[] = [rawurldecode($k), rawurldecode($v)];
        }
        return $pairs;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function rawQuery(): string
    {
        return $this->rawQuery;
    }

    public function query(string $key, ?string $default = null): ?string
    {
        return $this->query[$key] ?? $default;
    }

    public function header(string $name): ?string
    {
        return $this->headers[strtolower($name)] ?? null;
    }

    public function body(): string
    {
        return $this->body;
    }

    public function ip(): string
    {
        return $this->ip;
    }
}
