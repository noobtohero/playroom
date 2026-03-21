<?php

namespace App\Models;

use CodeIgniter\Model;

class UserTrophyModel extends Model
{
    protected $table            = 'user_trophies';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['user_id', 'trophy_id', 'earned_at'];

    // Dates
    protected $useTimestamps = false; // We use earned_at manually
}
