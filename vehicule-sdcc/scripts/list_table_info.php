<?php
$db = new PDO('sqlite:database/database.sqlite');
$cols = $db->query("PRAGMA table_info(users)")->fetchAll(PDO::FETCH_ASSOC);
print_r($cols);
