<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../assets/message_var/th.php';

use App\Admin\LoginResult;

$username = trim((string) ($_POST['username'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    json_out(['status' => 2, 'msg' => $txt_var['please_fill_data']]);
}

$result = $adminAuth->attempt($username, $password, (string) ($_SERVER['REMOTE_ADDR'] ?? ''));
if ($result === LoginResult::Success) {
    $csrf->rotate();
    json_out(['status' => 1, 'csrf' => $csrf->token()]);
}

$messages = [
    LoginResult::Suspended->name => $txt_var['your_account_suspense'],
    LoginResult::Throttled->name => 'Too many attempts. Please try again later.',
];
json_out(['status' => 2, 'msg' => $messages[$result->name] ?? $txt_var['username_password_invalid']]);
