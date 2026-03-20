<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RedeemCodeModel;
use App\Models\CourseModel;

class CodeController extends BaseController
{
    public function index()
    {
        $codeModel = new RedeemCodeModel();
        // Join with courses to show course title
        $db = \Config\Database::connect();
        $builder = $db->table('redeem_codes');
        $builder->select('redeem_codes.*, courses.title as course_title');
        $builder->join('courses', 'courses.id = redeem_codes.course_id');
        $builder->orderBy('redeem_codes.created_at', 'DESC');
        
        $data['codes'] = $builder->get()->getResultArray();
        
        return view('admin/codes/index', $data);
    }

    public function create()
    {
        $courseModel = new CourseModel();
        $data['courses'] = $courseModel->findAll();
        
        return view('admin/codes/create', $data);
    }

    public function store()
    {
        $codeModel = new RedeemCodeModel();
        
        $data = [
            'code'       => strtoupper($this->request->getPost('code')),
            'course_id'  => $this->request->getPost('course_id'),
            'max_use'    => $this->request->getPost('max_use') ?: 1,
            'expire_at'  => $this->request->getPost('expire_at'),
            'status'     => 'active',
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($codeModel->insert($data)) {
            return redirect()->to('admin/codes')->with('success', 'Redeem code created successfully.');
        }

        return redirect()->back()->withInput()->with('errors', $codeModel->errors());
    }

    public function delete($id)
    {
        $codeModel = new RedeemCodeModel();
        $codeModel->delete($id);
        
        return redirect()->to('admin/codes')->with('success', 'Redeem code deleted.');
    }
}
