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
  <style>
    body {
      min-height: 100vh;
      margin: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      background:
        linear-gradient(135deg, rgba(15, 77, 42, .85) 0%, rgba(40, 167, 69, .75) 100%),
        url('../assets/img/bg-img.png') center/cover no-repeat fixed;
      font-family: 'Segoe UI', system-ui, sans-serif;
      padding: 1rem;
    }

    .login-card {
      background: #fff;
      width: 100%;
      max-width: 410px;
      border-radius: 16px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, .25);
      padding: 2.25rem 2rem;
    }

    .login-logo {
      width: 72px;
      height: 72px;
      border-radius: 50%;
      background: linear-gradient(135deg, #28a745, #0f4d2a);
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      margin: 0 auto 1rem;
      box-shadow: 0 8px 20px rgba(40, 167, 69, .35);
    }

    .login-card h3 {
      font-weight: 700;
      color: #0f4d2a;
      margin-bottom: .25rem;
    }

    .login-card .sub {
      color: #6c757d;
      font-size: .92rem;
      margin-bottom: 1.5rem;
    }

    .form-control {
      padding: .7rem .9rem;
      border-radius: 8px;
    }

    .form-control:focus {
      border-color: #28a745;
      box-shadow: 0 0 0 .2rem rgba(40, 167, 69, .18);
    }

    .input-group-text {
      background: #f1f5f3;
      border-right: 0;
      color: #0f4d2a;
    }

    .input-group .form-control {
      border-left: 0;
    }

    .btn-login {
      background: linear-gradient(135deg, #28a745, #0f4d2a);
      color: #fff;
      font-weight: 600;
      padding: .7rem;
      border-radius: 8px;
      border: 0;
      width: 100%;
      transition: transform .15s, box-shadow .15s;
    }

    .btn-login:hover {
      transform: translateY(-1px);
      box-shadow: 0 8px 18px rgba(40, 167, 69, .35);
      color: #fff;
    }

    .hint {
      font-size: .8rem;
      color: #6c757d;
      text-align: center;
      margin-top: 1rem;
    }
  </style>
</head>

<body>
  <div class="login-card">
    <div class="text-center">
      <div class="login-logo"><i class="bi bi-shield-lock"></i></div>
      <h3>BPKMCH Admin</h3>
      <p class="sub">Sign in to manage your website</p>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-danger py-2 small mb-3">
        <i class="bi bi-exclamation-triangle me-1"></i>
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
      <div class="mb-3">
        <label class="form-label fw-semibold">Username</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-person"></i></span>
          <input type="text" name="username" class="form-control" placeholder="Enter username" required autofocus>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold">Password</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-lock"></i></span>
          <input type="password" name="password" class="form-control" placeholder="Enter password" required>
        </div>
      </div>

      <button type="submit" class="btn-login">
        <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
      </button>
    </form>
  </div>
</body>

</html>