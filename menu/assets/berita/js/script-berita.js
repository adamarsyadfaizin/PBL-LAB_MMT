// ==========================================================
// SCRIPT BERITA FIX (Timezone & Click Handler)
// ==========================================================

// Ambil data dari variabel global yang dicetak di PHP
const eventsData = typeof eventsByDate !== 'undefined' ? eventsByDate : {};

// DOM Elements
const modalBackdrop = document.getElementById('modalBackdrop');
const modalEventsList = document.getElementById('modalEventsList');
const modalDateTitle = document.getElementById('modalDateTitle');

// 1. FUNGSI MENAMPILKAN MODAL
// Fungsi ini dipanggil baik oleh PHP (onclick) maupun JS
window.showModal = function(dateStr, events) {
    if (!modalBackdrop || !modalEventsList || !modalDateTitle) return;

    // Format tampilan tanggal: "3 Desember 2025"
    // Kita parsing manual string YYYY-MM-DD agar aman dari Timezone Browser
    const parts = dateStr.split('-');
    const year = parseInt(parts[0]);
    const month = parseInt(parts[1]) - 1; // JS Month 0-11
    const day = parseInt(parts[2]);
    
    const dateObj = new Date(year, month, day);
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    
    modalDateTitle.textContent = `Agenda: ${dateObj.toLocaleDateString('id-ID', options)}`;
    modalEventsList.innerHTML = ''; 

    // Jika events dipanggil dari onclick PHP, kadang masih undefined kalau user iseng inspect element
    // Jadi kita pastikan ambil dari global data jika parameter events kosong
    const finalEvents = events || eventsData[dateStr];

    if (finalEvents && finalEvents.length > 0) {
        finalEvents.forEach(event => {
            const item = document.createElement('div');
            item.className = 'event-item';
            item.innerHTML = `
                <h4><a href="${event.link}">${event.title}</a></h4>
                <p>${event.summary ? event.summary.substring(0, 100) + '...' : 'Klik judul untuk melihat detail.'}</p>
            `;
            modalEventsList.appendChild(item);
        });
    } else {
        modalEventsList.innerHTML = '<p style="text-align:center; color:#666;">Tidak ada kegiatan terjadwal.</p>';
    }

    modalBackdrop.style.display = 'flex';
};

// 2. FUNGSI MENUTUP MODAL
function closeModal(event) {
    if(event) event.preventDefault();
    if (modalBackdrop) {
        modalBackdrop.style.display = 'none';
    }
}

// 3. FUNGSI GENERATE KALENDER (Saat tombol Next/Prev ditekan)
function generateCalendar(year, month) {
    const calendarTable = document.getElementById('calendarTable');
    if (!calendarTable) return;

    // Bersihkan isi tabel (kecuali header jika ada di thead, tapi di sini kita rebuild body)
    // Struktur HTML di PHP: table > thead, tbody. Kita akses tbody.
    let tbody = calendarTable.querySelector('tbody');
    if(!tbody) {
        tbody = document.createElement('tbody');
        calendarTable.appendChild(tbody);
    }
    tbody.innerHTML = '';
    
    // Konfigurasi Waktu
    // JS Month index: 0-11. Parameter month dari PHP logic masuk sebagai 1-12.
    const jsMonth = month - 1; 
    
    // Cari hari pertama: 0 (Minggu) - 6 (Sabtu)
    const firstDayIndex = new Date(year, jsMonth, 1).getDay(); 
    const daysInMonth = new Date(year, month, 0).getDate();
    
    // Cek Hari Ini (Real-time)
    const now = new Date();
    const isCurrentMonth = (now.getFullYear() === year && now.getMonth() === jsMonth);
    const todayDate = now.getDate();

    // -- Generate Baris --
    let row = tbody.insertRow();
    
    // Sel Kosong Awal
    for (let i = 0; i < firstDayIndex; i++) {
        row.insertCell().innerHTML = '';
    }

    // Loop Hari
    for (let day = 1; day <= daysInMonth; day++) {
        // Ganti baris jika hari minggu (kecuali baris pertama yang sudah dibuat)
        const currentWeekDay = (firstDayIndex + day - 1) % 7;
        if (currentWeekDay === 0 && day > 1) {
            row = tbody.insertRow();
        }

        const cell = row.insertCell();
        cell.textContent = day;
        
        // Buat Key Tanggal YYYY-MM-DD (Penting: Padding Zero!)
        // Contoh: 2025-01-02 (Bukan 2025-1-2)
        const sMonth = String(month).padStart(2, '0');
        const sDay = String(day).padStart(2, '0');
        const fullDateKey = `${year}-${sMonth}-${sDay}`;

        // Style Hari Ini
        if (isCurrentMonth && day === todayDate) {
            cell.classList.add('today-day');
        }

        // Cek Event
        if (eventsData[fullDateKey]) {
            cell.classList.add('event-day');
            cell.title = "Klik untuk lihat kegiatan";
            // Pasang OnClick Handler
            cell.onclick = function() {
                showModal(fullDateKey, eventsData[fullDateKey]);
            };
        }
    }
    
    // Sel Kosong Akhir (Opsional, biar rapi)
    const totalCells = firstDayIndex + daysInMonth;
    const remainingCells = 7 - (totalCells % 7);
    if (remainingCells < 7) {
        for(let k=0; k < remainingCells; k++) {
            row.insertCell().innerHTML = '';
        }
    }
}

// 4. INISIALISASI & EVENT LISTENER
// Default start date
let currentCalDate = new Date(); // Browser time

document.addEventListener('DOMContentLoaded', () => {
    // Note: Kalender awal SUDAH dirender oleh PHP (SSR).
    // Kita hanya perlu handle tombol navigasi Next/Prev.

    // Tombol Prev (<)
    const prevBtn = document.getElementById('calNavPrev');
    if (prevBtn) {
        prevBtn.addEventListener('click', (e) => {
            e.preventDefault();
            // Mundur 1 bulan
            currentCalDate.setMonth(currentCalDate.getMonth() - 1);
            updateUI();
        });
    }

    // Tombol Next (>)
    const nextBtn = document.getElementById('calNavNext');
    if (nextBtn) {
        nextBtn.addEventListener('click', (e) => {
            e.preventDefault();
            // Maju 1 bulan
            currentCalDate.setMonth(currentCalDate.getMonth() + 1);
            updateUI();
        });
    }

    function updateUI() {
        const y = currentCalDate.getFullYear();
        const m = currentCalDate.getMonth() + 1; // 1-12
        
        // Update Teks Judul
        const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        const monthTitle = document.getElementById('calCurrentMonth');
        if (monthTitle) {
            monthTitle.textContent = `${monthNames[m - 1]} ${y}`;
        }

        // Render Ulang Tabel via JS
        generateCalendar(y, m);
    }

    // Tombol Close Modal
    const closeBtn = document.getElementById('modalCloseBtn');
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    
    // Klik Backdrop Close
    if (modalBackdrop) {
        modalBackdrop.addEventListener('click', (e) => {
            if (e.target === modalBackdrop) closeModal(e);
        });
    }
    
    // Tombol ESC Close
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeModal(e);
    });

    // Scroll Animation
    const animatedElements = document.querySelectorAll('.animate-on-scroll');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, { threshold: 0.1 });

    animatedElements.forEach(el => observer.observe(el));
});