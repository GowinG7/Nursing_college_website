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
<div class="admin-wrapper">
  <aside class="sidebar">
    <div class="brand">
      <i class="bi bi-heart-pulse-fill"></i>
      <div>
        <strong>BPKMCH</strong>
        <small>Admin Panel</small>
      </div>
    </div>
    <nav class="nav flex-column">
      <a href="dashboard.php" class="nav-link <?= $current=='dashboard.php'?'active':'' ?>"><i class="bi bi-speedometer2"></i> Dashboard</a>
      <a href="programs.php" class="nav-link <?= $current=='programs.php'?'active':'' ?>"><i class="bi bi-mortarboard"></i> Programs</a>
      <a href="faculty.php" class="nav-link <?= $current=='faculty.php'?'active':'' ?>"><i class="bi bi-people"></i> Faculty</a>
      <a href="notices.php" class="nav-link <?= $current=='notices.php'?'active':'' ?>"><i class="bi bi-megaphone"></i> Notices</a>
      <a href="messages.php" class="nav-link <?= $current=='messages.php'?'active':'' ?>"><i class="bi bi-envelope"></i> Messages</a>
      <a href="../index.php" target="_blank" class="nav-link"><i class="bi bi-box-arrow-up-right"></i> View Site</a>
      <a href="logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </nav>
  </aside>
  <main class="main">
    <header class="topbar">
      <h4 class="mb-0"><?= htmlspecialchars($page_title ?? 'Dashboard') ?></h4>
      <div class="user-info">
        <i class="bi bi-person-circle"></i>
        <span><?= htmlspecialchars($admin['full_name'] ?: $admin['username']) ?></span>
      </div>
    </header>
    <div class="content">
