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
        $data['courses'] = $courseModel->findAll();
        
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
            $thumbnailName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/thumbnails', $thumbnailName);
        }

        $data = [
            'title'       => $this->request->getPost('title'),
            'slug'        => $this->request->getPost('slug'),
            'description' => $this->request->getPost('description'),
            'type'        => $this->request->getPost('type'),
            'price'       => $this->request->getPost('price'),
            'status'      => $this->request->getPost('status'),
            'thumbnail'   => $thumbnailName,
        ];

        $courseModel->insert($data);

        return redirect()->to('admin/courses')->with('success', 'Course created successfully');
    }

    public function edit($id)
    {
        $courseModel = new CourseModel();
        $data['course'] = $courseModel->find($id);

        if (!$data['course']) {
            return redirect()->to('admin/courses')->with('error', 'Course not found');
        }

        return view('admin/courses/edit', $data);
    }

    public function update($id)
    {
        $courseModel = new CourseModel();
        $course = $courseModel->find($id);

        if (!$course) {
            return redirect()->to('admin/courses')->with('error', 'Course not found');
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
            $thumbnailName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/thumbnails', $thumbnailName);
            $data['thumbnail'] = $thumbnailName;
            
            // Delete old file
            if (!empty($course['thumbnail']) && file_exists(FCPATH . 'uploads/thumbnails/' . $course['thumbnail'])) {
                unlink(FCPATH . 'uploads/thumbnails/' . $course['thumbnail']);
            }
        }

        $courseModel->update($id, $data);

        return redirect()->to('admin/courses')->with('success', 'Course updated successfully');
    }

    public function delete($id)
    {
        $courseModel = new CourseModel();
        $course = $courseModel->find($id);

        if ($course) {
            if (!empty($course['thumbnail']) && file_exists(FCPATH . 'uploads/thumbnails/' . $course['thumbnail'])) {
                unlink(FCPATH . 'uploads/thumbnails/' . $course['thumbnail']);
            }
            $courseModel->delete($id);
            return redirect()->to('admin/courses')->with('success', 'Course deleted successfully');
        }

        return redirect()->to('admin/courses')->with('error', 'Course not found');
    }
}
