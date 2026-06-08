<?php
$page_title = 'Contact Messages';
require_once __DIR__ . '/includes/auth.php';
require_login();
$rows = $conn->query("SELECT * FROM contact_messages ORDER BY id DESC");
include 'includes/header.php';
?>
<div class="panel">
  <div class="panel-header"><h5>All Messages</h5><span class="badge-soft"><?= $rows->num_rows ?> total</span></div>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead><tr><th>From</th><th>Subject</th><th>Message</th><th>Date</th><th class="text-end">Action</th></tr></thead>
      <tbody>
      <?php if($rows->num_rows): while($m = $rows->fetch_assoc()): ?>
        <tr>
          <td>
            <strong><?= htmlspecialchars($m['name']) ?></strong><br>
            <small class="text-muted"><?= htmlspecialchars($m['email']) ?></small>
            <?php if(!empty($m['phone'])): ?><br><small><?= htmlspecialchars($m['phone']) ?></small><?php endif; ?>
          </td>
          <td><?= htmlspecialchars($m['subject']) ?></td>
          <td style="max-width:340px"><?= nl2br(htmlspecialchars($m['message'])) ?></td>
          <td><small class="text-muted"><?= date('M d, Y', strtotime($m['created_at'])) ?></small></td>
          <td class="text-end">
            <a href="mailto:<?= htmlspecialchars($m['email']) ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-reply"></i></a>
            <button class="btn btn-sm btn-outline-danger btn-delete" data-id="<?= $m['id'] ?>" data-type="messages"><i class="bi bi-trash"></i></button>
          </td>
        </tr>
      <?php endwhile; else: ?>
        <tr><td colspan="5" class="text-center text-muted py-4">No messages yet</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
