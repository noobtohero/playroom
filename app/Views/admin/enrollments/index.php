<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Manage Enrollments<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Course Enrollments</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= base_url('admin/enrollments/create') ?>" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-plus-circle"></i> New Enrollment
        </a>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success mt-3"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger mt-3"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="card shadow-sm mt-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4">Student</th>
                        <th>Course</th>
                        <th>Source</th>
                        <th>Enrolled At</th>
                        <th class="text-end px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($enrollments)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No enrollments found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($enrollments as $enrollment): ?>
                            <tr>
                                <td class="px-4">
                                    <div class="fw-bold"><?= esc($enrollment['user_name']) ?></div>
                                </td>
                                <td><?= esc($enrollment['course_title']) ?></td>
                                <td>
                                    <span class="badge rounded-pill bg-<?= $enrollment['source'] === 'admin' ? 'info text-dark' : ($enrollment['source'] === 'purchase' ? 'success' : 'secondary') ?>">
                                        <?= esc(ucfirst($enrollment['source'])) ?>
                                    </span>
                                </td>
                                <td><?= date('M d, Y H:i', strtotime($enrollment['created_at'])) ?></td>
                                <td class="text-end px-4">
                                    <a href="<?= base_url('admin/enrollments/delete/' . $enrollment['id']) ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Are you sure you want to revoke this access?')">
                                        Revoke
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
