<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\MesDemandesController;
use App\Models\User;
use App\Models\Demande;

// Find an employee user to act as
$user = User::role('employee')->first() ?? User::query()->first();
if (! $user) {
    echo json_encode(['ok'=>false,'message'=>'No user found']) . PHP_EOL;
    exit(1);
}

$ctrl = app()->make(MesDemandesController::class);

$cases = [
    ['name'=>'samedi->lundi','start'=>'2026-05-30','startTime'=>'18:00','end'=>'2026-06-01','endTime'=>'11:00'],
    ['name'=>'dimanche->lundi','start'=>'2026-05-31','startTime'=>'18:00','end'=>'2026-06-01','endTime'=>'11:00'],
    ['name'=>'vendredi-matin->lundi','start'=>'2026-05-29','startTime'=>'09:00','end'=>'2026-06-01','endTime'=>'11:00'],
    ['name'=>'vendredi-soir->mardi','start'=>'2026-05-29','startTime'=>'18:00','end'=>'2026-06-02','endTime'=>'11:00'],
    ['name'=>'jeudi->lundi','start'=>'2026-05-28','startTime'=>'18:00','end'=>'2026-06-01','endTime'=>'11:00'],
    ['name'=>'valid-friday-to-monday','start'=>'2026-06-05','startTime'=>'18:00','end'=>'2026-06-08','endTime'=>'11:00'],
];

$results = [];
foreach ($cases as $c) {
    // Unique destination to track any created records
    $token = 'auto-test-' . $c['name'] . '-' . bin2hex(random_bytes(4));
    $params = [
        'destination' => 'Test ' . $token,
        'start_date' => $c['start'],
        'start_time' => $c['startTime'],
        'end_date' => $c['end'],
        'end_time' => $c['endTime'],
        'kilometers' => 10,
        'reason' => 'Automated validation test',
        'car_id' => 1,
    ];

    // Create request with Accept: application/json so controller returns JSON
    $request = Request::create('/mes-demandes', 'POST', $params, [], [], ['HTTP_ACCEPT' => 'application/json']);

    // Authenticate as chosen user
    Auth::loginUsingId($user->id);

    $response = $ctrl->store($request);

    // Count any demandes created with our token
    $created = Demande::query()->where('destination', 'like', '%' . $token . '%')->count();

    // Normalize response
    $status = null;
    $body = null;
    if (method_exists($response, 'getStatusCode')) {
        $status = $response->getStatusCode();
    }
    if (method_exists($response, 'getContent')) {
        $body = $response->getContent();
    }

    $results[] = [
        'case' => $c['name'],
        'status' => $status,
        'body' => $body,
        'created_count' => $created,
    ];

    // Cleanup any created records (should be zero for invalid cases)
    if ($created > 0) {
        Demande::query()->where('destination', 'like', '%' . $token . '%')->delete();
    }
}

echo json_encode($results, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) . PHP_EOL;
