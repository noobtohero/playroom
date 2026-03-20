<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddExternalUrlAndDownloadToLessons extends Migration
{
    public function up()
    {
        $fields = [
            'external_url' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'content_path',
            ],
            'is_downloadable' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'external_url',
            ],
        ];
        $this->forge->addColumn('lessons', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('lessons', ['external_url', 'is_downloadable']);
    }
}
