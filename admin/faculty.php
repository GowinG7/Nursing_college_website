<?php
$page_title = 'Faculty';
require_once __DIR__ . '/includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $designation = trim($_POST['designation'] ?? '');
    $qualification = trim($_POST['qualification'] ?? '');
    $email = trim($_POST['email'] ?? '');
    if ($name) {
        if ($id) {
            $stmt = $conn->prepare("UPDATE faculty SET name=?, designation=?, qualification=?, email=? WHERE id=?");
            $stmt->bind_param('ssssi', $name, $designation, $qualification, $email, $id);
            $stmt->execute();
        } else {
            $stmt = $conn->prepare("INSERT INTO faculty (name, designation, qualification, email) VALUES (?,?,?,?)");
            $stmt->bind_param('ssss', $name, $designation, $qualification, $email);
            $stmt->execute();
        }
        header('Location: faculty.php?msg=' . urlencode($id ? 'Faculty updated' : 'Faculty added'));
        exit;
    }
}

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
<?php if(isset($_GET['msg'])): ?><div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div><?php endif; ?>
<div class="row g-3">
  <div class="col-lg-5">
    <div class="panel">
      <div class="panel-header"><h5><?= $edit ? 'Edit Faculty' : 'Add Faculty' ?></h5></div>
      <form method="post">
        <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
        <div class="mb-3"><label class="form-label">Name</label>
          <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($edit['name'] ?? '') ?>"></div>
        <div class="mb-3"><label class="form-label">Designation</label>
          <input type="text" name="designation" class="form-control" value="<?= htmlspecialchars($edit['designation'] ?? '') ?>"></div>
        <div class="mb-3"><label class="form-label">Qualification</label>
          <input type="text" name="qualification" class="form-control" value="<?= htmlspecialchars($edit['qualification'] ?? '') ?>"></div>
        <div class="mb-3"><label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($edit['email'] ?? '') ?>"></div>
        <button class="btn btn-primary"><?= $edit ? 'Update' : 'Add Faculty' ?></button>
        <?php if($edit): ?><a href="faculty.php" class="btn btn-outline-secondary">Cancel</a><?php endif; ?>
      </form>
    </div>
  </div>
  <div class="col-lg-7">
    <div class="panel">
      <div class="panel-header"><h5>All Faculty</h5><span class="badge-soft"><?= $rows->num_rows ?> total</span></div>
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead><tr><th>Name</th><th>Designation</th><th>Email</th><th class="text-end">Actions</th></tr></thead>
          <tbody>
          <?php while($f = $rows->fetch_assoc()): ?>
            <tr>
              <td><strong><?= htmlspecialchars($f['name']) ?></strong><br><small class="text-muted"><?= htmlspecialchars($f['qualification']) ?></small></td>
              <td><?= htmlspecialchars($f['designation']) ?></td>
              <td><small><?= htmlspecialchars($f['email']) ?></small></td>
              <td class="text-end">
                <a href="?edit=<?= $f['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                <button class="btn btn-sm btn-outline-danger btn-delete" data-id="<?= $f['id'] ?>" data-type="faculty"><i class="bi bi-trash"></i></button>
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
