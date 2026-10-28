<?php

declare(strict_types=1);

// Mobile API front controller: every /api/v1/* request is rewritten here.
require_once dirname(__DIR__, 2) . '/src/bootstrap.php';

\App\Http\ApiFactory::kernel()->handle(\App\Http\Request::fromGlobals())->send();
