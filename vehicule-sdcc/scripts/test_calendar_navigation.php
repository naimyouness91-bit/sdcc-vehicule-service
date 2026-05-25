<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\CalendrierController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;

$ctrl = app()->make(CalendrierController::class);

$admin = User::role('super_admin')->first() ?? User::role('admin')->first();
if ($admin) Auth::loginUsingId($admin->id);

$year = 2050; $month = 12;
$request = Request::create('/calendrier', 'GET', ['year' => $year, 'month' => $month, 'car' => 'all']);

try {
    $view = $ctrl->index($request);
    $data = method_exists($view, 'getData') ? $view->getData() : [];
    $statusByDate = $data['statusByDate'] ?? null;
    $count = is_array($statusByDate) ? count($statusByDate) : 0;
    echo json_encode(['ok' => true, 'year' => $year, 'month' => $month, 'days_count' => $count], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) . PHP_EOL;
} catch (Throwable $e) {
    echo json_encode(['ok' => false, 'error' => $e->getMessage()], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) . PHP_EOL;
}
