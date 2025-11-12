/* ---
   File: script-galeri.js
   Deskripsi: Interaksi vanilla JS untuk Halaman Galeri Multimedia
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
     * 5. Filter Form Handling untuk Galeri
     */
    const filterForm = document.querySelector('.filter-bar');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const jenis = document.getElementById('filter-jenis').value;
            const acara = document.getElementById('filter-acara').value;
            const tahun = document.getElementById('filter-tahun').value;
            
            // Simulasi filter (dalam implementasi nyata, ini akan memfilter data dari server)
            console.log('Filter diterapkan:', { jenis, acara, tahun });
            
            // Di sini bisa ditambahkan logika untuk memfilter item galeri
            // berdasarkan kriteria yang dipilih
            filterGalleryItems(jenis, acara, tahun);
        });
    }

    /**
     * 6. Fungsi untuk memfilter item galeri
     */
    function filterGalleryItems(jenis, acara, tahun) {
        const galleryItems = document.querySelectorAll('.gallery-item');
        
        galleryItems.forEach(item => {
            let showItem = true;
            
            // Filter berdasarkan jenis media
            if (jenis !== 'semua') {
                const itemType = item.querySelector('span').textContent.toLowerCase();
                if (!itemType.includes(jenis)) {
                    showItem = false;
                }
            }
            
            // Filter berdasarkan acara (dalam implementasi nyata, 
            // data acara akan disimpan dalam atribut data)
            if (acara !== 'semua') {
                // Simulasi filter acara
                const itemTitle = item.querySelector('h4').textContent.toLowerCase();
                if (!itemTitle.includes(acara.replace('-', ' '))) {
                    showItem = false;
                }
            }
            
            // Tampilkan/sembunyikan item berdasarkan hasil filter
            if (showItem) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
        
        // Animasi untuk item yang ditampilkan
        setTimeout(() => {
            galleryItems.forEach(item => {
                if (item.style.display !== 'none') {
                    item.style.opacity = '0';
                    item.style.transform = 'translateY(20px)';
                    item.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                    
                    setTimeout(() => {
                        item.style.opacity = '1';
                        item.style.transform = 'translateY(0)';
                    }, 100);
                }
            });
        }, 50);
    }

    /**
     * 7. Efek hover yang lebih smooth untuk item galeri
     */
    const galleryItems = document.querySelectorAll('.gallery-item');
    galleryItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transition = 'transform 0.3s ease, box-shadow 0.3s ease';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transition = 'transform 0.3s ease, box-shadow 0.3s ease';
        });
    });

    /**
     * 8. Keyboard navigation untuk galeri
     */
    document.addEventListener('keydown', function(e) {
        const galleryItems = document.querySelectorAll('.gallery-item');
        const focusedElement = document.activeElement;
        
        if (focusedElement.classList.contains('gallery-item')) {
            const currentIndex = Array.from(galleryItems).indexOf(focusedElement);
            
            if (e.key === 'ArrowRight' && currentIndex < galleryItems.length - 1) {
                e.preventDefault();
                galleryItems[currentIndex + 1].focus();
            } else if (e.key === 'ArrowLeft' && currentIndex > 0) {
                e.preventDefault();
                galleryItems[currentIndex - 1].focus();
            }
        }
    });

});