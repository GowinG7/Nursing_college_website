<?php
$page_title = 'Gallery';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/upload.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $caption = trim($_POST['caption'] ?? '');
    $image = handle_image_upload('image', 'gallery');
    if ($image) {
        $stmt = $conn->prepare("INSERT INTO gallery (title, caption, image) VALUES (?,?,?)");
        $stmt->bind_param('sss', $title, $caption, $image);
        $stmt->execute();
        header('Location: gallery.php?msg=' . urlencode('Image uploaded'));
        exit;
    } else {
        $err = 'Please choose a valid image (jpg/png/gif/webp, max 5MB).';
    }
}

$rows = $conn->query("SELECT * FROM gallery ORDER BY uploaded_on DESC");
include 'includes/header.php';
?>
<?php if(isset($_GET['msg'])): ?><div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div><?php endif; ?>
<?php if(!empty($err)): ?><div class="alert alert-danger"><?= htmlspecialchars($err) ?></div><?php endif; ?>

<div class="panel mb-3">
  <div class="panel-header"><h5>Upload to Gallery</h5></div>
  <form method="post" enctype="multipart/form-data" class="row g-3">
    <div class="col-md-4">
      <label class="form-label">Title</label>
      <input type="text" name="title" class="form-control" placeholder="e.g. Convocation 2025">
    </div>
    <div class="col-md-5">
      <label class="form-label">Caption</label>
      <input type="text" name="caption" class="form-control" placeholder="Short description">
    </div>
    <div class="col-md-3">
      <label class="form-label">Image *</label>
      <input type="file" name="image" class="form-control" accept="image/*" required>
    </div>
    <div class="col-12">
      <button class="btn btn-primary"><i class="bi bi-cloud-upload"></i> Upload</button>
    </div>
  </form>
</div>

<div class="panel">
  <div class="panel-header"><h5>Gallery Images</h5><span class="badge-soft"><?= $rows->num_rows ?> images</span></div>
  <div class="row g-3">
    <?php while($g = $rows->fetch_assoc()): ?>
      <div class="col-sm-6 col-md-4 col-lg-3">
        <div class="border rounded overflow-hidden h-100">
          <img src="../<?= htmlspecialchars($g['image']) ?>" style="width:100%;height:160px;object-fit:cover;">
          <div class="p-2">
            <strong class="d-block text-truncate"><?= htmlspecialchars($g['title'] ?: 'Untitled') ?></strong>
            <small class="text-muted d-block text-truncate"><?= htmlspecialchars($g['caption']) ?></small>
            <div class="d-flex justify-content-between align-items-center mt-2">
              <small class="text-muted"><?= date('M d, Y', strtotime($g['uploaded_on'])) ?></small>
              <button class="btn btn-sm btn-outline-danger btn-delete" data-id="<?= $g['id'] ?>" data-type="gallery"><i class="bi bi-trash"></i></button>
            </div>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
    <?php if($rows->num_rows === 0): ?>
      <div class="col-12 text-center text-muted py-4">No images yet. Upload your first one above.</div>
    <?php endif; ?>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
