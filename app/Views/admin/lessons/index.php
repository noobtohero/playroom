<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Lessons for <?= esc($section['title']) ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
<style>
    :root { --plyr-color-main: #198754; } 
    .plyr { border-radius: 8px; overflow: hidden; }
    .modal-body .plyr { width: 100%; }
</style>
<?= $this->endSection() ?>

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
                                    <?php if($l['content_path'] || $l['external_url']): ?>
                                        <?php 
                                            if (!empty($l['external_url'])) {
                                                $previewUrl = $l['external_url'];
                                            } else {
                                                $prefix = 'uploads/lessons/' . $course['id'] . '/' . $l['section_id'] . '/';
                                                $fileName = ltrim(str_replace($prefix, '', $l['content_path']), '/');
                                                $rawUrl = base_url('media/stream/video/' . $course['id'] . '/' . $l['section_id'] . '/' . $fileName);
                                                $previewUrl = \App\Helpers\UrlSignerHelper::sign($rawUrl);
                                            }
                                        ?>
                                        <button type="button" class="btn btn-outline-success preview-btn" 
                                                data-url="<?= $previewUrl ?>" 
                                                data-type="<?= $l['type'] ?>"
                                                data-title="<?= esc($l['title']) ?>"
                                                data-is-external="<?= !empty($l['external_url']) ? '1' : '0' ?>"
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
</div>

<!-- Preview Modal Scripts -->
<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
<script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
        const container = document.getElementById('preview-container');
        const modalLabel = document.getElementById('previewModalLabel');
        const resBadge = document.getElementById('res-badge');
        const resText = document.getElementById('res-text');
        let hlsInstance = null;
        let plyrInstance = null;

        document.querySelectorAll('.preview-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const url = this.getAttribute('data-url');
                const type = this.getAttribute('data-type');
                const title = this.getAttribute('data-title');
                const isExternal = this.getAttribute('data-is-external') === '1';

                modalLabel.textContent = 'Preview: ' + title;
                container.innerHTML = ''; 
                resBadge.style.display = 'none';

                if (isExternal && type === 'video') {
                    let videoId = url;
                    if (url.includes('youtube.com') || url.includes('youtu.be')) {
                        const ytMatch = url.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i);
                        if (ytMatch) videoId = ytMatch[1];
                    }
                    container.innerHTML = `<div class="plyr__video-embed" id="admin-plyr-yt">
                        <iframe src="https://www.youtube.com/embed/${videoId}?origin=${window.location.origin}&iv_load_policy=3&modestbranding=1&playsinline=1&showinfo=0&rel=0&enablejsapi=1" allowfullscreen allowtransparency allow="autoplay"></iframe>
                    </div>`;
                    plyrInstance = new Plyr('#admin-plyr-yt');
                } else if (type === 'video') {
                    const video = document.createElement('video');
                    video.className = 'w-100';
                    video.playsinline = true;
                    video.controls = true;
                    container.appendChild(video);

                    plyrInstance = new Plyr(video);

                    if (Hls.isSupported()) {
                        hlsInstance = new Hls();
                        hlsInstance.loadSource(url);
                        hlsInstance.attachMedia(video);
                    } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                        video.src = url;
                    }
                } else if (type === 'slide') {
                    container.innerHTML = `
                        <div class="pdf-viewer-container bg-dark position-relative rounded overflow-hidden" style="height: 600px;">
                            <div class="pdf-toolbar d-flex justify-content-between align-items-center p-2 bg-dark border-bottom border-secondary text-white">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-light" id="pdf-prev"><i class="bi bi-chevron-left"></i></button>
                                    <button class="btn btn-outline-light" id="pdf-next"><i class="bi bi-chevron-right"></i></button>
                                </div>
                                <span class="small">Page: <span id="pdf-page-num">0</span> / <span id="pdf-page-count">0</span></span>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-light" id="pdf-zoom-out"><i class="bi bi-dash-lg"></i></button>
                                    <button class="btn btn-outline-light" id="pdf-zoom-in"><i class="bi bi-plus-lg"></i></button>
                                </div>
                            </div>
                            <div class="pdf-canvas-wrapper overflow-auto" style="height: calc(100% - 45px);">
                                <canvas id="pdf-canvas" class="d-block mx-auto"></canvas>
                                <div id="pdf-loader" class="position-absolute top-50 start-50 translate-middle text-center">
                                    <div class="spinner-border text-primary" role="status"></div>
                                    <div class="mt-2 text-white small">Loading Slide...</div>
                                </div>
                            </div>
                        </div>
                    `;
                    initPDFViewer(url);
                } else {
                    const audio = document.createElement('audio');
                    audio.className = 'w-100 p-4';
                    audio.controls = true;
                    audio.src = url;
                    container.appendChild(audio);
                    plyrInstance = new Plyr(audio);
                }
                previewModal.show();
            });
        });

        document.getElementById('previewModal').addEventListener('hidden.bs.modal', function () {
            if (plyrInstance) { plyrInstance.destroy(); plyrInstance = null; }
            if (hlsInstance) { hlsInstance.destroy(); hlsInstance = null; }
            container.innerHTML = '';
            resBadge.style.display = 'none';
        });
    });
</script>
    <!-- PDF.js library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        let pdfDoc = null,
            pageNum = 1,
            pageRendering = false,
            pageNumPending = null,
            scale = 1.5,
            canvas = null,
            ctx = null;

        function renderPage(num) {
            pageRendering = true;
            pdfDoc.getPage(num).then((page) => {
                const viewport = page.getViewport({ scale: scale });
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport,
                };
                const renderTask = page.render(renderContext);

                renderTask.promise.then(() => {
                    pageRendering = false;
                    if (pageNumPending !== null) {
                        renderPage(pageNumPending);
                        pageNumPending = null;
                    }
                });
            });
            document.getElementById('pdf-page-num').textContent = num;
        }

        function queueRenderPage(num) {
            if (pageRendering) {
                pageNumPending = num;
            } else {
                renderPage(num);
            }
        }

        function initPDFViewer(url) {
            canvas = document.getElementById('pdf-canvas');
            ctx = canvas.getContext('2d');
            document.getElementById('pdf-loader').style.display = 'block';

            pdfjsLib.getDocument(url).promise.then((pdfDoc_) => {
                pdfDoc = pdfDoc_;
                document.getElementById('pdf-page-count').textContent = pdfDoc.numPages;
                document.getElementById('pdf-loader').style.display = 'none';
                renderPage(pageNum);
            });

            document.getElementById('pdf-prev').addEventListener('click', () => {
                if (pageNum <= 1) return;
                pageNum--;
                queueRenderPage(pageNum);
            });
            document.getElementById('pdf-next').addEventListener('click', () => {
                if (pageNum >= pdfDoc.numPages) return;
                pageNum++;
                queueRenderPage(pageNum);
            });
            document.getElementById('pdf-zoom-in').addEventListener('click', () => {
                scale += 0.25;
                queueRenderPage(pageNum);
            });
            document.getElementById('pdf-zoom-out').addEventListener('click', () => {
                if (scale <= 0.5) return;
                scale -= 0.25;
                queueRenderPage(pageNum);
            });
        }
    </script>
</div>
<?= $this->endSection() ?>
