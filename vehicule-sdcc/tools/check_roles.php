<?php
$db=new PDO('sqlite:'.__DIR__.'/../database/database.sqlite');
$q=$db->query('SELECT r.id as role_id, r.name as role_name, m.model_id FROM roles r JOIN model_has_roles m ON r.id=m.role_id WHERE m.model_type="App\\\Models\\\User"');
$rows=$q->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rows, JSON_PRETTY_PRINT).PHP_EOL;
?>