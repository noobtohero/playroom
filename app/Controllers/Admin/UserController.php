<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class UserController extends BaseController
{
    public function index()
    {
        $userModel = new UserModel();
        $data['users'] = $userModel->orderBy('created_at', 'DESC')->findAll();
        
        return view('admin/users/index', $data);
    }

    public function edit($id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);
        
        if (!$user) {
            return redirect()->to('admin/users')->with('error', 'User not found.');
        }

        // Permission Check: Admin can only manage teachers and students (unless editing self)
        if (session()->get('role') === 'admin' && !in_array($user['role'], ['teacher', 'student']) && $id != session()->get('id')) {
            return redirect()->to('admin/users')->with('error', 'Unauthorized: You can only manage teachers and students.');
        }
        
        $data['user'] = $user;
        return view('admin/users/edit', $data);
    }

    public function update($id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (!$user) {
            return redirect()->to('admin/users')->with('error', 'User not found.');
        }

        // Permission Check: Admin can only manage teachers and students (unless updating self)
        if (session()->get('role') === 'admin' && !in_array($user['role'], ['teacher', 'student']) && $id != session()->get('id')) {
            return redirect()->to('admin/users')->with('error', 'Unauthorized action.');
        }
        
        $data = [
            'name'      => $this->request->getPost('name'),
            'full_name' => $this->request->getPost('full_name'),
            'email'     => $this->request->getPost('email'),
        ];

        // Explicitly set validation rules for this update to avoid issues with unique email or required password
        $rules = [
            'name'  => 'required|min_length[3]|max_length[100]',
            'email' => "required|valid_email|is_unique[users.email,id,{$id}]",
        ];

        if (!empty($this->request->getPost('password'))) {
            $rules['password'] = 'min_length[6]';
            $data['password']  = $this->request->getPost('password');
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Self-Edit Restriction: Cannot change own role or status
        if ($id != session()->get('id')) {
            $data['role'] = $this->request->getPost('role');
            $data['status'] = $this->request->getPost('status');

            // Restriction: Nobody can assign the 'super-admin' role via this method
            // Admin Limitation: Cannot promote to admin
            if (in_array($data['role'], ['super-admin']) || (session()->get('role') === 'admin' && $data['role'] === 'admin')) {
                return redirect()->back()->with('error', 'Unauthorized: You cannot assign this role.');
            }
        }

        // Bypass model validation since we validated in controller
        if ($userModel->skipValidation(true)->update($id, $data)) {
            return redirect()->to('admin/users')->with('success', 'User updated successfully.');
        }

        return redirect()->back()->withInput()->with('errors', ['Update failed.']);
    }

    public function delete($id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (!$user) {
            return redirect()->to('admin/users')->with('error', 'User not found.');
        }
        
        // Prevent deleting self
        if ($id == session()->get('id')) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        // Permission Check: Admin can only manage teachers and students
        if (session()->get('role') === 'admin' && !in_array($user['role'], ['teacher', 'student'])) {
            return redirect()->to('admin/users')->with('error', 'Unauthorized: You cannot delete this user.');
        }

        $userModel->delete($id);
        return redirect()->to('admin/users')->with('success', 'User deleted.');
    }
}
