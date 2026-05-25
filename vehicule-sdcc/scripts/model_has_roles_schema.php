<?php
$db=new PDO('sqlite:database/database.sqlite');
$cols=$db->query("PRAGMA table_info(model_has_roles)")->fetchAll(PDO::FETCH_ASSOC);
print_r($cols);
