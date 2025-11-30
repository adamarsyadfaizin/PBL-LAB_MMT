/* ---
   File: script-proyek.js
   Deskripsi: Script khusus interaksi kartu proyek
--- */

document.addEventListener("DOMContentLoaded", function() {

    /**
     * 1. Efek Klik pada Tag (Quick Filter)
     * Jika user klik tag di kartu proyek, otomatis isi search bar
     */
    const tagBadges = document.querySelectorAll('.tag-badge');
    
    tagBadges.forEach(tag => {
        tag.style.cursor = 'pointer';
        
        tag.addEventListener('click', function(e) {
            e.preventDefault();
            // Mencegah link kartu proyek terklik
            e.stopPropagation(); 
            
            const tagText = this.textContent.trim();
            const searchInput = document.getElementById('filter-search');
            
            // Isi search bar dan submit form
            if (searchInput) {
                searchInput.value = tagText;
                // Cari form terdekat dan submit
                const form = document.querySelector('.project-filter-bar');
                if(form) form.submit();
            }
        });
    });

});