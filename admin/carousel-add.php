<?php

$page_title = "Add Carousel Slide";

require '../includes/db.php';
require 'includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $media_type = trim($_POST['media_type']);
    $title = trim($_POST['title']);
    $subtitle = trim($_POST['subtitle']);

    if (!isset($_FILES['media_file']) || $_FILES['media_file']['error'] !== 0) {
        die("Please select a file.");
    }

    $file = $_FILES['media_file'];

    $filename = time() . '_' . basename($file['name']);

    $allowed = [
        'jpg',
        'jpeg',
        'png',
        'webp',
        'gif',
        'mp4'
    ];

    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        die("Invalid file type.");
    }

    $uploadDir = "../uploads/carousel/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
        die("File upload failed.");
    }

    $media_file = "uploads/carousel/" . $filename;

    $stmt = $conn->prepare("
        INSERT INTO carousel
        (
            media_type,
            media_file,
            title,
            subtitle
        )
        VALUES
        (?,?,?,?)
    ");

    $stmt->bind_param(
        "ssss",
        $media_type,
        $media_file,
        $title,
        $subtitle
    );

    $stmt->execute();

    header("Location: carousel.php");
    exit;
}

include 'includes/header.php';
?>

<div class="card shadow-sm border-0">

    <div class="card-header bg-white">
        <h5 class="mb-0">Add Carousel Slide</h5>
    </div>

    <div class="card-body">

        <form method="POST" enctype="multipart/form-data">

            <div class="row">

                <div class="col-md-3 mb-3">
                    <label class="form-label">Media Type</label>

                    <select name="media_type" class="form-select" required>
                        <option value="image">Image</option>
                        <option value="video">Video</option>
                    </select>
                </div>

            </div>

            <div class="mb-3">
                <label class="form-label">Upload File</label>

                <input type="file" name="media_file" class="form-control" accept=".jpg,.jpeg,.png,.webp,.gif,.mp4"
                    required>

                <small class="text-muted">
                    Supported formats: JPG, PNG, WEBP, GIF, MP4
                </small>
            </div>

            <div class="mb-3">
                <label class="form-label">Title</label>

                <input type="text" name="title" class="form-control" placeholder="Enter slide title" required>
            </div>

            <div class="mb-4">
                <label class="form-label">Description</label>

                <textarea name="subtitle" rows="4" class="form-control"
                    placeholder="Enter slide description"></textarea>
            </div>

            <button type="submit" class="btn btn-success">
                <i class="bi bi-check-circle me-1"></i>
                Save Slide
            </button>

            <a href="carousel.php" class="btn btn-light border ms-2">
                Cancel
            </a>

        </form>

    </div>

</div>

<?php include 'includes/footer.php'; ?>