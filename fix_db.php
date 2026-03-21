<?php
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
$system_path = __DIR__ . '/vendor/codeigniter4/framework/system';
require $system_path . '/bootstrap.php';

$db = \Config\Database::connect();
$fields = $db->getFieldNames('users');
if (!in_array('full_name', $fields)) {
    $db->query("ALTER TABLE users ADD COLUMN full_name VARCHAR(255) AFTER name");
    echo "Column 'full_name' added successfully.\n";
} else {
    echo "Column 'full_name' already exists.\n";
}
