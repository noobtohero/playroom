<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSessionIdToUsers extends Migration
{
    public function up()
    {
        $fields = [
            'last_session_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'status',
            ],
        ];
        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'last_session_id');
    }
}
