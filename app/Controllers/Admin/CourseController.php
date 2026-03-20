<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use \App\Models\CourseModel;

class CourseController extends BaseController
{
    public function index()
    {
        $courseModel = new CourseModel();
        
        // If teacher, show only their courses
        if (session()->get('role') === 'teacher') {
            $data['courses'] = $courseModel->where('author_id', session()->get('id'))->findAll();
        } else {
            $data['courses'] = $courseModel->findAll();
        }
        
        return view('admin/courses/index', $data);
    }

    public function create()
    {
        return view('admin/courses/create');
    }

    public function store()
    {
        $courseModel = new CourseModel();
        
        $rules = $courseModel->getValidationRules();

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle File Upload
        $thumbnailName = null;
        $file = $this->request->getFile('thumbnail');
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $uploadDir = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'thumbnails' . DIRECTORY_SEPARATOR;
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $thumbnailName = $file->getRandomName();
            $file->move($uploadDir, $thumbnailName);
        } elseif ($file && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            log_message('error', 'Thumbnail Upload Error: ' . $file->getErrorString() . ' (' . $file->getError() . ')');
        }

        $data = [
            'title'       => $this->request->getPost('title'),
            'slug'        => $this->request->getPost('slug'),
            'description' => $this->request->getPost('description'),
            'type'        => $this->request->getPost('type'),
            'price'       => $this->request->getPost('price'),
            'status'      => $this->request->getPost('status'),
            'thumbnail'   => $thumbnailName,
            'author_id'   => session()->get('id'), // Set author as current user
        ];

        $courseModel->insert($data);

        return redirect()->to('admin/courses')->with('success', 'Course created successfully');
    }

    public function edit($id)
    {
        $courseModel = new CourseModel();
        $course = $courseModel->find($id);

        if (!$course) {
            return redirect()->to('admin/courses')->with('error', 'Course not found');
        }

        // Ownership Check for Teachers
        if (session()->get('role') === 'teacher' && $course['author_id'] != session()->get('id')) {
            return redirect()->to('admin/courses')->with('error', 'Unauthorized: You do not own this course');
        }

        $data['course'] = $course;
        return view('admin/courses/edit', $data);
    }

    public function update($id)
    {
        $courseModel = new CourseModel();
        $course = $courseModel->find($id);

        if (!$course) {
            return redirect()->to('admin/courses')->with('error', 'Course not found');
        }

        // Ownership Check for Teachers
        if (session()->get('role') === 'teacher' && $course['author_id'] != session()->get('id')) {
            return redirect()->to('admin/courses')->with('error', 'Unauthorized: You do not own this course');
        }

        // Adjust slug validation rule for updates
        $rules = $courseModel->getValidationRules();
        $rules['slug'] = 'required|alpha_dash|is_unique[courses.slug,id,' . $id . ']';

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'title'       => $this->request->getPost('title'),
            'slug'        => $this->request->getPost('slug'),
            'description' => $this->request->getPost('description'),
            'type'        => $this->request->getPost('type'),
            'price'       => $this->request->getPost('price'),
            'status'      => $this->request->getPost('status'),
        ];

        // Handle File Upload
        $file = $this->request->getFile('thumbnail');
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $uploadDir = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'thumbnails' . DIRECTORY_SEPARATOR;
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $thumbnailName = $file->getRandomName();
            $file->move($uploadDir, $thumbnailName);
            $data['thumbnail'] = $thumbnailName;
            
            // Delete old file
            if (!empty($course['thumbnail']) && file_exists($uploadDir . $course['thumbnail'])) {
                @unlink($uploadDir . $course['thumbnail']);
            }
        } elseif ($file && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            log_message('error', 'Thumbnail Update Error: ' . $file->getErrorString() . ' (' . $file->getError() . ')');
        }

        $courseModel->update($id, $data);

        return redirect()->to('admin/courses')->with('success', 'Course updated successfully');
    }

    public function delete($id)
    {
        $courseModel = new CourseModel();
        $course = $courseModel->find($id);

        if (!$course) {
            return redirect()->to('admin/courses')->with('error', 'Course not found');
        }

        // Ownership Check for Teachers
        if (session()->get('role') === 'teacher' && $course['author_id'] != session()->get('id')) {
            return redirect()->to('admin/courses')->with('error', 'Unauthorized: You do not own this course');
        }

        if ($course) {
            try {
                $uploadDir = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'thumbnails' . DIRECTORY_SEPARATOR;
                if (!empty($course['thumbnail']) && file_exists($uploadDir . $course['thumbnail'])) {
                    @unlink($uploadDir . $course['thumbnail']);
                }
                
                if ($courseModel->delete($id)) {
                    return redirect()->to('admin/courses')->with('success', 'Course deleted successfully');
                } else {
                    return redirect()->to('admin/courses')->with('error', 'Failed to delete course. Please check if there are linked records.');
                }
            } catch (\Exception $e) {
                log_message('error', 'Course Deletion Exception: ' . $e->getMessage());
                return redirect()->to('admin/courses')->with('error', 'Database Error: ' . $e->getMessage());
            }
        }

        return redirect()->to('admin/courses')->with('error', 'Course not found');
    }
}
