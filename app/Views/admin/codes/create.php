<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>New Redeem Code<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Create Redeem Code</h1>
    <a href="<?= base_url('admin/codes') ?>" class="btn btn-sm btn-outline-secondary">Cancel</a>
</div>

<div class="row justify-content-center mt-4">
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form action="<?= base_url('admin/codes/store') ?>" method="POST">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label for="code" class="form-label fw-bold">Code String</label>
                        <div class="input-group">
                            <input type="text" name="code" id="code" class="form-control" placeholder="e.g. SUMMER2024" required>
                            <button type="button" class="btn btn-outline-secondary" onclick="generateRandomCode()">Random</button>
                        </div>
                        <small class="text-muted">Unique code for students to redeem access.</small>
                    </div>

                    <div class="mb-3">
                        <label for="course_id" class="form-label fw-bold">Target Course</label>
                        <select name="course_id" id="course_id" class="form-select" required>
                            <option value="">-- Choose Course --</option>
                            <?php foreach($courses as $course): ?>
                                <option value="<?= $course['id'] ?>"><?= esc($course['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="max_use" class="form-label fw-bold">Max Usage</label>
                            <input type="number" name="max_use" id="max_use" class="form-control" value="1" min="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="expire_at" class="form-label fw-bold">Expiry Date (Optional)</label>
                            <input type="date" name="expire_at" id="expire_at" class="form-control">
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4">Generate Code</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function generateRandomCode() {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let result = '';
        for (let i = 0; i < 10; i++) {
            result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('code').value = result;
    }
</script>
<?= $this->endSection() ?>
