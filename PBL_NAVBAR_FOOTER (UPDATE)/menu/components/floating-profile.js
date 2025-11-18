// floating-profile.js

document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById("floatingProfileBtn");
    const menu = document.getElementById("floatingMenu");

    if (!btn || !menu) return;

    let isDragging = false;
    let dragThreshold = 5; 
    let startX, startY; 
    let initialX, initialY; 

    const style = getComputedStyle(document.documentElement);
    const buttonSize = parseFloat(style.getPropertyValue('--button-size')) || 70;
    const menuWidth = parseFloat(style.getPropertyValue('--menu-width')) || 240;

    const defaultRight = 35;
    const defaultBottom = 35;
    const menuGap = 10; 

    // === FUNGSI TOGGLE MENU ===
    function toggleFloatingMenu() {
        if (!isDragging) {
            menu.classList.toggle("active");
            btn.classList.toggle("glow");
            updateMenuPosition();
        }
    }
    
    // === FUNGSI LOAD POSITION ===
    function loadPosition() {
        const savedPosition = localStorage.getItem('floatingProfilePosition');
        if (savedPosition) {
            const pos = JSON.parse(savedPosition);
            btn.style.right = 'auto';
            btn.style.bottom = 'auto';
            btn.style.left = pos.left;
            btn.style.top = pos.top;
        } else {
            btn.style.position = 'fixed';
        }
    }

    // === FUNGSI SAVE POSITION ===
    function savePosition() {
        const pos = {
            left: btn.style.left,
            top: btn.style.top
        };
        localStorage.setItem('floatingProfilePosition', JSON.stringify(pos));
    }

    // === FUNGSI UPDATE MENU POSITION ===
    function updateMenuPosition() {
        if (menu.classList.contains("active")) {
            const rect = btn.getBoundingClientRect();
            
            const menuLeft = rect.left - menuWidth + buttonSize;
            const menuTop = rect.top - menu.offsetHeight - menuGap; 

            menu.style.position = 'fixed';
            menu.style.left = `${menuLeft}px`;
            menu.style.top = `${menuTop}px`;
            menu.style.right = 'auto'; 
            menu.style.bottom = 'auto';
        } else {
            menu.style.left = 'auto';
            menu.style.top = 'auto';
            const btnRect = btn.getBoundingClientRect();
            menu.style.right = (window.innerWidth - btnRect.right) + 'px';
            menu.style.bottom = (window.innerHeight - btnRect.top - menu.offsetHeight - menuGap) + 'px';
        }
    }

    // === LOGIKA DRAG START ===
    function startDrag(e) {
        e.preventDefault(); 
        
        menu.classList.remove("active");
        btn.classList.remove("glow");

        const clientX = e.clientX || (e.touches ? e.touches[0].clientX : undefined);
        const clientY = e.clientY || (e.touches ? e.touches[0].clientY : undefined);

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
        const currentClientX = e.clientX || (e.touches ? e.touches[0].clientX : undefined);
        const currentClientY = e.clientY || (e.touches ? e.touches[0].clientY : undefined);
        
        if (currentClientX === undefined || currentClientY === undefined) return;

        if (!isDragging && (Math.abs(currentClientX - initialX) > dragThreshold || Math.abs(currentClientY - initialY) > dragThreshold)) {
            isDragging = true;
            btn.classList.add("dragging");
            btn.style.transition = 'none'; 
        }

        if (isDragging) {
            let newX = currentClientX - startX;
            let newY = currentClientY - startY;

            newX = Math.max(0, Math.min(newX, window.innerWidth - buttonSize));
            newY = Math.max(0, Math.min(newY, window.innerHeight - buttonSize));

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