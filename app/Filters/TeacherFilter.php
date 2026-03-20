<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class TeacherFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $role = session()->get('role');
        // Teachers, Admins, and Super-admins can access teacher tools
        if (! in_array($role, ['super-admin', 'admin', 'teacher'])) {
            return service('response')->setStatusCode(403)->setBody('Access Denied: Teacher privileges required.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
