document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById("floatingProfileBtn");
    const menu = document.getElementById("floatingMenu");

    if (!btn || !menu) {
        console.error("Floating Profile: Tombol atau Menu tidak ditemukan di DOM.");
        return;
    }

    let isDragging = false;
    let dragThreshold = 5; 
    let startX, startY; 
    let initialX, initialY; 

    // Ambil ukuran dari CSS (Pastikan variabel CSS di floating-profile.css sudah benar)
    const style = getComputedStyle(document.documentElement);
    // Menggunakan parseFloat untuk mendapatkan nilai numerik
    const buttonSize = parseFloat(style.getPropertyValue('--button-size')) || 70;
    const menuWidth = parseFloat(style.getPropertyValue('--menu-width')) || 240;
    const menuGap = 20; // Jarak antara tombol dan menu (disesuaikan agar pas dengan CSS)

    // === FUNGSI TOGGLE MENU ===
    function toggleFloatingMenu() {
        if (!isDragging) {
            menu.classList.toggle("active");
            btn.classList.toggle("glow");
            if(menu.classList.contains("active")) {
                updateMenuPosition(); // Posisikan ulang saat dibuka
            }
        }
    }
    
    // === FUNGSI LOAD POSITION (Perbaikan Anti-Error) ===
    function loadPosition() {
        const savedPosition = localStorage.getItem('floatingProfilePosition');
        
        // Atur posisi awal sebagai fixed dan matikan transisi
        btn.style.position = 'fixed';
        btn.style.transition = 'none';

        if (savedPosition) {
            try {
                // Mencegah script crash jika data rusak
                const pos = JSON.parse(savedPosition); 
                
                btn.style.right = 'auto'; // Reset right/bottom CSS
                btn.style.bottom = 'auto';
                
                // Terapkan posisi yang disimpan
                btn.style.left = pos.left;
                btn.style.top = pos.top;
                
            } catch (e) {
                // Jika error, hapus data rusak dan gunakan posisi default CSS
                console.error("Floating Profile: Data LocalStorage rusak, mengatur ulang.", e);
                localStorage.removeItem('floatingProfilePosition');
            }
        }
        
        // Aktifkan kembali transisi dan update posisi menu
        setTimeout(() => {
            btn.style.transition = ''; 
            updateMenuPosition();
        }, 50); 
    }

    // === FUNGSI SAVE POSITION ===
    function savePosition() {
        const pos = { left: btn.style.left, top: btn.style.top };
        localStorage.setItem('floatingProfilePosition', JSON.stringify(pos));
    }

    // === FUNGSI UPDATE MENU POSITION (Perbaikan Akumulasi/Stretching) ===
    function updateMenuPosition() {
        if (!menu.classList.contains("active")) return;
        
        // Dapatkan posisi real-time tombol di viewport
        const rect = btn.getBoundingClientRect();
        
        // Pastikan menu menggunakan positioning absolute/fixed yang benar
        menu.style.position = 'fixed';
        
        // 1. Posisi HORIZONTAL (Sejajar Kanan Tombol)
        // Hitung jarak tepi kanan tombol dari tepi kanan viewport
        const rightOffset = window.innerWidth - rect.right;
        // Terapkan jarak tersebut ke tepi kanan menu
        menu.style.right = `${rightOffset}px`;
        menu.style.left = 'auto'; 

        // 2. Posisi VERTIKAL (Di Atas Tombol)
        // Hitung jarak tepi atas tombol dari tepi bawah viewport
        const bottomOffset = window.innerHeight - rect.top;
        // Jarak dari bottom harus = (jarak ke tepi atas tombol) + (tinggi tombol) + (gap)
        menu.style.bottom = `${bottomOffset + menuGap}px`;
        menu.style.top = 'auto'; 
    }

    // === LOGIKA DRAG START ===
    function startDrag(e) {
        e.preventDefault(); 
        
        menu.classList.remove("active");
        btn.classList.remove("glow");

        const clientX = e.clientX || (e.touches && e.touches[0].clientX);
        const clientY = e.clientY || (e.touches && e.touches[0].clientY);
        
        if (clientX === undefined || clientY === undefined) return;

        const rect = btn.getBoundingClientRect();
        initialX = clientX;
        initialY = clientY;
        startX = clientX - rect.left; 
        startY = clientY - rect.top;

        btn.style.right = 'auto';
        btn.style.bottom = 'auto';
        btn.style.position = 'fixed';
        btn.style.left = rect.left + 'px';
        btn.style.top = rect.top + 'px';

        document.addEventListener('mousemove', onDragMove);
        document.addEventListener('mouseup', onDragEnd);
        document.addEventListener('touchmove', onDragMove);
        document.addEventListener('touchend', onDragEnd);
    }

    // === LOGIKA DRAG MOVE ===
    function onDragMove(e) {
        const currentClientX = e.clientX || (e.touches && e.touches[0].clientX);
        const currentClientY = e.clientY || (e.touches && e.touches[0].clientY);
        
        if (currentClientX === undefined || currentClientY === undefined) return;

        if (!isDragging && (Math.abs(currentClientX - initialX) > dragThreshold || Math.abs(currentClientY - initialY) > dragThreshold)) {
            isDragging = true;
            btn.classList.add("dragging");
            btn.style.transition = 'none'; 
        }

        if (isDragging) {
            let newX = currentClientX - startX;
            let newY = currentClientY - startY;

            const viewportW = window.innerWidth;
            const viewportH = window.innerHeight;

            newX = Math.max(0, Math.min(newX, viewportW - buttonSize));
            newY = Math.max(0, Math.min(newY, viewportH - buttonSize));

            btn.style.left = newX + 'px';
            btn.style.top = newY + 'px';
        }
    }

    // === LOGIKA DRAG END ===
    function onDragEnd(e) {
        document.removeEventListener('mousemove', onDragMove);
        document.removeEventListener('mouseup', onDragEnd);
        document.removeEventListener('touchmove', onDragMove);
        document.removeEventListener('touchend', onDragEnd);

        btn.style.transition = ''; 
        btn.classList.remove("dragging");

        if (isDragging) {
            savePosition(); 
        } else {
            toggleFloatingMenu(); 
        }

        isDragging = false; 
    }
    
    // --- EVENT LISTENERS ---
    window.addEventListener('load', loadPosition);
    btn.addEventListener('mousedown', startDrag);
    btn.addEventListener('touchstart', startDrag);
    window.addEventListener('resize', updateMenuPosition);
    
    // Sembunyikan menu jika mengklik di luar area menu/tombol
    document.addEventListener('click', function(event) {
        const isClickInside = btn.contains(event.target) || menu.contains(event.target);
        if (!isClickInside && menu.classList.contains("active")) {
            menu.classList.remove("active");
            btn.classList.remove("glow");
        }
    });
});