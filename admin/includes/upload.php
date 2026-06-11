<?php
// Shared image upload helper
function handle_image_upload($field, $subdir = 'general') {
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    $file = $_FILES[$field];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $allowed = ['jpg','jpeg','png','gif','webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) {
        return null;
    }
    if ($file['size'] > 5 * 1024 * 1024) { // 5MB
        return null;
    }
    $base = realpath(__DIR__ . '/../../') . '/uploads/' . $subdir;
    if (!is_dir($base)) {
        mkdir($base, 0775, true);
    }
    $name = uniqid($subdir . '_', true) . '.' . $ext;
    $dest = $base . '/' . $name;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return null;
    }
    // return web-relative path from project root
    return 'uploads/' . $subdir . '/' . $name;
}

function delete_upload($relative_path) {
    if (!$relative_path) return;
    $abs = realpath(__DIR__ . '/../../') . '/' . ltrim($relative_path, '/');
    if (is_file($abs)) {
        @unlink($abs);
    }
}
