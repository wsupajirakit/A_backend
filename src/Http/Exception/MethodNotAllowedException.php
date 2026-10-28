<?php

declare(strict_types=1);

namespace App\Http\Exception;

final class MethodNotAllowedException extends HttpException
{
    public function status(): int
    {
        return 405;
    }

    public function errorCode(): string
    {
        return 'method_not_allowed';
    }
}
