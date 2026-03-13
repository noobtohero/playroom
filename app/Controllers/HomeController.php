<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\CourseModel;

class HomeController extends BaseController
{
    public function index()
    {
        $courseModel = new CourseModel();
        // Fetch only published courses
        $data['courses'] = $courseModel->where('status', 'published')
                                       ->orderBy('created_at', 'DESC')
                                       ->findAll();
        
        return view('frontend/home', $data);
    }

    public function course($slug)
    {
        $courseModel = new CourseModel();
        $data['course'] = $courseModel->where('slug', $slug)
                                      ->where('status', 'published')
                                      ->first();

        if (!$data['course']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('frontend/course_details', $data);
    }
}
