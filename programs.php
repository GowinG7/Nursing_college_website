<?php $page_title = 'Programs';
include 'includes/db.php';
include 'includes/header.php';
$rs = $conn->query("SELECT * FROM programs ORDER BY id ASC");
?>
<header class="page-header">
  <div class="container">
    <h1>Academic Programs</h1>
    <nav>
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <li class="breadcrumb-item active">Programs</li>
      </ol>
    </nav>
  </div>
</header>

<section>
  <div class="container">
    <h2 class="section-title">Our Nursing Programs</h2>
    <p class="section-subtitle">Choose from a range of accredited programs designed to fit every stage of your nursing
      career.</p>
    <div class="row g-4">
      <?php while ($p = $rs->fetch_assoc()): ?>
        <div class="col-md-6">
          <div class="program-card h-100">
            <div class="card-top d-flex justify-content-between align-items-center">
              <div>
                <h4 class="mb-1"><?php echo htmlspecialchars($p['name']); ?></h4>
                <div class="program-meta">
                  <span><i class="bi bi-clock me-1"></i><?php echo htmlspecialchars($p['duration']); ?></span>
                  <span><i class="bi bi-people me-1"></i><?php echo (int) $p['seats']; ?> seats</span>
                </div>
              </div>
              <i class="bi bi-journal-medical fs-1 text-green"></i>
            </div>
            <div class="card-body">
              <p class="text-muted"><?php echo htmlspecialchars($p['description']); ?></p>
              <a href="admissions.php" class="btn btn-sm btn-outline-primary">Apply for this program</a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>