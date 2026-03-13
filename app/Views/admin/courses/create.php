<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Create Course<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Create Course</h1>
    <a href="<?= base_url('admin/courses') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Courses
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <?php if(session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('admin/courses/store') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="title" class="form-label">Course Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" value="<?= old('title') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label">URL Slug <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="slug" name="slug" value="<?= old('slug') ?>" required>
                        <div class="form-text">e.g., javascript-basic (must be unique)</div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="5"><?= old('description') ?></textarea>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="type" class="form-label">Access Type</label>
                        <select class="form-select" id="type" name="type" x-data="{ type: '<?= old('type', 'free') ?>' }" x-model="type">
                            <option value="free">Free</option>
                            <option value="paid">Paid</option>
                            <option value="code">Redeem Code Only</option>
                        </select>
                    </div>

                    <div class="mb-3" x-data="{ type: document.getElementById('type').value }" x-init="setInterval(() => type = document.getElementById('type').value, 500)" x-show="type === 'paid'">
                        <label for="price" class="form-label">Price (THB)</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?= old('price', '0.00') ?>">
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="draft" <?= (old('status') == 'draft') ? 'selected' : '' ?>>Draft</option>
                            <option value="published" <?= (old('status') == 'published') ? 'selected' : '' ?>>Published</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="thumbnail" class="form-label">Thumbnail Image</label>
                        <input class="form-control" type="file" id="thumbnail" name="thumbnail" accept="image/png, image/jpeg, image/webp">
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-save"></i> Save Course</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Auto-generate slug from title
    document.getElementById('title').addEventListener('input', function() {
        let title = this.value;
        let slug = title.toLowerCase()
            .replace(/[^\w\s-]/g, '') // remove non-word chars
            .replace(/[\s_-]+/g, '-') // swap spaces for dashes
            .replace(/^-+|-+$/g, ''); // trim leading/trailing dashes
        document.getElementById('slug').value = slug;
    });
</script>
<?= $this->endSection() ?>
