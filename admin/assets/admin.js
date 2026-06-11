// Generic AJAX delete handler
$(document).on("click", ".btn-delete", function (e) {
  e.preventDefault();
  if (!confirm("Delete this item? This cannot be undone.")) return;
  const $btn = $(this);
  const id = $btn.data("id");
  const type = $btn.data("type");
  $.post(
    "ajax/delete.php",
    { id: id, type: type },
    function (res) {
      if (res.success) {
        $btn.closest("tr").fadeOut(200, function () {
          $(this).remove();
        });
      } else {
        alert(res.message || "Failed to delete");
      }
    },
    "json",
  ).fail(function () {
    alert("Request failed");
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const sidebar = document.getElementById("sidebar");
  const hamburgerBtn = document.getElementById("hamburgerBtn");
  const sidebarClose = document.getElementById("sidebarClose");
  const backdrop = document.getElementById("sidebarBackdrop");

  if (!sidebar) return;

  function openSidebar() {
    sidebar.classList.add("open");
    backdrop.classList.add("show");
  }

  function closeSidebar() {
    sidebar.classList.remove("open");
    backdrop.classList.remove("show");
  }

  if (hamburgerBtn) {
    hamburgerBtn.addEventListener("click", openSidebar);
  }

  if (sidebarClose) {
    sidebarClose.addEventListener("click", closeSidebar);
  }

  if (backdrop) {
    backdrop.addEventListener("click", closeSidebar);
  }
});
