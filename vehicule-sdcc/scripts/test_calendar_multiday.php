<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\CalendrierController;
use App\Models\Car;
use App\Models\Demande;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

$admin = User::role('super_admin')->first() ?? User::role('admin')->first();
if ($admin) {
    Auth::loginUsingId($admin->id);
}

$car = Car::query()->first();
$employee = User::role('employee')->first() ?? $admin;

if (!$car || !$employee) {
    echo json_encode(['ok' => false, 'error' => 'missing fixtures']);
    exit(1);
}

$start = Carbon::create(2026, 5, 15);
$end = Carbon::create(2026, 5, 20);

Demande::query()
    ->where('car_id', $car->id)
    ->whereDate('start_date', $start)
    ->delete();

Demande::create([
    'user_id' => $employee->id,
    'car_id' => $car->id,
    'destination' => 'Test multi-day',
    'start_date' => $start->toDateString(),
    'start_time' => '09:00',
    'end_date' => $end->toDateString(),
    'end_time' => '17:00',
    'reason' => 'Test calendrier',
    'status' => Demande::STATUS_APPROVED,
]);

$ctrl = app(CalendrierController::class);
$view = $ctrl->index(Request::create('/calendrier', 'GET', [
    'year' => 2026,
    'month' => 5,
    'car' => (string) $car->id,
]));
$data = $view->getData();
$statusByDate = $data['statusByDate'];

$marked = [];
for ($d = 15; $d <= 20; $d++) {
    $key = sprintf('2026-05-%02d', $d);
    $marked[$key] = ($statusByDate[$key]['key'] ?? null) === 'approved';
}

echo json_encode(['ok' => !in_array(false, $marked, true), 'days' => $marked], JSON_PRETTY_PRINT) . PHP_EOL;
