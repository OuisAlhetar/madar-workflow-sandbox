<?php
declare(strict_types=1);

/**
 * Minimal test runner: no Composer, no extensions, no network.
 *
 *   php tests/run.php                     run everything
 *   php tests/run.php test_single_line    run tests whose name contains the filter
 *
 * Exit code 0 when every selected test passed, 1 otherwise.
 */

require __DIR__ . '/bootstrap.php';
require __DIR__ . '/PriceTest.php';

$filter = $argv[1] ?? null;
$class = PriceToy\Tests\PriceTest::class;

$passed = 0;
$failed = 0;
$skipped = 0;

foreach (get_class_methods($class) as $method) {
    if (!str_starts_with($method, 'test_')) {
        continue;
    }
    if ($filter !== null && !str_contains($method, $filter)) {
        $skipped++;
        continue;
    }

    try {
        (new $class())->{$method}();
        $passed++;
        echo "PASS {$method}\n";
    } catch (\Throwable $e) {
        $failed++;
        echo "FAIL {$method}\n      {$e->getMessage()}\n";
    }
}

printf("\n%d passed, %d failed, %d skipped\n", $passed, $failed, $skipped);

exit($failed === 0 ? 0 : 1);
