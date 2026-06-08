<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $password = $_POST['password'] ?? '';
  if ($username && $password) {
    $stmt = $conn->prepare("SELECT id, username, password, full_name FROM admins WHERE username = ? LIMIT 1");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    if ($res && password_verify($password, $res['password'])) {
      $_SESSION['admin_id'] = $res['id'];
      $_SESSION['admin_username'] = $res['username'];
      $_SESSION['admin_name'] = $res['full_name'];
      header('Location: dashboard.php');
      exit;
    }
    $error = 'Invalid username or password';
  } else {
    $error = 'Please fill in all fields';
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Admin Login | BPKMCH</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/admin.css" rel="stylesheet">
</head>

<body class="login-page">
  <form method="post" class="login-card">
    <div class="logo"><i class="bi bi-heart-pulse-fill"></i></div>
    <h3>BPKMCH Admin</h3>
    <p class="subtitle">Sign in to manage your website</p>
    <?php if ($error): ?>
      <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <div class="mb-3">
      <label class="form-label">Username</label>
      <input type="text" name="username" class="form-control" required autofocus>
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <button class="btn btn-primary w-100 py-2">Sign In</button>
  </form>
</body>

</html>