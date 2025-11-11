/* ---
    File: script-berita.js
    Deskripsi: Interaksi khusus untuk Halaman Berita & Kegiatan
--- */

document.addEventListener("DOMContentLoaded", function() {

    /**
     * 1. Filter Form Handling
     */
    const filterForm = document.querySelector('.filter-bar');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            // Implementasi filter bisa ditambahkan di sini
            console.log('Filter diterapkan');
            
            // Contoh: Redirect dengan parameter filter
            const kategori = document.getElementById('filter-kategori').value;
            const tahun = document.getElementById('filter-tahun').value;
            
            if (kategori !== 'semua' || tahun !== '2025') {
                window.location.href = `?kategori=${kategori}&tahun=${tahun}`;
            }
        });
    }

    /**
     * 2. Highlight tanggal hari ini di kalender
     */
    function highlightToday() {
        const today = new Date();
        const currentDay = today.getDate();
        const currentMonth = today.getMonth() + 1;
        const currentYear = today.getFullYear();
        
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

    highlightToday();

    /**
     * 3. Event listeners untuk kalender events
     */
    const calendarEvents = document.querySelectorAll('.calendar-table td.event a');
    calendarEvents.forEach(event => {
        event.addEventListener('click', function(e) {
            e.preventDefault();
            const eventTitle = this.getAttribute('title');
            alert(`Event: ${eventTitle}\nDetail event akan ditampilkan di sini.`);
        });
    });

});