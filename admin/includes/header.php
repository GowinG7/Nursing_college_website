<?php
require_once __DIR__ . '/auth.php';
require_login();
$admin = current_admin();
$current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title ?? 'Admin') ?> | BPKMCH Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/admin.css" rel="stylesheet">

</head>

<body>

  <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

  <div class="admin-wrapper">

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
      <div class="brand">
        <span class="brand-logo">
          <img src="../assets/img/logo.png" alt="Lab Logo" class="logo-img">
        </span>
        <div>
          <strong>BPKMCH</strong>
          <small>Nursing College</small>
        </div>
        <button type="button" class="sidebar-close d-md-none" id="sidebarClose">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>

      <nav class="nav flex-column">
        <a href="dashboard.php" class="nav-link <?= $current == 'dashboard.php' ? 'active' : '' ?>">
          <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="programs.php" class="nav-link <?= $current == 'programs.php' ? 'active' : '' ?>">
          <i class="bi bi-mortarboard"></i> Programs
        </a>
        <a href="faculty.php" class="nav-link <?= $current == 'faculty.php' ? 'active' : '' ?>">
          <i class="bi bi-people"></i> Faculty
        </a>
        <a href="gallery.php" class="nav-link <?= $current == 'gallery.php' ? 'active' : '' ?>">
          <i class="bi bi-images"></i> Gallery
        </a>
        <a href="notices.php" class="nav-link <?= $current == 'notices.php' ? 'active' : '' ?>">
          <i class="bi bi-megaphone"></i> Notices
        </a>
        <a href="messages.php" class="nav-link <?= $current == 'messages.php' ? 'active' : '' ?>">
          <i class="bi bi-envelope"></i> Messages
        </a>
        <a href="../index.php" target="_blank" class="nav-link">
          <i class="bi bi-box-arrow-up-right"></i> View Site
        </a>
        <a href="logout.php" class="nav-link text-danger">
          <i class="bi bi-box-arrow-right"></i> Logout
        </a>
      </nav>
    </aside>

    <!-- Main -->
    <main class="main">

      <!-- Topbar -->
      <header class="topbar">
        <div class="topbar-left">
          <button type="button" class="hamburger d-md-none" id="hamburgerBtn">
            <i class="bi bi-list"></i>
          </button>
          <h4 class="page-title mb-0">
            <?= htmlspecialchars($page_title ?? 'Dashboard') ?>
          </h4>
        </div>

        <div class="topbar-right">
          <div class="user-info">
            <div class="avatar">
              <i class="bi bi-person-fill"></i>
            </div>
            <div class="user-meta">
              <div class="user-name">
                <?= htmlspecialchars($admin['full_name'] ?: $admin['username']) ?>
              </div>
              <div class="user-role">Administrator</div>
            </div>
          </div>
        </div>
      </header>

      <!-- Content wrapper -->
      <div class="content">