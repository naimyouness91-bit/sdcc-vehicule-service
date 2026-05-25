<?php
// Assign a role to a user by ids
$db=new PDO('sqlite:'.__DIR__.'/../database/database.sqlite');
$userId = 1;
$roleId = 1; // super_admin
// Check existing
$stmt = $db->prepare('SELECT * FROM model_has_roles WHERE model_id=? AND role_id=? AND model_type="App\\Models\\User"');
$stmt->execute([$userId,$roleId]);
if ($stmt->fetch()) {
    echo "Role already assigned\n";
    exit;
}
// Insert
$ins = $db->prepare('INSERT INTO model_has_roles (role_id, model_type, model_id) VALUES (?, ?, ?)');
$ins->execute([$roleId, 'App\\Models\\User', $userId]);
if ($ins) echo "Assigned role $roleId to user $userId\n";
?>