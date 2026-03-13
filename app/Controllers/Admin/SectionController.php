<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\SectionModel;
use App\Models\CourseModel;

class SectionController extends BaseController
{
    public function index($course_id)
    {
        $courseModel = new CourseModel();
        $data['course'] = $courseModel->find($course_id);

        if (!$data['course']) {
            return redirect()->to('admin/courses')->with('error', 'Course not found');
        }

        $sectionModel = new SectionModel();
        // Get all sections for this course, ordered by sort_order
        $data['sections'] = $sectionModel->where('course_id', $course_id)
                                         ->orderBy('sort_order', 'ASC')
                                         ->findAll();
        
        return view('admin/sections/index', $data);
    }

    public function store($course_id)
    {
        $sectionModel = new SectionModel();
        $data = [
            'course_id'  => $course_id,
            'title'      => $this->request->getPost('title'),
            'sort_order' => $this->request->getPost('sort_order') ?? 0,
        ];

        if (! $sectionModel->insert($data)) {
            return redirect()->back()->withInput()->with('errors', $sectionModel->errors());
        }

        return redirect()->to('admin/courses/' . $course_id . '/sections')->with('success', 'Section added successfully');
    }

    public function update($id)
    {
        $sectionModel = new SectionModel();
        $section = $sectionModel->find($id);

        if (!$section) {
            return redirect()->back()->with('error', 'Section not found');
        }

        $data = [
            'title'      => $this->request->getPost('title'),
            'sort_order' => $this->request->getPost('sort_order'),
        ];

        if (! $sectionModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('errors', $sectionModel->errors());
        }

        return redirect()->to('admin/courses/' . $section['course_id'] . '/sections')->with('success', 'Section updated successfully');
    }

    public function delete($id)
    {
        $sectionModel = new SectionModel();
        $section = $sectionModel->find($id);

        if ($section) {
            $sectionModel->delete($id);
            return redirect()->to('admin/courses/' . $section['course_id'] . '/sections')->with('success', 'Section deleted successfully');
        }

        return redirect()->back()->with('error', 'Section not found');
    }
}
