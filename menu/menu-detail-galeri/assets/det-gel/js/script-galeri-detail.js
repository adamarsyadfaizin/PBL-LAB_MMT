/* ---
   File: script-galeri-detail.js
   Deskripsi: Script interaksi khusus halaman detail galeri
--- */

document.addEventListener("DOMContentLoaded", () => {

    /* 1. IMAGE ZOOM (Klik untuk memperbesar) */
    const detailBanner = document.querySelector('.detail-article-banner');
    if (detailBanner) {
        detailBanner.style.cursor = 'zoom-in';
        detailBanner.title = 'Klik untuk memperbesar gambar';

        const toggleZoom = () => {
            detailBanner.classList.toggle('zoomed');
            if (detailBanner.classList.contains('zoomed')) {
                detailBanner.style.cursor = 'zoom-out';
                detailBanner.style.transition = 'transform 0.3s ease';
            } else {
                detailBanner.style.cursor = 'zoom-in';
                detailBanner.style.transform = 'scale(1)';
            }
        };

        detailBanner.addEventListener('click', toggleZoom);
        
        // Klik di luar gambar untuk close zoom
        document.addEventListener('click', e => {
            if (detailBanner.classList.contains('zoomed') && !detailBanner.contains(e.target)) {
                detailBanner.classList.remove('zoomed');
                detailBanner.style.transform = 'scale(1)';
                detailBanner.style.cursor = 'zoom-in';
            }
        });
    }

    /* 2. SOSIAL SHARE (Pop-up Window) */
    const socialShareLinks = document.querySelectorAll('.social-share a');
    const shareMap = {
        'facebook': u => `https://www.facebook.com/sharer/sharer.php?u=${u}`,
        'twitter': (u, t) => `https://twitter.com/intent/tweet?url=${u}&text=${t}`,
        'linkedin': u => `https://www.linkedin.com/sharing/share-offsite/?url=${u}`
    };

    socialShareLinks.forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            const label = link.getAttribute('aria-label');
            let platform = '';
            
            if(label.includes('Facebook')) platform = 'facebook';
            else if(label.includes('Twitter')) platform = 'twitter';
            else if(label.includes('LinkedIn')) platform = 'linkedin';

            const currentUrl = encodeURIComponent(window.location.href);
            const title = encodeURIComponent(document.title);
            
            if (shareMap[platform]) {
                const shareUrl = shareMap[platform](currentUrl, title);
                window.open(shareUrl, '_blank', 'width=600,height=400');
            }
        });
    });

});