<?php

namespace App\Models;

use CodeIgniter\Model;

class VideoBookmarkModel extends Model
{
    protected $table            = 'video_bookmarks';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['user_id', 'lesson_id', 'last_time'];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Save or update bookmark for a user and lesson
     */
    public function saveBookmark(int $userId, int $lessonId, float $time)
    {
        $existing = $this->where('user_id', $userId)
                         ->where('lesson_id', $lessonId)
                         ->first();

        if ($existing) {
            return $this->update($existing['id'], ['last_time' => $time]);
        }

        return $this->insert([
            'user_id'   => $userId,
            'lesson_id' => $lessonId,
            'last_time' => $time
        ]);
    }
}
