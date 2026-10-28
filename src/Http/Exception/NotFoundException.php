<?php

declare(strict_types=1);

namespace App\Http\Exception;

final class NotFoundException extends HttpException
{
    public function status(): int
    {
        return 404;
    }

    public function errorCode(): string
    {
        return 'not_found';
    }
}
