<?php

declare(strict_types=1);

namespace App\Database;

use App\Config\Env;
use Closure;
use PDO;

/**
 * Lazy PDO holder: connects on first use.
 */
final class Connection
{
    private ?PDO $pdo = null;

    /** @param Closure(): PDO $factory */
    public function __construct(private Closure $factory)
    {
    }

    public static function fromEnv(): self
    {
        return new self(static function (): PDO {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                Env::get('DB_HOST', '127.0.0.1'),
                Env::int('DB_PORT', 3306),
                Env::get('DB_NAME', 'aradio')
            );
            return self::configure(new PDO($dsn, Env::get('DB_USER', ''), Env::get('DB_PASS', '')));
        });
    }

    public static function configure(PDO $pdo): PDO
    {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $pdo;
    }

    public function pdo(): PDO
    {
        return $this->pdo ??= ($this->factory)();
    }
}
