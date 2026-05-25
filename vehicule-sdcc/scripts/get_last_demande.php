<?php
$db = new PDO('sqlite:database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$stmt = $db->query('SELECT id FROM demandes ORDER BY created_at DESC, id DESC LIMIT 1');
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row) echo $row['id'];
else echo "";
