<?php
$db = new PDO('sqlite:database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$email = 'admin@sdcc.ma';
$stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row) {
    echo $row['id'];
    exit;
}
$password = password_hash('password', PASSWORD_BCRYPT);
$now = (new DateTime())->format('Y-m-d H:i:s');
$ins = $db->prepare('INSERT INTO users (name,email,password,service,is_active,created_at,updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)');
$ins->execute(['SDCC Admin', $email, $password, 'Moyens Généraux', 1, $now, $now]);
$userId = $db->lastInsertId();
// ensure roles
$roleStmt = $db->prepare('SELECT id FROM roles WHERE name = ?');
$roleStmt->execute(['super_admin']);
$role = $roleStmt->fetch(PDO::FETCH_ASSOC);
if ($role) {
    $rId = $role['id'];
    $chk = $db->prepare('SELECT 1 FROM model_has_roles WHERE role_id = ? AND model_type = ? AND model_id = ?');
    $chk->execute([$rId, 'App\\Models\\User', $userId]);
    if (!$chk->fetch()) {
        $insRole = $db->prepare('INSERT INTO model_has_roles (role_id, model_type, model_id) VALUES (?, ?, ?)');
        $insRole->execute([$rId, 'App\\Models\\User', $userId]);
    }
}
echo $userId;
