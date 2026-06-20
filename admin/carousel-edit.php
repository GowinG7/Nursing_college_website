<?php

$page_title = "Edit Carousel Slide";

require '../includes/db.php';
require 'includes/auth.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: carousel.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Get Slide
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT *
    FROM carousel
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: carousel.php");
    exit;
}

$row = $result->fetch_assoc();

/*
|--------------------------------------------------------------------------
| Update Slide
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $media_type = trim($_POST['media_type']);
    $title = trim($_POST['title']);
    $subtitle = trim($_POST['subtitle']);

    $media_file = $row['media_file'];

    /*
    |--------------------------------------------------------------------------
    | Replace Media (Optional)
    |--------------------------------------------------------------------------
    */
    if (!empty($_FILES['media_file']['name'])) {

        $uploadDir = "../uploads/carousel/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filename = time() . '_' . basename($_FILES['media_file']['name']);

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

        if (
            !empty($row['media_file']) &&
            file_exists("../" . $row['media_file'])
        ) {
            unlink("../" . $row['media_file']);
        }

        move_uploaded_file(
            $_FILES['media_file']['tmp_name'],
            $uploadDir . $filename
        );

        $media_file = "uploads/carousel/" . $filename;
    }

    /*
    |--------------------------------------------------------------------------
    | Update Database
    |--------------------------------------------------------------------------
    */
    $update = $conn->prepare("
        UPDATE carousel
        SET
            media_type = ?,
            media_file = ?,
            title = ?,
            subtitle = ?
        WHERE id = ?
    ");

    $update->bind_param(
        "ssssi",
        $media_type,
        $media_file,
        $title,
        $subtitle,
        $id
    );

    $update->execute();

    header("Location: carousel.php");
    exit;
}

include 'includes/header.php';
?>

<div class="card shadow-sm border-0">

    <div class="card-header bg-white">
        <h5 class="mb-0">Edit Carousel Slide</h5>
    </div>

    <div class="card-body">

        <form method="POST" enctype="multipart/form-data">

            <div class="row">

                <div class="col-md-3 mb-3">
                    <label class="form-label">Media Type</label>

                    <select name="media_type" class="form-select">

                        <option value="image" <?= $row['media_type'] === 'image' ? 'selected' : '' ?>>
                            Image
                        </option>

                        <option value="video" <?= $row['media_type'] === 'video' ? 'selected' : '' ?>>
                            Video
                        </option>

                    </select>
                </div>

            </div>

            <div class="mb-3">
                <label class="form-label">Current Media</label>

                <div>

                    <?php if ($row['media_type'] === 'image'): ?>

                        <img src="../<?= htmlspecialchars($row['media_file']) ?>" class="img-thumbnail"
                            style="max-width:300px; max-height:200px;">

                    <?php else: ?>

                        <video controls style="max-width:300px; max-height:200px;">
                            <source src="../<?= htmlspecialchars($row['media_file']) ?>">
                        </video>

                    <?php endif; ?>

                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">
                    Replace File (Optional)
                </label>

                <input type="file" name="media_file" class="form-control" accept=".jpg,.jpeg,.png,.webp,.gif,.mp4">
            </div>

            <div class="mb-3">
                <label class="form-label">Title</label>

                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($row['title']) ?>"
                    required>
            </div>

            <div class="mb-4">
                <label class="form-label">Description</label>

                <textarea name="subtitle" rows="4"
                    class="form-control"><?= htmlspecialchars($row['subtitle']) ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i>
                Update Slide
            </button>

            <a href="carousel.php" class="btn btn-light border ms-2">
                Cancel
            </a>

        </form>

    </div>

</div>

<?php include 'includes/footer.php'; ?>