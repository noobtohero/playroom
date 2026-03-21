<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\LessonProgressModel;
use App\Models\VideoBookmarkModel;
use App\Models\LessonModel;

class ProgressController extends BaseController
{
    /**
     * Helper to award a trophy by type if not already earned
     */
    private function awardTrophy(string $type): bool
    {
        $userId = session()->get('id');
        $trophyModel = new \App\Models\TrophyModel();
        $userTrophyModel = new \App\Models\UserTrophyModel();

        $trophy = $trophyModel->where('type', $type)->first();
        if (!$trophy) return false;

        $existing = $userTrophyModel->where('user_id', $userId)
                                    ->where('trophy_id', $trophy['id'])
                                    ->first();

        if (!$existing) {
            $userTrophyModel->insert([
                'user_id'   => $userId,
                'trophy_id' => $trophy['id'],
                'earned_at' => date('Y-m-d H:i:s')
            ]);
            return true;
        }
        return false;
    }

    /**
     * Save lesson completion status + trigger trophy checks
     */
    public function saveLessonProgress()
    {
        $userId   = session()->get('id');
        $lessonId = $this->request->getPost('lesson_id');
        $status   = $this->request->getPost('status'); // 1 = completed

        if (!$userId || !$lessonId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid data']);
        }

        $lessonModel = new LessonModel();
        $lesson = $lessonModel->find($lessonId);
        if (!$lesson) {
            return $this->response->setJSON(['success' => false, 'message' => 'Lesson not found']);
        }

        $progressModel = new LessonProgressModel();

        $existing = $progressModel->where('user_id', $userId)
                                  ->where('lesson_id', $lessonId)
                                  ->first();

        $data = [
            'user_id'      => $userId,
            'course_id'    => $lesson['course_id'],
            'lesson_id'    => $lessonId,
            'is_completed' => (int)$status
        ];

        if ($existing) {
            $progressModel->update($existing['id'], $data);
        } else {
            $progressModel->insert($data);
        }

        // === Trophy Triggers ===
        if ((int)$status === 1) {
            // Trophy: First Lesson
            $this->awardTrophy('first_lesson');

            // Trophy: Milestone 5 lessons
            $completedCount = $progressModel->where('user_id', $userId)
                                            ->where('is_completed', 1)
                                            ->countAllResults();
            if ($completedCount >= 5) {
                $this->awardTrophy('milestone_5');
            }

            // Trophy: Course Complete (100%)
            $prog = $progressModel->getCourseProgress($userId, (int)$lesson['course_id']);
            if ($prog >= 100) {
                $this->awardTrophy('course_complete');
            }
        }

        return $this->response->setJSON(['success' => true]);
    }

    /**
     * Save video playback position (bookmark)
     */
    public function saveVideoBookmark()
    {
        $userId   = session()->get('id');
        $lessonId = $this->request->getPost('lesson_id');
        $time     = $this->request->getPost('last_time');

        if (!$userId || !$lessonId || $time === null) {
            return $this->response->setJSON(['success' => false]);
        }

        $bookmarkModel = new VideoBookmarkModel();
        $bookmarkModel->saveBookmark($userId, (int)$lessonId, (float)$time);

        return $this->response->setJSON(['success' => true]);
    }

    /**
     * Save personal lesson note + trigger first_note trophy
     */
    public function saveNote()
    {
        $userId   = session()->get('id');
        $lessonId = $this->request->getPost('lesson_id');
        $content  = $this->request->getPost('content');

        if (!$userId || !$lessonId) {
            return $this->response->setJSON(['success' => false]);
        }

        $model = new \App\Models\UserNotesModel();
        $existing = $model->where('user_id', $userId)->where('lesson_id', $lessonId)->first();

        if ($existing) {
            $model->update($existing['id'], ['content' => $content]);
        } else {
            $model->insert([
                'user_id'   => $userId,
                'lesson_id' => $lessonId,
                'content'   => $content
            ]);
            // Trophy: First Note
            $this->awardTrophy('first_note');
        }

        return $this->response->setJSON(['success' => true]);
    }
}
