<?= $this->extend('layouts/frontend') ?>

<?= $this->section('title') ?>Login<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                    <h3 class="card-title text-center mb-4">Login to Playroom</h3>
                    
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                    <?php endif; ?>
                    
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                    <?php endif; ?>

                    <form action="<?= base_url('login') ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
                            <?php if(isset(session()->getFlashdata('errors')['email'])): ?>
                                <div class="text-danger small mt-1"><?= session()->getFlashdata('errors')['email'] ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                             <?php if(isset(session()->getFlashdata('errors')['password'])): ?>
                                <div class="text-danger small mt-1"><?= session()->getFlashdata('errors')['password'] ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Login</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4">
                        <p class="mb-0">Don't have an account? <a href="<?= base_url('register') ?>" class="text-decoration-none">Sign up</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
