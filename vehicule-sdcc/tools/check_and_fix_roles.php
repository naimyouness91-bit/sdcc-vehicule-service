<?php
$dbPath = __DIR__ . '/../database/database.sqlite';
$db = new PDO('sqlite:' . $dbPath);
$email = 'superadmin@sdcc.ma';

// Fetch roles
$roles = $db->query('SELECT id, name FROM roles')->fetchAll(PDO::FETCH_ASSOC);
// Fetch user
$stmt = $db->prepare('SELECT id, email, name FROM users WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$result = ['roles' => $roles, 'user' => $user];

if ($user) {
    $mid = $user['id'];
    $mhr = $db->prepare('SELECT role_id FROM model_has_roles WHERE model_type = ? AND model_id = ?');
    $mhr->execute(['App\\Models\\User', $mid]);
    $assigned = $mhr->fetchAll(PDO::FETCH_COLUMN, 0);
    $result['assigned_role_ids'] = $assigned;
    // find super_admin role id
    $superRole = null;
    foreach ($roles as $r) {
        if ($r['name'] === 'super_admin') { $superRole = $r; break; }
    }
    if (!$superRole) {
        // create role
        $now = date('Y-m-d H:i:s');
        $db->prepare('INSERT INTO roles (name, guard_name, created_at, updated_at) VALUES (?, ?, ?, ?)')
            ->execute(['super_admin','web',$now,$now]);
        $newId = $db->lastInsertId();
        $result['created_role'] = ['id'=>$newId, 'name'=>'super_admin'];
        $superRole = ['id'=>$newId, 'name'=>'super_admin'];
    }
    // assign if missing
    if (!in_array($superRole['id'], $assigned)) {
        $db->prepare('INSERT INTO model_has_roles (role_id, model_type, model_id) VALUES (?, ?, ?)')
            ->execute([$superRole['id'], 'App\\Models\\User', $mid]);
        $result['assigned'] = true;
    } else {
        $result['assigned'] = false;
    }
}

echo json_encode($result, JSON_PRETTY_PRINT) . PHP_EOL;
