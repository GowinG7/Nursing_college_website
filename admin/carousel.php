<?php
$page_title = "Carousel";
require '../includes/db.php';
require 'includes/auth.php';

$result = $conn->query("SELECT * FROM carousel ORDER BY id DESC");
include 'includes/header.php';
?>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Carousel Slides</h5>
        <a href="carousel-add.php" class="btn btn-success">
            <i class="bi bi-plus-circle me-1"></i> Add Slide
        </a>
    </div>

    <div class="card-body">

        <?php if ($result && $result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th width="120">Preview</th>
                            <th width="100">Type</th>
                            <th>Title</th>
                            <th width="180">Created</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <?php if ($row['media_type'] == 'image'): ?>
                                        <img src="../<?= htmlspecialchars($row['media_file']) ?>"
                                            class="img-thumbnail preview-image" data-bs-toggle="modal"
                                            data-bs-target="#imagePreviewModal"
                                            data-src="../<?= htmlspecialchars($row['media_file']) ?>"
                                            alt="<?= htmlspecialchars($row['title']) ?>"
                                            style="width:100px;height:70px;object-fit:cover;cursor:pointer;">
                                    <?php else: ?>
                                        <div class="video-thumb" data-bs-toggle="modal" data-bs-target="#videoPreviewModal"
                                            data-src="../<?= htmlspecialchars($row['media_file']) ?>"
                                            style="cursor:pointer;position:relative;width:100px;height:70px;">
                                            <video muted preload="metadata"
                                                style="width:100px;height:70px;object-fit:cover;border-radius:4px;">
                                                <source src="../<?= htmlspecialchars($row['media_file']) ?>">
                                            </video>
                                            <div
                                                style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.25);color:#fff;font-size:24px;">
                                                ▶</div>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge bg-primary"><?= ucfirst($row['media_type']) ?></span></td>
                                <td>
                                    <strong><?= htmlspecialchars($row['title']) ?></strong>
                                    <?php if (!empty($row['subtitle'])): ?>
                                        <br><small class="text-muted">
                                            <?= htmlspecialchars(substr($row['subtitle'], 0, 80)) ?>
                                            <?= strlen($row['subtitle']) > 80 ? '...' : '' ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                                <td>
                                    <a href="carousel-edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-success"><i
                                            class="bi bi-pencil"></i></a>
                                    <a href="carousel-delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Delete this slide?');"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-images fs-1 text-muted"></i>
                <h5 class="mt-3">No Carousel Slides Found</h5>
                <p class="text-muted">Add your first image or video slide.</p>
                <a href="carousel-add.php" class="btn btn-success">Add Slide</a>
            </div>
        <?php endif; ?>
    </div>
</div> <!-- ✅ card closed here -->

<!-- IMAGE MODAL -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-dark border-0">
            <div class="modal-header border-0">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalPreviewImage" src="" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</div>

<!-- VIDEO MODAL -->
<div class="modal fade" id="videoPreviewModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-dark border-0">
            <div class="modal-header border-0">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <video id="modalPreviewVideo" controls autoplay style="width:100%;border-radius:10px;">
                    <source id="modalVideoSource" src="">
                </video>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.preview-image').forEach(function (img) {
            img.addEventListener('click', function () {
                document.getElementById('modalPreviewImage').src = this.dataset.src;
            });
        });

        document.querySelectorAll('.video-thumb').forEach(function (video) {
            video.addEventListener('click', function () {
                let source = document.getElementById('modalVideoSource');
                source.src = this.dataset.src;
                let player = document.getElementById('modalPreviewVideo');
                player.load();
            });
        });

        document.getElementById('videoPreviewModal')
            .addEventListener('hidden.bs.modal', function () {
                let player = document.getElementById('modalPreviewVideo');
                player.pause();
                player.currentTime = 0;
            });
    });
</script>

<?php include 'includes/footer.php'; ?>