<?php
if (!isset($page_title))
  $page_title = 'BPKMCH Nursing College';
$current = basename($_SERVER['PHP_SELF']);
function nav_active($file)
{
  global $current;
  return $current === $file ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($page_title); ?> | BPKMCH Nursing College</title>
  <meta name="description"
    content="BPKMCH Nursing College, Bharatpur (Cancer Gate) — quality nursing education in Nepal. BSc Nursing, PCL Nursing, BN and MN programs.">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Merriweather:wght@700&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container-fluid px-lg-5">
      <a class="navbar-brand d-flex align-items-center" href="index.php" style="gap:0.5rem;">
        <img src="assets/img/logo.png" alt="BPKMCH Logo"
          style="height:68px; width:68px; object-fit:contain; display:block;">
        <span class="brand-text d-flex flex-column justify-content-center">
          <strong style="font-size:1.05rem; line-height:1;">BPKMCH</strong>
          <small class="text-muted" style="line-height:1;">Nursing College</small>
        </span>
      </a>


      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav ms-auto align-items-lg-center">
          <li class="nav-item"><a class="nav-link <?php echo nav_active('index.php'); ?>" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link <?php echo nav_active('about.php'); ?>" href="about.php">About</a>
          </li>
          <li class="nav-item"><a class="nav-link <?php echo nav_active('programs.php'); ?>"
              href="programs.php">Programs</a></li>
          <li class="nav-item"><a class="nav-link <?php echo nav_active('faculty.php'); ?>"
              href="faculty.php">Faculty</a></li>
          <li class="nav-item"><a class="nav-link <?php echo nav_active('admissions.php'); ?>"
              href="admissions.php">Admissions</a></li>
          <!-- <li class="nav-item"><a class="nav-link" href="results.php">Results</a></li> -->

          <li class="nav-item"><a class="nav-link <?php echo nav_active('gallery.php'); ?>"
              href="gallery.php">Gallery</a></li>
          <li class="nav-item"><a class="nav-link <?php echo nav_active('notices.php'); ?>"
              href="notices.php">Notices</a></li>
          <li class="nav-item ms-lg-2"><a class="btn btn-primary btn-sm px-3" href="contact.php">Contact</a></li>
        </ul>
      </div>
    </div>
  </nav>