<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $driver = DB::getDriverName();
    if ($driver === 'mysql') {
        $res = DB::select("SHOW COLUMNS FROM cars LIKE 'availability_type'");
        echo json_encode($res, JSON_PRETTY_PRINT);
    } elseif ($driver === 'sqlite') {
        $res = DB::select("PRAGMA table_info('cars')");
        echo json_encode($res, JSON_PRETTY_PRINT);
    } else {
        echo json_encode(['driver' => $driver]);
    }
} catch (Throwable $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
