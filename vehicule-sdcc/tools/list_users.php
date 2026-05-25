<?php
$db = new PDO('sqlite:'.__DIR__.'/../database/database.sqlite');
$stmt = $db->query('SELECT id, email, name, is_active, created_at FROM users ORDER BY id');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rows, JSON_PRETTY_PRINT) . PHP_EOL;
