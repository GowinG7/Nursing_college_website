<?php
include 'includes/header.php';
include 'includes/db.php';

$sql = "SELECT * FROM notices ORDER BY posted_on DESC";
$rs = $conn->query($sql);
?>

<!-- PAGE HEADER -->
<div class="page-header">
  <div class="container">
    <h1>Notices</h1>
    <nav>
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <li class="breadcrumb-item active">Notices</li>
      </ol>
    </nav>
  </div>
</div>

<!-- MAIN SECTION -->
<section>
  <div class="container">
    <h2 class="section-title mb-3">Latest Notices</h2>
    <p class="text-muted small mb-4">
      View official announcements and academic updates from BPKMCH Nursing College.
    </p>

    <!-- FILTERS -->
    <div class="row mb-4 align-items-center">
      <div class="col-md-4 mb-2">
        <input type="text" id="noticeSearch" class="form-control" placeholder="Search by title or content...">
      </div>
      <div class="col-md-3 mb-2">
        <input type="date" id="startDate" class="form-control" placeholder="Start date">
      </div>
      <div class="col-md-3 mb-2">
        <input type="date" id="endDate" class="form-control" placeholder="End date">
      </div>
      <div class="col-md-2 mb-2 d-flex gap-2">
        <button type="button" id="searchBtn" class="btn btn-primary w-100">Search</button>
        <button type="button" id="clearFilter" class="btn btn-outline-secondary w-100">Clear</button>
      </div>
    </div>

    <!-- NOTICES LIST -->
    <?php if (!$rs || $rs->num_rows === 0): ?>
      <div class="text-center py-5">
        <i class="bi bi-megaphone fs-1 text-muted"></i>
        <p class="text-muted mt-3">No notices available at the moment.</p>
      </div>
    <?php else: ?>
      <div class="row g-4" id="noticeContainer">
        <?php while ($n = $rs->fetch_assoc()): ?>
          <?php
          $imagePath = trim($n['image'] ?? '');
          $hasImage = !empty($imagePath);
          $noticeDate = date('Y-m-d', strtotime($n['posted_on']));
          ?>
          <div class="col-lg-12 notice-item" data-title="<?= strtolower(htmlspecialchars($n['title'])) ?>"
            data-body="<?= strtolower(htmlspecialchars($n['body'])) ?>" data-date="<?= $noticeDate ?>">
            <div class="notice-card <?= !$hasImage ? 'text-only' : '' ?>">
              <?php if ($hasImage): ?>
                <button type="button" class="notice-image-btn gallery-image-btn"
                  data-image="<?= htmlspecialchars($imagePath) ?>" data-title="<?= htmlspecialchars($n['title']) ?>">
                  <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($n['title']) ?>"
                    class="notice-image">
                </button>
              <?php endif; ?>

              <div class="notice-content">
                <h5 class="notice-title">
                  <?= htmlspecialchars($n['title']) ?>
                </h5>
                <div class="notice-date">
                  <i class="bi bi-calendar3 me-1"></i>
                  <?= date('M d, Y', strtotime($n['posted_on'])) ?>
                </div>
                <p class="notice-body">
                  <?= nl2br(htmlspecialchars($n['body'])) ?>
                </p>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>

      <!-- No results message -->
      <div id="noResults" class="text-center py-5 d-none">
        <i class="bi bi-search fs-1 text-muted"></i>
        <p class="text-muted mt-3">No notices match your search or date filters.</p>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- IMAGE MODAL -->
<div id="imageModal" class="image-modal">
  <span class="image-modal-close">&times;</span>
  <img id="modalImage" src="" alt="">
  <div id="modalCaption"></div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const modalCaption = document.getElementById('modalCaption');
    const closeBtn = document.querySelector('.image-modal-close');

    document.querySelectorAll('.gallery-image-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        modal.classList.add('show');
        modalImage.src = this.dataset.image;
        modalCaption.textContent = this.dataset.title || '';
      });
    });

    closeBtn.addEventListener('click', function () {
      modal.classList.remove('show');
    });

    modal.addEventListener('click', function (e) {
      if (e.target === modal) modal.classList.remove('show');
    });

    // Filtering logic
    const searchInput = document.getElementById('noticeSearch');
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');
    const searchBtn = document.getElementById('searchBtn');
    const clearBtn = document.getElementById('clearFilter');
    const notices = document.querySelectorAll('.notice-item');
    const noResults = document.getElementById('noResults');

    function filterNotices() {
      const keyword = searchInput.value.toLowerCase();
      const start = startDate.value;
      const end = endDate.value;
      let anyVisible = false;

      notices.forEach(function (notice) {
        const title = notice.dataset.title;
        const body = notice.dataset.body;
        const date = notice.dataset.date;

        const matchKeyword = title.includes(keyword) || body.includes(keyword);
        const matchDate = (!start || date >= start) && (!end || date <= end);

        if (matchKeyword && matchDate) {
          notice.style.display = '';
          anyVisible = true;
        } else {
          notice.style.display = 'none';
        }
      });

      noResults.classList.toggle('d-none', anyVisible);
    }

    // Live search on typing
    searchInput.addEventListener('keyup', filterNotices);

    // Date filters live update
    startDate.addEventListener('change', filterNotices);
    endDate.addEventListener('change', filterNotices);

    // Search button
    searchBtn.addEventListener('click', filterNotices);

    // Clear filters
    clearBtn.addEventListener('click', function () {
      searchInput.value = '';
      startDate.value = '';
      endDate.value = '';
      filterNotices();
    });
  });
</script>

<?php include 'includes/footer.php'; ?>