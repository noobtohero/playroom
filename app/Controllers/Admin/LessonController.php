<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\LessonModel;
use App\Models\SectionModel;
use App\Models\CourseModel;

class LessonController extends BaseController
{
    private function checkCourseOwnership($courseId)
    {
        if (session()->get('role') === 'teacher') {
            $courseModel = new CourseModel();
            $course = $courseModel->find($courseId);
            if (!$course || $course['author_id'] != session()->get('id')) {
                return false;
            }
        }
        return true;
    }

    public function index($section_id)
    {
        $sectionModel = new SectionModel();
        $data['section'] = $sectionModel->find($section_id);

        if (!$data['section']) {
            return redirect()->to('admin/courses')->with('error', 'Section not found');
        }

        $courseModel = new CourseModel();
        $course = $courseModel->find($data['section']['course_id']);
        
        if (!$this->checkCourseOwnership($data['section']['course_id'])) {
            return redirect()->to('admin/courses')->with('error', 'Unauthorized: You do not own this course');
        }

        $data['course'] = $course;

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

        if (!$this->checkCourseOwnership($data['section']['course_id'])) {
            return redirect()->to('admin/courses')->with('error', 'Unauthorized: You do not own this course');
        }

        return view('admin/lessons/create', $data);
    }

    public function store($section_id)
    {
        $sectionModel = new SectionModel();
        $section = $sectionModel->find($section_id);

        if (!$section) {
            return redirect()->to('admin/courses')->with('error', 'Section not found');
        }

        if (!$this->checkCourseOwnership($section['course_id'])) {
            return redirect()->to('admin/courses')->with('error', 'Unauthorized: You do not own this course');
        }

        $lessonModel = new LessonModel();

        // ── Pre-detect Type for Validation ───────────────────────────
        $type = $this->request->getPost('type');
        $file = $this->request->getFile('content_file');
        $externalUrl = $this->request->getPost('external_url');

        if (empty($type)) {
            if (!empty($externalUrl)) {
                $type = 'video'; // Default for external URL
            } elseif ($file && $file->isValid()) {
                $type = $this->_detectType($file->getExtension());
            } else {
                // Fallback for draft/empty lesson if needed, but usually we need something
                $type = 'video'; 
            }
        }

        $rules = $lessonModel->getValidationRules();
        $validationData = array_merge($this->request->getPost(), ['type' => $type]);

        if (! $this->validateData($validationData, $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $contentPath = null;
        $detectedType = $type; 

        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $destPath = 'uploads/lessons/' . $section['course_id'] . '/' . $section_id . '/';
            $fullPath = WRITEPATH . $destPath;

            if (!is_dir($fullPath)) {
                mkdir($fullPath, 0777, true);
            }

            $extension = strtolower($file->getExtension());
            $fileName  = $file->getClientName();

            if ($extension === 'zip') {
                $m3u8File = $this->_extractZipAndProcessKeys($file->getTempName(), $fullPath, $destPath);
                $contentPath  = $m3u8File ?? $destPath . $fileName;
                $detectedType = 'video';
            } else {
                $fileName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $fileName);
                $file->move($fullPath, $fileName);
                $contentPath  = $destPath . $fileName;
                $detectedType = $this->_detectType($extension);
            }
        }

        // Separate key_file upload (standalone) — goes directly to hls_keys/
        $keyFile = $this->request->getFile('key_file');
        if ($keyFile && $keyFile->isValid() && ! $keyFile->hasMoved()) {
            $keyDestPath = WRITEPATH . 'uploads/hls_keys/';
            if (!is_dir($keyDestPath)) {
                mkdir($keyDestPath, 0777, true);
            }
            $keyFileName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $keyFile->getClientName());
            $keyFile->move($keyDestPath, $keyFileName);
        }

        $data = [
            'course_id'       => $section['course_id'],
            'section_id'      => $section_id,
            'title'           => $this->request->getPost('title'),
            'type'            => $detectedType, 
            'duration'        => $this->request->getPost('duration') ?? 0,
            'sort_order'      => $this->request->getPost('sort_order') ?? 0,
            'status'          => $this->request->getPost('status'),
            'content_path'    => $contentPath,
            'external_url'    => $this->request->getPost('external_url'),
            'is_downloadable' => $this->request->getPost('is_downloadable') ? 1 : 0,
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

        if (!$this->checkCourseOwnership($data['lesson']['course_id'])) {
            return redirect()->to('admin/courses')->with('error', 'Unauthorized: You do not own this course');
        }

        return view('admin/lessons/edit', $data);
    }

    public function update($id)
    {
        $lessonModel = new LessonModel();
        $lesson = $lessonModel->find($id);

        if (!$lesson) {
            return redirect()->to('admin/courses')->with('error', 'Lesson not found');
        }

        if (!$this->checkCourseOwnership($lesson['course_id'])) {
            return redirect()->to('admin/courses')->with('error', 'Unauthorized: You do not own this course');
        }

        $type = $this->request->getPost('type');
        $file = $this->request->getFile('content_file');
        $externalUrl = $this->request->getPost('external_url');

        if (empty($type)) {
            if (!empty($externalUrl)) {
                 $type = 'video';
            } elseif ($file && $file->isValid()) {
                 $type = $this->_detectType($file->getExtension());
            } else {
                 $type = $lesson['type']; // keep existing
            }
        }

        $rules = $lessonModel->getValidationRules();
        $validationData = array_merge($this->request->getPost(), ['type' => $type]);

        if (! $this->validateData($validationData, $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'title'           => $this->request->getPost('title'),
            'type'            => $type,
            'duration'        => $this->request->getPost('duration') ?? 0,
            'sort_order'      => $this->request->getPost('sort_order') ?? 0,
            'status'          => $this->request->getPost('status'),
            'external_url'    => $this->request->getPost('external_url'),
            'is_downloadable' => $this->request->getPost('is_downloadable') ? 1 : 0,
        ];

        // Handle File Upload rewrite
        $file = $this->request->getFile('content_file');
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $destPath = 'uploads/lessons/' . $lesson['course_id'] . '/' . $lesson['section_id'] . '/';
            $fullPath = WRITEPATH . $destPath;

            if (!is_dir($fullPath)) {
                mkdir($fullPath, 0777, true);
            }

            $extension = strtolower($file->getExtension());
            $fileName  = $file->getClientName();

            if ($extension === 'zip') {
                $m3u8File = $this->_extractZipAndProcessKeys($file->getTempName(), $fullPath, $destPath);
                if ($m3u8File) {
                    $data['content_path'] = $m3u8File;
                }
                $data['type'] = 'video';
            } else {
                $fileName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $fileName);
                $file->move($fullPath, $fileName);
                $data['content_path'] = $destPath . $fileName;
                $data['type']         = $this->_detectType($extension);
            }

            // Delete old file
            if (!empty($lesson['content_path']) && file_exists(WRITEPATH . $lesson['content_path'])) {
                @unlink(WRITEPATH . $lesson['content_path']);
            }
        }

        // Separate key_file upload — goes to hls_keys/
        $keyFile = $this->request->getFile('key_file');
        if ($keyFile && $keyFile->isValid() && ! $keyFile->hasMoved()) {
            $keyDestPath = WRITEPATH . 'uploads/hls_keys/';
            if (!is_dir($keyDestPath)) {
                mkdir($keyDestPath, 0777, true);
            }
            $keyFileName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $keyFile->getClientName());
            $keyFile->move($keyDestPath, $keyFileName);
        }

        $lessonModel->update($id, $data);

        return redirect()->to('admin/sections/' . $lesson['section_id'] . '/lessons')->with('success', 'Lesson updated successfully');
    }

    public function delete($id)
    {
        $lessonModel = new LessonModel();
        $lesson = $lessonModel->find($id);

        if (!$lesson) {
             return redirect()->back()->with('error', 'Lesson not found');
        }

        if (!$this->checkCourseOwnership($lesson['course_id'])) {
            return redirect()->to('admin/courses')->with('error', 'Unauthorized: You do not own this course');
        }

        if ($lesson) {
            if (!empty($lesson['content_path']) && file_exists(WRITEPATH . $lesson['content_path'])) {
                @unlink(WRITEPATH . $lesson['content_path']);
            }
            $lessonModel->delete($id);
            return redirect()->to('admin/sections/' . $lesson['section_id'] . '/lessons')->with('success', 'Lesson deleted successfully');
        }

        return redirect()->back()->with('error', 'Lesson not found');
    }

    // ─────────────────────────────────────────────────────────────────
    // Private Helpers
    // ─────────────────────────────────────────────────────────────────

    /**
     * Auto-detect lesson type from file extension.
     */
    private function _detectType(string $ext): string
    {
        return match($ext) {
            'zip', 'm3u8', 'mp4', 'ts'      => 'video',
            'pdf'                            => 'slide',
            'mp3', 'm4a', 'aac', 'ogg'      => 'podcast',
            'md', 'markdown', 'txt'         => 'markdown',
            default                          => 'video',
        };
    }


    /**
     * Extracts a ZIP file containing HLS content.
     * - Moves all *.key / *.bin files to WRITEPATH/uploads/hls_keys/
     * - Rewrites EXT-X-KEY URI in every *.m3u8 to point to the API endpoint
     * - Returns the relative path of the found master/playlist .m3u8, or null
     */
    private function _extractZipAndProcessKeys(string $zipTempPath, string $fullDestPath, string $relDestPath): ?string
    {
        $zip = new \ZipArchive();
        if ($zip->open($zipTempPath) !== true) {
            return null;
        }

        $zip->extractTo($fullDestPath);

        $m3u8File    = null;
        $keyFiles    = [];  // relative paths inside zip (e.g. "1080p/encrypt.key")

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entry     = $zip->getNameIndex($i);
            $entryName = basename($entry);
            $ext       = strtolower(pathinfo($entry, PATHINFO_EXTENSION));

            // Collect key/bin files
            if (in_array($ext, ['key', 'bin'])) {
                $keyFiles[] = $entry;
            }

            // Pick master.m3u8 first, fallback to any .m3u8
            if ($entryName === 'master.m3u8' && !$m3u8File) {
                $m3u8File = $entry;
            }
        }

        // Fallback: find any .m3u8 if no master.m3u8
        if (!$m3u8File) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entry = $zip->getNameIndex($i);
                if (strtolower(pathinfo($entry, PATHINFO_EXTENSION)) === 'm3u8') {
                    $m3u8File = $entry;
                    break;
                }
            }
        }

        $zip->close();

        // ── Move key files to hls_keys/ ──────────────────────────────
        $hlsKeysPath = WRITEPATH . 'uploads/hls_keys/';
        if (!is_dir($hlsKeysPath)) {
            mkdir($hlsKeysPath, 0777, true);
        }

        $movedKeys = [];  // map: original basename → new safe filename
        foreach ($keyFiles as $keyEntry) {
            $keyBaseName    = basename($keyEntry);
            $safeKeyName    = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $keyBaseName);
            $srcPath        = $fullDestPath . $keyEntry;

            if (file_exists($srcPath)) {
                rename($srcPath, $hlsKeysPath . $safeKeyName);
                $movedKeys[$keyBaseName] = $safeKeyName;
            }
        }

        // ── Rewrite EXT-X-KEY URI in all .m3u8 files ─────────────────
        $keyApiBase = base_url('media/stream/key/');
        $this->_rewriteM3u8Keys($fullDestPath, $movedKeys, $keyApiBase);

        return $m3u8File ? ($relDestPath . $m3u8File) : null;
    }

    /**
     * Recursively finds all .m3u8 files under $dir and replaces EXT-X-KEY URI
     * with the API endpoint URL.
     *
     * @param string $dir         Absolute path to scan
     * @param array  $movedKeys   Map of originalBasename → safeFilename in hls_keys/
     * @param string $keyApiBase  Base URL of the key API (e.g. http://host/media/stream/key/)
     */
    private function _rewriteM3u8Keys(string $dir, array $movedKeys, string $keyApiBase): void
    {
        if (empty($movedKeys)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (strtolower($file->getExtension()) !== 'm3u8') {
                continue;
            }

            $content  = file_get_contents($file->getPathname());
            $modified = false;

            foreach ($movedKeys as $original => $safe) {
                // Match URI="anything/original.key" or URI='...'
                $pattern = '/(#EXT-X-KEY:[^\n]*URI=")([^"]*\/|)' . preg_quote($original, '/') . '(")/';
                $replace = '$1' . $keyApiBase . $safe . '$3';
                $new     = preg_replace($pattern, $replace, $content);

                if ($new !== $content) {
                    $content  = $new;
                    $modified = true;
                }
            }

            if ($modified) {
                file_put_contents($file->getPathname(), $content);
            }
        }
    }
}
