<?php

declare(strict_types=1);

// Entry point shared by the API, the public site and the admin panel.
require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/helpers.php';

App\Support\Bootstrap::boot(dirname(__DIR__));
