<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PremiumPhase2 extends Migration
{
    public function up()
    {
        // 1. Lesson Progress
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'course_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'lesson_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'is_completed' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['user_id', 'lesson_id']);
        $this->forge->createTable('lesson_progress');

        // 2. Video Bookmarks
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'lesson_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'last_time' => ['type' => 'FLOAT', 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['user_id', 'lesson_id']);
        $this->forge->createTable('video_bookmarks');

        // 3. User Notes
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'lesson_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'content' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['user_id', 'lesson_id']);
        $this->forge->createTable('user_notes');

        // 4. Trophies
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'description' => ['type' => 'TEXT'],
            'icon' => ['type' => 'VARCHAR', 'constraint' => 255],
            'type' => ['type' => 'VARCHAR', 'constraint' => 50],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('trophies');

        // 5. User Trophies
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'trophy_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'earned_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['user_id', 'trophy_id']);
        $this->forge->createTable('user_trophies');
    }

    public function down()
    {
        $this->forge->dropTable('lesson_progress');
        $this->forge->dropTable('video_bookmarks');
        $this->forge->dropTable('user_notes');
        $this->forge->dropTable('trophies');
        $this->forge->dropTable('user_trophies');
    }
}
