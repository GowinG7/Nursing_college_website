<?php
$page_title = 'Contact Messages';
require_once __DIR__ . '/includes/auth.php';
require_login();

// --- Pagination setup ---
$limit = 10; // messages per page
$page = $_GET['page'] ?? 1;
$offset = ($page - 1) * $limit;

// --- Date filter setup ---
$filter = $_GET['filter'] ?? '';
$where = '';
switch ($filter) {
  case 'today':
    $where = "WHERE DATE(created_at) = CURDATE()";
    break;
  case '7days':
    $where = "WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
    break;
  case '30days':
    $where = "WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
    break;
  case 'month':
    $where = "WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
    break;
}

// --- Custom date range filter ---
if (!empty($_GET['from']) && !empty($_GET['to'])) {
  $from = $_GET['from'];
  $to = $_GET['to'];
  $where = "WHERE DATE(created_at) BETWEEN '$from' AND '$to'";
}

// --- Queries ---
$total = $conn->query("SELECT COUNT(*) AS c FROM contact_messages $where")->fetch_assoc()['c'];
$total_pages = ceil($total / $limit);

$rows = $conn->query("SELECT * FROM contact_messages $where ORDER BY id DESC LIMIT $limit OFFSET $offset");

include 'includes/header.php';
?>
<div class="panel">
  <div class="panel-header d-flex justify-content-between align-items-center">
    <h5>All Messages</h5>
    <span class="badge-soft"><?= $total ?> total</span>
  </div>

  <!-- Filter buttons -->
  <div class="mb-3 d-flex flex-wrap gap-2">
    <a href="?filter=today" class="btn btn-sm btn-outline-primary <?= $filter == 'today' ? 'active' : '' ?>">Today</a>
    <a href="?filter=7days" class="btn btn-sm btn-outline-primary <?= $filter == '7days' ? 'active' : '' ?>">Last 7 Days</a>
    <a href="?filter=30days" class="btn btn-sm btn-outline-primary <?= $filter == '30days' ? 'active' : '' ?>">Last 30
      Days</a>
    <a href="?filter=month" class="btn btn-sm btn-outline-primary <?= $filter == 'month' ? 'active' : '' ?>">This Month</a>
    <a href="messages.php"
      class="btn btn-sm btn-outline-secondary <?= $filter == '' && empty($_GET['from']) ? 'active' : '' ?>">All</a>
  </div>

  <!-- Custom date range filter -->
  <form method="get" class="d-flex gap-2 mb-3">
    <input type="date" name="from" class="form-control" value="<?= $_GET['from'] ?? '' ?>">
    <input type="date" name="to" class="form-control" value="<?= $_GET['to'] ?? '' ?>">
    <button class="btn btn-success btn-sm">Filter</button>
  </form>

  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead>
        <tr>
          <th>From</th>
          <th>Subject</th>
          <th>Message</th>
          <th>Date</th>
          <th class="text-end">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($rows && $rows->num_rows):
          while ($m = $rows->fetch_assoc()): ?>
            <tr>
              <td>
                <strong><?= htmlspecialchars($m['name']) ?></strong><br>
                <small class="text-muted"><?= htmlspecialchars($m['email']) ?></small>
                <?php if (!empty($m['phone'])): ?><br><small><?= htmlspecialchars($m['phone']) ?></small><?php endif; ?>
              </td>
              <td><?= htmlspecialchars($m['subject']) ?></td>
              <td style="max-width:340px"><?= nl2br(htmlspecialchars($m['message'])) ?></td>
              <td><small class="text-muted"><?= date('M d, Y', strtotime($m['created_at'])) ?></small></td>
              <td class="text-end">
                <a href="mailto:<?= htmlspecialchars($m['email']) ?>" class="btn btn-sm btn-outline-primary"><i
                    class="bi bi-reply"></i></a>
                <button class="btn btn-sm btn-outline-danger btn-delete" data-id="<?= $m['id'] ?>" data-type="messages"><i
                    class="bi bi-trash"></i></button>
              </td>
            </tr>
          <?php endwhile; else: ?>
          <tr>
            <td colspan="5" class="text-center text-muted py-4">No messages found</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <nav>
    <ul class="pagination justify-content-end">
      <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
          <a class="page-link"
            href="?page=<?= $i ?>&filter=<?= $filter ?>&from=<?= $_GET['from'] ?? '' ?>&to=<?= $_GET['to'] ?? '' ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>
</div>
<?php include 'includes/footer.php'; ?>