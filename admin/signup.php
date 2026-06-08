<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (
        empty($full_name) ||
        empty($email) ||
        empty($username) ||
        empty($password) ||
        empty($confirm_password)
    ) {
        $error = "Please fill in all fields.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {

        $check = $conn->prepare("
            SELECT id 
            FROM admins
            WHERE username = ? OR email = ?
            LIMIT 1
        ");

        $check->bind_param("ss", $username, $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = "Username or email already exists.";
        } else {

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $insert = $conn->prepare("
                INSERT INTO admins
                (full_name, email, username, password)
                VALUES (?, ?, ?, ?)
            ");

            $insert->bind_param(
                "ssss",
                $full_name,
                $email,
                $username,
                $hashed_password
            );

            if ($insert->execute()) {
                $success = "Account created successfully.";
            } else {
                $error = "Failed to create account.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Signup | BPKMCH</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container">
        <div class="row justify-content-center min-vh-100 align-items-center">

            <div class="col-md-6 col-lg-5">

                <div class="card shadow border-0">

                    <div class="card-body p-4">

                        <div class="text-center mb-4">
                            <h2 class="fw-bold">BPKMCH Admin</h2>
                            <p class="text-muted">
                                Create Administrator Account
                            </p>
                        </div>

                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <?= htmlspecialchars($success) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST">

                            <div class="mb-3">
                                <label class="form-label">
                                    Full Name
                                </label>

                                <input type="text" name="full_name" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Email
                                </label>

                                <input type="email" name="email" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Username
                                </label>

                                <input type="text" name="username" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Password
                                </label>

                                <input type="password" name="password" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Confirm Password
                                </label>

                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">

                                <i class="bi bi-person-plus-fill"></i>
                                Create Account
                            </button>

                        </form>

                        <div class="text-center mt-3">
                            <a href="login.php">
                                Already have an account? Login
                            </a>
                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>

</body>

</html>