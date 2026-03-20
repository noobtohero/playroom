<?php
// bootstrap CI4
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
require __DIR__ . '/vendor/autoload.php';
$app = Config\Services::codeigniter();
$app->initialize();

$db = \Config\Database::connect();
$res = $db->table('users')
          ->where('email', 'admin@playroom.test')
          ->update(['role' => 'super-admin']);

if ($res) {
    echo "Successfully updated admin@playroom.test to super-admin\n";
} else {
    echo "Failed to update or user not found\n";
}
