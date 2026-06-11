<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/upload.php';
header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit;
}

$id = (int)($_POST['id'] ?? 0);
$type = $_POST['type'] ?? '';
$allowed = [
    'programs' => ['table' => 'programs',          'image_col' => null],
    'faculty'  => ['table' => 'faculty',           'image_col' => 'image'],
    'notices'  => ['table' => 'notices',           'image_col' => 'image'],
    'gallery'  => ['table' => 'gallery',           'image_col' => 'image'],
    'messages' => ['table' => 'contact_messages',  'image_col' => null],
];
if (!$id || !isset($allowed[$type])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']); exit;
}
$table = $allowed[$type]['table'];
$imgCol = $allowed[$type]['image_col'];

// Delete the file if any
if ($imgCol) {
    $res = $conn->query("SELECT $imgCol AS img FROM $table WHERE id=$id");
    if ($res && ($row = $res->fetch_assoc()) && !empty($row['img'])) {
        delete_upload($row['img']);
    }
}

$stmt = $conn->prepare("DELETE FROM $table WHERE id = ?");
$stmt->bind_param('i', $id);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
