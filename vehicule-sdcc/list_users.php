<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$users = App\Models\User::select('id','email','name')->get()->toArray();
echo json_encode($users, JSON_PRETTY_PRINT);
