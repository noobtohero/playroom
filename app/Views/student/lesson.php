<?= $this->extend('layouts/student') ?>

<?= $this->section('title') ?><?= esc($current_lesson['title']) ?> - <?= esc($course['title']) ?><?= $this->endSection() ?>

<?= $this->section('course_title') ?><?= esc($course['title']) ?><?= $this->endSection() ?>

<?= $this->section('player') ?>
<div class="position-relative w-100 h-100 d-flex align-items-center justify-content-center bg-black">
    <!-- Watermark Overlay -->
    <div class="watermark">
        <?= session()->get('email') ?> | <?= date('Y-m-d H:i') ?>
    </div>

    <?php if(!empty($current_lesson['external_url'])): ?>
        <?php 
            $url = $current_lesson['external_url'];
            $embedUrl = $url;
            if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
                // simple yt extract
                preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $url, $matches);
                if (isset($matches[1])) $embedUrl = "https://www.youtube.com/embed/" . $matches[1];
            }
        ?>
        <iframe src="<?= $embedUrl ?>" class="w-100 h-100" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    <?php elseif($current_lesson['type'] == 'video'): ?>
        <!-- Quality Selector -->
        <div id="quality-bar" style="position:absolute;top:12px;right:16px;z-index:20;display:none;">
            <select id="quality-select"
                style="background:rgba(0,0,0,0.65);color:#fff;border:1px solid rgba(255,255,255,0.3);
                       border-radius:6px;padding:4px 10px;font-size:0.82rem;cursor:pointer;backdrop-filter:blur(4px);">
                <option value="-1">🔁 Auto</option>
            </select>
        </div>

        <video id="video-player" controls autoplay class="w-100 h-100" style="max-height:100vh;">
            <p>Your browser does not support HTML5 video.</p>
        </video>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var video     = document.getElementById('video-player');
                var qualBar   = document.getElementById('quality-bar');
                var qualSel   = document.getElementById('quality-select');

                // ── Full path → supports master.m3u8 (multi-resolution) ────
                <?php
                    // Build the stream URL using full relative path stored in DB
                    // content_path = "uploads/lessons/{course}/{section}/[sub/]master.m3u8"
                    // We need only the part after "uploads/lessons/{course_id}/{section_id}/"
                    $prefix   = 'uploads/lessons/' . $course['id'] . '/' . $current_lesson['section_id'] . '/';
                    $m3u8Rel  = ltrim(str_replace($prefix, '', $current_lesson['content_path']), '/');
                ?>
                var videoSrc  = '<?= base_url('media/stream/video/' . $course['id'] . '/' . $current_lesson['section_id'] . '/' . $m3u8Rel) ?>';

                if (Hls.isSupported()) {
                    var hls = new Hls({ debug: false });
                    hls.loadSource(videoSrc);
                    hls.attachMedia(video);

                    hls.on(Hls.Events.MANIFEST_PARSED, function(event, data) {
                        // Autoplay
                        video.play().catch(function(){});

                        // Build quality options when manifest has multiple levels
                        if (data.levels && data.levels.length > 1) {
                            qualBar.style.display = 'block';

                            data.levels.forEach(function(level, index) {
                                var opt   = document.createElement('option');
                                opt.value = index;
                                // แสดงแค่ความสูงเช่น "480p" — เข้าใจง่าย
                                opt.text  = level.height ? level.height + 'p' : 'Level ' + (index + 1);
                                qualSel.appendChild(opt);
                            });
                        }
                    });

                    // Quality switch handler
                    qualSel.addEventListener('change', function() {
                        hls.currentLevel = parseInt(this.value);  // -1 = Auto ABR
                    });

                    // Sync selector when ABR switches level automatically
                    hls.on(Hls.Events.LEVEL_SWITCHED, function(event, data) {
                        if (parseInt(qualSel.value) === -1) return; // user chose Auto — don't override
                        qualSel.value = data.level;
                    });

                } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                    // Native HLS (Safari) — no JS quality control
                    video.src = videoSrc;
                    video.play().catch(function(){});
                }
            });
        </script>
    <?php elseif($current_lesson['type'] == 'slide'): ?>
        <div class="text-white text-center">
            <i class="bi bi-file-earmark-slides fs-1 mb-3 d-block"></i>
            <h4>Slide Content</h4>
            <p>Content Path: <?= esc($current_lesson['content_path']) ?></p>
            <a href="<?= base_url('uploads/lessons/' . $course['id'] . '/' . $current_lesson['section_id'] . '/' . $current_lesson['content_path']) ?>" target="_blank" class="btn btn-outline-light">View Slide (Static)</a>
        </div>
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
                var mdUrl = '<?= base_url('media/stream/video/' . $course['id'] . '/' . $current_lesson['section_id'] . '/' . $mdRel) ?>';

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
    <?php else: ?>
        <div class="text-white text-center">
            <i class="bi bi-mic fs-1 mb-3 d-block"></i>
            <h4>Audio Content</h4>
            <audio controls class="mt-3">
                <source src="<?= base_url('uploads/lessons/' . $course['id'] . '/' . $current_lesson['section_id'] . '/' . $current_lesson['content_path']) ?>" type="audio/mpeg">
            </audio>
        </div>
    <?php endif; ?>

    <!-- Download Button Overlay if allowed -->
    <?php if($current_lesson['is_downloadable'] && !empty($current_lesson['content_path'])): ?>
        <?php 
            $prefix   = 'uploads/lessons/' . $course['id'] . '/' . $current_lesson['section_id'] . '/';
            $fileRel  = ltrim(str_replace($prefix, '', $current_lesson['content_path']), '/');
            $downloadUrl = base_url('media/download/' . $course['id'] . '/' . $current_lesson['section_id'] . '/' . $fileRel);
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
</style>
<?= $this->endSection() ?>
