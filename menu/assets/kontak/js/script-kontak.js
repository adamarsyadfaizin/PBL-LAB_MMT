/* ---
   File: script-kontak.js
   Deskripsi: Script validasi form dan Peta Leaflet
--- */

document.addEventListener("DOMContentLoaded", function() {

    // --- 1. VALIDASI FORM REAL-TIME ---
    const contactForm = document.getElementById('contactForm');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Validasi Nama
            const nameInput = document.getElementById('contact-name');
            const nameError = document.getElementById('nameError');
            if (nameInput.value.length < 2) {
                nameError.style.display = 'block';
                isValid = false;
            } else {
                nameError.style.display = 'none';
            }
            
            // Validasi Email
            const emailInput = document.getElementById('contact-email');
            const emailError = document.getElementById('emailError');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailInput.value)) {
                emailError.style.display = 'block';
                isValid = false;
            } else {
                emailError.style.display = 'none';
            }
            
            if (!isValid) {
                e.preventDefault(); // Cegah submit jika error
            }
        });
    }

    // --- 2. PETA LOKASI (LEAFLET JS) ---
    if (document.getElementById('map')) {
       
        const lat = -7.943852816824638; 
        const lng = 112.61428690362587;

        // Inisialisasi Peta
        const map = L.map('map').setView([lat, lng], 16);

        // Tambahkan Tile Layer (OpenStreetMap)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Tambahkan Marker
        L.marker([lat, lng]).addTo(map)
            .bindPopup('<b>Laboratorium MMT</b><br>Politeknik Negeri Malang')
            .openPopup();
    }

});

