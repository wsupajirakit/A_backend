<?php

declare(strict_types=1);

namespace App\Http;

final class JsonResponse
{
    /**
     * @param array<string, mixed>  $payload
     * @param array<string, string> $headers
     */
    public function __construct(
        public readonly int $status,
        public readonly array $payload,
        public readonly array $headers = [],
    ) {
    }

    public static function ok(mixed $data, int $status = 200): self
    {
        return new self($status, ['ok' => true, 'data' => $data]);
    }

    /** @param array<string, string> $headers */
    public static function error(int $status, string $code, string $message, array $headers = []): self
    {
        return new self($status, ['ok' => false, 'error' => ['code' => $code, 'message' => $message]], $headers);
    }

    public function body(): string
    {
        return (string) json_encode(
            $this->payload,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE
        );
    }

    public function send(): void
    {
        http_response_code($this->status);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        header('X-Content-Type-Options: nosniff');
        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }
        echo $this->body();
    }
}
