<?php

declare(strict_types=1);

// Plain-PHP test runner: php tests/run.php
require_once dirname(__DIR__) . '/src/autoload.php';
require_once dirname(__DIR__) . '/src/helpers.php';

spl_autoload_register(static function (string $class): void {
    if (str_starts_with($class, 'Tests\\')) {
        $file = __DIR__ . '/' . str_replace('\\', '/', substr($class, 6)) . '.php';
        if (is_file($file)) {
            require $file;
        }
    }
});

$passed = 0;
$failed = 0;
$assertions = 0;
foreach (glob(__DIR__ . '/*Test.php') ?: [] as $file) {
    $class = 'Tests\\' . basename($file, '.php');
    foreach (get_class_methods($class) as $method) {
        if (!str_starts_with($method, 'test')) {
            continue;
        }
        $test = new $class();
        try {
            $test->$method();
            $passed++;
            echo "  ok   {$class}::{$method}\n";
        } catch (Throwable $e) {
            $failed++;
            echo "  FAIL {$class}::{$method}\n       " . str_replace("\n", "\n       ", $e->getMessage()) . "\n";
        }
        $assertions += $test->assertions();
    }
}

printf("\n%d passed, %d failed, %d assertions\n", $passed, $failed, $assertions);
exit($failed === 0 ? 0 : 1);
