/* ---
   File: script-galeri.js
   Lokasi: menu/galeri/js/script-galeri.js
   Deskripsi: Script interaksi khusus halaman galeri
--- */

document.addEventListener("DOMContentLoaded", function() {

    /**
     * 1. Efek Keyboard Navigation (Aksesibilitas)
     * Memungkinkan user berpindah antar item galeri menggunakan tombol Panah Kanan/Kiri
     */
    document.addEventListener('keydown', function(e) {
        // Ambil semua item galeri yang sedang tampil
        const galleryItems = document.querySelectorAll('.gallery-item');
        const focusedElement = document.activeElement;
        
        // Cek apakah elemen yang sedang fokus adalah salah satu item galeri
        if (focusedElement.classList.contains('gallery-item')) {
            const currentIndex = Array.from(galleryItems).indexOf(focusedElement);
            
            // Panah Kanan -> Pindah ke item berikutnya
            if (e.key === 'ArrowRight' && currentIndex < galleryItems.length - 1) {
                e.preventDefault();
                galleryItems[currentIndex + 1].focus();
            } 
            // Panah Kiri -> Pindah ke item sebelumnya
            else if (e.key === 'ArrowLeft' && currentIndex > 0) {
                e.preventDefault();
                galleryItems[currentIndex - 1].focus();
            }
        }
    });

    

});