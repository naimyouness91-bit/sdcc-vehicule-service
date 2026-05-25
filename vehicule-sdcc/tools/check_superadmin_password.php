<?php
$db = new PDO('sqlite:'.__DIR__.'/../database/database.sqlite');
$email = $argv[1] ?? 'superadmin@sdcc.ma';
$stmt = $db->prepare('SELECT password FROM users WHERE email = :email LIMIT 1');
$stmt->execute([':email' => $email]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) {
    echo "NOT FOUND\n";
    exit(1);
}
$hash = $row['password'];
$pw = $argv[2] ?? 'ChangeMe@123456';
$ok = password_verify($pw, $hash) ? 'MATCH' : 'NO MATCH';
echo json_encode(['email'=>$email,'checked_password'=>$pw,'result'=>$ok,'hash'=>$hash], JSON_PRETTY_PRINT) . PHP_EOL;