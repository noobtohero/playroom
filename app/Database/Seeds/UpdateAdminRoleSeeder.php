<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UpdateAdminRoleSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $db->table('users')
           ->where('email', 'admin@playroom.test')
           ->update(['role' => 'super-admin']);
    }
}
