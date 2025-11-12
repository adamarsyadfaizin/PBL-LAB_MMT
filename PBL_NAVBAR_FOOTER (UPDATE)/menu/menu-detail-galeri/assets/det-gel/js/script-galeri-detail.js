/* ---
   File: script-galeri-detail.js
   Deskripsi: Interaksi vanilla JS untuk Halaman Detail Galeri (versi clean & modular)
--- */

document.addEventListener("DOMContentLoaded", () => {

    /* =========================================================
       1. MENU HAMBURGER (Mobile)
    ========================================================= */
    const menuToggle = document.querySelector('.menu-toggle');
    const navMenu = document.querySelector('#primary-menu');

    if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', () => {
            navMenu.classList.toggle('is-active');
            menuToggle.setAttribute('aria-expanded', navMenu.classList.contains('is-active'));
        });
    }

    /* =========================================================
       2. STICKY HEADER DENGAN SHADOW
    ========================================================= */
    const header = document.querySelector('#siteHeader');
    if (header) {
        window.addEventListener('scroll', () => {
            header.classList.toggle('header-scrolled', window.scrollY > 10);
        });
    }

    /* =========================================================
       3. TOMBOL SCROLL TO TOP
    ========================================================= */
    const scrollTopBtn = document.querySelector('#scrollTopBtn');
    if (scrollTopBtn) {
        window.addEventListener('scroll', () => {
            scrollTopBtn.classList.toggle('show', window.scrollY > 300);
        });

        scrollTopBtn.addEventListener('click', e => {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    /* =========================================================
       4. DROPDOWN MENU (Mobile Only)
    ========================================================= */
    const dropdownToggles = document.querySelectorAll('.main-navigation .dropdown-toggle');
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', e => {
            if (window.innerWidth <= 768) {
                e.preventDefault();
                toggle.parentElement.classList.toggle('submenu-open');
            }
        });
    });

    /* =========================================================
       5. SOSIAL SHARE
    ========================================================= */
    const socialShareLinks = document.querySelectorAll('.social-share a');
    const shareMap = {
        'bagikan ke facebook': u => `https://www.facebook.com/sharer/sharer.php?u=${u}`,
        'bagikan ke twitter': (u, t) => `https://twitter.com/intent/tweet?url=${u}&text=${t}`,
        'bagikan ke linkedin': u => `https://www.linkedin.com/sharing/share-offsite/?url=${u}`
    };

    socialShareLinks.forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            const label = link.getAttribute('aria-label')?.toLowerCase();
            const currentUrl = encodeURIComponent(window.location.href);
            const title = encodeURIComponent(document.title);
            const makeUrl = shareMap[label];
            if (makeUrl) {
                const shareUrl = makeUrl(currentUrl, title);
                window.open(shareUrl, '_blank', 'width=600,height=400');
            }
        });
    });

    /* =========================================================
       6. IMAGE ZOOM DETAIL BANNER
    ========================================================= */
    const detailBanner = document.querySelector('.detail-article-banner');
    if (detailBanner) {
        detailBanner.style.cursor = 'zoom-in';
        detailBanner.title = 'Klik untuk memperbesar gambar';

        const toggleZoom = () => {
            const zoomed = detailBanner.classList.toggle('zoomed');
            Object.assign(detailBanner.style, zoomed ? {
                cursor: 'zoom-out',
                transform: 'scale(1.5)',
                transition: 'transform 0.3s ease',
                zIndex: '1000',
                position: 'relative'
            } : {
                cursor: 'zoom-in',
                transform: 'scale(1)',
                zIndex: 'auto',
                position: 'static'
            });
        };

        detailBanner.addEventListener('click', toggleZoom);

        document.addEventListener('click', e => {
            if (detailBanner.classList.contains('zoomed') && !detailBanner.contains(e.target)) {
                detailBanner.classList.remove('zoomed');
                Object.assign(detailBanner.style, {
                    cursor: 'zoom-in',
                    transform: 'scale(1)',
                    zIndex: 'auto',
                    position: 'static'
                });
            }
        });
    }

    /* =========================================================
       7. RELATED GALLERY ITEMS
    ========================================================= */
    const relatedItems = document.querySelectorAll('.widget-news li a');
    relatedItems.forEach(item => {
        const move = val => { item.style.transform = `translateX(${val}px)`; item.style.transition = 'transform 0.2s ease'; };
        item.addEventListener('mouseenter', () => move(5));
        item.addEventListener('mouseleave', () => move(0));
        item.addEventListener('focus', () => move(5));
        item.addEventListener('blur', () => move(0));
    });

    /* =========================================================
       8. CATEGORY FILTER
    ========================================================= */
    const categoryLinks = document.querySelectorAll('.widget-categories li a');
    const showCategoryMessage = msg => {
        document.querySelector('.category-message')?.remove();
        const msgDiv = document.createElement('div');
        msgDiv.className = 'category-message';
        msgDiv.textContent = msg;
        const backLink = document.querySelector('.back-link');
        if (backLink) backLink.parentNode.insertBefore(msgDiv, backLink);
        setTimeout(() => msgDiv.remove(), 3000);
    };

    categoryLinks.forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            categoryLinks.forEach(l => l.classList.remove('active'));
            link.classList.add('active');
            const category = link.textContent.toLowerCase();
            showCategoryMessage(`Menampilkan galeri: ${category}`);
        });
    });

    /* =========================================================
       9. IMAGE LOADING ENHANCEMENT
    ========================================================= */
    if (detailBanner) {
        detailBanner.style.opacity = '0';
        detailBanner.style.transition = 'opacity 0.3s ease';
        detailBanner.addEventListener('load', () => { detailBanner.style.opacity = '1'; });
        detailBanner.addEventListener('error', () => {
            detailBanner.src = '../../../../assets/images/placeholder-gallery.jpg';
            detailBanner.alt = 'Gambar tidak tersedia';
            detailBanner.style.opacity = '1';
        });
    }

    /* =========================================================
       10. PRINT BUTTON
    ========================================================= */
    const addPrintButton = () => {
        const backLink = document.querySelector('.back-link');
        if (!backLink) return;
        const printBtn = document.createElement('button');
        printBtn.className = 'btn btn-secondary print-btn';
        printBtn.innerHTML = '🖨️ Cetak Halaman';
        printBtn.addEventListener('click', () => window.print());
        backLink.parentNode.insertBefore(printBtn, backLink.nextSibling);
    };

    /* =========================================================
       11. SIMULASI VIEW COUNT
    ========================================================= */
    const simulateViewCount = () => {
        const metaInfo = document.querySelector('.meta-info');
        if (!metaInfo) return;
        const viewCount = Math.floor(Math.random() * 100) + 50;
        const viewSpan = document.createElement('span');
        viewSpan.innerHTML = `<strong>Dilihat:</strong> ${viewCount} kali`;
        metaInfo.appendChild(viewSpan);
    };

    /* =========================================================
       12. ANIMASI CONTENT & INITIALISASI
    ========================================================= */
    const initializeEnhancedFeatures = () => {
        addPrintButton();
        simulateViewCount();

        const primaryContent = document.querySelector('.primary-content');
        if (primaryContent) {
            Object.assign(primaryContent.style, {
                opacity: '0',
                transform: 'translateY(20px)',
                transition: 'opacity 0.6s ease, transform 0.6s ease'
            });
            setTimeout(() => {
                primaryContent.style.opacity = '1';
                primaryContent.style.transform = 'translateY(0)';
            }, 200);
        }
    };

    /* =========================================================
       13. KEYBOARD SHORTCUTS
    ========================================================= */
    document.addEventListener('keydown', e => {
        // ESC untuk keluar zoom
        if (e.key === 'Escape' && detailBanner?.classList.contains('zoomed')) {
            detailBanner.classList.remove('zoomed');
            Object.assign(detailBanner.style, {
                cursor: 'zoom-in',
                transform: 'scale(1)',
                zIndex: 'auto',
                position: 'static'
            });
        }

        // B = fokus ke tombol kembali
        if (e.key === 'b' && !e.ctrlKey && !e.altKey) {
            const backLink = document.querySelector('.back-link');
            if (backLink && document.activeElement !== backLink) {
                e.preventDefault();
                backLink.focus();
            }
        }
    });

    // Jalankan semua fitur
    initializeEnhancedFeatures();

});
