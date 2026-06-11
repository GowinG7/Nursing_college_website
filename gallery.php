<?php

$page_title = 'Gallery';

include 'includes/db.php';
include 'includes/header.php';

$rs = $conn->query("SELECT * FROM gallery ORDER BY uploaded_on DESC");

?>

<header class="page-header">
  <div class="container">
    <h1>Gallery</h1>

    <nav>
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item">
          <a href="index.php">Home</a>
        </li>
        <li class="breadcrumb-item active">
          Gallery
        </li>
      </ol>
    </nav>
  </div>
</header>

<section>
  <div class="container">

    <h2 class="section-title">Life at BPKMCH</h2>

    <p class="section-subtitle">
      A glimpse into our campus, classrooms, clinical training and student life.
    </p>

    <div class="row g-4">

      <?php if ($rs && $rs->num_rows > 0): ?>

        <?php while ($g = $rs->fetch_assoc()): ?>

          <?php
          $imagePath = $g['image'];
          $hasImage = !empty($imagePath) && file_exists($imagePath);
          ?>

          <div class="col-sm-6 col-md-4 col-lg-3">

            <div class="card border-0 shadow-sm h-100">

              <?php if ($hasImage): ?>

                <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($g['title']) ?>" class="card-img-top"
                  style="height:220px; object-fit:cover;">

              <?php else: ?>

                <div class="d-flex align-items-center justify-content-center bg-light" style="height:220px;">
                  <i class="bi bi-image fs-1 text-muted"></i>
                </div>

              <?php endif; ?>

              <div class="card-body">

                <h6 class="card-title mb-2">
                  <?= htmlspecialchars($g['title']) ?>
                </h6>

                <?php if (!empty($g['caption'])): ?>
                  <p class="card-text small text-muted mb-2">
                    <?= htmlspecialchars($g['caption']) ?>
                  </p>
                <?php endif; ?>

                <small class="text-muted">
                  <?= date('M d, Y', strtotime($g['uploaded_on'])) ?>
                </small>

              </div>

            </div>

          </div>

        <?php endwhile; ?>

      <?php else: ?>

        <div class="col-12 text-center py-5">
          <i class="bi bi-images fs-1 text-muted"></i>
          <p class="text-muted mt-3">
            No gallery images available yet.
          </p>
        </div>

      <?php endif; ?>

    </div>

  </div>
</section>

<?php include 'includes/footer.php'; ?>