<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\JsonResponse;
use App\Http\Request;

interface Middleware
{
    /** @param callable(Request): JsonResponse $next */
    public function process(Request $request, callable $next): JsonResponse;
}
