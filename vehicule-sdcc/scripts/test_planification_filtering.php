<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\PlanificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

$ctrl = app()->make(PlanificationController::class);

$admin = User::role('super_admin')->first() ?? User::role('admin')->first();
if ($admin) {
    Auth::loginUsingId($admin->id);
}

$request = Request::create('/planification', 'GET', ['status' => 'all']);
$view = $ctrl->index($request);

$data = method_exists($view, 'getData') ? $view->getData() : [];
$reservations = $data['reservations'] ?? [];

$bad = [];
foreach ($reservations as $r) {
    if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $r['date'])) {
        $bad[] = $r;
    }
}

echo json_encode(['checked' => count($reservations), 'bad_count' => count($bad), 'bad_examples' => array_slice($bad, 0, 5)], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) . PHP_EOL;
