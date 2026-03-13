<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Courses<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage Courses</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= base_url('admin/courses/create') ?>" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle"></i> Create New Course
        </a>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Thumbnail</th>
                <th scope="col">Title</th>
                <th scope="col">Type</th>
                <th scope="col">Price</th>
                <th scope="col">Status</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($courses)): ?>
                <tr>
                    <td colspan="7" class="text-center">No courses found.</td>
                </tr>
            <?php else: ?>
                <?php foreach($courses as $course): ?>
                <tr>
                    <td><?= $course['id'] ?></td>
                    <td>
                        <?php if($course['thumbnail']): ?>
                            <img src="<?= base_url('uploads/thumbnails/' . $course['thumbnail']) ?>" alt="<?= esc($course['title']) ?>" width="60" class="img-thumbnail">
                        <?php else: ?>
                            <span class="text-muted"><i class="bi bi-image"></i> None</span>
                        <?php endif; ?>
                    </td>
                    <td><?= esc($course['title']) ?></td>
                    <td><span class="badge bg-secondary text-uppercase"><?= $course['type'] ?></span></td>
                    <td><?= ($course['type'] == 'paid') ? '฿' . number_format($course['price'], 2) : '-' ?></td>
                    <td>
                        <?php if($course['status'] == 'published'): ?>
                            <span class="badge bg-success">Published</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">Draft</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="<?= base_url('admin/courses/' . $course['id'] . '/sections') ?>" class="btn btn-outline-info" title="Manage Content"><i class="bi bi-list-task"></i> Content</a>
                            <a href="<?= base_url('admin/courses/edit/' . $course['id']) ?>" class="btn btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                            <a href="<?= base_url('admin/courses/delete/' . $course['id']) ?>" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to delete this course? Sections and lessons will also be deleted.')" title="Delete"><i class="bi bi-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>
