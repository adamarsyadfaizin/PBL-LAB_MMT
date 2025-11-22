/* ---
    File: script-berita.js
    Deskripsi: Script pendukung interaksi ringan (Highlight Hari Ini)
    Catatan: Logika Modal & Filter sudah ditangani langsung di berita.php
--- */

document.addEventListener("DOMContentLoaded", function() {

    /**
     * Fungsi: Menandai tanggal hari ini di kalender
     */
    function highlightToday() {
        const today = new Date();
        const currentDay = today.getDate();
        const currentMonth = today.getMonth() + 1;
        const currentYear = today.getFullYear();
        
        // Cari caption kalender untuk memastikan bulan/tahun cocok
        const calendarCaption = document.querySelector('.calendar-table caption');
        
        if (calendarCaption) {
            const captionText = calendarCaption.textContent.toLowerCase();
            // Format bulan bahasa Indonesia (harus match dengan output PHP)
            const monthNames = ["januari", "februari", "maret", "april", "mei", "juni", "juli", "agustus", "september", "oktober", "november", "desember"];
            const currentMonthName = monthNames[today.getMonth()];
            
            // Cek apakah kalender menampilkan bulan & tahun saat ini
            if (captionText.includes(currentMonthName) && captionText.includes(currentYear)) {
                const cells = document.querySelectorAll('.calendar-table td');
                
                cells.forEach(cell => {
                    // Cek isi angka di dalam sel
                    if (parseInt(cell.textContent) === currentDay) {
                        // Beri styling khusus
                        cell.style.backgroundColor = '#ffc700'; // Warna Kuning Aksen
                        cell.style.color = '#000';
                        cell.style.fontWeight = 'bold';
                        cell.style.borderRadius = '50%';
                        cell.title = "Hari Ini";
                    }
                });
            }
        }
    }

    // Jalankan fungsi
    highlightToday();

});