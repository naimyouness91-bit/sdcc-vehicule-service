<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Car;
use Illuminate\Support\Facades\DB;

echo "Cars before:\n";
echo json_encode(Car::all()->toArray(), JSON_PRETTY_PRINT) . "\n";

echo "\nForcing car id=1 availability_type='weekend'...\n";
Car::where('id', 1)->update(['availability_type' => 'weekend']);
echo "updated\n";

echo "\nCars after:\n";
echo json_encode(Car::all()->toArray(), JSON_PRETTY_PRINT) . "\n";

echo "\nDeleting test demande id=3 if exists...\n";
$deleted = DB::table('demandes')->where('id', 3)->delete();
echo "deleted_count:" . (int)$deleted . "\n";

echo "\nDone.\n";
