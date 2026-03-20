<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Manage Purchases<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Purchase Requests</h1>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4">Student</th>
                        <th>Course</th>
                        <th>Slip</th>
                        <th>Created At</th>
                        <th>Status</th>
                        <th class="text-end px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($purchases)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No purchase requests found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($purchases as $purchase): ?>
                            <tr>
                                <td class="px-4">
                                    <div class="fw-bold"><?= esc($purchase['user_name']) ?></div>
                                </td>
                                <td><?= esc($purchase['course_title']) ?></td>
                                <td>
                                    <?php if ($purchase['slip_image']): ?>
                                        <a href="<?= base_url('uploads/slips/' . $purchase['slip_image']) ?>" target="_blank" class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-image"></i> View Slip
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">No slip</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('M d, Y H:i', strtotime($purchase['created_at'])) ?></td>
                                <td>
                                    <span class="badge rounded-pill bg-<?= $purchase['status'] === 'approved' ? 'success' : ($purchase['status'] === 'pending' ? 'warning text-dark' : 'danger') ?>">
                                        <?= esc(ucfirst($purchase['status'])) ?>
                                    </span>
                                </td>
                                <td class="text-end px-4">
                                    <?php if ($purchase['status'] === 'pending'): ?>
                                        <a href="<?= base_url('admin/purchases/approve/' . $purchase['id']) ?>" 
                                           class="btn btn-sm btn-success" 
                                           onclick="return confirm('Approve this purchase and enroll the user?')">
                                            Approve
                                        </a>
                                        <a href="<?= base_url('admin/purchases/reject/' . $purchase['id']) ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Reject this purchase?')">
                                            Reject
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">Processed</span>
                                    <?php endif; ?>
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
