<?= $this->extend('layouts/student') ?>

<?= $this->section('title') ?>Redeem Course<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center py-5">
    <div class="col-md-6">
        <div class="card shadow border-0">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <i class="bi bi-ticket-perforated fs-1 text-primary"></i>
                    <h2 class="fw-bold mt-2">Redeem Access Code</h2>
                    <p class="text-muted">Enter your special code below to unlock your course content.</p>
                </div>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>

                <form action="<?= base_url('student/redeem') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-4">
                        <label for="code" class="form-label fw-bold">Code String</label>
                        <input type="text" name="code" id="code" class="form-control form-control-lg text-center fw-bold" 
                               placeholder="e.g. COURSE-KEY-XXXX" style="letter-spacing: 2px;" required autofocus>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg py-3">Unlock Course Now</button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <a href="<?= base_url('student/dashboard') ?>" class="text-muted text-decoration-none">
                        <i class="bi bi-arrow-left"></i> Back to My Courses
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
