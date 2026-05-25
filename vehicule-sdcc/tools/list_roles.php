<?php
$db=new PDO('sqlite:'.__DIR__.'/../database/database.sqlite');
$q=$db->query('SELECT * FROM roles');
$rows=$q->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rows, JSON_PRETTY_PRINT).PHP_EOL;
?>