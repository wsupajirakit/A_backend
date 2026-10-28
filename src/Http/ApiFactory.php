<?php

declare(strict_types=1);

namespace App\Http;

use App\Config\Env;
use App\Controller\Api\PlayerController;
use App\Controller\Api\RadioProgramController;
use App\Controller\Api\TopChartController;
use App\Database\Connection;
use App\Http\Middleware\RateLimitMiddleware;
use App\Http\Middleware\SignatureMiddleware;
use App\Repository\PlayerRepository;
use App\Repository\RadioProgramRepository;
use App\Repository\TopChartRepository;
use App\Security\NonceStore;
use App\Security\RateLimiter;
use App\Security\RequestSignature;
use App\Support\Clock;
use App\Support\Logger;
use App\Support\Paths;
use DateTimeZone;

/** Composition root of the mobile API. */
final class ApiFactory
{
    public static function routes(Router $router, Connection $db, Clock $clock): Router
    {
        $base = rtrim((string) Env::get('PUBLIC_BASE_URL', 'https://www.example.com'), '/');
        $charts = new TopChartController(new TopChartRepository($db));
        $programs = new RadioProgramController(
            new RadioProgramRepository($db, $base . '/rsc/radio_presenter/'),
            $clock,
            new DateTimeZone((string) Env::get('APP_TIMEZONE', 'Asia/Bangkok'))
        );
        $player = new PlayerController(new PlayerRepository(
            Paths::root() . '/admin/data/app-data.json',
            (string) Env::get('STREAM_URL', ''),
            $db,
            $base . '/rsc/app/'
        ));

        $router->get('/api/v1/top-charts', [$charts, 'index']);
        $router->get('/api/v1/top-charts/dates', [$charts, 'dates']);
        $router->get('/api/v1/top-charts/{date}', [$charts, 'show']);
        $router->get('/api/v1/radio-programs', [$programs, 'index']);
        $router->get('/api/v1/player', [$player, 'show']);
        $router->get('/api/v1/app-data', [$player, 'appData']);
        return $router;
    }

    public static function kernel(): ApiKernel
    {
        $storage = Paths::storage();
        $clock = new Clock();
        $signature = new RequestSignature(
            RequestSignature::keysFromJson(Env::get('MOBILE_APP_KEYS')),
            new NonceStore($storage . '/nonces')
        );
        $limiter = new RateLimiter($storage . '/rate', Env::int('RATE_LIMIT_PER_MINUTE', 120), 60);

        return new ApiKernel(
            self::routes(new Router(), Connection::fromEnv(), $clock),
            [new RateLimitMiddleware($limiter, $clock), new SignatureMiddleware($signature, $clock)],
            new Logger($storage . '/logs')
        );
    }
}
