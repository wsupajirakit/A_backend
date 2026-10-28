<?php

declare(strict_types=1);

namespace App\Support;

use App\Config\Env;

final class Paths
{
    public static function root(): string
    {
        return dirname(__DIR__, 2);
    }

    public static function storage(): string
    {
        return rtrim((string) Env::get('STORAGE_PATH', self::root() . '/storage'), '/');
    }
}
