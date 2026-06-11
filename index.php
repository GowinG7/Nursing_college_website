<?php
$page_title = 'Home';
include 'includes/db.php';
include 'includes/header.php';

// Latest notices
$notices = $conn->query("SELECT * FROM notices ORDER BY posted_on DESC LIMIT 3");

// Programs preview
$programs = $conn->query("SELECT * FROM programs ORDER BY id ASC LIMIT 4");

// Dynamic counts
$programsCountQuery = $conn->query("SELECT COUNT(*) AS total_programs FROM programs");
$programCount = $programsCountQuery->fetch_assoc()['total_programs'] ?? 0;

$facultyCountQuery = $conn->query("SELECT COUNT(*) AS total_faculty FROM faculty");
$facultyCount = $facultyCountQuery->fetch_assoc()['total_faculty'] ?? 0;

// Static values
$alumniCount = "1500+";
$clinicalExposure = "100%";
?>

<!-- HERO -->
<section class="hero d-flex align-items-center text-light" style="background-image: linear-gradient(rgba(0,0,0,0.35), rgba(0,0,0,0.35)), url('assets/img/bg-img.png');
                background-position: center;
                background-size: cover;
                background-repeat: no-repeat;
                min-height: 550px;
                position: relative;">
  <div class="container position-relative">
    <div class="row">
      <div class="col-lg-7">
        <span class="badge-pill"><i class="bi bi-award-fill me-1"></i> Est. — Bharatpur, Nepal</span>
        <h1 class="mt-3">Shaping Nepal's Next Generation of
          <span style="color:#a6f0c6">Nursing Leaders</span>
        </h1>
        <p class="lead mt-3">
          BPKMCH Nursing College, Cancer Gate Bharatpur — delivering quality nursing education,
          clinical excellence and compassionate care since inception.
        </p>
        <div class="d-flex flex-wrap gap-2 mt-4">
          <a href="admissions.php" class="btn btn-light px-4">Apply for Admission</a>
          <a href="programs.php" class="btn btn-outline-light px-4">Explore Programs</a>
        </div>
      </div>
    </div>
  </div>
</section>

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
      <div class="col-md-4">
        <div class="feature-card">
          <div class="feature-icon"><i class="bi bi-hospital"></i></div>
          <h5>Hospital-Attached Campus</h5>
          <p class="text-muted mb-0">Hands-on training inside one of Nepal's leading cancer hospitals.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="feature-card">
          <div class="feature-icon"><i class="bi bi-mortarboard"></i></div>
          <h5>Recognized Curriculum</h5>
          <p class="text-muted mb-0">Affiliated programs under TU and CTEVT, designed for global standards.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="feature-card">
          <div class="feature-icon"><i class="bi bi-people"></i></div>
          <h5>Experienced Faculty</h5>
          <p class="text-muted mb-0">Mentorship from senior clinicians, researchers and educators.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="feature-card">
          <div class="feature-icon"><i class="bi bi-globe2"></i></div>
          <h5>Global Career Pathways</h5>
          <p class="text-muted mb-0">Graduates serving in Nepal, Australia, UK, USA and the Middle East.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="feature-card">
          <div class="feature-icon"><i class="bi bi-cash-coin"></i></div>
          <h5>Scholarships</h5>
          <p class="text-muted mb-0">Government and merit-based scholarships for eligible students.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="feature-card">
          <div class="feature-icon"><i class="bi bi-heart"></i></div>
          <h5>Care-First Culture</h5>
          <p class="text-muted mb-0">Empathy, ethics and patient dignity at the core of everything we teach.</p>
        </div>
      </div>
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

<!-- NOTICES -->
<section>
  <div class="container">
    <div class="d-flex justify-content-between align-items-end mb-4">
      <div>
        <h2 class="section-title mb-0">Latest Notices</h2>
        <p class="text-muted mb-0">Stay updated with announcements and events.</p>
      </div>
      <a href="notices.php" class="btn btn-outline-primary btn-sm">View All</a>
    </div>
    <?php while ($n = $notices->fetch_assoc()): ?>
      <div class="notice-item">
        <h5><?= htmlspecialchars($n['title']); ?></h5>
        <div class="notice-date mb-1"><i
            class="bi bi-calendar3 me-1"></i><?= date('M d, Y', strtotime($n['posted_on'])); ?></div>
        <p class="mb-0 text-muted small"><?= htmlspecialchars($n['body']); ?></p>
      </div>
    <?php endwhile; ?>
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