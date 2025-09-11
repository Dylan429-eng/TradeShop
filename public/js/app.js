// public/js/app.js

document.addEventListener("DOMContentLoaded", () => {
  // Mobile menu toggle
  const hamburger = document.querySelector("[data-hamburger]");
  const mobileMenu = document.querySelector("[data-mobile-menu]");

  if (hamburger && mobileMenu) {
    hamburger.addEventListener("click", () => {
      mobileMenu.classList.toggle("hidden");
    });
  }

  // Dropdown utilisateur
  const dropdownTrigger = document.querySelector("[data-dropdown-trigger]");
  const dropdownMenu = document.querySelector("[data-dropdown-menu]");

  if (dropdownTrigger && dropdownMenu) {
    dropdownTrigger.addEventListener("click", (e) => {
      e.stopPropagation();
      dropdownMenu.classList.toggle("hidden");
    });

    // Masquer dropdown si clic en dehors
    document.addEventListener("click", () => {
      dropdownMenu.classList.add("hidden");
    });
  }
});
