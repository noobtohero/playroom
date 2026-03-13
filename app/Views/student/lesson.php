<?= $this->extend('layouts/student') ?>

<?= $this->section('title') ?><?= esc($current_lesson['title']) ?> - <?= esc($course['title']) ?><?= $this->endSection() ?>

<?= $this->section('course_title') ?><?= esc($course['title']) ?><?= $this->endSection() ?>

<?= $this->section('player') ?>
<div class="position-relative w-100 h-100 d-flex align-items-center justify-content-center bg-black">
    <!-- Watermark Overlay -->
    <div class="watermark">
        <?= session()->get('email') ?> | <?= date('Y-m-d H:i') ?>
    </div>

    <?php if($current_lesson['type'] == 'video'): ?>
        <video id="video-player" controls class="w-100 h-100" style="max-height: 100vh;">
            <p>Your browser does not support HTML5 video.</p>
        </video>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var video = document.getElementById('video-player');
                // Path to m3u8 via StreamController
                var videoSrc = '<?= base_url('student/stream/video/' . $course['id'] . '/' . $current_lesson['section_id'] . '/' . basename($current_lesson['content_path'])) ?>';
                
                if (Hls.isSupported()) {
                    var hls = new Hls({
                        // Basic security: don't allow debug in production
                        debug: false,
                    });
                    hls.loadSource(videoSrc);
                    hls.attachMedia(video);
                    hls.on(Hls.Events.MANIFEST_PARSED, function() {
                        // video.play(); // Auto-play might be blocked by browser
                    });
                } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                    video.src = videoSrc;
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
    <?php else: ?>
        <div class="text-white text-center">
            <i class="bi bi-mic fs-1 mb-3 d-block"></i>
            <h4>Audio Content</h4>
            <audio controls class="mt-3">
                <source src="<?= base_url('uploads/lessons/' . $course['id'] . '/' . $current_lesson['section_id'] . '/' . $current_lesson['content_path']) ?>" type="audio/mpeg">
            </audio>
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
                                    <?php else: ?>
                                        <i class="bi bi-mic-fill fs-5"></i>
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
