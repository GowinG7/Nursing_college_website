<?php
$page_title = 'Dashboard';
include 'includes/header.php';

$counts = [
  'programs' => $conn->query("SELECT COUNT(*) c FROM programs")->fetch_assoc()['c'] ?? 0,
  'faculty'  => $conn->query("SELECT COUNT(*) c FROM faculty")->fetch_assoc()['c'] ?? 0,
  'notices'  => $conn->query("SELECT COUNT(*) c FROM notices")->fetch_assoc()['c'] ?? 0,
  'messages' => $conn->query("SELECT COUNT(*) c FROM contact_messages")->fetch_assoc()['c'] ?? 0,
];
$recent_msgs = $conn->query("SELECT * FROM contact_messages ORDER BY id DESC LIMIT 5");
$recent_notices = $conn->query("SELECT * FROM notices ORDER BY posted_on DESC LIMIT 5");
?>
<div class="stat-grid">
  <div class="stat-card">
    <div><div class="label">Programs</div><div class="value"><?= $counts['programs'] ?></div></div>
    <div class="icon"><i class="bi bi-mortarboard"></i></div>
  </div>
  <div class="stat-card">
    <div><div class="label">Faculty</div><div class="value"><?= $counts['faculty'] ?></div></div>
    <div class="icon"><i class="bi bi-people"></i></div>
  </div>
  <div class="stat-card">
    <div><div class="label">Notices</div><div class="value"><?= $counts['notices'] ?></div></div>
    <div class="icon"><i class="bi bi-megaphone"></i></div>
  </div>
  <div class="stat-card">
    <div><div class="label">Messages</div><div class="value"><?= $counts['messages'] ?></div></div>
    <div class="icon"><i class="bi bi-envelope"></i></div>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-6">
    <div class="panel">
      <div class="panel-header"><h5>Recent Messages</h5><a href="messages.php" class="btn btn-sm btn-outline-primary">View all</a></div>
      <div class="table-responsive">
        <table class="table table-hover">
          <thead><tr><th>Name</th><th>Subject</th><th>Date</th></tr></thead>
          <tbody>
          <?php if($recent_msgs && $recent_msgs->num_rows): while($m = $recent_msgs->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($m['name']) ?></td>
              <td class="text-truncate" style="max-width:200px"><?= htmlspecialchars($m['subject']) ?></td>
              <td><small class="text-muted"><?= date('M d, Y', strtotime($m['created_at'])) ?></small></td>
            </tr>
          <?php endwhile; else: ?>
            <tr><td colspan="3" class="text-center text-muted py-3">No messages yet</td></tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="panel">
      <div class="panel-header"><h5>Recent Notices</h5><a href="notices.php" class="btn btn-sm btn-outline-primary">View all</a></div>
      <div class="table-responsive">
        <table class="table table-hover">
          <thead><tr><th>Title</th><th>Posted</th></tr></thead>
          <tbody>
          <?php if($recent_notices && $recent_notices->num_rows): while($n = $recent_notices->fetch_assoc()): ?>
            <tr>
              <td class="text-truncate" style="max-width:280px"><?= htmlspecialchars($n['title']) ?></td>
              <td><small class="text-muted"><?= date('M d, Y', strtotime($n['posted_on'])) ?></small></td>
            </tr>
          <?php endwhile; else: ?>
            <tr><td colspan="2" class="text-center text-muted py-3">No notices yet</td></tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
