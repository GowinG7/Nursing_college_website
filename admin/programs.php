<?php
$page_title = 'Programs';
require_once __DIR__ . '/includes/auth.php';
require_login();

$edit = null;
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $duration = trim($_POST['duration'] ?? '');
    $seats = (int)($_POST['seats'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    if ($name) {
        if ($id) {
            $stmt = $conn->prepare("UPDATE programs SET name=?, duration=?, seats=?, description=? WHERE id=?");
            $stmt->bind_param('ssisi', $name, $duration, $seats, $description, $id);
            $stmt->execute();
            $msg = 'Program updated';
        } else {
            $stmt = $conn->prepare("INSERT INTO programs (name, duration, seats, description) VALUES (?,?,?,?)");
            $stmt->bind_param('ssis', $name, $duration, $seats, $description);
            $stmt->execute();
            $msg = 'Program added';
        }
        header('Location: programs.php?msg=' . urlencode($msg));
        exit;
    }
}

if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM programs WHERE id=?");
    $stmt->bind_param('i', $_GET['edit']);
    $stmt->execute();
    $edit = $stmt->get_result()->fetch_assoc();
}

$programs = $conn->query("SELECT * FROM programs ORDER BY id DESC");
include 'includes/header.php';
?>
<?php if(isset($_GET['msg'])): ?>
<div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
<?php endif; ?>

<div class="row g-3">
  <div class="col-lg-5">
    <div class="panel">
      <div class="panel-header"><h5><?= $edit ? 'Edit Program' : 'Add New Program' ?></h5></div>
      <form method="post">
        <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
        <div class="mb-3"><label class="form-label">Program Name</label>
          <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($edit['name'] ?? '') ?>"></div>
        <div class="row">
          <div class="col-7 mb-3"><label class="form-label">Duration</label>
            <input type="text" name="duration" class="form-control" placeholder="e.g. 3 Years" value="<?= htmlspecialchars($edit['duration'] ?? '') ?>"></div>
          <div class="col-5 mb-3"><label class="form-label">Seats</label>
            <input type="number" name="seats" class="form-control" value="<?= htmlspecialchars($edit['seats'] ?? '') ?>"></div>
        </div>
        <div class="mb-3"><label class="form-label">Description</label>
          <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($edit['description'] ?? '') ?></textarea></div>
        <button class="btn btn-primary"><?= $edit ? 'Update' : 'Add Program' ?></button>
        <?php if($edit): ?><a href="programs.php" class="btn btn-outline-secondary">Cancel</a><?php endif; ?>
      </form>
    </div>
  </div>
  <div class="col-lg-7">
    <div class="panel">
      <div class="panel-header"><h5>All Programs</h5><span class="badge-soft"><?= $programs->num_rows ?> total</span></div>
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead><tr><th>Name</th><th>Duration</th><th>Seats</th><th class="text-end">Actions</th></tr></thead>
          <tbody>
          <?php while($p = $programs->fetch_assoc()): ?>
            <tr>
              <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
              <td><?= htmlspecialchars($p['duration']) ?></td>
              <td><?= (int)$p['seats'] ?></td>
              <td class="text-end">
                <a href="?edit=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                <button class="btn btn-sm btn-outline-danger btn-delete" data-id="<?= $p['id'] ?>" data-type="programs"><i class="bi bi-trash"></i></button>
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
