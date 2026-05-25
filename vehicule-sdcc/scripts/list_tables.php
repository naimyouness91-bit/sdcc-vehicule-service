<?php
$db = new PDO('sqlite:database/database.sqlite');
$rows = $db->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_ASSOC);
print_r($rows);
