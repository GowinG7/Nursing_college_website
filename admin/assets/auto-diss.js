document.addEventListener("DOMContentLoaded", function () {
  setTimeout(function () {
    const alertBox = document.getElementById("successAlert");

    if (alertBox) {
      alertBox.style.transition = "opacity 0.5s ease";
      alertBox.style.opacity = "0";

      setTimeout(function () {
        alertBox.remove();

        // Remove ?msg= from URL without reloading
        const url = new URL(window.location);
        url.searchParams.delete("msg");
        window.history.replaceState({}, document.title, url.pathname);
      }, 500);
    }
  }, 4000);
});
