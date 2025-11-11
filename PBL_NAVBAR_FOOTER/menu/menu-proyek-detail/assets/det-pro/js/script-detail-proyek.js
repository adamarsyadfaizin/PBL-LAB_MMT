/* ---
   File: script-detail-proyek.js
   Deskripsi: Interaksi khusus halaman detail proyek
--- */

document.addEventListener("DOMContentLoaded", function() {

    /**
     * 1. Inisialisasi Video Mockup Interaksi
     */
    const mockupVideo = document.querySelector('.mockup-video');
    if (mockupVideo) {
        mockupVideo.addEventListener('click', function() {
            this.innerHTML = '<span style="font-size: 16px;">Video Demo Akan Diputar</span>';
            this.style.backgroundColor = '#555';
            
            // Reset setelah 2 detik
            setTimeout(() => {
                this.innerHTML = '<span>&#9658;</span>';
                this.style.backgroundColor = '#333';
            }, 2000);
        });
    }

    /**
     * 2. Form Komentar - Validasi & Feedback
     */
    const commentForm = document.querySelector('.comment-form');
    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const nameInput = document.getElementById('comment-name');
            const commentInput = document.getElementById('comment-text');
            
            // Validasi sederhana
            if (nameInput.value.trim() === '' || commentInput.value.trim() === '') {
                alert('Mohon isi nama dan komentar Anda.');
                return;
            }
            
            // Simulasi pengiriman data
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Mengirim...';
            submitBtn.disabled = true;
            
            // Simulasi delay server
            setTimeout(() => {
                // Tambahkan komentar baru ke daftar
                addNewComment(nameInput.value, commentInput.value);
                
                // Reset form
                this.reset();
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                
                // Tampilkan notifikasi
                showNotification('Komentar berhasil dikirim!');
            }, 1000);
        });
    }

    /**
     * 3. Fungsi untuk Menambah Komentar Baru
     */
    function addNewComment(name, comment) {
        const commentList = document.querySelector('.comment-list');
        if (!commentList) return;
        
        const commentCount = commentList.querySelector('h3');
        const commentsContainer = commentList.querySelector('.comment-item') ? 
            commentList.querySelector('.comment-item').parentElement : commentList;
        
        // Update jumlah komentar
        const currentCount = parseInt(commentCount.textContent.match(/\d+/)) || 0;
        commentCount.textContent = `Komentar (${currentCount + 1})`;
        
        // Buat elemen komentar baru
        const newComment = document.createElement('div');
        newComment.className = 'comment-item';
        newComment.innerHTML = `
            <p class="comment-author">${escapeHtml(name)}</p>
            <p class="comment-date">${getCurrentDate()}</p>
            <p>${escapeHtml(comment)}</p>
        `;
        
        // Tambahkan di atas komentar lama
        commentsContainer.insertBefore(newComment, commentsContainer.firstChild);
    }

    /**
     * 4. Utility Functions
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function getCurrentDate() {
        const now = new Date();
        const options = { day: 'numeric', month: 'long', year: 'numeric' };
        return now.toLocaleDateString('id-ID', options);
    }

    function showNotification(message) {
        // Buat elemen notifikasi
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--color-primary);
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 10000;
            font-weight: 600;
            transform: translateX(120%);
            transition: transform 0.3s ease;
        `;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Animasi masuk
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        // Hapus setelah 3 detik
        setTimeout(() => {
            notification.style.transform = 'translateX(120%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }

    /**
     * 5. Interaksi Tech Badges (opsional)
     */
    const techBadges = document.querySelectorAll('.tech-badge');
    techBadges.forEach(badge => {
        badge.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
        });
        
        badge.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    });

    /**
     * 6. Smooth Scroll untuk Anchor Links
     */
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            
            if (href !== '#' && document.querySelector(href)) {
                e.preventDefault();
                document.querySelector(href).scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

});