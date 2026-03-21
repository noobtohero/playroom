<?php

namespace App\Models;

use CodeIgniter\Model;

class TrophyModel extends Model
{
    protected $table            = 'trophies';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['name', 'description', 'icon', 'type'];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // No updated_at for trophies
}
