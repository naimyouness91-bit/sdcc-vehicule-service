<?php
$db = new PDO('sqlite:database/database.sqlite');
$rows = $db->query('SELECT id,name FROM roles')->fetchAll(PDO::FETCH_ASSOC);
print_r($rows);
