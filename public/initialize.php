<?php

declare(strict_types=1);

// Shared setup for the public site pages.
require_once dirname(__DIR__) . '/src/bootstrap.php';

use App\Config\Env;

/** @return array<string, mixed> */
function load_site_json(string $path): array
{
    $data = is_readable($path) ? json_decode((string) file_get_contents($path), true) : null;
    if (!is_array($data)) {
        throw new RuntimeException('Site data file is missing or invalid: ' . basename($path));
    }
    return $data;
}

$appData = load_site_json(dirname(__DIR__) . '/admin/data/app-data.json');
$appearanceData = load_site_json(dirname(__DIR__) . '/admin/data/web-client/appearance.json');
$streamUrl = (string) Env::get('STREAM_URL', '');
$meta_keywords_default = 'สถานีวิทยุ,คลื่นวิทยุ';
