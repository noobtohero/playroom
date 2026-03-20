<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Manage Redeem Codes<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Redeem Codes</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= base_url('admin/codes/create') ?>" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-plus-circle"></i> Create New Code
        </a>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4">Code</th>
                        <th>Course</th>
                        <th>Usage</th>
                        <th>Expires At</th>
                        <th>Status</th>
                        <th class="text-end px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($codes)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No codes found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($codes as $code): ?>
                            <tr>
                                <td class="px-4">
                                    <code class="fs-5 fw-bold text-primary"><?= esc($code['code']) ?></code>
                                </td>
                                <td><?= esc($code['course_title']) ?></td>
                                <td>
                                    <div class="progress" style="height: 10px; width: 100px;">
                                        <?php $perc = ($code['used_count'] / $code['max_use']) * 100; ?>
                                        <div class="progress-bar" role="progressbar" style="width: <?= $perc ?>%"></div>
                                    </div>
                                    <small class="text-muted"><?= $code['used_count'] ?> / <?= $code['max_use'] ?></small>
                                </td>
                                <td><?= $code['expire_at'] ? date('M d, Y', strtotime($code['expire_at'])) : '<span class="text-muted">Never</span>' ?></td>
                                <td>
                                    <span class="badge rounded-pill bg-<?= $code['status'] === 'active' ? 'success' : 'secondary' ?>">
                                        <?= esc(ucfirst($code['status'])) ?>
                                    </span>
                                </td>
                                <td class="text-end px-4">
                                    <a href="<?= base_url('admin/codes/delete/' . $code['id']) ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Are you sure you want to delete this code?')">
                                        Delete
                                    </a>
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
