<?php
$page_title = 'Contact';
include 'includes/header.php';
?>

<header class="page-header">
  <div class="container">
    <h1>Contact Us</h1>

    <nav>
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item">
          <a href="index.php">Home</a>
        </li>
        <li class="breadcrumb-item active">Contact</li>
      </ol>
    </nav>
  </div>
</header>

<section class="py-5">
  <div class="container">

    <!-- TOP SECTION: LEFT + RIGHT -->
    <div class="row g-5">

      <!-- LEFT: Contact Info -->
      <div class="col-lg-5">

        <h2 class="section-title">Get in Touch</h2>

        <p class="text-muted">
          We'd love to hear from you. Reach out for admissions, partnerships or any inquiries.
        </p>

        <!-- Address -->
        <a href="https://www.google.com/maps?q=Cancer+Gate+Bharatpur+Chitwan+Nepal" target="_blank"
          class="text-decoration-none text-reset">

          <div class="d-flex gap-3 mt-4">
            <div class="feature-icon">
              <i class="bi bi-geo-alt-fill"></i>
            </div>
            <div>
              <strong>Address</strong>
              <p class="text-muted small mb-0">
                Cancer Gate, Bharatpur-10, Chitwan, Nepal
              </p>
            </div>
          </div>

        </a>

        <!-- Phone -->
        <a href="tel:+977056XXXXXXX" class="text-decoration-none text-reset">

          <div class="d-flex gap-3 mt-3">
            <div class="feature-icon">
              <i class="bi bi-telephone-fill"></i>
            </div>
            <div>
              <strong>Phone</strong>
              <p class="text-muted small mb-0">
                +977-056-XXXXXXX
              </p>
            </div>
          </div>

        </a>

        <!-- Email -->
        <a href="mailto:info@bpkmch.edu.np" class="text-decoration-none text-reset">

          <div class="d-flex gap-3 mt-3">
            <div class="feature-icon">
              <i class="bi bi-envelope-fill"></i>
            </div>
            <div>
              <strong>Email</strong>
              <p class="text-muted small mb-0">
                info@bpkmch.edu.np
              </p>
            </div>
          </div>

        </a>

        <!-- Office Hours -->
        <div class="d-flex gap-3 mt-3">
          <div class="feature-icon">
            <i class="bi bi-clock-fill"></i>
          </div>
          <div>
            <strong>Office Hours</strong>
            <p class="text-muted small mb-0">
              Sun – Fri, 9:00 AM – 5:00 PM
            </p>
          </div>
        </div>

      </div>


      <!-- RIGHT: Form -->
      <div class="col-lg-7">

        <div class="form-card">

          <h4 class="text-green mb-3">Send us a message</h4>

          <form id="contactForm" novalidate>

            <div class="row g-3">

              <div class="col-md-6">
                <label class="form-label">Full Name *</label>
                <input type="text" class="form-control" name="name" required>
              </div>

              <div class="col-md-6">
                <label class="form-label">Email *</label>
                <input type="email" class="form-control" name="email" required>
              </div>

              <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input type="text" class="form-control" name="phone">
              </div>

              <div class="col-md-6">
                <label class="form-label">Subject</label>
                <input type="text" class="form-control" name="subject">
              </div>

              <div class="col-12">
                <label class="form-label">Message *</label>
                <textarea class="form-control" name="message" rows="5" required></textarea>
              </div>

              <!-- MESSAGE AREA -->
              <div class="col-12">
                <div id="contactMsg"></div>
              </div>

              <div class="col-12">
                <button type="submit" class="btn btn-primary px-4">
                  Send Message
                </button>
              </div>

            </div>

          </form>

        </div>

      </div>

    </div>

    <!-- BOTTOM: FULL WIDTH MAP -->
    <div class="row mt-5">
      <div class="col-12">

        <div class="rounded overflow-hidden" style="height: 350px;">
          <iframe src="https://www.google.com/maps?q=Bharatpur+Cancer+Hospital+Nepal&output=embed" width="100%"
            height="100%" style="border:0;" loading="lazy">
          </iframe>
        </div>

      </div>
    </div>

  </div>
</section>

<?php include 'includes/footer.php'; ?>