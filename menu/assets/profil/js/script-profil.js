/* ---
   File: script-profil.js
   Deskripsi: Script khusus halaman profil (Animasi Scroll & Tim)
--- */

document.addEventListener("DOMContentLoaded", function() {

    /**
     * 1. Animasi Scroll (Fade In Up)
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
                observer.unobserve(entry.target); // Hanya animasi sekali
            }
        });
    }, observerOptions);

    const profileSections = document.querySelectorAll('.profile-section');
    profileSections.forEach(section => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(30px)';
        section.style.transition = 'opacity 0.8s ease-out, transform 0.8s ease-out';
        observer.observe(section);
    });

    /**
     * 2. Fallback Gambar Tim (Jika error)
     */
    const teamPhotos = document.querySelectorAll('.team-photo');
    
    teamPhotos.forEach(photo => {
        photo.addEventListener('error', function() {
            // Ganti ke placeholder jika gambar tidak ditemukan
            this.src = '../assets/images/placeholder-team.jpg'; 
            this.alt = 'Foto tidak tersedia';
        });
    });

});