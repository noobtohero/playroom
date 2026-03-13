<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">Total Users</h6>
                        <h2 class="mb-0"><?= number_format($total_users) ?></h2>
                    </div>
                    <i class="bi bi-people fs-1 text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">Total Courses</h6>
                        <h2 class="mb-0"><?= number_format($total_courses) ?></h2>
                    </div>
                    <i class="bi bi-journal-check fs-1 text-white-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card bg-info text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">Revenue</h6>
                        <h2 class="mb-0">฿<?= number_format($revenue, 2) ?></h2>
                    </div>
                    <i class="bi bi-cash fs-1 text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">System Overview</h5>
    </div>
    <div class="card-body">
        <p>Welcome to the Playroom Admin CMS. Select an option from the sidebar to manage your platform.</p>
        <p class="text-muted"><i class="bi bi-info-circle"></i> Phase 1 development restricts full analytics. Basic CRUD operations will be available.</p>
    </div>
</div>
<?= $this->endSection() ?>
