<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Lessons for <?= esc($section['title']) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Lessons in: <?= esc($section['title']) ?></h1>
    <div>
        <a href="<?= base_url('admin/courses/' . $course['id'] . '/sections') ?>" class="btn btn-sm btn-outline-secondary me-2">
            <i class="bi bi-arrow-left"></i> Back to Sections
        </a>
        <a href="<?= base_url('admin/sections/' . $section['id'] . '/lessons/create') ?>" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle"></i> Add New Lesson
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th scope="col" width="60">Order</th>
                        <th scope="col">Title</th>
                        <th scope="col">Type</th>
                        <th scope="col">Duration</th>
                        <th scope="col">Status</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($lessons)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">No lessons added yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($lessons as $l): ?>
                        <tr>
                            <td class="text-center"><?= $l['sort_order'] ?></td>
                            <td class="fw-bold">
                                <?= esc($l['title']) ?>
                                <?php if($l['content_path']): ?>
                                    <br><small class="text-muted"><i class="bi bi-paperclip"></i> Has Content</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($l['type'] == 'video'): ?>
                                    <span class="badge bg-primary"><i class="bi bi-play-btn me-1"></i>Video</span>
                                <?php elseif($l['type'] == 'slide'): ?>
                                    <span class="badge bg-info text-dark"><i class="bi bi-file-earmark-slides me-1"></i>Slide</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><i class="bi bi-mic me-1"></i>Podcast</span>
                                <?php endif; ?>
                            </td>
                            <td><?= floor($l['duration'] / 60) ?>m <?= $l['duration'] % 60 ?>s</td>
                            <td>
                                <?php if($l['status'] == 'published'): ?>
                                    <span class="badge bg-success">Published</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= base_url('admin/lessons/edit/' . $l['id']) ?>" class="btn btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <a href="<?= base_url('admin/lessons/delete/' . $l['id']) ?>" class="btn btn-outline-danger" onclick="return confirm('Delete this lesson completely?')" title="Delete"><i class="bi bi-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
