<?php

declare(strict_types=1);

// Page guard: requires a signed-in admin, else redirects to the sign-in page.
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/assets/php-function/function.php';
require_once __DIR__ . '/assets/php-function/pdo-database.php';

$userData = $adminAuth->user();
if ($userData === null) {
    header('Location: signin.php');
    exit;
}
$db = new DatabaseConnection();
