<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Add Lesson<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Add Lesson to: <?= esc($section['title']) ?></h1>
    <a href="<?= base_url('admin/sections/' . $section['id'] . '/lessons') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Lessons
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

        <form action="<?= base_url('admin/sections/' . $section['id'] . '/lessons/store') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="title" class="form-label">Lesson Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" value="<?= old('title') ?>" required autofocus>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="type" class="form-label">Content Type</label>
                            <select class="form-select" id="type" name="type">
                                <option value="video" <?= old('type') == 'video' ? 'selected' : '' ?>>Video (HLS m3u8 playlist expected)</option>
                                <option value="slide" <?= old('type') == 'slide' ? 'selected' : '' ?>>Slide (PDF)</option>
                                <option value="podcast" <?= old('type') == 'podcast' ? 'selected' : '' ?>>Podcast (Audio)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="duration" class="form-label">Duration (in seconds)</label>
                            <input type="number" class="form-control" id="duration" name="duration" value="<?= old('duration', 0) ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="content_file" class="form-label">Upload Focus File</label>
                        <input class="form-control" type="file" id="content_file" name="content_file">
                        <div class="form-text text-muted">
                            <i class="bi bi-info-circle"></i> For Videos (HLS), normally you upload a `.m3u8` playlist here, and you manually move `.ts` parts to the server folder.<br/>
                            We will put files into: <code>writable/uploads/lessons/<?= $course['id'] ?>/<?= $section['id'] ?>/</code>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="sort_order" class="form-label">Sort Order</label>
                        <input type="number" class="form-control" id="sort_order" name="sort_order" value="<?= old('sort_order', 10) ?>">
                    </div>

                    <div class="mb-4">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="draft" <?= (old('status') == 'draft') ? 'selected' : '' ?>>Draft</option>
                            <option value="published" <?= (old('status') == 'published') ? 'selected' : '' ?>>Published</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-save"></i> Save Lesson</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
