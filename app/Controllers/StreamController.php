<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class StreamController extends BaseController
{
    /**
     * Serves media files from the writable directory.
     * Path pattern: stream/video/(:num)/(:num)/(:any)
     */
    public function video($courseId, $sectionId, $filename)
    {
        // Basic Security: User must be logged in
        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(403)->setBody('Unauthorized');
        }

        // In Phase 2, check if user actually purchased this course
        
        $filePath = WRITEPATH . 'uploads/lessons/' . $courseId . '/' . $sectionId . '/' . $filename;

        if (!file_exists($filePath)) {
            return $this->response->setStatusCode(404)->setBody('File not found');
        }

        $mimeType = 'video/mp4'; 
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        if ($extension === 'm3u8') {
            $mimeType = 'application/vnd.apple.mpegurl';
        } elseif ($extension === 'ts') {
            $mimeType = 'video/MP2T';
        } elseif ($extension === 'pdf') {
            $mimeType = 'application/pdf';
        } elseif ($extension === 'mp3') {
            $mimeType = 'audio/mpeg';
        }

        return $this->response
            ->setHeader('Content-Type', $mimeType)
            ->setBody(file_get_contents($filePath));
    }
}
