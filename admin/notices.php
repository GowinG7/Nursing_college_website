<?php
$page_title = 'Notices';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/upload.php';
require_login();

/* 
   CREATE / UPDATE NOTICE
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $id = (int) ($_POST['id'] ?? 0);
  $title = trim($_POST['title'] ?? '');
  $body = trim($_POST['content'] ?? ''); // form still uses "content"
  $image = handle_image_upload('image', 'notices');

  if ($title) {

    /* 
       UPDATE NOTICE
     */
    if ($id) {

      if ($image) {
        // delete old image
        $r = $conn->query("SELECT image FROM notices WHERE id=$id");
        $old = $r ? $r->fetch_assoc() : null;

        if (!empty($old['image'])) {
          delete_upload($old['image']);
        }

        $stmt = $conn->prepare("UPDATE notices SET title=?, body=?, image=? WHERE id=?");
        if (!$stmt) {
          die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param('sssi', $title, $body, $image, $id);

      } else {

        $stmt = $conn->prepare("UPDATE notices SET title=?, body=? WHERE id=?");
        if (!$stmt) {
          die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param('ssi', $title, $body, $id);
      }

      $stmt->execute();

    } else {

      /* 
         INSERT NOTICE
       */

      $stmt = $conn->prepare("INSERT INTO notices (title, body, image, posted_on, created_at)
                                    VALUES (?,?,?, CURDATE(), NOW())");

      if (!$stmt) {
        die("Prepare failed: " . $conn->error);
      }

      $stmt->bind_param('sss', $title, $body, $image);
      $stmt->execute();
    }

    header('Location: notices.php?msg=' . urlencode($id ? 'Notice updated' : 'Notice posted'));
    exit;
  }
}

/* 
   EDIT FETCH
 */
$edit = null;

if (isset($_GET['edit'])) {

  $id = (int) $_GET['edit'];

  $stmt = $conn->prepare("SELECT * FROM notices WHERE id=?");

  if (!$stmt) {
    die("Prepare failed: " . $conn->error);
  }

  $stmt->bind_param('i', $id);
  $stmt->execute();

  $edit = $stmt->get_result()->fetch_assoc();
}

/* 
   LIST ALL NOTICES with pagination + date filters
 */

// Pagination setup
$limit = 7;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1)
  $page = 1;
$offset = ($page - 1) * $limit;

// Quick filter presets
$filter = $_GET['filter'] ?? '';
$where_clauses = [];
$params = []; // not used for prepared here but kept for clarity

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
    $where_clauses[] = "MONTH(posted_on) = MONTH(CURDATE()) AND YEAR(posted_on) = YEAR(CURDATE())";
    break;
}

// Custom date range (overrides quick preset if provided)
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$use_custom_range = false;
if (!empty($from) && !empty($to)) {
  $d1 = DateTime::createFromFormat('Y-m-d', $from);
  $d2 = DateTime::createFromFormat('Y-m-d', $to);
  if ($d1 && $d2) {
    if ($d1 > $d2) {
      $tmp = $from;
      $from = $to;
      $to = $tmp;
      $d1 = DateTime::createFromFormat('Y-m-d', $from);
      $d2 = DateTime::createFromFormat('Y-m-d', $to);
    }
    $from_esc = $conn->real_escape_string($from);
    $to_esc = $conn->real_escape_string($to);
    $where_clauses = ["DATE(posted_on) BETWEEN '$from_esc' AND '$to_esc'"];
    $use_custom_range = true;
  }
}

$where = '';
if (!empty($where_clauses)) {
  $where = 'WHERE ' . implode(' AND ', $where_clauses);
}

// Count total (respecting filters)
$total_q = $conn->query("SELECT COUNT(*) AS c FROM notices $where");
$total = $total_q ? (int) $total_q->fetch_assoc()['c'] : 0;
$total_pages = $total ? (int) ceil($total / $limit) : 1;

// Fetch rows with limit/offset
$rows_q = $conn->query("SELECT * FROM notices $where ORDER BY posted_on DESC, id DESC LIMIT $limit OFFSET $offset");
$rows = $rows_q ?: null;

include 'includes/header.php';
?>

<?php if (isset($_GET['msg'])): ?>
  <div class="alert alert-success auto-dismiss" id="successAlert">
    <?= htmlspecialchars($_GET['msg']) ?>
  </div>
<?php endif; ?>

<div class="row g-3">

  <!-- FORM -->
  <div class="col-lg-5">
    <div class="panel">
      <div class="panel-header">
        <h5>
          <?= $edit ? 'Edit Notice' : 'Post Notice' ?>
        </h5>
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
              <img src="../<?= htmlspecialchars($edit['image']) ?>" style="max-height:90px;border-radius:6px;">
            </div>
          <?php endif; ?>
        </div>

        <button class="btn btn-primary">
          <?= $edit ? 'Update' : 'Publish' ?>
        </button>

        <?php if ($edit): ?>
          <a href="notices.php" class="btn btn-outline-secondary">Cancel</a>
        <?php endif; ?>

      </form>
    </div>
  </div>

  <!-- LIST -->
  <div class="col-lg-7">
    <div class="panel">

      <div class="panel-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
          <h5>All Notices</h5>
          <small class="text-muted">
            <?= $total ?> total
          </small>
        </div>

        <!-- Quick filters -->
        <div class="d-flex flex-wrap gap-2 align-items-center">
          <?php
          // Build base query for quick filter links (preserve custom range if set)
          $base_quick = [];
          if ($use_custom_range) {
            $base_quick['from'] = $from;
            $base_quick['to'] = $to;
          }
          ?>
          <a href="notices.php?<?= http_build_query(array_merge($base_quick, ['filter' => 'today'])) ?>"
            class="btn btn-sm btn-outline-primary <?= $filter === 'today' && !$use_custom_range ? 'active' : '' ?>">Today</a>

          <a href="notices.php?<?= http_build_query(array_merge($base_quick, ['filter' => '7days'])) ?>"
            class="btn btn-sm btn-outline-primary <?= $filter === '7days' && !$use_custom_range ? 'active' : '' ?>">Last
            7 Days</a>

          <a href="notices.php?<?= http_build_query(array_merge($base_quick, ['filter' => '30days'])) ?>"
            class="btn btn-sm btn-outline-primary <?= $filter === '30days' && !$use_custom_range ? 'active' : '' ?>">Last
            30 Days</a>

          <a href="notices.php?<?= http_build_query(array_merge($base_quick, ['filter' => 'month'])) ?>"
            class="btn btn-sm btn-outline-primary <?= $filter === 'month' && !$use_custom_range ? 'active' : '' ?>">This
            Month</a>

          <a href="notices.php"
            class="btn btn-sm btn-outline-secondary <?= $filter === '' && !$use_custom_range ? 'active' : '' ?>">All</a>
        </div>
      </div>

      <!-- Custom date range -->
      <div class="p-3 border-bottom">
        <form method="get" class="row g-2 align-items-end">
          <div class="col-auto">
            <label class="form-label small mb-0">From</label>
            <input type="date" name="from" class="form-control form-control-sm" value="<?= htmlspecialchars($from) ?>">
          </div>
          <div class="col-auto">
            <label class="form-label small mb-0">To</label>
            <input type="date" name="to" class="form-control form-control-sm" value="<?= htmlspecialchars($to) ?>">
          </div>
          <div class="col-auto">
            <button class="btn btn-success btn-sm">Apply</button>
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
                      <img src="../<?= htmlspecialchars($n['image']) ?>"
                        style="width:56px;height:42px;object-fit:cover;border-radius:4px;">
                    <?php else: ?>
                      <span class="text-muted">—</span>
                    <?php endif; ?>
                  </td>

                  <td>
                    <strong>
                      <?= htmlspecialchars($n['title']) ?>
                    </strong>
                  </td>

                  <td>
                    <small class="text-muted">
                      <?= date('M d, Y', strtotime($n['posted_on'])) ?>
                    </small>
                  </td>

                  <td class="text-end">
                    <a href="?edit=<?= $n['id'] ?>" class="btn btn-sm btn-outline-primary">
                      <i class="bi bi-pencil"></i>
                    </a>

                    <button class="btn btn-sm btn-outline-danger btn-delete" data-id="<?= $n['id'] ?>" data-type="notices">
                      <i class="bi bi-trash"></i>
                    </button>
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

      <!-- Pagination -->
      <?php if ($total_pages > 1): ?>
        <nav class="p-3">
          <ul class="pagination justify-content-end mb-0">
            <?php
            // Preserve current filters in pagination links
            $preserve = [];
            if ($use_custom_range) {
              $preserve['from'] = $from;
              $preserve['to'] = $to;
            } elseif ($filter) {
              $preserve['filter'] = $filter;
            }
            for ($i = 1; $i <= $total_pages; $i++):
              $qs = http_build_query(array_merge($preserve, ['page' => $i]));
              ?>
              <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                <a class="page-link" href="notices.php?<?= $qs ?>">
                  <?= $i ?>
                </a>
              </li>
            <?php endfor; ?>
          </ul>
        </nav>
      <?php endif; ?>

    </div>
  </div>

</div>

<?php include 'includes/footer.php'; ?>

<script src="assets/auto-diss.js"></script>