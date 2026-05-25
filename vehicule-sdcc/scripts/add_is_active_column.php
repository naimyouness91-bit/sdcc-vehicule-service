<?php
$db = new PDO('sqlite:database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// Add column if not exists
$cols = $db->query("PRAGMA table_info(users)")->fetchAll(PDO::FETCH_ASSOC);
$names = array_column($cols, 'name');
if (!in_array('is_active', $names)) {
    $db->exec("ALTER TABLE users ADD COLUMN is_active INTEGER NOT NULL DEFAULT 1");
    echo "Added is_active column\n";
} else {
    echo "Column already exists\n";
}
