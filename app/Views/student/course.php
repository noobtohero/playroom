<?= $this->extend('layouts/student') ?>

<?= $this->section('title') ?><?= esc($course['title']) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="bg-dark text-white py-4 px-4 mb-4" style="margin: -1.5rem -1.5rem 1.5rem -1.5rem;">
    <div class="row align-items-center">
        <div class="col-md-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-2">
                    <li class="breadcrumb-item"><a href="<?= base_url('student/dashboard') ?>" class="text-white-50 text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page"><?= esc($course['title']) ?></li>
                </ol>
            </nav>
            <h1 class="h2 fw-bold mb-0"><?= esc($course['title']) ?></h1>
            <p class="text-white-50 mt-2 mb-0">Track your progress and access course materials.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <div class="d-inline-block text-start">
                <div class="text-white-50 small mb-1">Course Progress</div>
                <div class="d-flex align-items-center">
                    <div class="progress flex-grow-1 me-3 bg-secondary" style="height: 8px; width: 150px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 0%;"></div>
                    </div>
                    <span class="fw-bold">0%</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <!-- Main Content: Course Curriculum -->
        <div class="col-lg-8 mb-4">
            <h4 class="mb-3">Course Curriculum</h4>

            <?php if(empty($sections)): ?>
                <div class="alert alert-warning">The instructor hasn't added any content to this course yet.</div>
            <?php else: ?>
                <div class="accordion" id="curriculumAccordion">
                    <?php foreach($sections as $index => $section): ?>
                        <div class="accordion-item shadow-sm mb-3 border-0 rounded">
                            <h2 class="accordion-header" id="heading<?= $section['id'] ?>">
                                <button class="accordion-button <?= $index !== 0 ? 'collapsed' : '' ?> fw-bold bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $section['id'] ?>" aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>" aria-controls="collapse<?= $section['id'] ?>">
                                    <?= esc($section['title']) ?>
                                    <span class="badge bg-light text-dark ms-3 border fw-normal"><?= count($section['lessons']) ?> lessons</span>
                                </button>
                            </h2>
                            <div id="collapse<?= $section['id'] ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" aria-labelledby="heading<?= $section['id'] ?>">
                                <div class="accordion-body p-0">
                                    <div class="list-group list-group-flush">
                                        <?php if(empty($section['lessons'])): ?>
                                            <div class="list-group-item text-muted py-3">No lessons found in this section.</div>
                                        <?php else: ?>
                                            <?php foreach($section['lessons'] as $lesson): ?>
                                                <a href="<?= base_url('student/course/' . $course['id'] . '/lesson/' . $lesson['id']) ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3 px-4 text-decoration-none text-dark">
                                                    <div class="d-flex align-items-center">
                                                        <?php if($lesson['type'] == 'video'): ?>
                                                            <i class="bi bi-play-circle-fill text-primary fs-4 me-3"></i>
                                                        <?php elseif($lesson['type'] == 'slide'): ?>
                                                            <i class="bi bi-file-earmark-slides-fill text-info fs-4 me-3"></i>
                                                        <?php else: ?>
                                                            <i class="bi bi-mic-fill text-secondary fs-4 me-3"></i>
                                                        <?php endif; ?>
                                                        
                                                        <div>
                                                            <h6 class="mb-0 fw-semibold hover-primary"><?= esc($lesson['title']) ?></h6>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="text-muted small">
                                                        <?php if($lesson['duration'] > 0): ?>
                                                            <?= floor($lesson['duration'] / 60) ?>m <?= $lesson['duration'] % 60 ?>s
                                                        <?php endif; ?>
                                                    </div>
                                                </a>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar: About Course -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 bg-white sticky-top" style="top: 20px;">
                <?php if($course['thumbnail']): ?>
                    <img src="<?= base_url('uploads/thumbnails/' . $course['thumbnail']) ?>" class="card-img-top" alt="<?= esc($course['title']) ?>">
                <?php endif; ?>
                <div class="card-body">
                    <h5 class="card-title fw-bold">About This Course</h5>
                    <p class="card-text text-muted mb-4"><?= esc(character_limiter($course['description'], 150)) ?></p>
                    
                    <h6 class="fw-bold mb-3">Course Features</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3 d-flex align-items-center text-muted">
                            <i class="bi bi-file-earmark-text me-3 fs-5 text-primary"></i> 
                            <div>
                                <strong class="d-block text-dark">Sections</strong>
                                <?= count($sections) ?>
                            </div>
                        </li>
                        <li class="mb-3 d-flex align-items-center text-muted">
                            <i class="bi bi-play-btn me-3 fs-5 text-primary"></i> 
                            <div>
                                <strong class="d-block text-dark">Access</strong>
                                Unlimited, lifetime
                            </div>
                        </li>
                        <li class="d-flex align-items-center text-muted">
                            <i class="bi bi-phone me-3 fs-5 text-primary"></i> 
                            <div>
                                <strong class="d-block text-dark">Available on</strong>
                                Web & Mobile
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-primary {
        transition: color 0.2s ease;
    }
    .list-group-item:hover .hover-primary {
        color: var(--bs-primary);
    }
</style>
<?= $this->endSection() ?>
