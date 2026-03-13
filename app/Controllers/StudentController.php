<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\CourseModel;

class StudentController extends BaseController
{
    public function index()
    {
        // For Phase 1, we haven't built the enrollment/purchase system yet.
        // We will just mock it by showing ALL published courses as "My Courses" for demonstration.
        // In Phase 2, this will check an `enrollments` table based on user ID.
        
        $courseModel = new CourseModel();
        $data['my_courses'] = $courseModel->where('status', 'published')
                                          ->orderBy('created_at', 'DESC')
                                          ->findAll();

        return view('student/dashboard', $data);
    }

    public function course($course_id)
    {
        $courseModel = new \App\Models\CourseModel();
        $data['course'] = $courseModel->find($course_id);

        if (!$data['course'] || $data['course']['status'] !== 'published') {
            return redirect()->to('student/dashboard')->with('error', 'Course not found or unavailable.');
        }

        // Fetch Sections and their Lessons
        $sectionModel = new \App\Models\SectionModel();
        $lessonModel = new \App\Models\LessonModel();

        $sections = $sectionModel->where('course_id', $course_id)
                                 ->orderBy('sort_order', 'ASC')
                                 ->findAll();

        foreach ($sections as &$section) {
            $section['lessons'] = $lessonModel->where('section_id', $section['id'])
                                              ->where('status', 'published')
                                              ->orderBy('sort_order', 'ASC')
                                              ->findAll();
        }
        
        $data['sections'] = $sections;

        return view('student/course', $data);
    }

    public function lesson($course_id, $lesson_id)
    {
        $courseModel = new \App\Models\CourseModel();
        $data['course'] = $courseModel->find($course_id);

        if (!$data['course'] || $data['course']['status'] !== 'published') {
            return redirect()->to('student/dashboard')->with('error', 'Course not found.');
        }

        $lessonModel = new \App\Models\LessonModel();
        $data['current_lesson'] = $lessonModel->find($lesson_id);

        if (!$data['current_lesson'] || $data['current_lesson']['status'] !== 'published' || $data['current_lesson']['course_id'] != $course_id) {
            return redirect()->to('student/course/' . $course_id)->with('error', 'Lesson not found.');
        }

        // Fetch sidebar navigation data
        $sectionModel = new \App\Models\SectionModel();
        $sections = $sectionModel->where('course_id', $course_id)
                                 ->orderBy('sort_order', 'ASC')
                                 ->findAll();

        foreach ($sections as &$section) {
            $section['lessons'] = $lessonModel->where('section_id', $section['id'])
                                              ->where('status', 'published')
                                              ->orderBy('sort_order', 'ASC')
                                              ->findAll();
        }
        $data['sections'] = $sections;

        return view('student/lesson', $data);
    }
}
