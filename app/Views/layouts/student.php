<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learning Layout - <?= $this->renderSection('title') ?? 'Playroom Online Courses' ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom CSS -->
    <style>
        body { background-color: #f0f2f5; margin: 0; padding: 0; height: 100vh; display: flex; flex-direction: column; }
        .learning-header { flex-shrink: 0; }
        .learning-body { flex-grow: 1; overflow: hidden; display: flex; }
        .content-area { flex-grow: 1; overflow-y: auto; padding: 1rem; background-color: #000; display: flex; flex-direction: column; align-items: center; justify-content: center;}
        .sidebar-area { width: 350px; flex-shrink: 0; background-color: #fff; border-left: 1px solid #dee2e6; overflow-y: auto; }
        .lesson-item { padding: 0.75rem 1rem; border-bottom: 1px solid #eee; cursor: pointer; transition: background-color 0.2s; }
        .lesson-item:hover { background-color: #f8f9fa; }
        .lesson-item.active { background-color: #e9ecef; border-left: 4px solid #0d6efd; font-weight: bold; }
        @media (max-width: 768px) {
            .learning-body { flex-direction: column; }
            .sidebar-area { width: 100%; height: 50vh; border-left: none; border-top: 1px solid #dee2e6; }
            .content-area { height: 50vh; }
        }
    </style>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <!-- HLS.js -->
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    
    <style>
        .watermark {
            position: absolute;
            top: 20px;
            left: 20px;
            color: rgba(255, 255, 255, 0.3);
            font-size: 14px;
            pointer-events: none;
            z-index: 1000;
            user-select: none;
            font-family: sans-serif;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }
        .learning-body { flex-grow: 1; overflow: hidden; display: flex; height: calc(100vh - 56px); }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body>

    <!-- Simple Header -->
    <header class="learning-header bg-dark text-white p-3 d-flex justify-content-between align-items-center">
        <div>
            <a href="<?= base_url('student/dashboard') ?>" class="text-white text-decoration-none me-3"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
            <span class="fw-bold fs-5"><?= $this->renderSection('course_title') ?? 'Course Title' ?></span>
        </div>
        <div>
            <span class="me-3"><?= session()->get('name') ?? 'Student' ?></span>
            <a href="<?= base_url('logout') ?>" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </header>

    <!-- Learning Area -->
    <main class="learning-body">
        <?php if ($this->hasSection('player')): ?>
            <!-- Video/Slide/Audio Player Area -->
            <section class="content-area">
                <?= $this->renderSection('player') ?>
            </section>

            <!-- Course Outline Sidebar -->
            <aside class="sidebar-area">
                <div class="p-3 border-bottom bg-light sticky-top">
                    <h5 class="mb-0">Course Content</h5>
                </div>
                <div class="lesson-list">
                    <?= $this->renderSection('sidebar_content') ?>
                </div>
            </aside>
        <?php else: ?>
            <!-- Standard Content (Dashboard, etc) -->
            <div class="container-fluid p-4 overflow-auto w-100 bg-white">
                <?= $this->renderSection('content') ?>
            </div>
        <?php endif; ?>
    </main>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom Scripts -->
    <script>
        // Disable Right Click globally on learning layout
        document.addEventListener('contextmenu', event => event.preventDefault());

        // Basic DevTools disabled check (F12)
        document.onkeydown = function(e) {
            if(event.keyCode == 123) {
                return false;
            }
            if(e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) {
                return false;
            }
            if(e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)) {
                return false;
            }
            if(e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) {
                return false;
            }
            if(e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) {
                return false;
            }
        }
    </script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
