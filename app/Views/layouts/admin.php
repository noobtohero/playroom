<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - <?= $this->renderSection('title') ?? 'Dashboard' ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <style>
        .sidebar { min-height: calc(100vh - 56px); }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body>

    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= base_url('admin/dashboard') ?>">Playroom Admin</a>
            <div class="d-flex text-white">
                <span class="me-3">Welcome, <?= session()->get('name') ?? 'Admin' ?></span>
                <a href="<?= base_url('logout') ?>" class="text-white text-decoration-none"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?= (url_is('admin/dashboard')) ? 'active fw-bold' : '' ?>" href="<?= base_url('admin/dashboard') ?>">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (url_is('admin/courses*')) ? 'active fw-bold' : '' ?>" href="<?= base_url('admin/courses') ?>">
                                <i class="bi bi-journal-check me-2"></i> Courses
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (url_is('admin/users*')) ? 'active fw-bold' : '' ?>" href="<?= base_url('admin/users') ?>">
                                <i class="bi bi-people me-2"></i> Users
                            </a>
                        </li>
                        <li class="nav-item border-top mt-2 pt-2">
                            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase small">
                                <span>Sales & Access</span>
                            </h6>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (url_is('admin/enrollments*')) ? 'active fw-bold' : '' ?>" href="<?= base_url('admin/enrollments') ?>">
                                <i class="bi bi-person-plus me-2"></i> Enrollments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (url_is('admin/codes*')) ? 'active fw-bold' : '' ?>" href="<?= base_url('admin/codes') ?>">
                                <i class="bi bi-ticket-perforated me-2"></i> Redeem Codes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (url_is('admin/purchases*')) ? 'active fw-bold' : '' ?>" href="<?= base_url('admin/purchases') ?>">
                                <i class="bi bi-wallet2 me-2"></i> Purchases
                            </a>
                        </li>
                        <li class="nav-item border-top mt-2 pt-2"></li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url() ?>" target="_blank">
                                <i class="bi bi-box-arrow-up-right me-2"></i> View Site
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?= $this->renderSection('content') ?>
            </main>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
