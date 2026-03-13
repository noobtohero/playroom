<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\LessonModel;
use App\Models\SectionModel;
use App\Models\CourseModel;

class LessonController extends BaseController
{
    public function index($section_id)
    {
        $sectionModel = new SectionModel();
        $data['section'] = $sectionModel->find($section_id);

        if (!$data['section']) {
            return redirect()->to('admin/courses')->with('error', 'Section not found');
        }

        $courseModel = new CourseModel();
        $data['course'] = $courseModel->find($data['section']['course_id']);

        $lessonModel = new LessonModel();
        $data['lessons'] = $lessonModel->where('section_id', $section_id)
                                       ->orderBy('sort_order', 'ASC')
                                       ->findAll();
        
        return view('admin/lessons/index', $data);
    }

    public function create($section_id)
    {
        $sectionModel = new SectionModel();
        $data['section'] = $sectionModel->find($section_id);
        
        if (!$data['section']) {
            return redirect()->to('admin/courses')->with('error', 'Section not found');
        }
        
        $courseModel = new CourseModel();
        $data['course'] = $courseModel->find($data['section']['course_id']);

        return view('admin/lessons/create', $data);
    }

    public function store($section_id)
    {
        $sectionModel = new SectionModel();
        $section = $sectionModel->find($section_id);

        if (!$section) {
            return redirect()->to('admin/courses')->with('error', 'Section not found');
        }

        $lessonModel = new LessonModel();

        if (! $this->validate($lessonModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle File Upload based on Type (Video/Slide/Podcast)
        // For Phase 1 we focus on Video structure (m3u8), but let's allow basic file upload for mock testing
        $contentPath = null;
        $file = $this->request->getFile('content_file');
        
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            // we will store them in writable/uploads/lessons/{course_id}/{section_id}/
            $path = 'uploads/lessons/' . $section['course_id'] . '/' . $section_id . '/';
            $fileName = $file->getClientName(); // preserve name to handle .m3u8 if needed
            // Ensure safe name
            $fileName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $fileName);
            
            // Move file
            $file->move(WRITEPATH . $path, $fileName);
            $contentPath = $path . $fileName;
        }

        $data = [
            'course_id'    => $section['course_id'],
            'section_id'   => $section_id,
            'title'        => $this->request->getPost('title'),
            'type'         => $this->request->getPost('type'),
            'duration'     => $this->request->getPost('duration') ?? 0,
            'sort_order'   => $this->request->getPost('sort_order') ?? 0,
            'status'       => $this->request->getPost('status'),
            'content_path' => $contentPath,
        ];

        $lessonModel->insert($data);

        return redirect()->to('admin/sections/' . $section_id . '/lessons')->with('success', 'Lesson created successfully');
    }

    public function edit($id)
    {
        $lessonModel = new LessonModel();
        $data['lesson'] = $lessonModel->find($id);

        if (!$data['lesson']) {
            return redirect()->to('admin/courses')->with('error', 'Lesson not found');
        }

        $sectionModel = new SectionModel();
        $data['section'] = $sectionModel->find($data['lesson']['section_id']);
        
        $courseModel = new CourseModel();
        $data['course'] = $courseModel->find($data['lesson']['course_id']);

        return view('admin/lessons/edit', $data);
    }

    public function update($id)
    {
        $lessonModel = new LessonModel();
        $lesson = $lessonModel->find($id);

        if (!$lesson) {
            return redirect()->to('admin/courses')->with('error', 'Lesson not found');
        }

        if (! $this->validate($lessonModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'title'        => $this->request->getPost('title'),
            'type'         => $this->request->getPost('type'),
            'duration'     => $this->request->getPost('duration') ?? 0,
            'sort_order'   => $this->request->getPost('sort_order') ?? 0,
            'status'       => $this->request->getPost('status'),
        ];

        // Handle File Upload rewrite
        $file = $this->request->getFile('content_file');
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $path = 'uploads/lessons/' . $lesson['course_id'] . '/' . $lesson['section_id'] . '/';
            $fileName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $file->getClientName());
            
            $file->move(WRITEPATH . $path, $fileName);
            $data['content_path'] = $path . $fileName;

            // Simple delete old file
            if (!empty($lesson['content_path']) && file_exists(WRITEPATH . $lesson['content_path'])) {
                @unlink(WRITEPATH . $lesson['content_path']);
            }
        }

        $lessonModel->update($id, $data);

        return redirect()->to('admin/sections/' . $lesson['section_id'] . '/lessons')->with('success', 'Lesson updated successfully');
    }

    public function delete($id)
    {
        $lessonModel = new LessonModel();
        $lesson = $lessonModel->find($id);

        if ($lesson) {
            if (!empty($lesson['content_path']) && file_exists(WRITEPATH . $lesson['content_path'])) {
                @unlink(WRITEPATH . $lesson['content_path']);
            }
            $lessonModel->delete($id);
            return redirect()->to('admin/sections/' . $lesson['section_id'] . '/lessons')->with('success', 'Lesson deleted successfully');
        }

        return redirect()->back()->with('error', 'Lesson not found');
    }
}
