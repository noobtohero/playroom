<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardController extends BaseController
{
    public function index()
    {
        // For Phase 1 we just pass dummy stats
        $data = [
            'total_users'   => 1, // Admin user
            'total_courses' => 0,
            'revenue'       => 0,
        ];

        return view('admin/dashboard', $data);
    }
}
