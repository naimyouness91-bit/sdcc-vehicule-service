<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Carbon\Carbon;
use App\Http\Controllers\MesDemandesController;

$ctrl = app()->make(MesDemandesController::class);
$ref = new ReflectionClass($ctrl);
$method = $ref->getMethod('validateWeekendOnlyConstraints');
$method->setAccessible(true);

$cases = [
    ['name'=>'samedi->lundi','start'=>'2026-05-30','startTime'=>'18:00','end'=>'2026-06-01','endTime'=>'11:00'],
    ['name'=>'dimanche->lundi','start'=>'2026-05-31','startTime'=>'18:00','end'=>'2026-06-01','endTime'=>'11:00'],
    ['name'=>'vendredi-matin->lundi','start'=>'2026-05-29','startTime'=>'09:00','end'=>'2026-06-01','endTime'=>'11:00'],
    ['name'=>'vendredi-soir->mardi','start'=>'2026-05-29','startTime'=>'18:00','end'=>'2026-06-02','endTime'=>'11:00'],
    ['name'=>'jeudi->lundi','start'=>'2026-05-28','startTime'=>'18:00','end'=>'2026-06-01','endTime'=>'11:00'],
    ['name'=>'valid-friday-to-monday','start'=>'2026-06-05','startTime'=>'18:00','end'=>'2026-06-08','endTime'=>'11:00'],
];

$out = [];
foreach ($cases as $c) {
    $res = $method->invoke($ctrl, Carbon::parse($c['start']), $c['startTime'], Carbon::parse($c['end']), $c['endTime']);
    $out[] = ['case'=>$c['name'],'result'=>$res];
}

echo json_encode($out, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) . "\n";
