<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\JsonResponse;
use App\Http\Request;
use App\Security\RequestSignature;
use App\Support\Clock;

final class SignatureMiddleware implements Middleware
{
    public function __construct(private RequestSignature $signature, private Clock $clock)
    {
    }

    public function process(Request $request, callable $next): JsonResponse
    {
        if (!$this->signature->verify($request, $this->clock->now())) {
            return JsonResponse::error(401, 'bad_signature', 'Request signature is invalid.');
        }
        return $next($request);
    }
}
