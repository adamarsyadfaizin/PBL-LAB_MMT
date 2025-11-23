/* ---
   File: script-detail-proyek.js
   Deskripsi: Script khusus halaman detail proyek
--- */

document.addEventListener("DOMContentLoaded", function() {

    /**
     * 1. Smooth Scroll untuk Anchor Links (jika ada)
     */
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            
            if (href !== '#' && document.querySelector(href)) {
                e.preventDefault();
                document.querySelector(href).scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    /**
     * 2. Efek Hover pada Widget Link
     */
    const widgetLinks = document.querySelectorAll('.widget a');
    widgetLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.style.color = '#ffc700'; // Warna Aksen
            this.style.transition = 'color 0.2s';
        });
        link.addEventListener('mouseleave', function() {
            this.style.color = '#333'; // Kembali Hitam
        });
    });

});