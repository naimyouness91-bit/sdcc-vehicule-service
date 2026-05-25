<?php
$db = new PDO('sqlite:'.__DIR__.'/../database/database.sqlite');
$stmt = $db->prepare('SELECT id, email, password, is_active, created_at FROM users WHERE email = ?');
$stmt->execute(['superadmin@sdcc.ma']);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode($row) . PHP_EOL;
