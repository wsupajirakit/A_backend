<?php

declare(strict_types=1);

namespace Tests;

use App\Admin\AccessPolicy;
use App\Admin\AdminAuth;
use App\Admin\Csrf;
use App\Admin\LoginResult;
use App\Admin\PasswordHasher;
use App\Admin\UserRepository;
use App\Database\Connection;
use App\Security\RateLimiter;
use PDO;

final class AdminSecurityTest extends TestCase
{
    public function testWriteEndpointWithoutSessionIs401(): void
    {
        $this->assertSame(401, (new AccessPolicy())->decide('/x/admin/webservice/SongDelete.php', 'POST', false, true));
    }

    public function testWriteEndpointWithBadCsrfIs403(): void
    {
        $this->assertSame(403, (new AccessPolicy())->decide('SongDelete.php', 'POST', true, false));
    }

    public function testWriteEndpointRequiresPost(): void
    {
        $this->assertSame(405, (new AccessPolicy())->decide('SongDelete.php', 'GET', true, true));
    }

    public function testAllowedCases(): void
    {
        $policy = new AccessPolicy();
        $this->assertSame(AccessPolicy::ALLOW, $policy->decide('SongUpdate.php', 'POST', true, true));
        $this->assertSame(AccessPolicy::ALLOW, $policy->decide('SongList.php', 'GET', true, false));
        $this->assertSame(401, $policy->decide('SongList.php', 'GET', false, false));
        $this->assertSame(AccessPolicy::ALLOW, $policy->decide('CheckLogin.php', 'POST', false, true));
        $this->assertSame(403, $policy->decide('CheckLogin.php', 'POST', false, false));
    }

    public function testCsrfToken(): void
    {
        $session = [];
        $csrf = new Csrf($session);
        $token = $csrf->token();
        $this->assertSame(64, strlen($token));
        $this->assertSame($token, $session['_csrf']);
        $this->assertTrue($csrf->isValid($token));
        $this->assertFalse($csrf->isValid('wrong'));
        $this->assertFalse($csrf->isValid(null));
    }

    public function testPasswordHasherModernAndLegacy(): void
    {
        $hasher = new PasswordHasher();
        $modern = $hasher->hash('s3cret-pass');
        $this->assertTrue($hasher->verify('s3cret-pass', $modern));
        $this->assertFalse($hasher->verify('wrong', $modern));
        $this->assertFalse($hasher->needsRehash($modern));
        $this->assertTrue($hasher->verify('s3cret-pass', md5('s3cret-pass')));
        $this->assertTrue($hasher->needsRehash(md5('s3cret-pass')));
        $this->assertTrue($hasher->verify('s3cret-pass', sha1('s3cret-pass')));
        $this->assertFalse($hasher->verify('', ''));
    }

    public function testLoginRehashesLegacyPasswordAndRegeneratesSession(): void
    {
        $pdo = $this->userDb(md5('legacy-pass'));
        $session = [];
        $regenerated = false;
        $auth = $this->auth($pdo, $session, $regenerated);

        $this->assertSame(LoginResult::Success, $auth->attempt('editor', 'legacy-pass', '198.51.100.1', 1000));
        $this->assertTrue($regenerated, 'session id regenerated');
        $stored = (string) $pdo->query('SELECT password FROM user WHERE user_id = 1')->fetchColumn();
        $this->assertTrue(password_verify('legacy-pass', $stored), 'hash upgraded');
        $this->assertSame(1, $auth->user(true, 1001)['user_id'] ?? null);
    }

    public function testIdleTimeoutAndBadPassword(): void
    {
        $pdo = $this->userDb(password_hash('good-pass', PASSWORD_DEFAULT));
        $session = [];
        $regenerated = false;
        $auth = $this->auth($pdo, $session, $regenerated);

        $this->assertSame(LoginResult::InvalidCredentials, $auth->attempt('editor', 'bad', '198.51.100.2', 1000));
        $this->assertSame(LoginResult::Success, $auth->attempt('editor', 'good-pass', '198.51.100.2', 1000));
        $this->assertSame(null, $auth->user(true, 1000 + 1801), 'idle session expires');
    }

    public function testLoginIsThrottled(): void
    {
        $pdo = $this->userDb(password_hash('good-pass', PASSWORD_DEFAULT));
        $session = [];
        $regenerated = false;
        $auth = $this->auth($pdo, $session, $regenerated);
        for ($i = 0; $i < 3; $i++) {
            $auth->attempt('editor', 'bad', '198.51.100.3', 1000);
        }
        $this->assertSame(LoginResult::Throttled, $auth->attempt('editor', 'good-pass', '198.51.100.3', 1000));
    }

    /** @param array<string, mixed> $session */
    private function auth(PDO $pdo, array &$session, bool &$regenerated): AdminAuth
    {
        return new AdminAuth(
            $session,
            new UserRepository(new Connection(static fn (): PDO => $pdo)),
            new PasswordHasher(),
            new RateLimiter(self::tempDir('login'), 3, 900),
            static function () use (&$regenerated): void {
                $regenerated = true;
            },
            1800
        );
    }

    private function userDb(string $passwordHash): PDO
    {
        $pdo = Connection::configure(new PDO('sqlite::memory:'));
        $pdo->exec('CREATE TABLE user (user_id INTEGER PRIMARY KEY, username TEXT, password TEXT, active_status INT)');
        $stmt = $pdo->prepare("INSERT INTO user VALUES (1, 'editor', :p, 1)");
        $stmt->execute([':p' => $passwordHash]);
        return $pdo;
    }
}
