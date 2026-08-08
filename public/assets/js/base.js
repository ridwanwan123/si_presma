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

  // FIX: konten (mis. tabel DataTables dengan autoWidth: false) dihitung
  // lebarnya sekali saat render pertama. Transisi CSS lebar sidebar TIDAK
  // memicu event resize bawaan browser, jadi kolom tabel jadi tidak sesuai
  // lagi dengan lebar area konten yang baru. Begitu transisi sidebar
  // selesai, kita trigger resize (untuk komponen lain seperti chart) dan
  // paksa DataTables menghitung ulang lebar kolomnya kalau ada di halaman.
  sidebar.addEventListener(
    "transitionend",
    function handler(e) {
      if (e.propertyName !== "width") return;

      window.dispatchEvent(new Event("resize"));

      if (window.jQuery && $.fn.dataTable) {
        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
      }

      sidebar.removeEventListener("transitionend", handler);
    }
  );
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

// =========================================================
// DROPDOWN "BUTUH BANTUAN" — fix biar gak kepotong overflow:hidden .main
// Popper dipaksa pakai strategy "fixed" (relatif ke viewport), bukan
// "absolute" (relatif ke .dropdown di dalam .main yang overflow:hidden).
// =========================================================
document.addEventListener("DOMContentLoaded", () => {
  const btnBantuan = document.getElementById("btnBantuan");
  if (btnBantuan && window.bootstrap) {
    new bootstrap.Dropdown(btnBantuan, {
      popperConfig: (defaultConfig) => ({
        ...defaultConfig,
        strategy: "fixed",
      }),
    });
  }
});