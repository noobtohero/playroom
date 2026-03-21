<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TrophySeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name'        => 'First Step',
                'description' => 'Complete your very first lesson.',
                'icon'        => 'bi-rocket-takeoff',
                'type'        => 'first_lesson',
                'created_at'  => date('Y-m-d H:i:s')
            ],
            [
                'name'        => 'Course Master',
                'description' => 'Finish any course with 100% progress.',
                'icon'        => 'bi-mortarboard-fill',
                'type'        => 'course_complete',
                'created_at'  => date('Y-m-d H:i:s')
            ],
            [
                'name'        => 'Knowledge Seeker',
                'description' => 'Watch or read 5 different lessons.',
                'icon'        => 'bi-search',
                'type'        => 'milestone_5',
                'created_at'  => date('Y-m-d H:i:s')
            ],
            [
                'name'        => 'Deep Thinker',
                'description' => 'Write your first personal lesson note.',
                'icon'        => 'bi-journal-check',
                'type'        => 'first_note',
                'created_at'  => date('Y-m-d H:i:s')
            ],
        ];

        // Using simple query to check if empty first
        $this->db->table('trophies')->insertBatch($data);
    }
}
