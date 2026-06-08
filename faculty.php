<?php $page_title = 'Faculty';
include 'includes/db.php';
include 'includes/header.php';
$rs = $conn->query("SELECT * FROM faculty ORDER BY id ASC");
?>
<header class="page-header">
  <div class="container">
    <h1>Meet Our Faculty</h1>
    <nav>
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <li class="breadcrumb-item active">Faculty</li>
      </ol>
    </nav>
  </div>
</header>

<section>
  <div class="container">
    <h2 class="section-title">Experienced Educators & Clinicians</h2>
    <p class="section-subtitle">Our faculty combines decades of bedside experience with academic excellence.</p>
    <div class="row g-4">
      <?php while ($f = $rs->fetch_assoc()):
        $initials = strtoupper(substr($f['full_name'], 0, 1));
        $parts = explode(' ', $f['full_name']);
        if (count($parts) > 1)
          $initials .= strtoupper(substr($parts[count($parts) - 1], 0, 1));
        ?>
        <div class="col-md-6 col-lg-4">
          <div class="faculty-card">
            <div class="faculty-avatar"><?php echo $initials; ?></div>
            <h5 class="mb-1"><?php echo htmlspecialchars($f['full_name']); ?></h5>
            <p class="text-green small mb-1"><?php echo htmlspecialchars($f['designation']); ?></p>
            <p class="text-muted small mb-2"><?php echo htmlspecialchars($f['qualification']); ?></p>
            <?php if ($f['email']): ?>
              <a href="mailto:<?php echo htmlspecialchars($f['email']); ?>" class="small text-muted"><i
                  class="bi bi-envelope me-1"></i><?php echo htmlspecialchars($f['email']); ?></a>
            <?php endif; ?>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>