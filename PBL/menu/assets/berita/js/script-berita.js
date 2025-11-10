/* ---
    File: script-berita.js
    Deskripsi: Interaksi vanilla JS untuk Halaman Berita & Kegiatan
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
    * 5. Filter Form Handling (Opsional - bisa dikembangkan)
    */
    const filterForm = document.querySelector('.filter-bar');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            // Implementasi filter bisa ditambahkan di sini
            console.log('Filter diterapkan');
        });
    }

    /**
    * 6. Highlight tanggal hari ini di kalender
    */
    function highlightToday() {
        const today = new Date();
        const currentDay = today.getDate();
        const currentMonth = today.getMonth() + 1; // JavaScript months are 0-indexed
        const currentYear = today.getFullYear();
        
        // Cek jika kalender menunjukkan bulan dan tahun saat ini
        const calendarCaption = document.querySelector('.calendar-table caption');
        if (calendarCaption) {
            const captionText = calendarCaption.textContent.toLowerCase();
            const currentMonthName = today.toLocaleString('id-ID', { month: 'long' });
            
            if (captionText.includes(currentMonthName.toLowerCase()) && captionText.includes(currentYear)) {
                const todayCells = document.querySelectorAll('.calendar-table td.today');
                todayCells.forEach(cell => {
                    if (parseInt(cell.textContent) === currentDay) {
                        cell.style.backgroundColor = 'var(--color-accent)';
                        cell.style.color = 'var(--color-text)';
                        cell.style.fontWeight = 'bold';
                    }
                });
            }
        }
    }

    // Panggil fungsi highlightToday
    highlightToday();

});