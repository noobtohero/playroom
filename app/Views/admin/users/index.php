<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Manage Users<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">System Users</h1>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4">Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th class="text-end px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="px-4">
                                <div class="fw-bold"><?= esc($user['name']) ?></div>
                            </td>
                            <td><?= esc($user['email']) ?></td>
                            <td>
                                <span class="badge bg-<?= $user['role'] === 'super-admin' ? 'dark' : ($user['role'] === 'admin' ? 'danger' : ($user['role'] === 'teacher' ? 'info text-dark' : 'primary')) ?>">
                                    <?= esc(ucfirst($user['role'])) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge rounded-pill bg-<?= $user['status'] === 'active' ? 'success' : 'secondary' ?>">
                                    <?= esc(ucfirst($user['status'])) ?>
                                </span>
                            </td>
                            <td class="small text-muted"><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                            <td class="text-end px-4">
                                <?php 
                                    $canManage = false;
                                    if (session()->get('role') === 'super-admin') {
                                        $canManage = true;
                                    } elseif (session()->get('role') === 'admin' && in_array($user['role'], ['teacher', 'student'])) {
                                        $canManage = true;
                                    }
                                    
                                    // Special case: can edit OWN profile (but controller will restrict role/status)
                                    if ($user['id'] == session()->get('id')) {
                                        $canManage = true;
                                    }
                                ?>

                                <?php if ($canManage): ?>
                                    <a href="<?= base_url('admin/users/edit/' . $user['id']) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    
                                    <?php if ($user['id'] != session()->get('id')): ?>
                                        <a href="<?= base_url('admin/users/delete/' . $user['id']) ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted small"><i class="bi bi-lock-fill"></i> Locked</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
