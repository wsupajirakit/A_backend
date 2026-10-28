<?php

declare(strict_types=1);

// Session ping: reports remaining idle time without extending it.
require_once __DIR__ . '/../bootstrap.php';

json_out(['status' => 1, 'nextRefresh' => $adminAuth->secondsUntilIdle() + 61]);
