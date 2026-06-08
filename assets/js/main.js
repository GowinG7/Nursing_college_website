// Smooth scroll for in-page anchors
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', e => {
    const id = a.getAttribute('href');
    if (id.length > 1) {
      const el = document.querySelector(id);
      if (el) { e.preventDefault(); el.scrollIntoView({behavior:'smooth'}); }
    }
  });
});

// Contact form AJAX (jQuery)
$(function () {
  $('#contactForm').on('submit', function (e) {
    e.preventDefault();
    const $form = $(this);
    const $msg = $('#contactMsg');
    const $btn = $form.find('button[type=submit]');
    $btn.prop('disabled', true).text('Sending...');
    $msg.html('');

    $.ajax({
      url: 'ajax/contact_submit.php',
      type: 'POST',
      data: $form.serialize(),
      dataType: 'json',
      success: function (res) {
        if (res.status === 'success') {
          $msg.html('<div class="alert alert-success">' + res.message + '</div>');
          $form[0].reset();
        } else {
          $msg.html('<div class="alert alert-danger">' + res.message + '</div>');
        }
      },
      error: function () {
        $msg.html('<div class="alert alert-danger">Something went wrong. Try again.</div>');
      },
      complete: function () {
        $btn.prop('disabled', false).text('Send Message');
      }
    });
  });
});
