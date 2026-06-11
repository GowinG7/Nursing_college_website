<?php
$page_title = 'Faculty';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/upload.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = (int) ($_POST['id'] ?? 0);
  $name = trim($_POST['name'] ?? '');
  $designation = trim($_POST['designation'] ?? '');
  $qualification = trim($_POST['qualification'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $image = handle_image_upload('image', 'faculty');

  if ($name) {
    if ($id) {
      // Update existing record
      if ($image) {
        $r = $conn->query("SELECT image FROM faculty WHERE id=$id")->fetch_assoc();
        if (!empty($r['image'])) {
          delete_upload($r['image']);
        }
        $stmt = $conn->prepare("UPDATE faculty 
                    SET full_name=?, designation=?, qualification=?, email=?, image=? 
                    WHERE id=?");
        $stmt->bind_param('sssssi', $name, $designation, $qualification, $email, $image, $id);
      } else {
        $stmt = $conn->prepare("UPDATE faculty 
                    SET full_name=?, designation=?, qualification=?, email=? 
                    WHERE id=?");
        $stmt->bind_param('ssssi', $name, $designation, $qualification, $email, $id);
      }
    } else {
      // Insert new record
      $stmt = $conn->prepare("INSERT INTO faculty 
                (full_name, designation, qualification, email, image) 
                VALUES (?,?,?,?,?)");
      $stmt->bind_param('sssss', $name, $designation, $qualification, $email, $image);
    }

    if (!$stmt->execute()) {
      die("Database error: " . $stmt->error);
    }

    header('Location: faculty.php?msg=' . urlencode($id ? 'Faculty updated' : 'Faculty added'));
    exit;
  }
}

// Edit mode
$edit = null;
if (isset($_GET['edit'])) {
  $stmt = $conn->prepare("SELECT * FROM faculty WHERE id=?");
  $stmt->bind_param('i', $_GET['edit']);
  $stmt->execute();
  $edit = $stmt->get_result()->fetch_assoc();
}

$rows = $conn->query("SELECT * FROM faculty ORDER BY id DESC");
include 'includes/header.php';
?>

<?php if (isset($_GET['msg'])): ?>
  <div class="alert alert-success auto-dismiss" id="successAlert">
    <?= htmlspecialchars($_GET['msg']) ?>
  </div>
<?php endif; ?>

<div class="row g-3">
  <div class="col-lg-5">
    <div class="panel">
      <div class="panel-header">
        <h5>
          <?= $edit ? 'Edit Faculty' : 'Add Faculty' ?>
        </h5>
      </div>
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">

        <div class="mb-3">
          <label for="name" class="form-label">Name</label>
          <input id="name" type="text" name="name" class="form-control" required autocomplete="name"
            value="<?= htmlspecialchars($edit['full_name'] ?? '') ?>">
        </div>

        <div class="mb-3">
          <label for="designation" class="form-label">Designation</label>
          <input id="designation" type="text" name="designation" class="form-control"
            value="<?= htmlspecialchars($edit['designation'] ?? '') ?>">
        </div>

        <div class="mb-3">
          <label for="qualification" class="form-label">Qualification</label>
          <input id="qualification" type="text" name="qualification" class="form-control"
            value="<?= htmlspecialchars($edit['qualification'] ?? '') ?>">
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input id="email" type="email" name="email" class="form-control" autocomplete="email"
            value="<?= htmlspecialchars($edit['email'] ?? '') ?>">
        </div>

        <div class="mb-3">
          <label for="image" class="form-label">Photo (optional, max 5MB)</label>
          <input id="image" type="file" name="image" class="form-control" accept="image/*">
          <?php if (!empty($edit['image'])): ?>
            <div class="mt-2">
              <img src="../<?= htmlspecialchars($edit['image']) ?>" alt="Faculty photo"
                style="height:80px;width:80px;object-fit:cover;border-radius:50%;">
            </div>
          <?php endif; ?>
        </div>

        <button class="btn btn-primary">
          <?= $edit ? 'Update' : 'Add Faculty' ?>
        </button>
        <?php if ($edit): ?>
          <a href="faculty.php" class="btn btn-outline-secondary">Cancel</a>
        <?php endif; ?>
      </form>
    </div>
  </div>

  <div class="col-lg-7">
    <div class="panel">
      <div class="panel-header">
        <h5>All Faculty</h5>
        <span class="badge-soft">
          <?= $rows->num_rows ?> total
        </span>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th>Photo</th>
              <th>Name</th>
              <th>Designation</th>
              <th>Email</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($f = $rows->fetch_assoc()): ?>
              <tr>
                <td>
                  <?php if (!empty($f['image'])): ?>
                    <img src="../<?= htmlspecialchars($f['image']) ?>" alt="Faculty photo"
                      style="width:44px;height:44px;object-fit:cover;border-radius:50%;">
                  <?php else: ?>
                    <span class="text-muted"><i class="bi bi-person-circle fs-3"></i></span>
                  <?php endif; ?>
                </td>
                <td>
                  <strong>
                    <?= htmlspecialchars($f['full_name']) ?>
                  </strong><br>
                  <small class="text-muted">
                    <?= htmlspecialchars($f['qualification']) ?>
                  </small>
                </td>
                <td>
                  <?= htmlspecialchars($f['designation']) ?>
                </td>
                <td><small>
                    <?= htmlspecialchars($f['email']) ?>
                  </small></td>
                <td class="text-end">
                  <a href="?edit=<?= $f['id'] ?>" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-pencil"></i>
                  </a>
                  <button class="btn btn-sm btn-outline-danger btn-delete" data-id="<?= $f['id'] ?>" data-type="faculty">
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

<script src="assets/auto-diss.js"></script>