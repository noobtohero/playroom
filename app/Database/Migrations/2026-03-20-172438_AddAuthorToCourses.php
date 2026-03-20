<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAuthorToCourses extends Migration
{
    public function up()
    {
        $fields = [
            'author_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id'
            ],
        ];
        $this->forge->addColumn('courses', $fields);
        $this->forge->addForeignKey('author_id', 'users', 'id', 'SET NULL', 'CASCADE');
        // Note: For existing migrations, we might need a separate query to add FK if addColumn doesn't support it directly in some drivers, but CI4 forge usually handles it.
    }

    public function down()
    {
        $this->forge->dropForeignKey('courses', 'courses_author_id_foreign');
        $this->forge->dropColumn('courses', 'author_id');
    }
}
