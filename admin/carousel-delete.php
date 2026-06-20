<?php

require '../includes/db.php';
require 'includes/auth.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: carousel.php");
    exit;
}

/*
 Get Slide
*/
$stmt = $conn->prepare("
    SELECT media_file
    FROM carousel
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $row = $result->fetch_assoc();

    /*
    Delete Uploaded File 
    */
    if (
        !empty($row['media_file']) &&
        file_exists("../" . $row['media_file'])
    ) {
        unlink("../" . $row['media_file']);
    }

    /*   
    Delete Database Record  
    */
    $delete = $conn->prepare("
        DELETE FROM carousel
        WHERE id = ?
    ");

    $delete->bind_param("i", $id);
    $delete->execute();
}

header("Location: carousel.php");
exit;