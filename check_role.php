<?php
require __DIR__ . '/vendor/autoload.php';
$db = \Config\Database::connect();
$user = $db->table('users')->where('email', 'admin@playroom.test')->get()->getRow();
echo "Role: " . $user->role . "\n";
