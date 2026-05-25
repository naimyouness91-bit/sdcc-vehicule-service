<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Car;
use Illuminate\Support\Facades\Log;

try {
    $car = Car::find(1);
    echo "Before: availability_type=".($car->availability_type ?? 'NULL')."\n";
    $car->availability_type = 'unavailable';
    $car->save();
    echo "Saved OK\n";
} catch (Throwable $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
