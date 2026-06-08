<?php
$page_title = 'Notices';
require_once __DIR__ . '/includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    if ($title) {
        if ($id) {
            $stmt = $conn->prepare("UPDATE notices SET title=?, content=? WHERE id=?");
            $stmt->bind_param('ssi', $title, $content, $id);
            $stmt->execute();
        } else {
            $stmt = $conn->prepare("INSERT INTO notices (title, content) VALUES (?,?)");
            $stmt->bind_param('ss', $title, $content);
            $stmt->execute();
        }
        header('Location: notices.php?msg=' . urlencode($id ? 'Notice updated' : 'Notice posted'));
        exit;
    }
}

$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM notices WHERE id=?");
    $stmt->bind_param('i', $_GET['edit']);
    $stmt->execute();
    $edit = $stmt->get_result()->fetch_assoc();
}
$rows = $conn->query("SELECT * FROM notices ORDER BY posted_on DESC");
include 'includes/header.php';
?>
<?php if(isset($_GET['msg'])): ?><div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div><?php endif; ?>
<div class="row g-3">
  <div class="col-lg-5">
    <div class="panel">
      <div class="panel-header"><h5><?= $edit ? 'Edit Notice' : 'Post Notice' ?></h5></div>
      <form method="post">
        <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
        <div class="mb-3"><label class="form-label">Title</label>
          <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($edit['title'] ?? '') ?>"></div>
        <div class="mb-3"><label class="form-label">Content</label>
          <textarea name="content" class="form-control" rows="6"><?= htmlspecialchars($edit['content'] ?? '') ?></textarea></div>
        <button class="btn btn-primary"><?= $edit ? 'Update' : 'Publish' ?></button>
        <?php if($edit): ?><a href="notices.php" class="btn btn-outline-secondary">Cancel</a><?php endif; ?>
      </form>
    </div>
  </div>
  <div class="col-lg-7">
    <div class="panel">
      <div class="panel-header"><h5>All Notices</h5><span class="badge-soft"><?= $rows->num_rows ?> total</span></div>
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead><tr><th>Title</th><th>Posted</th><th class="text-end">Actions</th></tr></thead>
          <tbody>
          <?php while($n = $rows->fetch_assoc()): ?>
            <tr>
              <td><strong><?= htmlspecialchars($n['title']) ?></strong></td>
              <td><small class="text-muted"><?= date('M d, Y', strtotime($n['posted_on'])) ?></small></td>
              <td class="text-end">
                <a href="?edit=<?= $n['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                <button class="btn btn-sm btn-outline-danger btn-delete" data-id="<?= $n['id'] ?>" data-type="notices"><i class="bi bi-trash"></i></button>
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
