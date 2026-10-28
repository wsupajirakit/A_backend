<?php

declare(strict_types=1);

// Admin bootstrap: shared app bootstrap + hardened session + auth + CSRF.
// Every admin page and endpoint includes this; endpoints under webservice/
// are checked here by AccessPolicy before their own code runs.
require_once __DIR__ . '/../src/bootstrap.php';

use App\Admin\AccessPolicy;
use App\Admin\AdminAuth;
use App\Admin\AdminSession;
use App\Admin\Csrf;
use App\Admin\PasswordHasher;
use App\Admin\UserRepository;
use App\Config\Env;
use App\Database\Connection;
use App\Security\RateLimiter;
use App\Support\Paths;

AdminSession::start();

$adminAuth = new AdminAuth(
    $_SESSION,
    new UserRepository(Connection::fromEnv()),
    new PasswordHasher(),
    new RateLimiter(Paths::storage() . '/login', Env::int('ADMIN_LOGIN_MAX_ATTEMPTS', 5), 900),
    static function (): void {
        session_regenerate_id(true);
    },
    Env::int('ADMIN_IDLE_TIMEOUT', 1800)
);
$csrf = new Csrf($_SESSION);
$csrfToken = $csrf->token();

$adminScript = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_FILENAME'] ?? ''));
if (str_contains($adminScript, '/admin/webservice/')) {
    $adminUser = $adminAuth->user(basename($adminScript) !== 'CheckRefresh.php');
    $givenToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['_csrf'] ?? null);
    $decision = (new AccessPolicy())->decide(
        $adminScript,
        (string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'),
        $adminUser !== null,
        $csrf->isValid(is_string($givenToken) ? $givenToken : null)
    );
    if ($decision !== AccessPolicy::ALLOW) {
        $messages = [401 => 'Authentication required.', 403 => 'Invalid CSRF token.', 405 => 'Method not allowed.'];
        json_out(['status' => 0, 'msg' => $messages[$decision] ?? 'Forbidden.'], $decision);
    }
}
