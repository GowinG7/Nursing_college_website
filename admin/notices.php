<?php
$page_title = 'Notices';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/upload.php';
require_login();

/* =========================
   CREATE / UPDATE NOTICE
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $id = (int) ($_POST['id'] ?? 0);
  $title = trim($_POST['title'] ?? '');
  $body = trim($_POST['content'] ?? ''); // form still uses "content"
  $image = handle_image_upload('image', 'notices');

  if ($title) {

    /* =========================
       UPDATE NOTICE
    ========================= */
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

      /* =========================
         INSERT NOTICE
      ========================= */

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

/* =========================
   EDIT FETCH
========================= */
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

/* =========================
   LIST ALL NOTICES
========================= */
$rows = $conn->query("SELECT * FROM notices ORDER BY posted_on DESC");

include 'includes/header.php';
?>

<?php if (isset($_GET['msg'])): ?>
  <div class="alert alert-success">
    <?= htmlspecialchars($_GET['msg']) ?>
  </div>
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

      <div class="panel-header">
        <h5>All Notices</h5>
        <span class="badge-soft"><?= $rows->num_rows ?> total</span>
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
                  <strong><?= htmlspecialchars($n['title']) ?></strong>
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
          </tbody>

        </table>

      </div>
    </div>
  </div>

</div>

<?php include 'includes/footer.php'; ?>