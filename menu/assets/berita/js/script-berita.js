// ==========================================================
// 1. FUNGSI KALENDER WIDGET (Jika Anda menggunakannya)
// ==========================================================

// Variabel global yang diisi dari PHP (asumsi data sudah ada di berita.php)
const eventsByDate = typeof eventsByDate !== 'undefined' ? eventsByDate : {};
const calendarModal = document.getElementById('calendarModal');
const modalBackdrop = document.getElementById('modalBackdrop');
const modalEventsList = document.getElementById('modalEventsList');
const modalDateTitle = document.getElementById('modalDateTitle');

function showModal(date, events) {
    if (!calendarModal || !modalBackdrop || !modalEventsList || !modalDateTitle) return;

    modalDateTitle.textContent = `Kegiatan pada Tanggal ${date}`;
    modalEventsList.innerHTML = ''; 

    if (events.length > 0) {
        events.forEach(event => {
            const item = document.createElement('div');
            item.className = 'event-item';
            item.innerHTML = `
                <h4><a href="${event.link}" onclick="closeModal(event);">${event.title}</a></h4>
                <p>Lihat detail berita/kegiatan.</p>
            `;
            modalEventsList.appendChild(item);
        });
    } else {
        modalEventsList.innerHTML = '<p class="text-center">Tidak ada kegiatan terjadwal pada tanggal ini.</p>';
    }

    modalBackdrop.style.display = 'flex';
}

function closeModal(event) {
    event.preventDefault();
    if (modalBackdrop) {
        modalBackdrop.style.display = 'none';
    }
}

function generateCalendar(year, month, events) {
    const calendarTable = document.getElementById('calendarTable');
    if (!calendarTable) return;

    calendarTable.innerHTML = '';
    
    const date = new Date(year, month - 1);
    const firstDay = new Date(year, month - 1, 1).getDay(); // 0 = Sunday, 1 = Monday
    const daysInMonth = new Date(year, month, 0).getDate();
    const today = new Date();
    const currentYear = today.getFullYear();
    const currentMonth = today.getMonth() + 1;
    const currentDay = today.getDate();

    // Header Hari
    const headerRow = calendarTable.insertRow();
    ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'].forEach(day => {
        const th = document.createElement('th');
        th.textContent = day;
        headerRow.appendChild(th);
    });

    let dateCounter = 1;
    for (let i = 0; i < 6; i++) { // Maksimum 6 baris (minggu)
        const row = calendarTable.insertRow();
        let weekCompleted = true;

        for (let j = 0; j < 7; j++) {
            const cell = row.insertCell();
            cell.dataset.day = j;

            if (i === 0 && j < firstDay) {
                // Sel kosong sebelum hari pertama bulan
                cell.innerHTML = '';
            } else if (dateCounter > daysInMonth) {
                // Sel kosong setelah hari terakhir bulan
                cell.innerHTML = '';
                weekCompleted = false; // Baris ini belum diisi penuh, tapi sudah lewat
            } else {
                const dayNumber = dateCounter;
                const fullDate = `${year}-${String(month).padStart(2, '0')}-${String(dayNumber).padStart(2, '0')}`;
                
                cell.textContent = dayNumber;

                // Tanda Hari Ini (Today)
                if (year === currentYear && month === currentMonth && dayNumber === currentDay) {
                    cell.classList.add('today-day');
                }

                // Tanda Hari Event
                if (events[fullDate]) {
                    cell.classList.add('event-day');
                    cell.onclick = () => showModal(fullDate, events[fullDate]);
                } else {
                    cell.onclick = () => {}; // Biarkan klik kosong jika tidak ada event
                }
                
                dateCounter++;
            }
        }
        // Hentikan looping jika sudah melewati hari terakhir dan tidak ada lagi hari di baris ini
        if (dateCounter > daysInMonth && row.lastChild.textContent === '') break;
    }
}

// Inisialisasi Kalender
let currentCalDate = new Date();

function updateCalendar() {
    const year = currentCalDate.getFullYear();
    const month = currentCalDate.getMonth() + 1;

    // Perbarui judul
    const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    const monthTitle = document.getElementById('calCurrentMonth');
    if (monthTitle) {
        monthTitle.textContent = `${monthNames[month - 1]} ${year}`;
    }

    // Generate kalender
    generateCalendar(year, month, eventsByDate);
}

document.addEventListener('DOMContentLoaded', () => {
    updateCalendar();

    // Handler tombol navigasi kalender
    const prevBtn = document.getElementById('calNavPrev');
    const nextBtn = document.getElementById('calNavNext');
    const closeBtn = document.getElementById('modalCloseBtn');
    
    if (prevBtn) {
        prevBtn.addEventListener('click', (e) => {
            e.preventDefault();
            currentCalDate.setMonth(currentCalDate.getMonth() - 1);
            updateCalendar();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', (e) => {
            e.preventDefault();
            currentCalDate.setMonth(currentCalDate.getMonth() + 1);
            updateCalendar();
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }
    if (modalBackdrop) {
        modalBackdrop.addEventListener('click', (e) => {
            if (e.target === modalBackdrop) {
                closeModal(e);
            }
        });
    }
});


// ==========================================================
// 2. FUNGSI ANIMASI SCROLL (Scroll Reveal)
// ==========================================================

function handleScrollAnimation() {
    // Memilih semua elemen yang memiliki kelas animasi scroll
    const animatedElements = document.querySelectorAll(
        '.event-highlight, .search-filter-container, .section-separator, .facility-item, .pagination-controls, .widget, .no-results'
    );

    animatedElements.forEach((element) => {
        const rect = element.getBoundingClientRect();
        // Titik pemicu: ketika elemen berada di 90% dari tinggi viewport
        const triggerPoint = window.innerHeight * 0.9; 

        if (rect.top <= triggerPoint && rect.bottom >= 0) {
            // Menambahkan kelas 'animate-in' untuk memicu transisi CSS
            element.classList.add('animate-in');
        } 
        // Opsional: Jika Anda ingin elemen menghilang saat discroll ke atas (kurang umum)
        /* else if (rect.bottom < 0) {
            element.classList.remove('animate-in');
        } */
    });
}

// Menghubungkan fungsi ke event scroll dan load
window.addEventListener('scroll', handleScrollAnimation);
window.addEventListener('load', handleScrollAnimation);

// Jalankan sekali saat load untuk elemen yang sudah terlihat di awal
document.addEventListener('DOMContentLoaded', handleScrollAnimation);