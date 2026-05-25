<?php
$db = new PDO('sqlite:database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$now = (new DateTime())->format('Y-m-d H:i:s');
$matricule = 'TEST-'.time();
$stmt = $db->prepare('INSERT INTO cars (name, matricule, model, year, km, status, availability_type, is_core, weekday_available, weekend_available, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
$stmt->execute([
    'Test Vehicle',
    $matricule,
    'TestModel',
    2020,
    0,
    'disponible',
    'both',
    0,
    1,
    0,
    $now,
    $now,
]);
echo $db->lastInsertId();
