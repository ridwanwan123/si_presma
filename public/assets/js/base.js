// =========================================================
// SIDEBAR & TOGGLE
// =========================================================
const sidebar = document.getElementById("sidebar");
const toggle = document.getElementById("toggleSidebar");
const content = document.querySelector(".content");

// =========================
// SIDEBAR TOGGLE
// =========================
toggle?.addEventListener("click", () => {
  if (window.innerWidth > 992) {
    // Desktop → toggle collapse
    sidebar.classList.toggle("collapsed");
  } else {
    // Mobile → toggle overlay
    sidebar.classList.toggle("active");
  }
});

// =========================
// AUTO CLOSE SIDEBAR (MOBILE)
// =========================
content?.addEventListener("click", () => {
  if (window.innerWidth <= 992) {
    sidebar.classList.remove("active");
  }
});

// =========================
// WINDOW RESIZE FIX
// =========================
window.addEventListener("resize", () => {
  if (window.innerWidth > 992) {
    // Desktop: hide mobile overlay if resizing up
    sidebar.classList.remove("active");
  } else {
    // Mobile: hide collapsed if resizing down
    sidebar.classList.remove("collapsed");
  }
});

// =========================================================
// SUBMENU TOGGLE (Master Data / Bidang Prestasi / Pengurangan Poin)
// Satu-satunya handler untuk .has-submenu — cocok dengan CSS di
// sidebar.blade.php: .submenu.show untuk buka/tutup, .has-submenu.open
// untuk rotasi ikon chevron.
// =========================================================
document.querySelectorAll(".has-submenu").forEach((menu) => {
  menu.addEventListener("click", function (e) {
    e.preventDefault();
    e.stopPropagation();

    const submenu = this.nextElementSibling;
    if (submenu) {
      submenu.classList.toggle("show");
    }

    this.classList.toggle("open");
  });
});

// =========================================================
// LOADER
// =========================================================
window.addEventListener("load", () => {
  const loader = document.getElementById("loader");
  if (!loader) return;

  setTimeout(() => {
    loader.style.opacity = "0";
    loader.style.visibility = "hidden";
  }, 500);
});

// =========================================================
// MODAL PERIODE AKTIF — muncul sekali setelah login
// Nilainya dititipkan dari base.blade.php lewat window.PRESMA.showPeriodeModal
// (lihat komentar di base.blade.php)
// =========================================================
document.addEventListener("DOMContentLoaded", () => {
  const showPeriodeModal = window.PRESMA?.showPeriodeModal ?? false;

  if (!showPeriodeModal) return;

  const modalEl = document.getElementById("modalPeriodeAktif");

  if (modalEl && window.bootstrap) {
    new bootstrap.Modal(modalEl).show();
  }
});