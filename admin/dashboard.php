<?php
$page_title = 'Dashboard';
include 'includes/header.php';

// --- COUNTS ---
$counts = [
  'programs' => $conn->query("SELECT COUNT(*) c FROM programs")->fetch_assoc()['c'] ?? 0,
  'faculty' => $conn->query("SELECT COUNT(*) c FROM faculty")->fetch_assoc()['c'] ?? 0,
  'notices' => $conn->query("SELECT COUNT(*) c FROM notices")->fetch_assoc()['c'] ?? 0,
  'messages' => $conn->query("SELECT COUNT(*) c FROM contact_messages")->fetch_assoc()['c'] ?? 0,
];

// --- PAGINATION SETUP ---
$limit = 5;
$page_msgs = $_GET['page_msgs'] ?? 1;
$page_notices = $_GET['page_notices'] ?? 1;
$offset_msgs = ($page_msgs - 1) * $limit;
$offset_notices = ($page_notices - 1) * $limit;

// --- DATE FILTERS ---
$msg_where = '';
if (!empty($_GET['from_msg']) && !empty($_GET['to_msg'])) {
  $from = $_GET['from_msg'];
  $to = $_GET['to_msg'];
  $msg_where = "WHERE DATE(created_at) BETWEEN '$from' AND '$to'";
}

$notice_where = '';
if (!empty($_GET['from_notice']) && !empty($_GET['to_notice'])) {
  $from = $_GET['from_notice'];
  $to = $_GET['to_notice'];
  $notice_where = "WHERE DATE(posted_on) BETWEEN '$from' AND '$to'";
}

// --- QUERIES ---
$total_msgs = $conn->query("SELECT COUNT(*) AS total FROM contact_messages $msg_where")->fetch_assoc()['total'];
$total_pages_msgs = ceil($total_msgs / $limit);
$recent_msgs = $conn->query("SELECT * FROM contact_messages $msg_where ORDER BY id DESC LIMIT $limit OFFSET $offset_msgs");

$total_notices = $conn->query("SELECT COUNT(*) AS total FROM notices $notice_where")->fetch_assoc()['total'];
$total_pages_notices = ceil($total_notices / $limit);
$recent_notices = $conn->query("SELECT * FROM notices $notice_where ORDER BY posted_on DESC LIMIT $limit OFFSET $offset_notices");
?>

<!-- STATS -->
<div class="stat-grid">
  <a href="programs.php" class="stat-link">
    <div class="stat-card">
      <div><div class="label">Programs</div><div class="value"><?= $counts['programs'] ?></div></div>
      <div class="icon"><i class="bi bi-mortarboard"></i></div>
    </div>
  </a>
  <a href="faculty.php" class="stat-link">
    <div class="stat-card">
      <div><div class="label">Faculty</div><div class="value"><?= $counts['faculty'] ?></div></div>
      <div class="icon"><i class="bi bi-people"></i></div>
    </div>
  </a>
  <a href="notices.php" class="stat-link">
    <div class="stat-card">
      <div><div class="label">Notices</div><div class="value"><?= $counts['notices'] ?></div></div>
      <div class="icon"><i class="bi bi-megaphone"></i></div>
    </div>
  </a>
  <a href="messages.php" class="stat-link">
    <div class="stat-card">
      <div><div class="label">Messages</div><div class="value"><?= $counts['messages'] ?></div></div>
      <div class="icon"><i class="bi bi-envelope"></i></div>
    </div>
  </a>
</div>

<div class="row g-3">
  <!-- MESSAGES -->
  <div class="col-lg-6">
    <div class="panel">
      <div class="panel-header d-flex justify-content-between align-items-center">
        <h5>Recent Messages</h5>
        <a href="messages.php" class="btn btn-sm btn-outline-primary">View all</a>
      </div>

      <!-- Date filter -->
      <form method="get" class="d-flex gap-2 mb-3">
        <input type="date" name="from_msg" class="form-control" value="<?= $_GET['from_msg'] ?? '' ?>">
        <input type="date" name="to_msg" class="form-control" value="<?= $_GET['to_msg'] ?? '' ?>">
        <button class="btn btn-success btn-sm">Filter</button>
      </form>

      <div class="table-responsive">
        <table class="table table-hover">
          <thead><tr><th>Name</th><th>Subject</th><th>Date</th></tr></thead>
          <tbody>
            <?php if ($recent_msgs && $recent_msgs->num_rows):
              while ($m = $recent_msgs->fetch_assoc()): ?>
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

      <!-- Pagination -->
      <nav>
        <ul class="pagination justify-content-end">
          <?php for ($i = 1; $i <= $total_pages_msgs; $i++): ?>
            <li class="page-item <?= $i == $page_msgs ? 'active' : '' ?>">
              <a class="page-link" href="?page_msgs=<?= $i ?>"><?= $i ?></a>
            </li>
          <?php endfor; ?>
        </ul>
      </nav>
    </div>
  </div>

  <!-- NOTICES -->
  <div class="col-lg-6">
    <div class="panel">
      <div class="panel-header d-flex justify-content-between align-items-center">
        <h5>Recent Notices</h5>
        <a href="notices.php" class="btn btn-sm btn-outline-primary">View all</a>
      </div>

      <!-- Date filter -->
      <form method="get" class="d-flex gap-2 mb-3">
        <input type="date" name="from_notice" class="form-control" value="<?= $_GET['from_notice'] ?? '' ?>">
        <input type="date" name="to_notice" class="form-control" value="<?= $_GET['to_notice'] ?? '' ?>">
        <button class="btn btn-success btn-sm">Filter</button>
      </form>

      <div class="table-responsive">
        <table class="table table-hover">
          <thead><tr><th>Title</th><th>Posted</th></tr></thead>
          <tbody>
            <?php if ($recent_notices && $recent_notices->num_rows):
              while ($n = $recent_notices->fetch_assoc()): ?>
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

      <!-- Pagination -->
      <nav>
        <ul class="pagination justify-content-end">
          <?php for ($i = 1; $i <= $total_pages_notices; $i++): ?>
            <li class="page-item <?= $i == $page_notices ? 'active' : '' ?>">
              <a class="page-link" href="?page_notices=<?= $i ?>"><?= $i ?></a>
            </li>
          <?php endfor; ?>
        </ul>
      </nav>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
