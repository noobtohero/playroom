<?= $this->extend('layouts/student') ?>

<?= $this->section('title') ?>My Courses<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <div class="row mb-5 align-items-center">
        <div class="col-md-8">
            <h1 class="fw-bold mb-1" style="color: #0f172a; letter-spacing: -1px;">My Learning Dashboard</h1>
            <p class="text-muted lead mb-0">Welcome back, <?= esc(session()->get('name')) ?>. Ready to level up?</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="<?= base_url('student/redeem') ?>" class="btn btn-premium px-4 py-2 rounded-pill shadow">
                <i class="bi bi-stars me-2"></i> Redeem New Course
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success border-0 shadow-sm rounded-4 px-4 py-3 mb-4 d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-3 fs-3"></i>
            <div><?= session()->getFlashdata('success') ?></div>
        </div>
    <?php endif; ?>

    <h3 class="fw-bold mb-4 d-flex align-items-center">
        <i class="bi bi-journal-bookmark-fill text-primary me-2"></i> Enrolled Courses
    </h3>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php if(empty($my_courses)): ?>
            <div class="col-12 text-center py-5 w-100 bg-light rounded-5 border-2 border-dashed">
                <div class="display-1 text-muted mb-3 opacity-25"><i class="bi bi-mortarboard"></i></div>
                <h4 class="fw-bold">No courses enrolled yet</h4>
                <p class="text-muted px-5">It looks like you haven't started any journey. Check out our store and pick your first course!</p>
                <a href="<?= base_url('/') ?>" class="btn btn-primary rounded-pill px-5 mt-3 shadow-sm">Explore Courses</a>
            </div>
        <?php else: ?>
            <?php foreach($my_courses as $course): ?>
                <div class="col">
                    <div class="card h-100 shadow border-0 overflow-hidden hover-lift transition-all rounded-4">
                        <div class="position-relative">
                            <?php if($course['thumbnail']): ?>
                                <img src="<?= base_url('uploads/thumbnails/' . $course['thumbnail']) ?>" class="card-img-top" alt="<?= esc($course['title']) ?>" style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <div class="card-img-top bg-gradient-primary d-flex align-items-center justify-content-center" style="height: 200px; background: linear-gradient(135deg, #0d6efd 0%, #001f3f 100%);">
                                    <i class="bi bi-play-circle text-white-50" style="font-size: 4rem;"></i>
                                </div>
                            <?php endif; ?>
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-white text-dark shadow-sm rounded-pill px-3 py-2 opacity-90"><i class="bi bi-star-fill text-warning me-1"></i> Course</span>
                            </div>
                        </div>
                        
                        <div class="card-body p-4">
                            <h5 class="card-title fw-bold mb-3 line-clamp-2" style="min-height: 2.8em;"><?= esc($course['title']) ?></h5>
                            
                            <div class="d-flex align-items-center text-muted small mb-4">
                                <i class="bi bi-layers me-2"></i> Professional Training
                            </div>

                            <div class="progress mb-2" style="height: 8px; background-color: #f1f5f9;">
                                <div class="progress-bar bg-primary rounded-pill" role="progressbar" style="width: <?= $course['progress'] ?>%;" aria-valuenow="<?= $course['progress'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between text-muted extra-small">
                                <span><?= $course['progress'] ?>% Completed</span>
                                <span class="fw-bold text-primary"><?= $course['completed_lessons'] ?> / <?= $course['total_lessons'] ?> Lessons</span>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 p-4 pt-0">
                            <a href="<?= base_url('student/course/' . $course['id']) ?>" class="btn btn-premium w-100 py-2 rounded-pill shadow-sm fw-bold">
                                <i class="bi bi-rocket-takeoff me-2"></i> Continue Learning
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- ===== TROPHY SHOWCASE ===== -->
<?php if (!empty($user_trophies)): ?>
<div class="container-fluid px-4 mt-2 mb-4">
    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%); border-radius: 16px; overflow: hidden;">
        <div class="card-body p-4">
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-trophy-fill text-warning fs-4 me-2"></i>
                <h5 class="mb-0 text-white fw-bold">My Achievements</h5>
                <span class="badge bg-warning text-dark ms-2 rounded-pill"><?= count($user_trophies) ?></span>
            </div>
            <div class="d-flex flex-wrap gap-3">
                <?php foreach ($user_trophies as $trophy): ?>
                <div class="trophy-badge" title="<?= esc($trophy['description']) ?>">
                    <div class="d-flex flex-column align-items-center p-3" style="background: rgba(255,255,255,0.1); border-radius: 14px; min-width: 100px; border: 1px solid rgba(255,255,255,0.15);">
                        <i class="bi <?= esc($trophy['icon']) ?> fs-1 mb-1" style="color: #fbbf24;"></i>
                        <span class="small fw-bold text-white text-center" style="font-size: 0.78rem;"><?= esc($trophy['name']) ?></span>
                        <span class="text-white-50" style="font-size: 0.65rem;"><?= date('M j', strtotime($trophy['earned_at'])) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
    .transition-all {
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    }
    .hover-lift:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
    }
    .extra-small {
        font-size: 0.75rem;
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .bg-gradient-primary {
        background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
    }
    .border-dashed {
        border-style: dashed !important;
    }
</style>
<?= $this->endSection() ?>
