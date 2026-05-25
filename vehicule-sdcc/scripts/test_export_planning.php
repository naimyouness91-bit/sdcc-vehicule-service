<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\PlanificationController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

$admin = User::role('super_admin')->first() ?? User::role('admin')->first();
if (! $admin) {
    echo json_encode(['ok'=>false,'message'=>'No admin user found']) . PHP_EOL;
    exit(1);
}

Auth::loginUsingId($admin->id);

$ctrl = app()->make(PlanificationController::class);

$request = Request::create('/planification/export/excel', 'GET', [], [], [], ['HTTP_ACCEPT' => 'text/html']);

$response = $ctrl->exportPlanningExcel($request);

$status = method_exists($response, 'getStatusCode') ? $response->getStatusCode() : null;
$headers = method_exists($response, 'headers') ? $response->headers->all() : [];

echo json_encode(['status' => $status, 'headers' => $headers], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) . PHP_EOL;
