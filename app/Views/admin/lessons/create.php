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
            <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
            <input type="hidden" name="section_id" value="<?= $section['id'] ?>">
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="title" class="form-label">Lesson Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" value="<?= old('title') ?>" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="duration" class="form-label">Duration (seconds)</label>
                        <input type="number" class="form-control" id="duration" name="duration" value="<?= old('duration', 0) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="external_url" class="form-label fw-bold">External URL (e.g. YouTube, Vimeo)</label>
                        <input type="url" class="form-control" id="external_url" name="external_url" value="<?= old('external_url') ?>" placeholder="https://www.youtube.com/watch?v=...">
                        <div class="form-text">หากใส่ URL ระบบจะใช้ลิงก์นี้แทนการอัปโหลดไฟล์</div>
                    </div>

                    <div class="mb-3">
                        <label for="content_file" class="form-label fw-semibold">Upload Content File</label>
                        <input class="form-control" type="file" id="content_file" name="content_file"
                               accept=".zip,.m3u8,.pdf,.mp3,.m4a,.aac,.ogg,.md,.markdown">
                        <div class="form-text mt-2">
                            <table class="table table-sm table-bordered small mb-0">
                                <thead class="table-light"><tr><th>ไฟล์</th><th>ประเภทที่ตรวจพบอัตโนมัติ</th></tr></thead>
                                <tbody>
                                    <tr><td><code>.zip</code> (HLS), <code>.m3u8</code></td><td><span class="badge bg-primary">📹 Video</span></td></tr>
                                    <tr><td><code>.pdf</code></td><td><span class="badge bg-info text-dark">📑 Slide</span></td></tr>
                                    <tr><td><code>.mp3</code>, <code>.m4a</code>, <code>.aac</code></td><td><span class="badge bg-warning text-dark">🎵 Podcast</span></td></tr>
                                    <tr><td><code>.md</code>, <code>.markdown</code></td><td><span class="badge bg-success">📝 Markdown</span></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="key_file" class="form-label">Encryption Key (Optional)</label>
                        <input class="form-control" type="file" id="key_file" name="key_file" accept=".key,.bin">
                        <div class="form-text text-muted">
                            <i class="bi bi-shield-lock"></i> ใช้เฉพาะกรณี HLS zip ไม่มี .key อยู่ข้างใน
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

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_downloadable" name="is_downloadable" value="1" <?= old('is_downloadable') ? 'checked' : '' ?>>
                            <label class="form-check-label fw-bold" for="is_downloadable">Allow Download</label>
                        </div>
                        <div class="form-text small">อนุญาตให้นักเรียนดาวน์โหลดไฟล์นี้ได้ (เช่น PDF, MP3)</div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-save"></i> Save Lesson</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
