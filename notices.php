<?php
$page_title = 'Notices';
include 'includes/db.php';
include 'includes/header.php';

$rs = $conn->query("SELECT * FROM notices ORDER BY posted_on DESC");
?>

<header class="page-header">
  <div class="container">
    <h1>Notices & News</h1>
    <nav>
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <li class="breadcrumb-item active">Notices</li>
      </ol>
    </nav>
  </div>
</header>

<section>
  <div class="container">
    <div class="row">
      <div class="col-lg-9 mx-auto">

        <?php if ($rs->num_rows === 0): ?>
          <p class="text-muted">No notices yet.</p>
        <?php endif; ?>

        <?php while ($n = $rs->fetch_assoc()): ?>

          <div class="notice-item mb-4 p-3 border rounded">

            <!-- IMAGE (FIX ADDED HERE) -->
            <?php if (!empty($n['image'])): ?>
              <div class="mb-3">
                <img src="<?= htmlspecialchars($n['image']) ?>" alt="Notice Image" class="img-fluid rounded"
                  style="max-height:300px; object-fit:cover;">
              </div>
            <?php endif; ?>

            <h5>
              <?= htmlspecialchars($n['title']); ?>
            </h5>

            <div class="notice-date mb-2">
              <i class="bi bi-calendar3 me-1"></i>
              <?= date('M d, Y', strtotime($n['posted_on'])); ?>
            </div>

            <p class="mb-0 text-muted">
              <?= nl2br(htmlspecialchars($n['body'])); ?>
            </p>

          </div>

        <?php endwhile; ?>

      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>