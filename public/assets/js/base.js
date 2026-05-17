// =========================================================
// SIDEBAR & TOGGLE
// =========================================================
const sidebar = document.getElementById("sidebar");
const toggle = document.getElementById("toggleSidebar");
const content = document.querySelector(".content");

// =========================
// SIDEBAR TOGGLE
// =========================
toggle.addEventListener("click", () => {
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
content.addEventListener("click", () => {
  if (window.innerWidth <= 992) {
    sidebar.classList.remove("active");
  }
});

// =========================
// SUBMENU TOGGLE
// =========================
document.querySelectorAll(".menu-item.has-submenu").forEach((item) => {
  item.addEventListener("click", (e) => {
    e.stopPropagation(); // prevent bubbling
    item.classList.toggle("active");
  });
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
// LOADER
// =========================================================
window.addEventListener("load", () => {
  const loader = document.getElementById("loader");
  setTimeout(() => {
    loader.style.opacity = "0";
    loader.style.visibility = "hidden";
  }, 500);
});