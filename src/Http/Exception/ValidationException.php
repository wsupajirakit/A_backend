<?php

declare(strict_types=1);

namespace App\Http\Exception;

final class ValidationException extends HttpException
{
    public function __construct(private string $errorCode, string $message)
    {
        parent::__construct($message);
    }

    public function status(): int
    {
        return 400;
    }

    public function errorCode(): string
    {
        return $this->errorCode;
    }
}
