<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Demande;

$d = Demande::query()->with(['user','car'])->orderByDesc('created_at')->first();
if (! $d) {
    echo json_encode(['ok' => false, 'message' => 'Aucune demande trouvée'], JSON_UNESCAPED_UNICODE) . PHP_EOL;
    exit(0);
}

$out = [
    'id' => $d->id,
    'user_id' => $d->user_id,
    'user_name' => $d->user->name ?? null,
    'car_id' => $d->car_id,
    'car' => $d->car ? ($d->car->matricule . ' (' . $d->car->name . ')') : null,
    'start_date' => $d->start_date?->format('Y-m-d'),
    'start_time' => $d->start_time,
    'end_date' => $d->end_date?->format('Y-m-d'),
    'end_time' => $d->end_time,
    'status' => $d->status,
    'destination' => $d->destination,
    'created_at' => $d->created_at?->toDateTimeString(),
];

echo json_encode(['ok' => true, 'demande' => $out], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) . PHP_EOL;
