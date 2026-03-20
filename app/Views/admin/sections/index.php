<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Manage Sections - <?= esc($course['title']) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage Sections: <?= esc($course['title']) ?></h1>
    <a href="<?= base_url('admin/courses') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Courses
    </a>
</div>

<div class="row">
    <!-- Section List -->
    <div class="col-md-8 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Sections List</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" width="60">Order</th>
                                <th scope="col">Title</th>
                                <th scope="col" width="180">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($sections)): ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4">No sections found. Add one on the right.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($sections as $s): ?>
                                <tr>
                                    <td class="text-center"><?= $s['sort_order'] ?></td>
                                    <td class="fw-bold"><?= esc($s['title']) ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= base_url('admin/sections/' . $s['id'] . '/lessons') ?>" class="btn btn-primary">
                                                <i class="bi bi-play-circle"></i> Lessons
                                            </a>
                                            <a href="#" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editSectionModal<?= $s['id'] ?>"><i class="bi bi-pencil"></i></a>
                                            <a href="<?= base_url('admin/sections/delete/' . $s['id']) ?>" class="btn btn-outline-danger" onclick="return confirm('Delete this section? All lessons inside will be deleted too.')"><i class="bi bi-trash"></i></a>
                                        </div>

                                        <!-- Edit Modal -->
                                        <div class="modal fade" id="editSectionModal<?= $s['id'] ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="<?= base_url('admin/sections/update/' . $s['id']) ?>" method="post">
                                                        <?= csrf_field() ?>
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Section</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">Section Title</label>
                                                                <input type="text" class="form-control" name="title" value="<?= esc($s['title']) ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Sort Order</label>
                                                                <input type="number" class="form-control" name="sort_order" value="<?= $s['sort_order'] ?>">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary">Save changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
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
    </div>

    <!-- Add Section Form -->
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Add New Section</h5>
            </div>
            <div class="card-body">
                <?php if(session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger pb-0">
                        <ul>
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('admin/courses/' . $course['id'] . '/sections') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="title" class="form-label">Section Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" value="<?= old('title') ?>" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="sort_order" class="form-label">Sort Order</label>
                        <input type="number" class="form-control" id="sort_order" name="sort_order" value="<?= old('sort_order', (count($sections) + 1) * 10) ?>">
                        <div class="form-text">e.g., 10, 20, 30</div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-plus-circle"></i> Add Section</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
