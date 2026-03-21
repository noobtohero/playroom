<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class StreamController extends BaseController
{
    /**
     * Serves media files (m3u8, ts, pdf, mp3) from writable/uploads/lessons/.
     * Path pattern: media/stream/video/(:num)/(:num)/(.+)
     */
    public function video($courseId, $sectionId, ...$segments)
    {
        // 1. Signature Check
        $expires   = $this->request->getGet('expires');
        $signature = $this->request->getGet('signature');
        
        // Base URL for verification (strip query params)
        $currentUrl = current_url();
        $isVerified = \App\Helpers\UrlSignerHelper::verify($currentUrl, (string)$signature, (int)$expires);

        // OPTIONAL: We can allow TS files without signature if the master was signed 
        // OR we can require it for everything. 
        // For HLS to work easily, we often only sign the manifest.
        $filename = implode('/', $segments);
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        // 2. Authorization Logic
        // If the URL is signed and verified, we trust it (authorized by a view that has access)
        // If not verified, we check for a valid session and specific permissions.
        if (! $isVerified) {
            if (!session()->get('isLoggedIn')) {
                return $this->response->setStatusCode(401)->setBody('Unauthorized: Please login or provide a valid signature.');
            }

            $userId = session()->get('id');
            $role   = session()->get('role');

            // Access Control: Students must be enrolled
            if (! in_array($role, ['super-admin', 'admin', 'teacher'])) {
                $enrollmentModel = new \App\Models\EnrollmentModel();
                if (! $enrollmentModel->isEnrolled($userId, $courseId)) {
                    return $this->response->setStatusCode(403)->setBody('Access Denied: You are not enrolled in this course.');
                }
            }
        }

        $filename = implode('/', $segments);
        // Prevent directory traversal
        if (strpos($filename, '..') !== false || strpos($filename, './') !== false) {
             return $this->response->setStatusCode(400)->setBody('Invalid filename');
        }

        $filePath = WRITEPATH . 'uploads/lessons/' . $courseId . '/' . $sectionId . '/' . $filename;

        if (!file_exists($filePath)) {
            return $this->response->setStatusCode(404)->setBody('File not found: ' . $filename);
        }

        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $mimeType  = match($extension) {
            'm3u8'             => 'application/vnd.apple.mpegurl',
            'ts'               => 'video/MP2T',
            'pdf'              => 'application/pdf',
            'mp3'              => 'audio/mpeg',
            'key', 'bin'       => 'application/octet-stream',
            default            => 'video/mp4',
        };

        $file = new \CodeIgniter\Files\File($filePath);
        
        return $this->response
            ->setHeader('Content-Type', $file->getMimeType())
            ->setHeader('Cache-Control', 'no-store, no-cache')
            ->setBody(file_get_contents($filePath));
    }

    /**
     * API Endpoint: Serves HLS encryption key files from writable/uploads/hls_keys/.
     * Path pattern: media/stream/key/(:any)
     * Requires login — prevents unauthorized key download.
     */
    public function key($filename)
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(403)->setBody('Unauthorized');
        }

        // Prevent directory traversal
        $filename = basename(str_replace(['../', './'], '', $filename));

        $keyPath = WRITEPATH . 'uploads/hls_keys/' . $filename;

        if (!file_exists($keyPath)) {
            return $this->response->setStatusCode(404)->setBody('Key not found: ' . $filename);
        }

        return $this->response
            ->setHeader('Content-Type', 'application/octet-stream')
            ->setHeader('Cache-Control', 'no-store, no-cache')
            ->setBody(file_get_contents($keyPath));
    }

    /**
     * Downloads a file if allowed.
     */
    public function download($courseId, $sectionId, ...$segments)
    {
        // 1. Signature Check First
        $expires   = $this->request->getGet('expires');
        $signature = $this->request->getGet('signature');
        $currentUrl = current_url();
        $isVerified = \App\Helpers\UrlSignerHelper::verify($currentUrl, (string)$signature, (int)$expires);

        // 2. Authorization
        if (!$isVerified) {
            if (!session()->get('isLoggedIn')) {
                return redirect()->to('login')->with('error', 'Please login or use a valid download link.');
            }

            $userId = session()->get('id');
            $role   = session()->get('role');

            // Access Control
            if (! in_array($role, ['super-admin', 'admin', 'teacher'])) {
                $enrollmentModel = new \App\Models\EnrollmentModel();
                if (! $enrollmentModel->isEnrolled($userId, $courseId)) {
                    return $this->response->setStatusCode(403)->setBody('Access Denied.');
                }
            }
        }

        $filename = implode('/', $segments);
        $filePath = WRITEPATH . 'uploads/lessons/' . $courseId . '/' . $sectionId . '/' . $filename;

        if (!file_exists($filePath)) {
            return $this->response->setStatusCode(404)->setBody('File not found.');
        }

        // Check if lesson is actually downloadable
        $lessonModel = new \App\Models\LessonModel();
        $lesson = $lessonModel->where([
            'course_id' => $courseId,
            'section_id' => $sectionId,
            'content_path LIKE' => '%'.$filename.'%'
        ])->first();

        if (!$lesson || !$lesson['is_downloadable']) {
             return $this->response->setStatusCode(403)->setBody('This file is not authorized for download.');
        }

        return $this->response->download($filePath, null);
    }
}

