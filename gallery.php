<?php $page_title = 'Gallery';
include 'includes/header.php';
$items = [
  ['Campus Building', 'bi-building'],
  ['Skills Lab', 'bi-clipboard-pulse'],
  ['Library', 'bi-book'],
  ['Clinical Practice', 'bi-hospital'],
  ['Graduation Day', 'bi-mortarboard'],
  ['Community Outreach', 'bi-people'],
  ['Sports Day', 'bi-trophy'],
  ['Cultural Program', 'bi-music-note-beamed'],
];
?>
<header class="page-header">
  <div class="container">
    <h1>Gallery</h1>
    <nav>
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <li class="breadcrumb-item active">Gallery</li>
      </ol>
    </nav>
  </div>
</header>

<section>
  <div class="container">
    <h2 class="section-title">Life at BPKMCH</h2>
    <p class="section-subtitle">A glimpse into our campus, classrooms, clinical training and student life.</p>
    <div class="row g-3">
      <?php foreach ($items as $it): ?>
        <div class="col-6 col-md-4 col-lg-3">
          <div class="gallery-item">
            <i class="bi <?php echo $it[1]; ?>"></i>
            <span><?php echo $it[0]; ?></span>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
   
  </div>
</section>

<?php include 'includes/footer.php'; ?>