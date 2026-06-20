<?php
$page_title = 'Home';
include 'includes/db.php';
include 'includes/header.php';

// Fetch carousel slides
$slides = $conn->query("SELECT * FROM carousel ORDER BY id DESC");

// Latest notices
$notices = $conn->query("SELECT * FROM notices ORDER BY posted_on DESC LIMIT 3");

// Programs preview
$programs = $conn->query("SELECT * FROM programs ORDER BY id ASC LIMIT 4");

// Dynamic counts
$programCount = ($conn->query("SELECT COUNT(*) AS total_programs FROM programs")->fetch_assoc()['total_programs']) ?? 0;
$facultyCount = ($conn->query("SELECT COUNT(*) AS total_faculty FROM faculty")->fetch_assoc()['total_faculty']) ?? 0;

// Static values
$alumniCount = "1500+";
$clinicalExposure = "100%";
?>

<!-- HERO CAROUSEL -->
<div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel" data-bs-interval="5000"
  data-bs-pause="false">
  <div class="carousel-inner">
    <?php $active = true;
    while ($slide = $slides->fetch_assoc()): ?>
      <div class="carousel-item <?= $active ? 'active' : '' ?>">
        <div class="hero-slide position-relative">
          <?php if ($slide['media_type'] === 'image'): ?>
            <img src="<?= htmlspecialchars($slide['media_file']) ?>" alt="<?= htmlspecialchars($slide['title']) ?>"
              class="hero-media">
          <?php else: ?>
            <video autoplay muted loop playsinline class="hero-media">
              <source src="<?= htmlspecialchars($slide['media_file']) ?>" type="video/mp4">
            </video>
          <?php endif; ?>

          <div class="hero-overlay"></div>

          <div class="hero-content container">
            <span class="badge-pill">
              <i class="bi bi-award-fill me-1"></i> Est. — Bharatpur, Nepal
            </span>
            <h1 class="mt-3"><?= htmlspecialchars($slide['title']) ?></h1>
            <?php if (!empty($slide['subtitle'])): ?>
              <p class="lead mt-3"><?= htmlspecialchars($slide['subtitle']) ?></p>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php $active = false; endwhile; ?>
  </div>

  <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
  </button>
</div>

<!-- STATS -->
<div class="container">
  <div class="stats-strip text-center">
    <div class="row">
      <div class="col-6 col-md-3 stat-item">
        <h3><?= $alumniCount ?></h3>
        <p>Alumni Nurses</p>
      </div>
      <div class="col-6 col-md-3 stat-item">
        <h3><?= $facultyCount ?></h3>
        <p>Expert Faculty</p>
      </div>
      <div class="col-6 col-md-3 stat-item">
        <h3><?= $programCount ?></h3>
        <p>Programs Offered</p>
      </div>
      <div class="col-6 col-md-3 stat-item">
        <h3><?= $clinicalExposure ?></h3>
        <p>Clinical Exposure</p>
      </div>
    </div>
  </div>
</div>

<!-- WHY US -->
<section>
  <div class="container">
    <h2 class="section-title">Why Choose BPKMCH</h2>
    <p class="section-subtitle">A learning environment built around clinical excellence, compassionate care, and the
      realities of Nepal's healthcare system.</p>
    <div class="row g-4">
      <?php
      $features = [
        ['icon' => 'hospital', 'title' => 'Hospital-Attached Campus', 'desc' => 'Hands-on training inside one of Nepal\'s leading cancer hospitals.'],
        ['icon' => 'mortarboard', 'title' => 'Recognized Curriculum', 'desc' => 'Affiliated programs under TU and CTEVT, designed for global standards.'],
        ['icon' => 'people', 'title' => 'Experienced Faculty', 'desc' => 'Mentorship from senior clinicians, researchers and educators.'],
        ['icon' => 'globe2', 'title' => 'Global Career Pathways', 'desc' => 'Graduates serving in Nepal, Australia, UK, USA and the Middle East.'],
        ['icon' => 'cash-coin', 'title' => 'Scholarships', 'desc' => 'Government and merit-based scholarships for eligible students.'],
        ['icon' => 'heart', 'title' => 'Care-First Culture', 'desc' => 'Empathy, ethics and patient dignity at the core of everything we teach.']
      ];
      foreach ($features as $f): ?>
        <div class="col-md-4">
          <div class="feature-card">
            <div class="feature-icon"><i class="bi bi-<?= $f['icon'] ?>"></i></div>
            <h5><?= $f['title'] ?></h5>
            <p class="text-muted mb-0"><?= $f['desc'] ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- PROGRAMS -->
<section class="bg-green-soft">
  <div class="container">
    <h2 class="section-title">Our Programs</h2>
    <p class="section-subtitle">From certificate-level training to masters research — pathways for every stage of your
      nursing career.</p>
    <div class="row g-4">
      <?php while ($p = $programs->fetch_assoc()): ?>
        <div class="col-md-6 col-lg-3">
          <div class="program-card">
            <div class="card-top">
              <i class="bi bi-journal-medical fs-3"></i>
              <h5 class="mt-2 mb-0"><?= htmlspecialchars($p['name']); ?></h5>
            </div>
            <div class="card-body">
              <div class="program-meta mb-2">
                <span><i class="bi bi-clock me-1"></i><?= htmlspecialchars($p['duration']); ?></span>
                <span><i class="bi bi-people me-1"></i><?= (int) $p['seats']; ?> seats</span>
              </div>
              <p class="text-muted small mb-0"><?= htmlspecialchars(substr($p['description'], 0, 100)); ?>...</p>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
    <div class="text-center mt-4">
      <a href="programs.php" class="btn btn-primary px-4">View All Programs</a>
    </div>
  </div>
</section>

<section class="bg-light">

  <div class="container">

    <div class="d-flex justify-content-between align-items-end mb-4">

      <div>

        <h2 class="section-title mb-0">
          Latest Notices
        </h2>

        <p class="section-subtitle mb-0">
          Stay updated with announcements and events.
        </p>

      </div>

      <a href="notices.php" class="btn btn-outline-primary">
        View All
      </a>

    </div>

    <div class="row g-4">

      <?php while ($n = $notices->fetch_assoc()): ?>

        <div class="col-md-6 col-lg-4">

          <div class="notice-card h-100">

            <?php if (!empty($n['image']) && file_exists($n['image'])): ?>

              <img src="<?= htmlspecialchars($n['image']) ?>" class="notice-image"
                alt="<?= htmlspecialchars($n['title']) ?>">

            <?php endif; ?>

            <div class="notice-content">

              <div class="notice-date">

                <i class="bi bi-calendar3 me-1"></i>

                <?= date('M d, Y', strtotime($n['posted_on'])) ?>

              </div>

              <h5 class="notice-title">

                <?= htmlspecialchars($n['title']) ?>

              </h5>

              <p class="notice-body">

                <?= htmlspecialchars(substr($n['body'], 0, 120)) ?>

                <?= strlen($n['body']) > 120 ? '...' : '' ?>

              </p>

            </div>

          </div>

        </div>

      <?php endwhile; ?>

    </div>

  </div>

</section>

<!-- CTA -->
<section>
  <div class="container">
    <div class="cta-band">
      <h2>Ready to begin your nursing journey?</h2>
      <p class="mb-4">Applications for the upcoming academic session are now open. Limited seats available.</p>
      <a href="admissions.php" class="btn btn-light px-4">Apply Now <i class="bi bi-arrow-right ms-1"></i></a>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>