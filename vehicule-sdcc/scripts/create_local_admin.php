<?php
$db = new PDO('sqlite:database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Create roles if missing
$roles = ['super_admin','admin','employee'];
foreach ($roles as $r) {
    $stmt = $db->prepare('SELECT id FROM roles WHERE name = ?');
    $stmt->execute([$r]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        $ins = $db->prepare('INSERT INTO roles (name, guard_name, created_at, updated_at) VALUES (?,"web", datetime("now"), datetime("now"))');
        $ins->execute([$r]);
        echo "Inserted role: $r\n";
    } else {
        echo "Role exists: $r\n";
    }
}

// Create user if missing
$email = 'admin@local.test';
$stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    $password = password_hash('password', PASSWORD_BCRYPT);
    $ins = $db->prepare('INSERT INTO users (name,email,password,created_at,updated_at) VALUES (?, ?, ?, datetime("now"), datetime("now"))');
    $ins->execute(['Local Admin', $email, $password]);
    $userId = $db->lastInsertId();
    echo "Created user id=$userId\n";
} else {
    $userId = $user['id'];
    echo "User exists id=$userId\n";
}

// Assign super_admin role to user
$stmt = $db->prepare('SELECT id FROM roles WHERE name = ?');
$stmt->execute(['super_admin']);
$role = $stmt->fetch(PDO::FETCH_ASSOC);
if ($role) {
    $roleId = $role['id'];
    $stmt = $db->prepare('SELECT 1 FROM model_has_roles WHERE role_id = ? AND model_type = ? AND model_id = ?');
    $stmt->execute([$roleId, 'App\\Models\\User', $userId]);
    if (!$stmt->fetch()) {
        $ins = $db->prepare('INSERT INTO model_has_roles (role_id, model_type, model_id) VALUES (?, ?, ?)');
        $ins->execute([$roleId, 'App\\Models\\User', $userId]);
        echo "Assigned role super_admin to user id=$userId\n";
    } else {
        echo "User already has super_admin role\n";
    }
} else {
    echo "Role super_admin not found\n";
}

echo "Done. Login: admin@local.test / password\n";
