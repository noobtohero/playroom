<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Models\CourseModel;
use App\Models\UserModel;

class EnrollmentController extends BaseController
{
    public function index()
    {
        $enrollmentModel = new EnrollmentModel();
        // Simple join to show user and course names
        $db = \Config\Database::connect();
        $builder = $db->table('enrollments');
        $builder->select('enrollments.*, users.name as user_name, courses.title as course_title');
        $builder->join('users', 'users.id = enrollments.user_id');
        $builder->join('courses', 'courses.id = enrollments.course_id');
        $builder->orderBy('enrollments.created_at', 'DESC');
        
        $data['enrollments'] = $builder->get()->getResultArray();
        
        return view('admin/enrollments/index', $data);
    }

    public function create()
    {
        $courseModel = new CourseModel();
        $userModel = new UserModel();
        
        $data['courses'] = $courseModel->findAll();
        $data['users']   = $userModel->where('role', 'student')->findAll();
        
        return view('admin/enrollments/create', $data);
    }

    public function store()
    {
        $enrollmentModel = new EnrollmentModel();
        
        $data = [
            'user_id'   => $this->request->getPost('user_id'),
            'course_id' => $this->request->getPost('course_id'),
            'source'    => 'admin',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // check if already enrolled
        if ($enrollmentModel->where(['user_id' => $data['user_id'], 'course_id' => $data['course_id']])->first()) {
            return redirect()->back()->with('error', 'User is already enrolled in this course.');
        }

        if ($enrollmentModel->insert($data)) {
            return redirect()->to('admin/enrollments')->with('success', 'User enrolled successfully.');
        }

        return redirect()->back()->withInput()->with('errors', $enrollmentModel->errors());
    }

    public function delete($id)
    {
        $enrollmentModel = new EnrollmentModel();
        $enrollmentModel->delete($id);
        
        return redirect()->to('admin/enrollments')->with('success', 'Enrollment revoked.');
    }
}
