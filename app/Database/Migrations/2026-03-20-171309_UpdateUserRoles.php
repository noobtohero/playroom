<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateUserRoles extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('users', [
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['super-admin', 'admin', 'teacher', 'student'],
                'default'    => 'student',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('users', [
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['student', 'admin'],
                'default'    => 'student',
            ],
        ]);
    }
}
