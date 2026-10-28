/* ---
    File: assets/js/scrolltop.js
    Menampilkan tombol "kembali ke atas" setelah halaman di-scroll.
--- */

document.addEventListener('DOMContentLoaded', function () {
    const scrollTopBtn = document.querySelector('#scrollTopBtn');

    if (!scrollTopBtn) {
        return;
    }

    const toggleButton = function () {
        scrollTopBtn.classList.toggle('show', window.scrollY > 300);
    };

    window.addEventListener('scroll', toggleButton, { passive: true });
    toggleButton();

    scrollTopBtn.addEventListener('click', function (e) {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});
