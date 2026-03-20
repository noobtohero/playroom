<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table            = 'enrollments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id', 'course_id', 'source', 'created_at'];

    // Dates
    protected $useTimestamps = false; // We use a manual created_at if needed, or let DB handle it
    protected $createdField  = 'created_at';

    // Validation
    protected $validationRules = [
        'user_id'   => 'required|numeric',
        'course_id' => 'required|numeric',
        'source'    => 'required|in_list[free,purchase,code,admin]',
    ];

    /**
     * Check if a user is enrolled in a specific course
     */
    public function isEnrolled($userId, $courseId)
    {
        return $this->where([
            'user_id'   => $userId,
            'course_id' => $courseId
        ])->first() !== null;
    }
}
