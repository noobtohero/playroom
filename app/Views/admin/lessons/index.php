<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Lessons for <?= esc($section['title']) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Lessons in: <?= esc($section['title']) ?></h1>
    <div>
        <a href="<?= base_url('admin/courses/' . $course['id'] . '/sections') ?>" class="btn btn-sm btn-outline-secondary me-2">
            <i class="bi bi-arrow-left"></i> Back to Sections
        </a>
        <a href="<?= base_url('admin/sections/' . $section['id'] . '/lessons/create') ?>" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle"></i> Add New Lesson
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th scope="col" width="60">Order</th>
                        <th scope="col">Title</th>
                        <th scope="col">Type</th>
                        <th scope="col">Duration</th>
                        <th scope="col">Status</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($lessons)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">No lessons added yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($lessons as $l): ?>
                        <tr>
                            <td class="text-center"><?= $l['sort_order'] ?></td>
                            <td class="fw-bold">
                                <?= esc($l['title']) ?>
                                <?php if($l['content_path']): ?>
                                    <br><small class="text-muted"><i class="bi bi-paperclip"></i> Has Content</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($l['type'] == 'video'): ?>
                                    <span class="badge bg-primary"><i class="bi bi-play-btn me-1"></i>Video</span>
                                <?php elseif($l['type'] == 'slide'): ?>
                                    <span class="badge bg-info text-dark"><i class="bi bi-file-earmark-slides me-1"></i>Slide</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><i class="bi bi-mic me-1"></i>Podcast</span>
                                <?php endif; ?>
                            </td>
                            <td><?= floor($l['duration'] / 60) ?>m <?= $l['duration'] % 60 ?>s</td>
                            <td>
                                <?php if($l['status'] == 'published'): ?>
                                    <span class="badge bg-success">Published</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <?php if($l['content_path']): ?>
                                        <button type="button" class="btn btn-outline-success preview-btn" 
                                                data-url="<?= base_url('media/stream/video/' . $course['id'] . '/' . $l['section_id'] . '/' . basename($l['content_path'])) ?>" 
                                                data-type="<?= $l['type'] ?>"
                                                data-title="<?= esc($l['title']) ?>"
                                                title="Preview"><i class="bi bi-eye"></i></button>
                                    <?php endif; ?>
                                    <a href="<?= base_url('admin/lessons/edit/' . $l['id']) ?>" class="btn btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <a href="<?= base_url('admin/lessons/delete/' . $l['id']) ?>" class="btn btn-outline-danger" onclick="return confirm('Delete this lesson completely?')" title="Delete"><i class="bi bi-trash"></i></a>
                                </div>
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

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-dark text-white shadow-lg">
            <div class="modal-header border-secondary">
                <h5 class="modal-title" id="previewModalLabel">Lesson Preview</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 bg-black d-flex align-items-center justify-content-center" style="min-height: 480px; position: relative;">
                <div id="preview-container" class="w-100">
                    <!-- Content injected by JS -->
                </div>
                <!-- Resolution Indicator (Hidden by default, shown by JS) -->
                <div id="res-badge" class="position-absolute top-0 end-0 m-3 badge bg-opacity-50 bg-dark border border-secondary" style="display:none; z-index: 10;">
                    <i class="bi bi-hd"></i> <span id="res-text">Multi-bitrate</span>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/hls.js@1.5.20/dist/hls.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
        const container = document.getElementById('preview-container');
        const modalLabel = document.getElementById('previewModalLabel');
        const resBadge = document.getElementById('res-badge');
        const resText = document.getElementById('res-text');
        let hlsInstance = null;

        document.querySelectorAll('.preview-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const url = this.getAttribute('data-url');
                const type = this.getAttribute('data-type');
                const title = this.getAttribute('data-title');

                modalLabel.textContent = 'Preview: ' + title;
                container.innerHTML = ''; // Clear previous
                resBadge.style.display = 'none';

                if (type === 'video') {
                    const video = document.createElement('video');
                    video.className = 'w-100';
                    video.controls = true;
                    video.id = 'preview-video';
                    container.appendChild(video);

                    if (Hls.isSupported()) {
                        hlsInstance = new Hls();
                        hlsInstance.loadSource(url);
                        hlsInstance.attachMedia(video);
                        hlsInstance.on(Hls.Events.MANIFEST_PARSED, function(event, data) {
                            const levels = hlsInstance.levels;
                            if (levels && levels.length > 1) {
                                resBadge.style.display = 'block';
                                resText.textContent = levels.length + ' Resolutions';
                            } else if (levels && levels.length === 1) {
                                resBadge.style.display = 'block';
                                resText.textContent = levels[0].height + 'p';
                            }
                        });
                    } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                        video.src = url;
                    }
                } else if (type === 'slide') {
                    container.innerHTML = `<div class="p-5 text-center"><i class="bi bi-file-earmark-slides fs-1 mb-3 d-block"></i><h4>PDF/Slide Preview</h4><p class="text-white-50">Direct preview for slide files is coming soon in Phase 2. <br>Path: ${url}</p></div>`;
                } else {
                    const audio = document.createElement('audio');
                    audio.className = 'w-100 p-4';
                    audio.controls = true;
                    audio.src = url;
                    container.appendChild(audio);
                }

                previewModal.show();
            });
        });

        // Cleanup on hidden
        document.getElementById('previewModal').addEventListener('hidden.bs.modal', function () {
            if (hlsInstance) {
                hlsInstance.destroy();
                hlsInstance = null;
            }
            container.innerHTML = '';
            resBadge.style.display = 'none';
        });
    });
</script>
<?= $this->endSection() ?>
