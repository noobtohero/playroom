<?php

namespace App\Models;

use CodeIgniter\Model;

class LessonProgressModel extends Model
{
    protected $table            = 'lesson_progress';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['user_id', 'course_id', 'lesson_id', 'is_completed'];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get completion percentage for a user in a specific course
     */
    public function getCourseProgress(int $userId, int $course_id): int
    {
        $lessonModel = new LessonModel();
        $totalLessons = $lessonModel->where('course_id', $course_id)
                                    ->where('status', 'published')
                                    ->countAllResults();

        if ($totalLessons === 0) return 0;

        $completedCount = $this->where('user_id', $userId)
                               ->where('course_id', $course_id)
                               ->where('is_completed', 1)
                               ->countAllResults();

        return (int) round(($completedCount / $totalLessons) * 100);
    }
}
