<?php

declare(strict_types=1);

namespace App\Http;

use App\Http\Exception\HttpException;
use App\Http\Middleware\Middleware;
use App\Support\Logger;
use Throwable;

/**
 * Runs the middleware pipeline, then the router.
 * The single place where exceptions become HTTP responses.
 */
final class ApiKernel
{
    /** @param list<Middleware> $middleware */
    public function __construct(
        private Router $router,
        private array $middleware,
        private Logger $logger,
    ) {
    }

    public function handle(Request $request): JsonResponse
    {
        try {
            return $this->pipeline(0)($request);
        } catch (HttpException $e) {
            return JsonResponse::error($e->status(), $e->errorCode(), $e->getMessage());
        } catch (Throwable $e) {
            $this->logger->error('api', $e);
            return JsonResponse::error(500, 'server_error', 'Internal server error.');
        }
    }

    /** @return callable(Request): JsonResponse */
    private function pipeline(int $index): callable
    {
        if (!isset($this->middleware[$index])) {
            return fn (Request $request): JsonResponse => $this->router->dispatch($request);
        }
        $current = $this->middleware[$index];
        $next = $this->pipeline($index + 1);
        return static fn (Request $request): JsonResponse => $current->process($request, $next);
    }
}
