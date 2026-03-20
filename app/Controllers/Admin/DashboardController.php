<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\CourseModel;
use App\Models\UserModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $courseModel = new CourseModel();
        $userModel = new UserModel();
        
        // Calculate Revenue: Sum of prices of courses from approved purchases
        $db = \Config\Database::connect();
        $revenueBuilder = $db->table('purchases');
        $revenueBuilder->selectSum('courses.price', 'total_revenue');
        $revenueBuilder->join('courses', 'courses.id = purchases.course_id');
        $revenueBuilder->where('purchases.status', 'approved');
        $revenue = $revenueBuilder->get()->getRow()->total_revenue ?? 0;

        $data = [
            'total_users'    => $userModel->countAllResults(),
            'total_students' => $userModel->where('role', 'student')->countAllResults(),
            'total_courses'  => $courseModel->countAllResults(),
            'revenue'        => $revenue,
            'recent_purchases' => $db->table('purchases')
                                     ->select('purchases.*, users.name as user_name, courses.title as course_title')
                                     ->join('users', 'users.id = purchases.user_id')
                                     ->join('courses', 'courses.id = purchases.course_id')
                                     ->orderBy('purchases.created_at', 'DESC')
                                     ->limit(5)
                                     ->get()
                                     ->getResultArray()
        ];

        return view('admin/dashboard', $data);
    }
}
