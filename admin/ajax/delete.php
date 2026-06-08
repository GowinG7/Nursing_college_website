<?php
require_once __DIR__ . '/../includes/auth.php';
header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit;
}

$id = (int)($_POST['id'] ?? 0);
$type = $_POST['type'] ?? '';
$allowed = [
    'programs' => 'programs',
    'faculty'  => 'faculty',
    'notices'  => 'notices',
    'messages' => 'contact_messages',
];
if (!$id || !isset($allowed[$type])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']); exit;
}
$table = $allowed[$type];
$stmt = $conn->prepare("DELETE FROM $table WHERE id = ?");
$stmt->bind_param('i', $id);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
