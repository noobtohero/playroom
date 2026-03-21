<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\CourseModel;

class StudentController extends BaseController
{
    public function index()
    {
        $userId = session()->get('id');
        
        $db = \Config\Database::connect();
        $builder = $db->table('enrollments');
        $builder->select('courses.*');
        $builder->join('courses', 'courses.id = enrollments.course_id');
        $builder->where('enrollments.user_id', $userId);
        $builder->where('courses.status', 'published');
        $builder->orderBy('enrollments.created_at', 'DESC');
        
        $my_courses = $builder->get()->getResultArray();
        
        $progressModel = new \App\Models\LessonProgressModel();
        $lessonModel = new \App\Models\LessonModel();

        foreach ($my_courses as &$course) {
            $course['progress'] = $progressModel->getCourseProgress($userId, (int)$course['id']);
            $course['total_lessons'] = $lessonModel->where('course_id', $course['id'])->where('status', 'published')->countAllResults();
            $course['completed_lessons'] = $progressModel->where('user_id', $userId)->where('course_id', $course['id'])->where('is_completed', 1)->countAllResults();
        }

        $data['my_courses'] = $my_courses;

        // Fetch user trophies
        $db2 = \Config\Database::connect();
        $data['user_trophies'] = $db2->table('user_trophies')
                                     ->select('trophies.name, trophies.description, trophies.icon, user_trophies.earned_at')
                                     ->join('trophies', 'trophies.id = user_trophies.trophy_id')
                                     ->where('user_trophies.user_id', $userId)
                                     ->orderBy('user_trophies.earned_at', 'DESC')
                                     ->get()
                                     ->getResultArray();

        return view('student/dashboard', $data);
    }

    public function redeem()
    {
        return view('student/redeem');
    }

    public function redeemAttempt()
    {
        $codeString = strtoupper($this->request->getPost('code'));
        $userId     = session()->get('id');

        $codeModel       = new \App\Models\RedeemCodeModel();
        $enrollmentModel = new \App\Models\EnrollmentModel();

        $code = $codeModel->where('code', $codeString)->first();

        if (!$code) {
            return redirect()->back()->with('error', 'Invalid redeem code.');
        }

        if ($code['status'] !== 'active' || ($code['expire_at'] && $code['expire_at'] < date('Y-m-d H:i:s'))) {
            return redirect()->back()->with('error', 'This code has expired or is inactive.');
        }

        if ($code['used_count'] >= $code['max_use']) {
            return redirect()->back()->with('error', 'This code has reached its maximum usage limit.');
        }

        // Check if already enrolled
        if ($enrollmentModel->isEnrolled($userId, $code['course_id'])) {
            return redirect()->to('student/dashboard')->with('info', 'You are already enrolled in this course.');
        }

        // Process Redemption
        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Create Enrollment
        $enrollmentModel->insert([
            'user_id'   => $userId,
            'course_id' => $code['course_id'],
            'source'    => 'code',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // 2. Increment usage count
        $codeModel->update($code['id'], [
            'used_count' => $code['used_count'] + 1
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Redemption failed. Please try again.');
        }

        return redirect()->to('student/dashboard')->with('success', 'Course redeemed successfully! Happy learning.');
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

        $progressModel = new \App\Models\LessonProgressModel();
        $completedLessons = $progressModel->where('user_id', session()->get('id'))
                                          ->where('course_id', $course_id)
                                          ->where('is_completed', 1)
                                          ->findColumn('lesson_id') ?? [];

        foreach ($sections as &$section) {
            $section['lessons'] = $lessonModel->where('section_id', $section['id'])
                                              ->where('status', 'published')
                                              ->orderBy('sort_order', 'ASC')
                                              ->findAll();
            
            foreach ($section['lessons'] as &$lesson) {
                $lesson['is_completed'] = in_array($lesson['id'], $completedLessons);
            }
        }
        
        $data['sections'] = $sections;
        $data['course_progress'] = $progressModel->getCourseProgress(session()->get('id'), (int)$course_id);

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
        $data['isViewer'] = true;

        if (!$data['current_lesson'] || $data['current_lesson']['status'] !== 'published' || $data['current_lesson']['course_id'] != $course_id) {
            return redirect()->to('student/course/' . $course_id)->with('error', 'Lesson not found.');
        }

        // Fetch sidebar navigation data
        $sectionModel = new \App\Models\SectionModel();
        $sections = $sectionModel->where('course_id', $course_id)
                                 ->orderBy('sort_order', 'ASC')
                                 ->findAll();

        $progressModel = new \App\Models\LessonProgressModel();
        $bookmarkModel = new \App\Models\VideoBookmarkModel();

        $completedLessons = $progressModel->where('user_id', session()->get('id'))
                                          ->where('course_id', $course_id)
                                          ->where('is_completed', 1)
                                          ->findColumn('lesson_id') ?? [];

        $bookmark = $bookmarkModel->where('user_id', session()->get('id'))
                                  ->where('lesson_id', $lesson_id)
                                  ->first();
        $data['last_watched_time'] = $bookmark ? $bookmark['last_time'] : 0;

        $notesModel = new \App\Models\UserNotesModel();
        $userNote = $notesModel->where('user_id', session()->get('id'))->where('lesson_id', $lesson_id)->first();
        $data['user_note'] = $userNote ? $userNote['content'] : '';

        foreach ($sections as &$section) {
            $section['lessons'] = $lessonModel->where('section_id', $section['id'])
                                              ->where('status', 'published')
                                              ->orderBy('sort_order', 'ASC')
                                              ->findAll();
            
            foreach ($section['lessons'] as &$lesson) {
                $lesson['is_completed'] = in_array($lesson['id'], $completedLessons);
            }
        }
        $data['sections'] = $sections;

        return view('student/lesson', $data);
    }

    public function certificate($course_id)
    {
        $userId = session()->get('id');
        $courseModel = new \App\Models\CourseModel();
        $course = $courseModel->find($course_id);

        if (!$course) {
            return redirect()->to('student/dashboard')->with('error', 'Course not found.');
        }

        $progressModel = new \App\Models\LessonProgressModel();
        $progress = $progressModel->getCourseProgress($userId, (int)$course_id);

        if ($progress < 100) {
            return redirect()->to('student/course/' . $course_id)->with('error', 'You must complete all lessons to earn a certificate.');
        }

        $userModel = new \App\Models\UserModel();
        $data['user'] = $userModel->find($userId);
        $data['course'] = $course;
        $data['completed_at'] = date('F j, Y');

        return view('student/certificate', $data);
    }
}
