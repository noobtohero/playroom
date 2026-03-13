<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class AuthController extends BaseController
{
    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to($this->getDashboardRoute(session()->get('role')));
        }
        return view('auth/login');
    }

    public function loginAttempt()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[5]'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('email', $this->request->getPost('email'))->first();

        if (! $user || ! password_verify($this->request->getPost('password'), $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Invalid email or password');
        }

        if ($user['status'] !== 'active') {
            return redirect()->back()->with('error', 'Your account is currently inactive.');
        }

        $this->setUserSession($user);
        return redirect()->to($this->getDashboardRoute($user['role']));
    }

    public function register()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to($this->getDashboardRoute(session()->get('role')));
        }
        return view('auth/register');
    }

    public function registerAttempt()
    {
        $rules = [
            'name'             => 'required|min_length[3]|max_length[100]',
            'email'            => 'required|valid_email|is_unique[users.email]',
            'password'         => 'required|min_length[6]',
            'password_confirm' => 'matches[password]'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new \App\Models\UserModel();
        // create normal student
        $data = [
            'name'     => $this->request->getPost('name'),
            'email'    => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'), // model will hash this
            'role'     => 'student',
            'status'   => 'active'
        ];
        
        // Remove 'password' from data to prevent the model from hashing it again if we manually hashed it.
        // But our UserModel beforeInsert hook already handles hashing. So passing plain password is correct.
        
        $userModel->insert($data);

        return redirect()->to('login')->with('success', 'Registration successful. You can now login.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('login');
    }

    private function setUserSession($user)
    {
        $data = [
            'id'         => $user['id'],
            'name'       => $user['name'],
            'email'      => $user['email'],
            'role'       => $user['role'],
            'isLoggedIn' => true,
        ];

        session()->set($data);
        return true;
    }

    private function getDashboardRoute($role)
    {
        if ($role === 'admin') {
            return 'admin/dashboard';
        }
        return 'student/dashboard';
    }
}
