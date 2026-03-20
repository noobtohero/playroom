<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>New Enrollment<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Grant Course Access</h1>
    <a href="<?= base_url('admin/enrollments') ?>" class="btn btn-sm btn-outline-secondary">Cancel</a>
</div>

<div class="row justify-content-center mt-4">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>

                <form action="<?= base_url('admin/enrollments/store') ?>" method="POST">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label for="user_id" class="form-label fw-bold">Select Student</label>
                        <select name="user_id" id="user_id" class="form-select" required>
                            <option value="">-- Choose Student --</option>
                            <?php foreach($users as $user): ?>
                                <option value="<?= $user['id'] ?>"><?= esc($user['name']) ?> (<?= esc($user['email']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="course_id" class="form-label fw-bold">Select Course</label>
                        <select name="course_id" id="course_id" class="form-select" required>
                            <option value="">-- Choose Course --</option>
                            <?php foreach($courses as $course): ?>
                                <option value="<?= $course['id'] ?>"><?= esc($course['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4">Confirm Enrollment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
