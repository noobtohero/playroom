<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'name'       => 'Admin',
            'email'      => 'admin@playroom.test',
            'password'   => password_hash('password', PASSWORD_DEFAULT),
            'role'       => 'admin',
            'status'     => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Using Query Builder
        $this->db->table('users')->insert($data);
    }
}
