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

    <div class="col-md-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-graph-up text-primary"></i> Revenue (Last 30 Days)
            </div>
            <div class="card-body">
                <canvas id="revenueChart" style="width: 100%; height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-lightning-charge text-warning"></i> Quick Actions
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="<?= base_url('admin/courses') ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold">Manage Courses & Lessons</div>
                            <small class="text-muted">Upload HLS Videos, Slides, and manage content.</small>
                        </div>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                    <a href="<?= base_url('admin/courses/create') ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold">Create New Course</div>
                            <small class="text-muted">Start a new course and add sections.</small>
                        </div>
                        <i class="bi bi-plus-circle"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
                <span><i class="bi bi-cart-check text-success"></i> Recent Purchases</span>
                <a href="<?= base_url('admin/purchases') ?>" class="btn btn-sm btn-link text-decoration-none p-0">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="bg-light small font-monospace">
                            <tr>
                                <th class="ps-3">User</th>
                                <th>Course</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <?php if (empty($recent_purchases)): ?>
                                <tr><td colspan="3" class="text-center py-3 text-muted">No recent purchases</td></tr>
                            <?php else: ?>
                                <?php foreach ($recent_purchases as $p): ?>
                                    <tr>
                                        <td class="ps-3"><?= esc($p['user_name']) ?></td>
                                        <td><?= esc($p['course_title']) ?></td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill bg-<?= $p['status'] === 'approved' ? 'success' : ($p['status'] === 'pending' ? 'warning text-dark' : 'danger') ?>" style="font-size: 0.65rem;">
                                                <?= ucfirst($p['status']) ?>
                                            </span>
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
</div>
<?= $this->endSection() ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const canvas = document.getElementById('revenueChart');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        const data = <?= json_encode($revenue_chart ?? []) ?>;
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.map(item => item.date),
                datasets: [{
                    label: 'Revenue (฿)',
                    data: data.map(item => item.total),
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '฿' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: ฿' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    });
</script>
