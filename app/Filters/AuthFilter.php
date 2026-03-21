<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        helper('url');
        // 1. Signature Check (Automatic Bypass for Signed URLs)
        $expires   = $request->getGet('expires');
        $signature = $request->getGet('signature');
        if ($expires && $signature) {
            $currentUrl = current_url();
            $isVerified = \App\Helpers\UrlSignerHelper::verify($currentUrl, (string)$signature, (int)$expires);
            if ($isVerified) {
                return; // Access Granted via Signature
            }
        }

        // 2. Standard Session Check
        if (! session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Please login to access this area.');
        }

        // 3. Account Sharing Protection
        $userId = session()->get('id');
        $loginToken = session()->get('login_token');
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);

        if (!$user || $user['last_session_id'] !== $loginToken) {
            session()->destroy();
            return redirect()->to('login')->with('error', 'You have been logged out because another device logged in with your account.');
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
