<?php
$db=new PDO('sqlite:database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$rows=$db->query('SELECT id,user_id,car_id,destination,start_date,end_date,status,created_at FROM demandes ORDER BY created_at DESC LIMIT 20')->fetchAll(PDO::FETCH_ASSOC);
print_r($rows);
