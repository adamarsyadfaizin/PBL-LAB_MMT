/* ---
   File: script-detail-berita.js
   Deskripsi: Interaksi khusus halaman detail berita
--- */

document.addEventListener("DOMContentLoaded", function() {

    /**
     * 1. Social Share Functionality
     */
    const socialShareLinks = document.querySelectorAll('.social-share a');
    
    socialShareLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const platform = this.textContent.toLowerCase();
            const currentUrl = encodeURIComponent(window.location.href);
            const title = encodeURIComponent(document.title);
            
            let shareUrl = '';
            
            switch(platform) {
                case 'f': // Facebook
                    shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${currentUrl}`;
                    break;
                case 't': // Twitter
                    shareUrl = `https://twitter.com/intent/tweet?url=${currentUrl}&text=${title}`;
                    break;
                case 'l': // LinkedIn
                    shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${currentUrl}`;
                    break;
            }
            
            if (shareUrl) {
                window.open(shareUrl, '_blank', 'width=600,height=400');
            }
        });
    });

    /**
     * 2. Copy Link Functionality
     */
    const copyLinkBtn = document.createElement('a');
    copyLinkBtn.textContent = 'C';
    copyLinkBtn.setAttribute('aria-label', 'Salin tautan');
    copyLinkBtn.style.cssText = `
        display: inline-block;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: var(--color-bg-light);
        border: 1px solid var(--color-border-light);
        color: var(--color-primary);
        text-align: center;
        line-height: 28px;
        font-weight: bold;
        text-decoration: none;
        cursor: pointer;
        transition: background 0.2s ease, color 0.2s ease;
    `;
    
    copyLinkBtn.addEventListener('mouseenter', function() {
        this.style.background = 'var(--color-primary)';
        this.style.color = 'var(--color-white)';
    });
    
    copyLinkBtn.addEventListener('mouseleave', function() {
        this.style.background = 'var(--color-bg-light)';
        this.style.color = 'var(--color-primary)';
    });
    
    copyLinkBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        const currentUrl = window.location.href;
        
        navigator.clipboard.writeText(currentUrl).then(() => {
            // Show temporary notification
            showCopyNotification('Tautan berhasil disalin!');
        }).catch(err => {
            console.error('Gagal menyalin tautan: ', err);
            showCopyNotification('Gagal menyalin tautan');
        });
    });

    // Add copy button to social share
    const socialShareContainer = document.querySelector('.social-share');
    if (socialShareContainer) {
        socialShareContainer.appendChild(copyLinkBtn);
    }

    /**
     * 3. Show Copy Notification
     */
    function showCopyNotification(message) {
        const notification = document.createElement('div');
        notification.textContent = message;
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
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        // Remove after 3 seconds
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
     * 4. Attachment Download Tracking
     */
    const attachmentLinks = document.querySelectorAll('.article-attachments a');
    
    attachmentLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const fileName = this.textContent;
            console.log(`File downloaded: ${fileName}`);
            // Here you can add analytics tracking
            // Example: trackDownload(fileName);
        });
    });

    /**
     * 5. Smooth Scroll for Anchor Links in Content
     */
    document.querySelectorAll('.article-content a[href^="#"]').forEach(anchor => {
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

    /**
     * 6. Image Zoom Enhancement
     */
    const articleImages = document.querySelectorAll('.article-content img');
    
    articleImages.forEach(img => {
        img.style.cursor = 'zoom-in';
        img.addEventListener('click', function() {
            this.classList.toggle('zoomed');
            if (this.classList.contains('zoomed')) {
                this.style.transform = 'scale(1.5)';
                this.style.transition = 'transform 0.3s ease';
                this.style.zIndex = '1000';
                this.style.position = 'relative';
            } else {
                this.style.transform = 'scale(1)';
            }
        });
    });

});