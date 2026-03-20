<?php

namespace App\Models;

use CodeIgniter\Model;

class RedeemCodeModel extends Model
{
    protected $table            = 'redeem_codes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['code', 'course_id', 'max_use', 'used_count', 'expire_at', 'status', 'created_at'];

    // Dates
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    // Validation
    protected $validationRules = [
        'code'      => 'required|is_unique[redeem_codes.code,id,{id}]',
        'course_id' => 'required|numeric',
        'status'    => 'required|in_list[active,inactive,expired]',
    ];
}
