<?= $this->extend('layouts/student') ?>

<?= $this->section('title') ?>My Courses<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0">My Learning Dashboard</h2>
            <p class="text-muted">Welcome back, <?= esc(session()->get('name')) ?>. Here are your enrolled courses.</p>
        </div>
    </div>

    <!-- Info Alert for Phase 1 -->
    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4">
        <i class="bi bi-info-circle-fill me-3 fs-3"></i>
        <div>
            <strong>Phase 1 Preview</strong><br>
            Currently showing all published courses as "enrolled" for demonstration purposes.
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php if(empty($my_courses)): ?>
            <div class="col-12 text-center py-5 w-100">
                <i class="bi bi-journal-x fs-1 text-muted mb-3 d-block"></i>
                <h5>You aren't enrolled in any courses yet.</h5>
                <p class="text-muted">Explore our catalog and start learning today!</p>
                <a href="<?= base_url('/') ?>" class="btn btn-primary mt-2">Browse Courses</a>
            </div>
        <?php else: ?>
            <?php foreach($my_courses as $course): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0 bg-white hover-shadow transition-all">
                        <?php if($course['thumbnail']): ?>
                            <img src="<?= base_url('uploads/thumbnails/' . $course['thumbnail']) ?>" class="card-img-top" alt="<?= esc($course['title']) ?>" style="height: 180px; object-fit: cover;">
                        <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                                <i class="bi bi-play-circle text-muted" style="font-size: 3rem;"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <h5 class="card-title fw-bold mb-3"><?= esc($course['title']) ?></h5>
                            
                            <!-- Mock Progress Bar -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between text-muted small mb-1">
                                    <span>Overall Progress</span>
                                    <span>0%</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top-0 d-flex justify-content-between align-items-center pb-3 pt-0">
                            <a href="<?= base_url('student/course/' . $course['id']) ?>" class="btn btn-primary btn-sm w-100">
                                <i class="bi bi-play-circle me-1"></i> Continue Course
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<style>
    .transition-all {
        transition: all 0.3s ease;
    }
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
</style>
<?= $this->endSection() ?>
