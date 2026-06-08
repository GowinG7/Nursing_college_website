<?php
session_start();
require_once __DIR__ . '/../../includes/db.php';

function is_logged_in() {
    return isset($_SESSION['admin_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function current_admin() {
    return [
        'id' => $_SESSION['admin_id'] ?? null,
        'username' => $_SESSION['admin_username'] ?? '',
        'full_name' => $_SESSION['admin_name'] ?? '',
    ];
}
