<?php

declare(strict_types=1);

// Sign-in page guard: an already signed-in admin goes to the dashboard.
require_once __DIR__ . '/bootstrap.php';

if ($adminAuth->user() !== null) {
    header('Location: index.php');
    exit;
}
