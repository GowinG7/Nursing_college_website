<?php
$page_title = 'Faculty';

include 'includes/db.php';
include 'includes/header.php';

$rs = $conn->query("SELECT * FROM faculty ORDER BY id ASC");
?>

<header class="page-header">
  <div class="container">
    <h1>Meet Our Faculty</h1>

    <nav>
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item">
          <a href="index.php">Home</a>
        </li>
        <li class="breadcrumb-item active">
          Faculty
        </li>
      </ol>
    </nav>
  </div>
</header>

<section>
  <div class="container">

    <h2 class="section-title">Experienced Educators & Clinicians</h2>

    <p class="section-subtitle">
      Our faculty combines decades of bedside experience with academic excellence.
    </p>

    <div class="row g-4">

      <?php while ($f = $rs->fetch_assoc()): ?>

        <?php
        $initials = '';

        if (!empty($f['full_name'])) {
          $parts = explode(' ', trim($f['full_name']));

          $initials .= strtoupper(substr($parts[0], 0, 1));

          if (count($parts) > 1) {
            $initials .= strtoupper(substr(end($parts), 0, 1));
          }
        }

        $imagePath = $f['image'];
        $hasImage = !empty($imagePath) && file_exists($imagePath);
        ?>

        <div class="col-md-6 col-lg-4">
          <div class="faculty-card">

            <div class="faculty-avatar">

              <?php if ($hasImage): ?>

                <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($f['full_name']) ?>"
                  style="width:100%; height:100%; object-fit:cover; border-radius:50%;">

              <?php else: ?>

                <?= htmlspecialchars($initials) ?>

              <?php endif; ?>

            </div>

            <h5 class="mb-1">
              <?= htmlspecialchars($f['full_name']) ?>
            </h5>

            <p class="text-green small mb-1">
              <?= htmlspecialchars($f['designation']) ?>
            </p>

            <p class="text-muted small mb-2">
              <?= htmlspecialchars($f['qualification']) ?>
            </p>

            <?php if (!empty($f['email'])): ?>
              <a href="mailto:<?= htmlspecialchars($f['email']) ?>" class="small text-muted">
                <i class="bi bi-envelope me-1"></i>
                <?= htmlspecialchars($f['email']) ?>
              </a>
            <?php endif; ?>

          </div>
        </div>

      <?php endwhile; ?>

    </div>

  </div>
</section>

<?php include 'includes/footer.php'; ?>