<?php

declare(strict_types=1);

// Website AJAX: current weekly schedule of a station.
require_once dirname(__DIR__, 2) . '/src/bootstrap.php';

use App\Config\Env;
use App\Database\Connection;
use App\Http\SiteAjax;
use App\Repository\RadioProgramRepository;

$radio = (string) ($_GET['radio'] ?? '1');
if (preg_match('/^[1-9]\d{0,5}$/', $radio) !== 1) {
    json_out(['status' => 2, 'result' => null], 400);
}
$base = rtrim((string) Env::get('PUBLIC_BASE_URL', 'https://www.example.com'), '/');
SiteAjax::respond(static fn (Connection $db): ?array => (new RadioProgramRepository($db, $base . '/rsc/radio_presenter/'))
    ->currentSchedule((int) $radio, date('Y-m-d')));
