<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\Hash;
$emails = ['superadmin@sdcc.ma','admin@sdcc.ma'];
foreach ($emails as $e) {
    $u = App\Models\User::where('email', $e)->first();
    if ($u) {
        $u->password = Hash::make('SuperAdmin123456');
        $u->save();
        echo "Updated: $e\n";
    } else {
        echo "Not found: $e\n";
    }
}
