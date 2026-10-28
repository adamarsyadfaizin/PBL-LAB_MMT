/* ---
    File: assets/js/navbar.js
    Interaksi navbar: menu mobile, shadow saat scroll, dropdown mobile.
--- */

class NavbarManager {
    constructor() {
        this.init();
    }

    init() {
        this.setupMobileMenu();
        this.setupScrollEffects();
        this.setupDropdowns();
    }

    /** 1. Toggle menu hamburger (mobile) */
    setupMobileMenu() {
        const menuToggle = document.querySelector('.menu-toggle');
        const navMenu = document.querySelector('.main-navigation');

        if (menuToggle && navMenu) {
            menuToggle.addEventListener('click', () => {
                navMenu.classList.toggle('is-active');
                const isExpanded = navMenu.classList.contains('is-active');
                menuToggle.setAttribute('aria-expanded', isExpanded);
            });

            // Tutup menu setelah salah satu tautan diklik
            navMenu.querySelectorAll('a').forEach((link) => {
                link.addEventListener('click', () => {
                    if (window.innerWidth <= 768) {
                        navMenu.classList.remove('is-active');
                        menuToggle.setAttribute('aria-expanded', 'false');
                    }
                });
            });
        }
    }

    /** 2. Efek shadow pada header saat halaman di-scroll */
    setupScrollEffects() {
        const header = document.querySelector('.site-header') || document.querySelector('#siteHeader');
        if (!header) {
            return;
        }

        const toggle = () => header.classList.toggle('header-scrolled', window.scrollY > 10);
        window.addEventListener('scroll', toggle, { passive: true });
        toggle();
    }

    /** 3. Dropdown menu (hanya untuk mobile) */
    setupDropdowns() {
        const dropdownToggles = document.querySelectorAll('.main-navigation .dropdown-toggle');

        dropdownToggles.forEach((toggle) => {
            toggle.addEventListener('click', (e) => {
                if (window.innerWidth <= 768) {
                    e.preventDefault();
                    const parentLi = toggle.closest('li');
                    if (parentLi) {
                        parentLi.classList.toggle('submenu-open');
                    }
                }
            });
        });

        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768 && !e.target.closest('.main-navigation')) {
                document.querySelectorAll('.has-dropdown.submenu-open').forEach((dropdown) => {
                    dropdown.classList.remove('submenu-open');
                });
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.navbarManager = new NavbarManager();
});
