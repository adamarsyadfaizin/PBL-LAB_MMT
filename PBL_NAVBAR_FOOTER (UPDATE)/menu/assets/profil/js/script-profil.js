/* ---
   File: script-profil.js
   Deskripsi: Interaksi vanilla JS untuk Halaman Profil Laboratorium
--- */

document.addEventListener("DOMContentLoaded", function() {

    /**
     * 1. Toggle Menu Hamburger (Mobile)
     */
    const menuToggle = document.querySelector('.menu-toggle');
    const navMenu = document.querySelector('#primary-menu');

    if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('is-active');
            const isExpanded = navMenu.classList.contains('is-active');
            menuToggle.setAttribute('aria-expanded', isExpanded);
        });
    }

    /**
     * 2. Efek Shadow pada Sticky Header saat scroll
     */
    const header = document.querySelector('#siteHeader');
    if (header) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 10) {
                header.classList.add('header-scrolled');
            } else {
                header.classList.remove('header-scrolled');
            }
        });
    }

    /**
     * 3. Tombol Scroll-to-Top
     */
    const scrollTopBtn = document.querySelector('#scrollTopBtn');
    
    if (scrollTopBtn) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                scrollTopBtn.classList.add('show');
            } else {
                scrollTopBtn.classList.remove('show');
            }
        });

        scrollTopBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    /**
     * 4. Dropdown Menu Toggle (HANYA UNTUK MOBILE)
     */
    const dropdownToggles = document.querySelectorAll('.main-navigation .dropdown-toggle');

    dropdownToggles.forEach(function(toggle) {
        toggle.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                e.preventDefault(); 
                const parentLi = this.parentElement;
                parentLi.classList.toggle('submenu-open');
            }
        });
    });

    /**
     * 5. Smooth Scroll untuk navigasi internal
     */
    const internalLinks = document.querySelectorAll('a[href^="#"]');
    
    internalLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            // Skip jika href hanya "#" atau link untuk dropdown
            if (href === '#' || this.classList.contains('dropdown-toggle')) {
                return;
            }
            
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                
                // Tutup menu mobile jika terbuka
                if (window.innerWidth <= 768 && navMenu.classList.contains('is-active')) {
                    navMenu.classList.remove('is-active');
                    menuToggle.setAttribute('aria-expanded', 'false');
                }
                
                const headerHeight = document.querySelector('.site-header').offsetHeight;
                const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - headerHeight;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
                
                // Update URL tanpa reload page
                history.pushState(null, null, href);
            }
        });
    });

    /**
     * 6. Animasi scroll untuk section
     */
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Terapkan animasi pada section profile
    const profileSections = document.querySelectorAll('.profile-section');
    profileSections.forEach(section => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(20px)';
        section.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(section);
    });

    /**
     * 7. Enhanced team card interactions
     */
    const teamCards = document.querySelectorAll('.team-card');
    
    teamCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(-5px) scale(1)';
        });
        
        // Keyboard navigation untuk team cards
        card.addEventListener('focus', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });
        
        card.addEventListener('blur', function() {
            this.style.transform = 'translateY(-5px) scale(1)';
        });
    });

    /**
     * 8. Active section highlighting
     */
    const sections = document.querySelectorAll('.profile-section');
    const navLinks = document.querySelectorAll('.dropdown-menu a[href^="#"]');
    
    function highlightActiveSection() {
        let currentSection = '';
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            const headerHeight = document.querySelector('.site-header').offsetHeight;
            
            if (window.scrollY >= (sectionTop - headerHeight - 100)) {
                currentSection = section.getAttribute('id');
            }
        });
        
        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === `#${currentSection}`) {
                link.classList.add('active');
            }
        });
    }
    
    // Panggil fungsi saat scroll
    window.addEventListener('scroll', highlightActiveSection);
    
    // Panggil sekali saat load
    highlightActiveSection();

    /**
     * 9. Enhanced loading untuk gambar team
     */
    const teamPhotos = document.querySelectorAll('.team-photo');
    
    teamPhotos.forEach(photo => {
        // Tambah loading lazy
        photo.setAttribute('loading', 'lazy');
        
        // Fallback untuk gambar yang gagal load
        photo.addEventListener('error', function() {
            this.src = '../assets/images/placeholder-team.jpg';
            this.alt = 'Foto tim tidak tersedia';
        });
    });

});