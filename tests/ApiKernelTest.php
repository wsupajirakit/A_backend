<?php

declare(strict_types=1);

namespace Tests;

use App\Database\Connection;
use App\Http\ApiFactory;
use App\Http\ApiKernel;
use App\Http\JsonResponse;
use App\Http\Middleware\RateLimitMiddleware;
use App\Http\Middleware\SignatureMiddleware;
use App\Http\Request;
use App\Http\Router;
use App\Security\NonceStore;
use App\Security\RateLimiter;
use App\Security\RequestSignature;
use App\Support\FixedClock;
use App\Support\Logger;
use PDO;

final class ApiKernelTest extends TestCase
{
    private const APP_ID = 'aradio-ios';
    private const SECRET = 'test-secret-0123456789';
    private const NOW = 1700000000;

    public function testValidSignedRequestReturnsData(): void
    {
        $response = $this->kernel()->handle($this->signed('/api/v1/top-charts'));
        $this->assertSame(200, $response->status);
        $this->assertSame(true, $response->payload['ok']);
        $this->assertSame('Song A', $response->payload['data'][0]['num_1_song']);
        $this->assertSame('20/10/2026', $response->payload['data'][0]['date']);
    }

    public function testChartByDateKeepsLegacyFields(): void
    {
        $response = $this->kernel()->handle($this->signed('/api/v1/top-charts/2026-10-20'));
        $this->assertSame(200, $response->status);
        $song = $response->payload['data']['songs'][0];
        $this->assertSame(['num', 'name', 'artists', 'lastChartNum', 'onChartLong'], array_keys($song));
        $this->assertSame('20/10/2026', $response->payload['data']['date']);
    }

    public function testBadSignatureIs401(): void
    {
        $request = $this->signed('/api/v1/top-charts', signature: 'AAAA');
        $this->assertError($this->kernel()->handle($request), 401, 'bad_signature');
    }

    public function testOldTimestampIs401(): void
    {
        $request = $this->signed('/api/v1/top-charts', timestamp: self::NOW - 301);
        $this->assertError($this->kernel()->handle($request), 401, 'bad_signature');
    }

    public function testReplayedNonceIs401(): void
    {
        $kernel = $this->kernel();
        $this->assertSame(200, $kernel->handle($this->signed('/api/v1/top-charts', nonce: 'replay-nonce-0001'))->status);
        $this->assertError($kernel->handle($this->signed('/api/v1/top-charts', nonce: 'replay-nonce-0001')), 401, 'bad_signature');
    }

    public function testUnknownAppIdIs401(): void
    {
        $request = $this->signed('/api/v1/top-charts', appId: 'someone-else');
        $this->assertError($this->kernel()->handle($request), 401, 'bad_signature');
    }

    public function testRateLimitIs429(): void
    {
        $kernel = $this->kernel(limit: 2);
        $kernel->handle($this->signed('/api/v1/top-charts'));
        $kernel->handle($this->signed('/api/v1/top-charts'));
        $response = $kernel->handle($this->signed('/api/v1/top-charts'));
        $this->assertError($response, 429, 'rate_limited');
        $this->assertTrue(isset($response->headers['Retry-After']), 'Retry-After header');
    }

    public function testUnknownRouteIs404(): void
    {
        $this->assertError($this->kernel()->handle($this->signed('/api/v1/nope')), 404, 'not_found');
    }

    public function testInvalidDateIs400(): void
    {
        $kernel = $this->kernel();
        $this->assertError($kernel->handle($this->signed('/api/v1/top-charts/2026-02-30')), 400, 'invalid_date');
        $this->assertError($kernel->handle($this->signed('/api/v1/top-charts/20-10-2026')), 400, 'invalid_date');
    }

    public function testMissingChartIs404(): void
    {
        $this->assertError($this->kernel()->handle($this->signed('/api/v1/top-charts/2020-01-01')), 404, 'not_found');
    }

    public function testDatabaseErrorIs500WithoutDetails(): void
    {
        $broken = new Connection(static fn (): PDO => throw new \PDOException('SQLSTATE secret detail'));
        $response = $this->kernel(db: $broken)->handle($this->signed('/api/v1/top-charts'));
        $this->assertError($response, 500, 'server_error');
        $this->assertFalse(str_contains($response->body(), 'SQLSTATE'), 'no SQL details leak');
    }

    private function assertError(JsonResponse $response, int $status, string $code): void
    {
        $this->assertSame($status, $response->status, 'HTTP status');
        $this->assertSame(false, $response->payload['ok']);
        $this->assertSame($code, $response->payload['error']['code']);
    }

    private function kernel(int $limit = 100, ?Connection $db = null): ApiKernel
    {
        $clock = new FixedClock(self::NOW);
        $signature = new RequestSignature([self::APP_ID => self::SECRET], new NonceStore(self::tempDir('nonce')));
        return new ApiKernel(
            ApiFactory::routes(new Router(), $db ?? $this->sqlite(), $clock),
            [
                new RateLimitMiddleware(new RateLimiter(self::tempDir('rate'), $limit, 60), $clock),
                new SignatureMiddleware($signature, $clock),
            ],
            new Logger(self::tempDir('logs'))
        );
    }

    private function signed(
        string $path,
        string $query = '',
        ?string $appId = null,
        ?int $timestamp = null,
        ?string $nonce = null,
        ?string $signature = null,
    ): Request {
        $timestamp ??= self::NOW;
        $nonce ??= bin2hex(random_bytes(12));
        $signature ??= RequestSignature::sign(
            self::SECRET,
            RequestSignature::canonical('GET', $path, $query, (string) $timestamp, $nonce, '')
        );
        return new Request('GET', $path, $query, [
            'X-App-Id' => $appId ?? self::APP_ID,
            'X-Timestamp' => (string) $timestamp,
            'X-Nonce' => $nonce,
            'X-Signature' => $signature,
        ], '', '203.0.113.7');
    }

    private function sqlite(): Connection
    {
        return new Connection(static function (): PDO {
            $pdo = Connection::configure(new PDO('sqlite::memory:'));
            $pdo->exec('CREATE TABLE top_chart (top_chart_id INTEGER PRIMARY KEY, top_chart_date TEXT, user_id INT)');
            $pdo->exec('CREATE TABLE song (song_id INTEGER PRIMARY KEY, song_name TEXT, artist_name_map TEXT)');
            $pdo->exec('CREATE TABLE top_chart_song (top_chart_id INT, song_id INT, song_order INT, last_week_order INT, on_chart_week_number TEXT)');
            $pdo->exec("INSERT INTO top_chart VALUES (1, '2026-10-20', 1)");
            $pdo->exec("INSERT INTO song VALUES (1, 'Song A', 'Artist A'), (2, 'Song B', 'Artist B')");
            $pdo->exec("INSERT INTO top_chart_song VALUES (1, 1, 1, 2, '3'), (1, 2, 2, NULL, NULL)");
            return $pdo;
        });
    }
}
