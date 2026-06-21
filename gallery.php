<?php

$page_title = 'Gallery';

include 'includes/db.php';
include 'includes/header.php';


$rs = $conn->query("
    SELECT *
    FROM gallery
    ORDER BY id DESC
");
?>

<header class="page-header">
  <div class="container">
    <h1>Gallery</h1>

    <nav>
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item">
          <a href="index.php">Home</a>
        </li>
        <li class="breadcrumb-item active">
          Gallery
        </li>
      </ol>
    </nav>
  </div>
</header>

<section>
  <div class="container">

    <h2 class="section-title">Life at BPKMCH</h2>

    <p class="section-subtitle">
      A glimpse into our campus, classrooms, clinical training and student life.
    </p>

    <div class="row g-4">

      <?php if ($rs && $rs->num_rows > 0): ?>

        <?php while ($g = $rs->fetch_assoc()): ?>

          <?php
          $imagePath = trim($g['image']);

          $hasImage =
            !empty($imagePath) &&
            file_exists(__DIR__ . '/' . $imagePath);
          ?>

          <div class="col-md-6 col-lg-4">

            <div class="gallery-card">

              <button type="button" class="gallery-image-btn" data-image="<?= htmlspecialchars($imagePath) ?>"
                data-title="<?= htmlspecialchars($g['title']) ?>">

                <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($g['title']) ?>"
                  class="gallery-image">

                <div class="gallery-overlay">

                  <h5>
                    <?= htmlspecialchars($g['title']) ?>
                  </h5>

                  <?php if (!empty($g['caption'])): ?>
                    <p>
                      <?= htmlspecialchars($g['caption']) ?>
                    </p>
                  <?php endif; ?>

                </div>

              </button>

            </div>

          </div>

        <?php endwhile; ?>

      <?php else: ?>

        <div class="col-12 text-center py-5">

          <i class="bi bi-images fs-1 text-muted"></i>

          <p class="text-muted mt-3">
            No gallery images available yet.
          </p>

        </div>

      <?php endif; ?>

    </div>

  </div>
</section>

<!-- IMAGE MODAL -->

<div id="imageModal" class="image-modal">

  <span class="image-modal-close">&times;</span>

  <img id="modalImage" src="" alt="">

  <div id="modalCaption"></div>

</div>

<div id="imageModal" class="image-modal">

  <span class="image-modal-close">&times;</span>

  <img id="modalImage" src="" alt="">

  <div id="modalCaption"></div>

</div>

<script>

  document.addEventListener("DOMContentLoaded", function () {

    const modal = document.getElementById("imageModal");
    const modalImg = document.getElementById("modalImage");
    const caption = document.getElementById("modalCaption");
    const closeBtn = document.querySelector(".image-modal-close");

    document.querySelectorAll(".gallery-image-btn").forEach(btn => {

      btn.addEventListener("click", function () {

        modal.classList.add("show");

        modalImg.src = this.dataset.image;

        caption.textContent = this.dataset.title || "";

        document.body.style.overflow = "hidden";

      });

    });

    closeBtn.addEventListener("click", closeModal);

    modal.addEventListener("click", function (e) {

      if (e.target === modal) {

        closeModal();

      }

    });

    document.addEventListener("keydown", function (e) {

      if (e.key === "Escape") {

        closeModal();

      }

    });

    function closeModal() {

      modal.classList.remove("show");

      modalImg.src = "";

      document.body.style.overflow = "";

    }

  });

</script>

<?php include 'includes/footer.php'; ?>