<?= $this->extend('layouts/frontend') ?>

<?= $this->section('title') ?>Learn Anywhere, Anytime<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Hero Section -->
<div class="bg-dark text-white text-center py-5 mb-5 rounded-3">
    <div class="container py-4">
        <h1 class="display-5 fw-bold">Welcome to Playroom</h1>
        <div class="col-lg-6 mx-auto">
            <p class="lead mb-4">Master new skills with our premium courses. From web development to design, we have everything you need to succeed.</p>
            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                <?php if(!session()->get('isLoggedIn')): ?>
                    <a href="<?= base_url('register') ?>" class="btn btn-primary btn-lg px-4 gap-3">Get Started for Free</a>
                    <a href="<?= base_url('login') ?>" class="btn btn-outline-light btn-lg px-4">Sign In</a>
                <?php else: ?>
                    <a href="#courses-section" class="btn btn-primary btn-lg px-4 gap-3">Browse Courses</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Courses Section -->
<div id="courses-section" class="container mb-5">
    <h2 class="mb-4">Explore Our Courses</h2>
    
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php if(empty($courses)): ?>
            <div class="col-12 w-100 text-center py-5">
                <p class="text-muted">No courses are currently available. Please check back later.</p>
            </div>
        <?php else: ?>
            <?php foreach($courses as $course): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm course-card hover-shadow transition-all">
                        <?php if($course['thumbnail']): ?>
                            <img src="<?= base_url('uploads/thumbnails/' . $course['thumbnail']) ?>" class="card-img-top" alt="<?= esc($course['title']) ?>" style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center border-bottom" style="height: 200px;">
                                <i class="bi bi-play-circle text-muted" style="font-size: 4rem;"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge <?= $course['type'] == 'free' ? 'bg-success' : ($course['type'] == 'code' ? 'bg-info text-dark' : 'bg-primary') ?> text-uppercase">
                                    <?= $course['type'] ?>
                                </span>
                            </div>
                            <h5 class="card-title fw-bold">
                                <a href="<?= base_url('course/' . $course['slug']) ?>" class="text-decoration-none text-dark stretched-link">
                                    <?= esc($course['title']) ?>
                                </a>
                            </h5>
                            <p class="card-text text-muted flex-grow-1" style="font-size: 0.9rem;">
                                <?= esc(character_limiter($course['description'], 100)) ?>
                            </p>
                        </div>
                        <div class="card-footer bg-white border-top-0 d-flex justify-content-between align-items-center pb-3">
                            <span class="fs-5 fw-bold text-primary">
                                <?= $course['type'] == 'paid' ? '฿' . number_format($course['price'], 2) : 'Free' ?>
                            </span>
                            <span class="text-muted small">Learn More <i class="bi bi-arrow-right"></i></span>
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
