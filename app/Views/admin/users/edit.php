<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Edit User<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit User Profile</h1>
    <a href="<?= base_url('admin/users') ?>" class="btn btn-sm btn-outline-secondary">Cancel</a>
</div>

<div class="row justify-content-center mt-4">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger">
                        <?= esc(session()->getFlashdata('error')) ?>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('admin/users/update/' . $user['id']) ?>" method="POST">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">Full Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="<?= esc($user['name']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?= esc($user['email']) ?>" required>
                    </div>

                    <?php 
                        $isSelf = ($user['id'] == session()->get('id')); 
                        $currentRole = session()->get('role');
                    ?>
                    
                    <?php if (!$isSelf): ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label fw-bold">User Role</label>
                                <select name="role" id="role" class="form-select" required>
                                    <?php if ($currentRole === 'super-admin'): ?>
                                        <option value="student" <?= $user['role'] === 'student' ? 'selected' : '' ?>>Student</option>
                                        <option value="teacher" <?= $user['role'] === 'teacher' ? 'selected' : '' ?>>Teacher</option>
                                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    <?php else: // Admin role ?>
                                        <option value="student" <?= $user['role'] === 'student' ? 'selected' : '' ?>>Student</option>
                                        <option value="teacher" <?= $user['role'] === 'teacher' ? 'selected' : '' ?>>Teacher</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label fw-bold">Status</label>
                                <select name="status" id="status" class="form-select" required>
                                    <option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                    <option value="inactive" <?= $user['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </div>
                        </div>
                    <?php else: ?>
                        <input type="hidden" name="role" value="<?= $user['role'] ?>">
                        <input type="hidden" name="status" value="<?= $user['status'] ?>">
                    <?php endif; ?>

                    <div class="mb-4 pt-3 border-top">
                        <label for="password" class="form-label fw-bold">Reset Password (Optional)</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Leave blank to keep current">
                        <small class="text-muted">Enter a new password ONLY if you want to change it.</small>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4 py-2">Update User Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
