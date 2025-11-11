/* ---
    File: navbar.js
    Deskripsi: Interaksi untuk navbar yang reusable
--- */

class NavbarManager {
    constructor() {
        this.init();
    }

    init() {
        this.setupMobileMenu();
        this.setupScrollEffects();
        this.setupScrollToTop();
        this.setupDropdowns();
    }

    /**
     * 1. Toggle Menu Hamburger (Mobile)
     */
    setupMobileMenu() {
        const menuToggle = document.querySelector('.menu-toggle');
        const navMenu = document.querySelector('#primary-menu');

        if (menuToggle && navMenu) {
            menuToggle.addEventListener('click', () => {
                navMenu.classList.toggle('is-active');
                const isExpanded = navMenu.classList.contains('is-active');
                menuToggle.setAttribute('aria-expanded', isExpanded);
            });
        }
    }

    /**
     * 2. Efek Shadow pada Sticky Header saat scroll
     */
    setupScrollEffects() {
        const header = document.querySelector('#siteHeader');
        if (header) {
            window.addEventListener('scroll', () => {
                if (window.scrollY > 10) {
                    header.classList.add('header-scrolled');
                } else {
                    header.classList.remove('header-scrolled');
                }
            });
        }
    }

    /**
     * 3. Tombol Scroll-to-Top
     */
    setupScrollToTop() {
        const scrollTopBtn = document.querySelector('#scrollTopBtn');
        
        if (scrollTopBtn) {
            window.addEventListener('scroll', () => {
                if (window.scrollY > 300) {
                    scrollTopBtn.classList.add('show');
                } else {
                    scrollTopBtn.classList.remove('show');
                }
            });

            scrollTopBtn.addEventListener('click', (e) => {
                e.preventDefault();
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }
    }

    /**
     * 4. Dropdown Menu Toggle (HANYA UNTUK MOBILE)
     */
    setupDropdowns() {
        const dropdownToggles = document.querySelectorAll('.main-navigation .dropdown-toggle');

        dropdownToggles.forEach((toggle) => {
            toggle.addEventListener('click', (e) => {
                if (window.innerWidth <= 768) {
                    e.preventDefault(); 
                    const parentLi = e.target.closest('li');
                    parentLi.classList.toggle('submenu-open');
                }
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768) {
                if (!e.target.closest('.main-navigation')) {
                    const openDropdowns = document.querySelectorAll('.has-dropdown.submenu-open');
                    openDropdowns.forEach(dropdown => {
                        dropdown.classList.remove('submenu-open');
                    });
                }
            }
        });
    }

    /**
     * Method untuk update active state (jika diperlukan secara dinamis)
     */
    setActivePage(page) {
        const links = document.querySelectorAll('.main-navigation a');
        links.forEach(link => {
            link.removeAttribute('aria-current');
        });
        
        const activeLink = document.querySelector(`.main-navigation a[href*="${page}"]`);
        if (activeLink) {
            activeLink.setAttribute('aria-current', 'page');
        }
    }
}

// Initialize navbar when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.navbarManager = new NavbarManager();
});