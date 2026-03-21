<?= $this->extend('layouts/student') ?>

<?= $this->section('title') ?><?= esc($current_lesson['title']) ?> - <?= esc($course['title']) ?><?= $this->endSection() ?>

<?= $this->section('course_title') ?><?= esc($course['title']) ?><?= $this->endSection() ?>

<?= $this->section('player') ?>
<div class="position-relative w-100 h-100 d-flex align-items-center justify-content-center bg-black">
    <!-- Watermark Overlay -->
    <!-- Watermark Overlay (Moved to Top Right and subtle) -->
    <div class="watermark" style="top: 15px; left: auto; right: 15px; opacity: 0.25;">
        <?= session()->get('email') ?> | <?= date('Y-m-d H:i') ?>
    </div>

    <style>
        /* Force Plyr to be Huge and Fill Container */
        .plyr {
            width: 100% !important;
            height: 100% !important;
            max-width: none !important;
        }
        .plyr--video, .plyr__video-wrapper {
            height: 100% !important;
            background: #000 !important;
        }
        .plyr__video-wrapper video,
        .plyr__video-wrapper iframe {
            object-fit: contain !important;
        }
        /* Aspect Ratio for Responsive Embed (YouTube) handled by Plyr but we force it to expand */
        .plyr__video-embed {
            padding-bottom: 0 !important; /* Remove fixed ratio padding */
            height: 100% !important;
        }
    </style>

    <?php
        $nextLessonUrl = null;
        $foundCurrent = false;
        foreach($sections as $s) {
            foreach($s['lessons'] as $l) {
                if ($foundCurrent) {
                    $nextLessonUrl = base_url('student/course/' . $course['id'] . '/lesson/' . $l['id']);
                    break 2;
                }
                if ($l['id'] == $current_lesson['id']) {
                    $foundCurrent = true;
                }
            }
        }
    ?>

    <?php if(!empty($current_lesson['external_url'])): ?>
        <?php 
            $url = $current_lesson['external_url'];
            $embedUrl = $url;
            if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
                preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $url, $matches);
                if (isset($matches[1])) $embedUrl = "https://www.youtube.com/embed/" . $matches[1] . "?origin=" . base_url() . "&amp;iv_load_policy=3&amp;modestbranding=1&amp;playsinline=1&amp;showinfo=0&amp;rel=0&amp;enablejsapi=1";
            }
        ?>
        <div id="youtube-player" class="plyr__video-embed w-100" style="height: 100%;">
            <iframe
                src="<?= $embedUrl ?>"
                allowfullscreen
                allowtransparency
                allow="autoplay"
            ></iframe>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const player = new Plyr('#youtube-player', {
                    controls: ['play-large', 'play', 'progress', 'current-time', 'mute', 'volume', 'fullscreen'],
                    youtube: { noCookie: true, rel: 0, showinfo: 0, iv_load_policy: 3, modestbranding: 1 }
                });
                player.on('ended', () => {
                    if (nextLessonUrl) window.location.href = nextLessonUrl;
                });
            });
        </script>
    <?php elseif($current_lesson['type'] == 'video'): ?>
        <!-- Quality Selector -->
        <div id="quality-bar" style="position:absolute;top:12px;right:16px;z-index:20;display:none;">
            <select id="quality-select"
                style="background:rgba(0,0,0,0.65);color:#fff;border:1px solid rgba(255,255,255,0.3);
                       border-radius:6px;padding:4px 10px;font-size:0.82rem;cursor:pointer;backdrop-filter:blur(4px);">
                <option value="-1">🔁 Auto</option>
            </select>
        </div>

        <video id="video-player" playsinline controls class="w-100 h-100" style="object-fit: contain;"></video>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const video     = document.getElementById('video-player');
                const qualBar   = document.getElementById('quality-bar');
                const qualSel   = document.getElementById('quality-select');
                
                // Initialize Plyr
                const player = new Plyr(video, {
                    controls: ['play-large', 'play', 'progress', 'current-time', 'mute', 'volume', 'settings', 'fullscreen'],
                    settings: ['quality', 'speed'],
                    quality: { default: 576, options: [1080, 720, 576, 480, 360, 240] }
                });

                <?php
                    $prefix   = 'uploads/lessons/' . $course['id'] . '/' . $current_lesson['section_id'] . '/';
                    $m3u8Rel  = ltrim(str_replace($prefix, '', $current_lesson['content_path']), '/');
                ?>
                var videoSrc  = '<?= \App\Helpers\UrlSignerHelper::sign(base_url('media/stream/video/' . $course['id'] . '/' . $current_lesson['section_id'] . '/' . $m3u8Rel)) ?>';
                var nextLessonUrl = '<?= $nextLessonUrl ?>';

                if (Hls.isSupported()) {
                    var hls = new Hls({ debug: false });
                    hls.loadSource(videoSrc);
                    hls.attachMedia(video);

                    hls.on(Hls.Events.MANIFEST_PARSED, function(event, data) {
                        video.play().catch(function(){});
                        if (data.levels && data.levels.length > 1) {
                            qualBar.style.display = 'block';
                            data.levels.forEach(function(level, index) {
                                var opt   = document.createElement('option');
                                opt.value = index;
                                opt.text  = level.height ? level.height + 'p' : 'Level ' + (index + 1);
                                qualSel.appendChild(opt);
                            });
                        }
                    });

                    qualSel.addEventListener('change', function() {
                        hls.currentLevel = parseInt(this.value);
                    });
                } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                    video.src = videoSrc;
                    video.play().catch(function(){});
                }

                player.on('ended', () => {
                    if (nextLessonUrl) window.location.href = nextLessonUrl;
                });
            });
        </script>
    <?php elseif($current_lesson['type'] == 'slide'): ?>
        <?php 
             $prefix   = 'uploads/lessons/' . $course['id'] . '/' . $current_lesson['section_id'] . '/';
             $fileRel  = ltrim(str_replace($prefix, '', $current_lesson['content_path']), '/');
             $pdfUrl   = \App\Helpers\UrlSignerHelper::sign(base_url('media/stream/video/' . $course['id'] . '/' . $current_lesson['section_id'] . '/' . $fileRel));
        ?>
        <div class="w-100 h-100 bg-black d-flex flex-column position-relative overflow-hidden">
            <!-- Glassmorphism Floating Toolbar (Top) -->
            <div class="position-absolute top-0 start-50 translate-middle-x mt-3 d-flex align-items-center bg-dark bg-opacity-75 backdrop-blur border border-white border-opacity-10 rounded-pill px-3 py-2 text-white shadow-lg" style="z-index: 100; backdrop-filter: blur(8px);">
                <div class="btn-group me-2">
                    <button class="btn btn-link text-white p-0 me-3" id="pdf-prev" title="Previous (Left Arrow)"><i class="bi bi-arrow-left-circle fs-4"></i></button>
                    <button class="btn btn-link text-white p-0" id="pdf-next" title="Next (Right Arrow)"><i class="bi bi-arrow-right-circle fs-4"></i></button>
                </div>
                <div class="vr mx-2 opacity-25"></div>
                <div class="px-2 d-flex align-items-center gap-1">
                    <span class="small fw-medium text-white-50">Slide</span>
                    <input type="number" id="pdf-page-input" value="1" min="1" 
                           class="form-control form-control-sm bg-white bg-opacity-10 border border-white border-opacity-10 text-primary text-center fw-bold p-0" 
                           style="width: 48px; height: 28px; border-radius: 6px; box-shadow: none; font-size: 0.95rem; appearance: textfield; -moz-appearance: textfield;">
                    <span class="small opacity-25">/</span>
                    <span id="pdf-page-count" class="small fw-medium text-white-50">0</span>
                </div>
                <div class="vr mx-2 opacity-25"></div>
                <div class="btn-group ms-2">
                    <button class="btn btn-link text-white p-0 me-3" id="pdf-zoom-out"><i class="bi bi-dash-circle"></i></button>
                    <button class="btn btn-link text-white p-0" id="pdf-zoom-in"><i class="bi bi-plus-circle"></i></button>
                </div>
            </div>

            <!-- PDF Scroll Container -->
            <div class="flex-grow-1 overflow-auto d-flex align-items-start justify-content-center p-5 pt-5" id="pdf-scroll-container" style="scroll-behavior: smooth; background: radial-gradient(circle at center, #1e293b 0%, #000 100%);">
                <div class="position-relative">
                    <canvas id="pdf-canvas" class="shadow-2xl rounded-1 transition-all" style="max-width: 100%; height: auto;"></canvas>
                    <div id="pdf-loader" class="position-absolute top-50 start-50 translate-middle text-center">
                        <div class="spinner-grow text-primary" role="status"></div>
                        <div class="mt-2 text-white-50 small">Preparing Slides...</div>
                    </div>
                </div>
            </div>

            <!-- Custom Next Lesson Toast (Hidden initially) -->
            <div id="next-lesson-toast" class="position-absolute bottom-0 start-50 translate-middle-x mb-4 bg-primary text-white px-4 py-2 rounded-pill shadow-lg transition-all" style="display:none; z-index: 200; transform: translateY(100px); opacity: 0;">
                <i class="bi bi-rocket-takeoff me-2"></i> Last slide reached! Moving to next lesson...
            </div>
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
        <script>
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
            (function() {
                let pdfDoc = null, pageNum = 1, pageRendering = false, pageNumPending = null, scale = 1.5,
                    canvas = document.getElementById('pdf-canvas'), ctx = canvas.getContext('2d'),
                    pageInput = document.getElementById('pdf-page-input'),
                    nextLessonUrl = '<?= $nextLessonUrl ?>';

                function renderPage(num) {
                    pageRendering = true;
                    pdfDoc.getPage(num).then((page) => {
                        const viewport = page.getViewport({ scale: scale });
                        canvas.height = viewport.height;
                        canvas.width = viewport.width;
                        const renderContext = { canvasContext: ctx, viewport: viewport };
                        page.render(renderContext).promise.then(() => {
                            pageRendering = false;
                            if (pageNumPending !== null) { renderPage(pageNumPending); pageNumPending = null; }
                        });
                    });
                    pageInput.value = num;
                }

                function queueRenderPage(num) {
                    if (pageRendering) pageNumPending = num; else renderPage(num);
                }

                function handleNext() {
                    if (pageNum >= pdfDoc.numPages) {
                        if (nextLessonUrl) {
                            const toast = document.getElementById('next-lesson-toast');
                            toast.style.display = 'block';
                            setTimeout(() => {
                                toast.style.transform = 'translate(-50%, -20px)';
                                toast.style.opacity = '1';
                            }, 10);
                            setTimeout(() => { window.location.href = nextLessonUrl; }, 1500);
                        }
                        return;
                    }
                    pageNum++;
                    queueRenderPage(pageNum);
                    document.getElementById('pdf-scroll-container').scrollTop = 0;
                }

                function handlePrev() {
                    if (pageNum <= 1) return;
                    pageNum--;
                    queueRenderPage(pageNum);
                    document.getElementById('pdf-scroll-container').scrollTop = 0;
                }

                pdfjsLib.getDocument('<?= $pdfUrl ?>').promise.then((pdfDoc_) => {
                    pdfDoc = pdfDoc_;
                    document.getElementById('pdf-page-count').textContent = pdfDoc.numPages;
                    pageInput.max = pdfDoc.numPages;
                    document.getElementById('pdf-loader').style.display = 'none';
                    renderPage(pageNum);
                });

                document.getElementById('pdf-prev').onclick = handlePrev;
                document.getElementById('pdf-next').onclick = handleNext;
                document.getElementById('pdf-zoom-in').onclick = () => { scale += 0.25; queueRenderPage(pageNum); };
                document.getElementById('pdf-zoom-out').onclick = () => { if (scale <= 0.5) return; scale -= 0.25; queueRenderPage(pageNum); };

                pageInput.onchange = () => {
                    let num = parseInt(pageInput.value);
                    if (isNaN(num) || num < 1) num = 1;
                    if (num > pdfDoc.numPages) num = pdfDoc.numPages;
                    pageNum = num;
                    queueRenderPage(pageNum);
                };
                pageInput.onkeydown = (e) => { e.stopPropagation(); };

                // Keyboard Shortcuts
                window.addEventListener('keydown', (e) => {
                    if (e.key === 'ArrowRight' || e.key === ' ' || e.key === 'PageDown') { e.preventDefault(); handleNext(); }
                    if (e.key === 'ArrowLeft' || e.key === 'PageUp') { e.preventDefault(); handlePrev(); }
                });
            })();
        </script>
    <?php elseif($current_lesson['type'] == 'markdown'): ?>
        <!-- Markdown Viewer -->
        <div id="md-viewer" style="width:100%;height:100%;overflow-y:auto;background:#1e1e2e;color:#cdd6f4;padding:2rem 2.5rem;">
            <div id="md-content" style="max-width:820px;margin:0 auto;line-height:1.8;font-size:1rem;"></div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                <?php
                    $prefix  = 'uploads/lessons/' . $course['id'] . '/' . $current_lesson['section_id'] . '/';
                    $mdRel   = ltrim(str_replace($prefix, '', $current_lesson['content_path']), '/');
                ?>
                var mdUrl = '<?= \App\Helpers\UrlSignerHelper::sign(base_url('media/stream/video/' . $course['id'] . '/' . $current_lesson['section_id'] . '/' . $mdRel)) ?>';

                fetch(mdUrl, { credentials: 'same-origin' })
                    .then(function(r) {
                        if (!r.ok) throw new Error('Cannot load markdown: ' + r.status);
                        return r.text();
                    })
                    .then(function(text) {
                        document.getElementById('md-content').innerHTML = marked.parse(text);
                    })
                    .catch(function(e) {
                        document.getElementById('md-content').innerHTML =
                            '<p style="color:#f38ba8">โหลดเนื้อหาไม่สำเร็จ: ' + e.message + '</p>';
                    });
            });
        </script>
        <style>
            /* Markdown typography inside dark viewer */
            #md-content h1,#md-content h2 { border-bottom:1px solid #45475a; padding-bottom:.4em; margin-bottom:1em; }
            #md-content h1 { font-size:1.8rem; color:#cba6f7; }
            #md-content h2 { font-size:1.4rem; color:#89b4fa; }
            #md-content h3 { font-size:1.1rem; color:#94e2d5; }
            #md-content a  { color:#89dceb; }
            #md-content code { background:#313244; padding:2px 6px; border-radius:4px; font-size:.88em; }
            #md-content pre  { background:#313244; padding:1rem; border-radius:8px; overflow-x:auto; }
            #md-content blockquote { border-left:3px solid #cba6f7; padding-left:1rem; color:#a6adc8; }
            #md-content table  { border-collapse:collapse; width:100%; }
            #md-content td,#md-content th { border:1px solid #45475a; padding:6px 12px; }
            #md-content th { background:#313244; }
        </style>
    <?php elseif($current_lesson['type'] == 'podcast'): ?>
        <div class="text-white text-center p-5">
            <i class="bi bi-mic fs-1 mb-3 d-block"></i>
            <h4>Audio Content</h4>
            <?php 
                 $prefix   = 'uploads/lessons/' . $course['id'] . '/' . $current_lesson['section_id'] . '/';
                 $fileRel  = ltrim(str_replace($prefix, '', $current_lesson['content_path']), '/');
                 $audioUrl = \App\Helpers\UrlSignerHelper::sign(base_url('media/stream/video/' . $course['id'] . '/' . $current_lesson['section_id'] . '/' . $fileRel));
            ?>
            <audio id="audio-player" controls class="mt-3">
                <source src="<?= $audioUrl ?>" type="audio/mpeg">
            </audio>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const player = new Plyr('#audio-player', {
                    controls: ['play', 'progress', 'current-time', 'mute', 'volume']
                });
                player.on('ended', () => {
                    if (nextLessonUrl) window.location.href = nextLessonUrl;
                });
            });
        </script>
    <?php endif; ?>

    <!-- Download Button Overlay if allowed -->
    <?php if($current_lesson['is_downloadable'] && !empty($current_lesson['content_path'])): ?>
        <?php 
            $prefix   = 'uploads/lessons/' . $course['id'] . '/' . $current_lesson['section_id'] . '/';
            $fileRel  = ltrim(str_replace($prefix, '', $current_lesson['content_path']), '/');
            $rawDownloadUrl = base_url('media/download/' . $course['id'] . '/' . $current_lesson['section_id'] . '/' . $fileRel);
            $downloadUrl = \App\Helpers\UrlSignerHelper::sign($rawDownloadUrl);
        ?>
        <div style="position:absolute;bottom:20px;right:20px;z-index:30;">
            <a href="<?= $downloadUrl ?>" class="btn btn-sm btn-light shadow-sm">
                <i class="bi bi-download"></i> Download Content
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Bottom Info (Optional: if we want to show description below player in a scrollable area, 
we'd need to adjust the layout. For now, let's keep it simple.) -->
<?= $this->endSection() ?>

<?= $this->section('sidebar_content') ?>
<div class="accordion accordion-flush" id="sidebarAccordion">
    <?php foreach($sections as $index => $section): ?>
        <?php $isCurrentSection = ($current_lesson['section_id'] == $section['id']); ?>
        <div class="accordion-item border-bottom">
            <h2 class="accordion-header" id="sidebarHeading<?= $section['id'] ?>">
                <button class="accordion-button <?= !$isCurrentSection ? 'collapsed' : '' ?> bg-light rounded-0 py-3" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarCollapse<?= $section['id'] ?>" aria-expanded="<?= $isCurrentSection ? 'true' : 'false' ?>" aria-controls="sidebarCollapse<?= $section['id'] ?>">
                    <div class="w-100 me-2">
                        <div class="small text-muted mb-1 fw-bold text-uppercase">Section <?= $index + 1 ?></div>
                        <div class="fw-semibold text-dark lh-sm"><?= esc($section['title']) ?></div>
                    </div>
                </button>
            </h2>
            <div id="sidebarCollapse<?= $section['id'] ?>" class="accordion-collapse collapse <?= $isCurrentSection ? 'show' : '' ?>" aria-labelledby="sidebarHeading<?= $section['id'] ?>">
                <div class="accordion-body p-0">
                    <div class="list-group list-group-flush">
                        <?php foreach($section['lessons'] as $lesson): ?>
                            <?php $isActive = ($current_lesson['id'] == $lesson['id']); ?>
                            <a href="<?= base_url('student/course/' . $course['id'] . '/lesson/' . $lesson['id']) ?>" 
                               class="list-group-item list-group-item-action d-flex align-items-center py-3 px-3 border-0 transition-all <?= $isActive ? 'bg-primary text-white active' : '' ?>">
                                
                                <span class="me-3">
                                    <?php if($lesson['type'] == 'video'): ?>
                                        <i class="bi bi-play-circle-fill fs-5"></i>
                                    <?php elseif($lesson['type'] == 'slide'): ?>
                                        <i class="bi bi-file-earmark-slides-fill fs-5"></i>
                                    <?php elseif($lesson['type'] == 'markdown'): ?>
                                        <i class="bi bi-file-earmark-text-fill fs-5"></i>
                                    <?php elseif($lesson['type'] == 'podcast'): ?>
                                        <i class="bi bi-mic-fill fs-5"></i>
                                    <?php else: ?>
                                        <i class="bi bi-file-fill fs-5"></i>
                                    <?php endif; ?>
                                </span>
                                
                                <div class="flex-grow-1">
                                    <div class="small fw-semibold lh-sm"><?= esc($lesson['title']) ?></div>
                                    <div class="x-small opacity-75 mt-1">
                                        <?php if($lesson['duration']): ?>
                                            <?= floor($lesson['duration'] / 60) ?>m <?= $lesson['duration'] % 60 ?>s
                                        <?php else: ?>
                                            Lesson
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <?php if($isActive): ?>
                                    <i class="bi bi-play-fill ms-2"></i>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<style>
    .x-small { font-size: 0.75rem; }
    .transition-all { transition: all 0.2s ease; }
    .list-group-item.active { border-left: 4px solid #fff; }

    /* Hide Spinners (Up/Down buttons) for PDF Page Input */
    input#pdf-page-input::-webkit-outer-spin-button,
    input#pdf-page-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input#pdf-page-input[type=number] {
        -moz-appearance: textfield;
    }
</style>
<?= $this->endSection() ?>
