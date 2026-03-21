<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learning Layout - <?= $this->renderSection('title') ?? 'Playroom Online Courses' ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Plyr CSS -->
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
    <style>
        :root {
            /* Custom Plyr Colors */
            --plyr-color-main: #0d6efd;
            --plyr-video-background: #000;
        }
        .plyr--full-ui.plyr--video .plyr__control--overlaid {
            background: rgba(13, 110, 253, 0.8);
        }
        .plyr--video .plyr__controls {
            padding: 15px !important;
            background: linear-gradient(rgba(0,0,0,0), rgba(0,0,0,0.6)) !important;
        }
    </style>
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #0d6efd;
            --dark-bg: #0f172a;
            --sidebar-bg: #ffffff;
            --card-border: rgba(0,0,0,0.05);
        }
        body { 
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc; 
            margin: 0; 
            padding: 0; 
            height: 100vh; 
            overflow: hidden;
            display: flex; 
            flex-direction: column; 
            color: #1e293b;
        }
        .learning-header { 
            position: sticky;
            top: 0;
            z-index: 1030;
            flex-shrink: 0; 
            background: linear-gradient(90deg, #1e293b 0%, #0f172a 100%);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .learning-body { flex-grow: 1; display: flex; height: calc(100vh - 64px); overflow: hidden; }
        .content-area { 
            flex-grow: 1; 
            overflow: hidden; 
            padding: 0; 
            background-color: #000; 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            justify-content: center;
            position: relative;
        }
        .sidebar-area { 
            width: 360px; 
            flex-shrink: 0; 
            background-color: var(--sidebar-bg); 
            border-left: 1px solid #e2e8f0; 
            overflow-y: auto; 
            display: flex;
            flex-direction: column;
        }
        .lesson-item { 
            padding: 1rem 1.25rem; 
            border-bottom: 1px solid #f1f5f9; 
            cursor: pointer; 
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            color: #475569;
            text-decoration: none;
            display: block;
        }
        .lesson-item:hover { background-color: #f8fafc; color: var(--primary-color); }
        .lesson-item.active { 
            background-color: #f1f5f9; 
            border-left: 4px solid var(--primary-color); 
            color: var(--primary-color);
            font-weight: 600; 
        }
        .btn-premium {
            background: linear-gradient(135deg, #0d6efd 0%, #001f3f 100%);
            border: none;
            color: white;
            transition: transform 0.2s;
        }
        .btn-premium:hover {
            transform: scale(1.02);
            color: #fff;
        }
    </style>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <!-- HLS.js -->
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <!-- Plyr JS -->
    <script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
    
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
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body>

    <!-- Simple Header -->
    <header class="learning-header text-white px-4 py-3 d-flex justify-content-between align-items-center shadow-sm">
        <div class="d-flex align-items-center">
            <?php if (uri_string() !== 'student/dashboard'): ?>
                <a href="<?= base_url('student/dashboard') ?>" class="btn btn-sm btn-outline-light rounded-circle me-3 p-0 d-flex align-items-center justify-content-center" style="width:32px; height:32px;"><i class="bi bi-arrow-left"></i></a>
            <?php endif; ?>
            <div>
                <span class="text-white-50 small d-block" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px;">
                    <?= (uri_string() === 'student/dashboard') ? 'Learning Portal' : 'Course Viewer' ?>
                </span>
                <span class="fw-bold fs-5 line-clamp-1"><?= $this->renderSection('course_title') ?: 'Student Portal' ?></span>
            </div>
        </div>
        <div class="d-flex align-items-center">
            <?php if(session()->get('role') === 'admin'): ?>
                <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-warning btn-sm me-3 fw-bold rounded-pill px-3">Admin Panel</a>
            <?php endif; ?>
            <div class="d-none d-md-block me-3 text-end">
                <span class="small text-white-50 d-block" style="font-size: 0.7rem;">Signed in as</span>
                <span class="fw-medium"><?= session()->get('name') ?? 'Student' ?></span>
            </div>
            <a href="<?= base_url('logout') ?>" class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm">Logout</a>
        </div>
    </header>

    <!-- Learning Area -->
    <main class="learning-body">
        <?php if (isset($isViewer) && $isViewer): ?>
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
