<?php

namespace App\Models;

use CodeIgniter\Model;

class UserNotesModel extends Model
{
    protected $table            = 'user_notes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['user_id', 'lesson_id', 'content'];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
