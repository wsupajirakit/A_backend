<?php

declare(strict_types=1);

namespace App\Http;

use App\Http\Exception\MethodNotAllowedException;
use App\Http\Exception\NotFoundException;

/**
 * Minimal router. Patterns like /api/v1/top-charts/{date}; params match [^/]+.
 */
final class Router
{
    /** @var list<array{method: string, regex: string, handler: callable(Request, array<string, string>): JsonResponse}> */
    private array $routes = [];

    /** @param callable(Request, array<string, string>): JsonResponse $handler */
    public function get(string $pattern, callable $handler): void
    {
        $this->add('GET', $pattern, $handler);
    }

    /** @param callable(Request, array<string, string>): JsonResponse $handler */
    public function add(string $method, string $pattern, callable $handler): void
    {
        $regex = (string) preg_replace('#\{([a-z_]+)\}#i', '(?P<$1>[^/]+)', rtrim($pattern, '/'));
        $this->routes[] = ['method' => strtoupper($method), 'regex' => '#^' . $regex . '/?$#', 'handler' => $handler];
    }

    public function dispatch(Request $request): JsonResponse
    {
        $pathMatched = false;
        foreach ($this->routes as $route) {
            if (preg_match($route['regex'], $request->path(), $matches) !== 1) {
                continue;
            }
            $pathMatched = true;
            if ($route['method'] === $request->method()) {
                return ($route['handler'])($request, $this->params($matches));
            }
        }
        if ($pathMatched) {
            throw new MethodNotAllowedException('Method not allowed.');
        }
        throw new NotFoundException('Endpoint not found.');
    }

    /**
     * @param array<int|string, string> $matches
     * @return array<string, string>
     */
    private function params(array $matches): array
    {
        $params = [];
        foreach ($matches as $key => $value) {
            if (is_string($key)) {
                $params[$key] = rawurldecode($value);
            }
        }
        return $params;
    }
}
