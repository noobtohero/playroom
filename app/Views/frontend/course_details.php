<?= $this->extend('layouts/frontend') ?>

<?= $this->section('title') ?><?= esc($course['title']) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Course Header -->
<div class="bg-dark text-white py-5 mb-5 rounded-3">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent p-0 mb-3">
                        <li class="breadcrumb-item"><a href="<?= base_url('/') ?>" class="text-white-50 text-decoration-none">Home</a></li>
                        <li class="breadcrumb-item"><a href="#" class="text-white-50 text-decoration-none">Courses</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page"><?= esc($course['title']) ?></li>
                    </ol>
                </nav>
                <h1 class="display-5 fw-bold mb-3"><?= esc($course['title']) ?></h1>
                <p class="lead mb-4"><?= esc($course['description']) ?></p>
                <div class="d-flex align-items-center gap-3">
                    <span class="badge <?= $course['type'] == 'free' ? 'bg-success' : ($course['type'] == 'code' ? 'bg-info text-dark' : 'bg-primary') ?> fs-5">
                        <?= $course['type'] == 'paid' ? '฿' . number_format($course['price'], 2) : ucfirst($course['type']) ?>
                    </span>
                    <span class="text-white-50"><i class="bi bi-clock me-1"></i> Self-paced</span>
                </div>
            </div>
            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="card text-dark shadow-lg border-0 bg-white">
                    <?php if($course['thumbnail']): ?>
                        <img src="<?= base_url('uploads/thumbnails/' . $course['thumbnail']) ?>" class="card-img-top" alt="<?= esc($course['title']) ?>">
                    <?php else: ?>
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                        </div>
                    <?php endif; ?>
                    <div class="card-body p-4">
                        <h4 class="card-title text-center mb-4">
                            <?= $course['type'] == 'paid' ? '฿' . number_format($course['price'], 2) : 'Free Access' ?>
                        </h4>
                        
                        <?php if(session()->get('isLoggedIn')): ?>
                            <?php if($course['type'] == 'paid' || $course['type'] == 'code'): ?>
                                <a href="<?= base_url('student/course/' . $course['id']) ?>" class="btn btn-primary btn-lg w-100 mb-3"><i class="bi bi-play-circle me-2"></i>Start Learning (Phase 1 Preview)</a>
                                <div class="text-center text-muted small mt-2">Payment gateway integration planned for Phase 2.</div>
                            <?php else: ?>
                                <a href="<?= base_url('student/course/' . $course['id']) ?>" class="btn btn-success btn-lg w-100 mb-3"><i class="bi bi-play-circle me-2"></i>Start Learning</a>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="<?= base_url('login') ?>" class="btn btn-outline-primary btn-lg w-100 mb-3">Sign In to Enroll</a>
                            <div class="text-center mt-3">
                                <span class="text-muted small">Don't have an account?</span> <a href="<?= base_url('register') ?>" class="text-decoration-none fw-bold">Sign up</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Course Content Breakdown -->
<div class="container mb-5">
    <div class="row">
        <div class="col-lg-8">
            <h3 class="mb-4">Course Content</h3>
            <div class="alert alert-info border-0 shadow-sm d-flex align-items-center">
                <i class="bi bi-info-circle-fill me-3 fs-3"></i>
                <div>
                    <strong>Phase 1 Preview</strong><br>
                    You are viewing the details for <em><?= esc($course['title']) ?></em>. The curriculum view (Sections & Lessons) will be rendered dynamically here once enrolled.
                </div>
            </div>
            
            <div class="mt-5">
                <h4>What you'll learn</h4>
                <ul class="list-group list-group-flush mt-3">
                    <li class="list-group-item bg-transparent border-bottom-0 ps-0"><i class="bi bi-check-circle-fill text-success me-2"></i> Comprehensive understanding of the subject matter.</li>
                    <li class="list-group-item bg-transparent border-bottom-0 ps-0"><i class="bi bi-check-circle-fill text-success me-2"></i> Practical skills applied in real-world scenarios.</li>
                    <li class="list-group-item bg-transparent border-bottom-0 ps-0"><i class="bi bi-check-circle-fill text-success me-2"></i> Access to exclusive community resources.</li>
                </ul>
            </div>
        </div>
        
        <div class="col-lg-4 mt-5 mt-lg-0">
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-3">Requirements</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="bi bi-laptop me-2 text-muted"></i> A computer with internet access</li>
                        <li class="mb-2"><i class="bi bi-lightning-charge me-2 text-muted"></i> Eagerness to learn</li>
                        <li><i class="bi bi-bookmark-check me-2 text-muted"></i> No prior experience needed</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
