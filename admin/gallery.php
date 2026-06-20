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

<style>
  .gallery-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 12px;
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
    transition: transform .15s ease, border-color .15s ease, box-shadow .15s ease;
  }

  .gallery-card:hover {
    transform: translateY(-3px);
    border-color: var(--primary);
    box-shadow: 0 10px 24px rgba(31, 122, 58, .12);
  }

  .gallery-media {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 250px;
    padding: 10px;
    background: #f8f9fa;
    overflow: hidden;
  }

  .gallery-media:focus {
    outline: 3px solid rgba(31, 122, 58, .25);
    outline-offset: -3px;
  }

  .gallery-media img {
    max-width: 100%;
    max-height: 100%;
    width: auto;
    height: auto;
    object-fit: contain;
  }

  .gallery-card:hover .gallery-media img {
    transform: scale(1.02);
  }

  .zoom-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(15, 46, 26, .78);
    color: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: .95rem;
    opacity: 0;
    transition: opacity .15s ease;
  }

  .gallery-card:hover .zoom-badge,
  .gallery-media:focus .zoom-badge {
    opacity: 1;
  }

  .gallery-body {
    padding: .75rem .9rem;
  }

  .gallery-body .g-title {
    font-weight: 600;
    color: var(--text);
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .gallery-body .g-caption {
    color: var(--muted);
    font-size: .8rem;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .admin-hover-preview {
    position: fixed;
    left: 0;
    top: 0;
    width: min(440px, 86vw);
    max-height: 340px;
    display: none;
    padding: 8px;
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 12px;
    box-shadow: 0 18px 48px rgba(15, 46, 26, .28);
    pointer-events: none;
    z-index: 2147483646;
  }

  .admin-hover-preview.show {
    display: block;
  }

  .admin-hover-preview img {
    width: 100%;
    max-height: 320px;
    object-fit: contain;
    display: block;
    border-radius: 8px;
    background: #fff;
  }

  .admin-image-modal {
    position: fixed;
    inset: 0;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 18px;
    background: rgba(15, 46, 26, .92);
    z-index: 2147483647;
  }

  .admin-image-modal.show {
    display: flex;
  }

  .admin-image-modal img {
    max-width: 96vw;
    max-height: 88vh;
    object-fit: contain;
    border-radius: 10px;
    background: #fff;
    box-shadow: 0 24px 70px rgba(0, 0, 0, .52);
  }

  .admin-image-modal-close {
    position: fixed;
    top: 14px;
    right: 16px;
    width: 44px;
    height: 44px;
    border: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, .14);
    color: #fff;
    font-size: 1.8rem;
    line-height: 1;
    cursor: pointer;
  }

  .admin-image-modal-close:hover,
  .admin-image-modal-close:focus {
    background: var(--primary);
    outline: none;
  }

  .admin-image-modal-caption {
    position: fixed;
    left: 50%;
    bottom: 18px;
    transform: translateX(-50%);
    max-width: 82vw;
    padding: .4rem .75rem;
    border-radius: 999px;
    background: rgba(0, 0, 0, .42);
    color: #fff;
    font-size: .9rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  @media (max-width: 767.98px) {
    .admin-hover-preview {
      display: none !important;
    }

    .admin-image-modal img {
      max-height: 84vh;
    }
  }
</style>

<?php if (isset($_GET['msg'])): ?>
  <div class="alert alert-success auto-dismiss" id="successAlert">
    <?= htmlspecialchars($_GET['msg']) ?>
  </div>
<?php endif; ?>
<?php if (!empty($err)): ?>
  <div class="alert alert-danger">
    <?= htmlspecialchars($err) ?>
  </div>
<?php endif; ?>

<div class="panel mb-3">
  <div class="panel-header">
    <h5>Upload to Gallery</h5>
  </div>
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
  <div class="panel-header">
    <h5>Gallery Images</h5>
    <span class="badge-soft">
      <?= $rows->num_rows ?> images
    </span>
  </div>

  <div class="row g-3">
    <?php while ($g = $rows->fetch_assoc()): ?>
      <div class="col-sm-6 col-md-4 col-lg-3">
        <div class="gallery-card">
          <button type="button" class="gallery-media js-image-viewer" data-full="../<?= htmlspecialchars($g['image']) ?>"
            data-caption="<?= htmlspecialchars($g['title'] ?: 'Untitled') ?>" title="Click to view full image"
            aria-label="View <?= htmlspecialchars($g['title'] ?: 'gallery') ?> image">
            <img src="../<?= htmlspecialchars($g['image']) ?>" alt="<?= htmlspecialchars($g['title']) ?>" loading="lazy">
            <span class="zoom-badge"><i class="bi bi-zoom-in"></i></span>
          </button>
          <div class="gallery-body">
            <span class="g-title">
              <?= htmlspecialchars($g['title'] ?: 'Untitled') ?>
            </span>
            <span class="g-caption">
              <?= htmlspecialchars($g['caption']) ?: '&nbsp;' ?>
            </span>
            <div class="d-flex justify-content-between align-items-center mt-2">
              <small class="text-muted">
                <?= date('M d, Y', strtotime($g['uploaded_on'])) ?>
              </small>
              <button class="btn btn-sm btn-outline-danger btn-delete" data-id="<?= $g['id'] ?>" data-type="gallery">
                <i class="bi bi-trash"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
    <?php if ($rows->num_rows === 0): ?>
      <div class="col-12 text-center text-muted py-4">No images yet. Upload your first one above.</div>
    <?php endif; ?>
  </div>
</div>

<div class="admin-hover-preview" id="adminHoverPreview" aria-hidden="true"><img src="" alt=""></div>
<div class="admin-image-modal" id="adminImageModal" role="dialog" aria-modal="true" aria-label="Full image preview">
  <button type="button" class="admin-image-modal-close" aria-label="Close image preview">&times;</button>
  <img src="" alt="">
  <div class="admin-image-modal-caption" id="adminImageModalCaption"></div>
</div>

<script>
  (function () {
    var modal = document.getElementById('adminImageModal');
    var hover = document.getElementById('adminHoverPreview');
    if (!modal || !hover) return;

    var modalImg = modal.getElementsByTagName('img')[0];
    var closeBtn = modal.getElementsByClassName('admin-image-modal-close')[0];
    var captionBox = document.getElementById('adminImageModalCaption');
    var hoverImg = hover.getElementsByTagName('img')[0];
    var activeLink = null;

    // function hasClass(el, name) {
    //   return el && ((' ' + el.className + ' ').indexOf(' ' + name + ' ') > -1);
    // }

    // function findViewer(el) {
    //   while (el && el !== document) {
    //     if (hasClass(el, 'js-image-viewer')) return el;
    //     el = el.parentNode;
    //   }
    //   return null;
    // }


    function findViewer(el) {
      return el.closest('.js-image-viewer');
    }

    function imageSrc(link) {
      return link.getAttribute('data-full') || '';
    }

    function caption(link) {
      return link.getAttribute('data-caption') || '';
    }

    function openModal(link) {
      var src = imageSrc(link);
      if (!src) return;
      var text = caption(link);
      modalImg.src = src;
      modalImg.alt = text || 'Full image preview';
      if (captionBox) {
        captionBox.textContent = text;
        captionBox.style.display = text ? 'block' : 'none';
      }
      modal.className = 'admin-image-modal show';
      document.body.style.overflow = 'hidden';
    }

    function closeModal() {
      modal.className = 'admin-image-modal';
      modalImg.src = '';
      document.body.style.overflow = '';
    }

    function moveHover(ev) {
      if (!activeLink || window.innerWidth < 768) return;
      var gap = 18;
      var w = hover.offsetWidth || 440;
      var h = hover.offsetHeight || 340;
      var x = ev.clientX + gap;
      var y = ev.clientY + gap;
      if (x + w > window.innerWidth - 12) x = ev.clientX - w - gap;
      if (y + h > window.innerHeight - 12) y = window.innerHeight - h - 12;
      hover.style.left = Math.max(12, x) + 'px';
      hover.style.top = Math.max(12, y) + 'px';
    }

    document.addEventListener('click', function (ev) {
      var link = findViewer(ev.target);
      if (link) {
        ev.preventDefault();
        openModal(link);
        return;
      }
      if (ev.target === modal || ev.target === closeBtn) closeModal();
    }, false);

    document.addEventListener('mouseover', function (ev) {
      var link = findViewer(ev.target);
      if (!link || window.innerWidth < 768) return;
      activeLink = link;
      hoverImg.src = imageSrc(link);
      hoverImg.alt = caption(link) || 'Image preview';
      hover.className = 'admin-hover-preview show';
      moveHover(ev);
    }, false);

    document.addEventListener('mousemove', moveHover, false);

    document.addEventListener('mouseout', function (ev) {
      var link = findViewer(ev.target);
      if (!link || (ev.relatedTarget && link.contains(ev.relatedTarget))) return;
      activeLink = null;
      hover.className = 'admin-hover-preview';
      hoverImg.src = '';
    }, false);

    document.addEventListener('keydown', function (ev) {
      if (ev.key === 'Escape' || ev.keyCode === 27) closeModal();
    }, false);
  })();
</script>

<?php include 'includes/footer.php'; ?>
<script src="assets/auto-diss.js"></script>