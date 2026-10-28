<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\JsonResponse;
use App\Http\Request;
use App\Security\RateLimiter;
use App\Support\Clock;

final class RateLimitMiddleware implements Middleware
{
    public function __construct(private RateLimiter $limiter, private Clock $clock)
    {
    }

    public function process(Request $request, callable $next): JsonResponse
    {
        $retryAfter = $this->limiter->hit('ip:' . $request->ip(), $this->clock->now());
        if ($retryAfter > 0) {
            return JsonResponse::error(429, 'rate_limited', 'Too many requests.', ['Retry-After' => (string) $retryAfter]);
        }
        return $next($request);
    }
}
