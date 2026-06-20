<?php
$page_title = 'Notices';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/upload.php';
require_login();

/* CREATE / UPDATE NOTICE */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = (int) ($_POST['id'] ?? 0);
  $title = trim($_POST['title'] ?? '');
  $body = trim($_POST['content'] ?? '');
  $image = handle_image_upload('image', 'notices');

  if ($title) {
    if ($id) {
      if ($image) {
        $r = $conn->query("SELECT image FROM notices WHERE id=$id");
        $old = $r ? $r->fetch_assoc() : null;
        if (!empty($old['image']))
          delete_upload($old['image']);
        $stmt = $conn->prepare("UPDATE notices SET title=?, body=?, image=? WHERE id=?");
        $stmt->bind_param('sssi', $title, $body, $image, $id);
      } else {
        $stmt = $conn->prepare("UPDATE notices SET title=?, body=? WHERE id=?");
        $stmt->bind_param('ssi', $title, $body, $id);
      }
      $stmt->execute();
    } else {
      $stmt = $conn->prepare("INSERT INTO notices (title, body, image, posted_on, created_at)
                                    VALUES (?,?,?, CURDATE(), NOW())");
      $stmt->bind_param('sss', $title, $body, $image);
      $stmt->execute();
    }
    header('Location: notices.php?msg=' . urlencode($id ? 'Notice updated' : 'Notice posted'));
    exit;
  }
}

/* EDIT FETCH */
$edit = null;
if (isset($_GET['edit'])) {
  $id = (int) $_GET['edit'];
  $stmt = $conn->prepare("SELECT * FROM notices WHERE id=?");
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $edit = $stmt->get_result()->fetch_assoc();
}

/* PAGINATION + FILTERS */
$limit = 7;
$page = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

$filter = $_GET['filter'] ?? '';
$where_clauses = [];
switch ($filter) {
  case 'today':
    $where_clauses[] = "DATE(posted_on) = CURDATE()";
    break;
  case '7days':
    $where_clauses[] = "posted_on >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
    break;
  case '30days':
    $where_clauses[] = "posted_on >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
    break;
  case 'month':
    $where_clauses[] = "MONTH(posted_on)=MONTH(CURDATE()) AND YEAR(posted_on)=YEAR(CURDATE())";
    break;
}

$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$use_custom_range = false;
if (!empty($from) && !empty($to)) {
  $d1 = DateTime::createFromFormat('Y-m-d', $from);
  $d2 = DateTime::createFromFormat('Y-m-d', $to);
  if ($d1 && $d2) {
    if ($d1 > $d2) {
      [$from, $to] = [$to, $from];
    }
    $from_esc = $conn->real_escape_string($from);
    $to_esc = $conn->real_escape_string($to);
    $where_clauses = ["DATE(posted_on) BETWEEN '$from_esc' AND '$to_esc'"];
    $use_custom_range = true;
  }
}
$where = $where_clauses ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

$total_q = $conn->query("SELECT COUNT(*) AS c FROM notices $where");
$total = $total_q ? (int) $total_q->fetch_assoc()['c'] : 0;
$total_pages = $total ? (int) ceil($total / $limit) : 1;

$rows_q = $conn->query("SELECT * FROM notices $where ORDER BY posted_on DESC, id DESC LIMIT $limit OFFSET $offset");
$rows = $rows_q ?: null;

include 'includes/header.php';
?>

<style>
  .admin-image-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 120px;
    height: 90px;
    border: 1px solid var(--border);
    border-radius: 8px;
    background: var(--primary-light);
    overflow: hidden;
    cursor: zoom-in;
    position: relative;
    transition: transform .15s ease, border-color .15s ease, box-shadow .15s ease;
  }

  .admin-image-link:hover,
  .admin-image-link:focus {
    transform: translateY(-1px);
    border-color: var(--primary);
    box-shadow: 0 8px 20px rgba(31, 122, 58, .18);
    outline: none;
  }

  .notice-thumb {
    width: 100%;
    height: 100%;
    object-fit: contain;
    display: block;
    background: #fff;
  }

  .admin-image-link::after {
    content: "\F62E";
    font-family: "bootstrap-icons";
    position: absolute;
    right: 5px;
    bottom: 5px;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: rgba(15, 46, 26, .78);
    color: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: .78rem;
    opacity: 0;
    transition: opacity .15s ease;
  }

  .admin-image-link:hover::after,
  .admin-image-link:focus::after {
    opacity: 1;
  }

  .no-img {
    width: 74px;
    height: 58px;
    border-radius: 8px;
    border: 1px dashed var(--border);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: var(--muted);
    font-size: 1.1rem;
    background: #fafcfb;
  }

  .admin-hover-preview {
    position: fixed;
    left: 0;
    top: 0;
    width: min(420px, 86vw);
    max-height: 320px;
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
    max-height: 300px;
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
    max-height: 92vh;
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

  @media (max-width: 767.98px) {
    .admin-hover-preview {
      display: none !important;
    }

    .admin-image-link {
      width: 68px;
      height: 54px;
    }
  }
</style>

<?php if (isset($_GET['msg'])): ?>
  <div class="alert alert-success auto-dismiss" id="successAlert"><?= htmlspecialchars($_GET['msg']) ?></div>
<?php endif; ?>

<div class="row g-3">

  <!-- FORM -->
  <div class="col-lg-5">
    <div class="panel">
      <div class="panel-header">
        <h5><?= $edit ? 'Edit Notice' : 'Post Notice' ?></h5>
      </div>
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
        <div class="mb-3">
          <label class="form-label">Title</label>
          <input type="text" name="title" class="form-control" required
            value="<?= htmlspecialchars($edit['title'] ?? '') ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Content</label>
          <textarea name="content" class="form-control" rows="6"><?= htmlspecialchars($edit['body'] ?? '') ?></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Image (optional)</label>
          <input type="file" name="image" class="form-control" accept="image/*">
          <?php if (!empty($edit['image'])): ?>
            <div class="mt-2">
              <a class="admin-image-link js-image-viewer" href="../<?= htmlspecialchars($edit['image']) ?>"
                target="_blank" rel="noopener" data-full="../<?= htmlspecialchars($edit['image']) ?>"
                style="width:120px;height:90px;" aria-label="View notice image">
                <img src="../<?= htmlspecialchars($edit['image']) ?>" class="notice-thumb" alt="Current notice image">
              </a>
            </div>
          <?php endif; ?>
        </div>
        <button class="btn btn-primary"><?= $edit ? 'Update' : 'Publish' ?></button>
        <?php if ($edit): ?><a href="notices.php" class="btn btn-outline-secondary">Cancel</a><?php endif; ?>
      </form>
    </div>
  </div>

  <!-- LIST -->
  <div class="col-lg-7">
    <div class="panel">
      <div class="panel-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
          <h5>All Notices</h5>
          <small class="text-muted"><?= $total ?> total</small>
        </div>
        <div class="d-flex flex-wrap gap-2 align-items-center">
          <?php $base_quick = $use_custom_range ? ['from' => $from, 'to' => $to] : []; ?>
          <a href="notices.php?<?= http_build_query(array_merge($base_quick, ['filter' => 'today'])) ?>"
            class="btn btn-sm btn-outline-primary <?= $filter === 'today' && !$use_custom_range ? 'active' : '' ?>">Today</a>
          <a href="notices.php?<?= http_build_query(array_merge($base_quick, ['filter' => '7days'])) ?>"
            class="btn btn-sm btn-outline-primary <?= $filter === '7days' && !$use_custom_range ? 'active' : '' ?>">7
            Days</a>
          <a href="notices.php?<?= http_build_query(array_merge($base_quick, ['filter' => '30days'])) ?>"
            class="btn btn-sm btn-outline-primary <?= $filter === '30days' && !$use_custom_range ? 'active' : '' ?>">30
            Days</a>
          <a href="notices.php?<?= http_build_query(array_merge($base_quick, ['filter' => 'month'])) ?>"
            class="btn btn-sm btn-outline-primary <?= $filter === 'month' && !$use_custom_range ? 'active' : '' ?>">Month</a>
          <a href="notices.php"
            class="btn btn-sm btn-outline-secondary <?= $filter === '' && !$use_custom_range ? 'active' : '' ?>">All</a>
        </div>
      </div>

      <div class="p-3 border-bottom">
        <form method="get" class="row g-2 align-items-end">
          <div class="col-auto"><label class="form-label small mb-0">From</label>
            <input type="date" name="from" class="form-control form-control-sm" value="<?= htmlspecialchars($from) ?>">
          </div>
          <div class="col-auto"><label class="form-label small mb-0">To</label>
            <input type="date" name="to" class="form-control form-control-sm" value="<?= htmlspecialchars($to) ?>">
          </div>
          <div class="col-auto">
            <button class="btn btn-primary btn-sm">Apply</button>
            <a href="notices.php" class="btn btn-outline-secondary btn-sm">Reset</a>
          </div>
        </form>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th>Image</th>
              <th>Title</th>
              <th>Posted</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($rows && $rows->num_rows): ?>
              <?php while ($n = $rows->fetch_assoc()): ?>
                <tr>
                  <td>
                    <?php if (!empty($n['image'])): ?>
                      <a class="admin-image-link js-image-viewer" href="../<?= htmlspecialchars($n['image']) ?>"
                        target="_blank" rel="noopener" data-full="../<?= htmlspecialchars($n['image']) ?>"
                        data-caption="<?= htmlspecialchars($n['title']) ?>" title="Click to view full image"
                        aria-label="View <?= htmlspecialchars($n['title']) ?> image">
                        <img src="../<?= htmlspecialchars($n['image']) ?>" class="notice-thumb"
                          alt="<?= htmlspecialchars($n['title']) ?>" loading="lazy">
                      </a>
                    <?php else: ?>
                      <span class="no-img"><i class="bi bi-image"></i></span>
                    <?php endif; ?>
                  </td>
                  <td><strong><?= htmlspecialchars($n['title']) ?></strong></td>
                  <td><small class="text-muted"><?= date('M d, Y', strtotime($n['posted_on'])) ?></small></td>
                  <td class="text-end">
                    <a href="?edit=<?= $n['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                    <button class="btn btn-sm btn-outline-danger btn-delete" data-id="<?= $n['id'] ?>"
                      data-type="notices"><i class="bi bi-trash"></i></button>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="4" class="text-center text-muted py-4">No notices found</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <?php if ($total_pages > 1): ?>
        <nav class="p-3">
          <ul class="pagination justify-content-end mb-0">
            <?php
            $preserve = $use_custom_range ? ['from' => $from, 'to' => $to] : ($filter ? ['filter' => $filter] : []);
            for ($i = 1; $i <= $total_pages; $i++):
              $qs = http_build_query(array_merge($preserve, ['page' => $i])); ?>
              <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                <a class="page-link" href="notices.php?<?= $qs ?>"><?= $i ?></a>
              </li>
            <?php endfor; ?>
          </ul>
        </nav>
      <?php endif; ?>
    </div>
  </div>
</div>

<div class="admin-hover-preview" id="adminHoverPreview" aria-hidden="true"><img src="" alt=""></div>
<div class="admin-image-modal" id="adminImageModal" role="dialog" aria-modal="true" aria-label="Full image preview">
  <button type="button" class="admin-image-modal-close" aria-label="Close image preview">&times;</button>
  <img src="" alt="">
</div>

<script>
  (function () {
    var modal = document.getElementById('adminImageModal');
    var hover = document.getElementById('adminHoverPreview');
    if (!modal || !hover) return;

    var modalImg = modal.getElementsByTagName('img')[0];
    var closeBtn = modal.getElementsByClassName('admin-image-modal-close')[0];
    var hoverImg = hover.getElementsByTagName('img')[0];
    var activeLink = null;

    function hasClass(el, name) {
      return el && ((' ' + el.className + ' ').indexOf(' ' + name + ' ') > -1);
    }

    function findViewer(el) {
      while (el && el !== document) {
        if (hasClass(el, 'js-image-viewer')) return el;
        el = el.parentNode;
      }
      return null;
    }

    function imageSrc(link) {
      return link.getAttribute('data-full') || link.getAttribute('href') || '';
    }

    function openModal(link) {
      var src = imageSrc(link);
      if (!src) return;
      modalImg.src = src;
      modalImg.alt = link.getAttribute('data-caption') || 'Full image preview';
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
      var w = hover.offsetWidth || 420;
      var h = hover.offsetHeight || 320;
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
      hoverImg.alt = link.getAttribute('data-caption') || 'Image preview';
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