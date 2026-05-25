<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::beginTransaction();

    // Create new table with expanded availability_type CHECK
    DB::statement(<<<'SQL'
CREATE TABLE cars_new (
  id integer primary key autoincrement not null,
  name varchar not null,
  matricule varchar not null,
  model varchar not null,
  year integer not null,
  km integer not null default '0',
  status varchar check ("status" in ('disponible', 'maintenance')) not null default 'disponible',
  created_at datetime,
  updated_at datetime,
  is_core tinyint(1) not null default '0',
  weekday_available tinyint(1) not null default '1',
  weekend_available tinyint(1) not null default '0',
  availability_type varchar check ("availability_type" in ('weekday', 'weekend', 'both', 'unavailable')) not null default 'both',
  kilometrage_max integer not null default '0',
  kilometrage_actuel integer not null default '0',
  planning_zone_id integer
);
SQL
    );

    // Copy data
    DB::statement('INSERT INTO cars_new (id, name, matricule, model, year, km, status, created_at, updated_at, is_core, weekday_available, weekend_available, availability_type, kilometrage_max, kilometrage_actuel, planning_zone_id) SELECT id, name, matricule, model, year, km, status, created_at, updated_at, is_core, weekday_available, weekend_available, availability_type, kilometrage_max, kilometrage_actuel, planning_zone_id FROM cars');

    // Drop old table and rename new
    DB::statement('DROP TABLE cars');
    DB::statement('ALTER TABLE cars_new RENAME TO cars');

    DB::commit();
    echo "Recreated cars table with updated availability_type CHECK.\n";
} catch (Throwable $e) {
    DB::rollBack();
    echo "Failed: " . $e->getMessage() . "\n";
}
