<?php

declare(strict_types=1);

// Website AJAX: one top chart by date (Y-m-d).
require_once dirname(__DIR__, 2) . '/src/bootstrap.php';

use App\Database\Connection;
use App\Http\SiteAjax;
use App\Repository\TopChartRepository;
use App\Support\ThaiDate;

$date = (string) ($_GET['date'] ?? '');
if (!ThaiDate::isValidYmd($date)) {
    json_out(['status' => 2, 'result' => null], 400);
}
SiteAjax::respond(static fn (Connection $db): ?array => (new TopChartRepository($db))->byDate($date));
