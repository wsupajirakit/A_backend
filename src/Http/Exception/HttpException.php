<?php

declare(strict_types=1);

namespace App\Http\Exception;

use RuntimeException;

/** Base for errors that map to an HTTP status and a public error code. */
abstract class HttpException extends RuntimeException
{
    abstract public function status(): int;

    abstract public function errorCode(): string;
}
