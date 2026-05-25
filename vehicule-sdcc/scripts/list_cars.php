<?php
$db = new PDO('sqlite:database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$stmt = $db->query('SELECT id, name, matricule, status FROM cars LIMIT 20');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($rows);
