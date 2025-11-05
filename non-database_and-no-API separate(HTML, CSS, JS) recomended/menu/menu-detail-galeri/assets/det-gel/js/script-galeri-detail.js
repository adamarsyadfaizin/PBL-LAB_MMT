/* ---
   File: script-galeri-detail.js
   Deskripsi: Interaksi vanilla JS untuk Halaman Detail Galeri
--- */

document.addEventListener("DOMContentLoaded", function() {

    /**
     * 1. Toggle Menu Hamburger (Mobile)
     */
    const menuToggle = document.querySelector('.menu-toggle');
    const navMenu = document.querySelector('#primary-menu');

    if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('is-active');
            const isExpanded = navMenu.classList.contains('is-active');
            menuToggle.setAttribute('aria-expanded', isExpanded);
        });
    }

    /**
     * 2. Efek Shadow pada Sticky Header saat scroll
     */
    const header = document.querySelector('#siteHeader');
    if (header) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 10) {
                header.classList.add('header-scrolled');
            } else {
                header.classList.remove('header-scrolled');
            }
        });
    }

    /**
     * 3. Tombol Scroll-to-Top
     */
    const scrollTopBtn = document.querySelector('#scrollTopBtn');
    
    if (scrollTopBtn) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                scrollTopBtn.classList.add('show');
            } else {
                scrollTopBtn.classList.remove('show');
            }
        });

        scrollTopBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    /**
     * 4. Dropdown Menu Toggle (HANYA UNTUK MOBILE)
     */
    const dropdownToggles = document.querySelectorAll('.main-navigation .dropdown-toggle');

    dropdownToggles.forEach(function(toggle) {
        toggle.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                e.preventDefault(); 
                const parentLi = this.parentElement;
                parentLi.classList.toggle('submenu-open');
            }
        });
    });

    /**
     * 5. Social Share Functionality
     */
    const socialShareLinks = document.querySelectorAll('.social-share a');
    
    socialShareLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const platform = this.getAttribute('aria-label').toLowerCase();
            const currentUrl = encodeURIComponent(window.location.href);
            const title = encodeURIComponent(document.title);
            
            let shareUrl = '';
            
            switch(platform) {
                case 'bagikan ke facebook':
                    shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${currentUrl}`;
                    break;
                case 'bagikan ke twitter':
                    shareUrl = `https://twitter.com/intent/tweet?url=${currentUrl}&text=${title}`;
                    break;
                case 'bagikan ke linkedin':
                    shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${currentUrl}`;
                    break;
            }
            
            if (shareUrl) {
                window.open(shareUrl, '_blank', 'width=600,height=400');
            }
        });
    });

    /**
     * 6. Image Zoom Functionality
     */
    const detailBanner = document.querySelector('.detail-article-banner');
    
    if (detailBanner) {
        detailBanner.style.cursor = 'zoom-in';
        detailBanner.setAttribute('title', 'Klik untuk memperbesar gambar');
        
        detailBanner.addEventListener('click', function() {
            this.classList.toggle('zoomed');
            
            if (this.classList.contains('zoomed')) {
                this.style.cursor = 'zoom-out';
                this.style.transform = 'scale(1.5)';
                this.style.transition = 'transform 0.3s ease';
                this.style.zIndex = '1000';
                this.style.position = 'relative';
            } else {
                this.style.cursor = 'zoom-in';
                this.style.transform = 'scale(1)';
                this.style.zIndex = 'auto';
                this.style.position = 'static';
            }
        });
        
        // Reset zoom when clicking outside
        document.addEventListener('click', function(e) {
            if (detailBanner.classList.contains('zoomed') && !detailBanner.contains(e.target)) {
                detailBanner.classList.remove('zoomed');
                detailBanner.style.cursor = 'zoom-in';
                detailBanner.style.transform = 'scale(1)';
                detailBanner.style.zIndex = 'auto';
                detailBanner.style.position = 'static';
            }
        });
    }

    /**
     * 7. Related Gallery Items Interaction
     */
    const relatedItems = document.querySelectorAll('.widget-news li a');
    
    relatedItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(5px)';
            this.style.transition = 'transform 0.2s ease';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
        
        // Keyboard navigation
        item.addEventListener('focus', function() {
            this.style.transform = 'translateX(5px)';
        });
        
        item.addEventListener('blur', function() {
            this.style.transform = 'translateX(0)';
        });
    });

    /**
     * 8. Category Filter Enhancement
     */
    const categoryLinks = document.querySelectorAll('.widget-categories li a');
    
    categoryLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Add active state
            categoryLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            
            // Simulate category filtering
            const category = this.textContent.toLowerCase();
            showCategoryMessage(`Menampilkan galeri: ${category}`);
        });
    });

    /**
     * 9. Show Category Message
     */
    function showCategoryMessage(message) {
        // Remove existing message
        const existingMessage = document.querySelector('.category-message');
        if (existingMessage) {
            existingMessage.remove();
        }
        
        const messageDiv = document.createElement('div');
        messageDiv.className = 'category-message';
        messageDiv.style.padding = '12px 16px';
        messageDiv.style.margin = '16px 0';
        messageDiv.style.backgroundColor = 'rgba(0, 59, 142, 0.1)';
        messageDiv.style.color = 'var(--color-primary)';
        messageDiv.style.border = '1px solid var(--color-primary)';
        messageDiv.style.borderRadius = '6px';
        messageDiv.style.fontWeight = '600';
        messageDiv.style.textAlign = 'center';
        messageDiv.textContent = message;
        
        // Insert before the back link
        const backLink = document.querySelector('.back-link');
        backLink.parentNode.insertBefore(messageDiv, backLink);
        
        // Auto-remove after 3 seconds
        setTimeout(() => {
            messageDiv.remove();
        }, 3000);
    }

    /**
     * 10. Image Loading Enhancement
     */
    if (detailBanner) {
        // Add loading state
        detailBanner.style.opacity = '0';
        detailBanner.style.transition = 'opacity 0.3s ease';
        
        detailBanner.addEventListener('load', function() {
            this.style.opacity = '1';
        });
        
        // Fallback for image error
        detailBanner.addEventListener('error', function() {
            this.src = '../../../../assets/images/placeholder-gallery.jpg';
            this.alt = 'Gambar tidak tersedia';
            this.style.opacity = '1';
        });
    }

    /**
     * 11. Print Functionality
     */
    function addPrintButton() {
        const printBtn = document.createElement('button');
        printBtn.className = 'btn btn-secondary';
        printBtn.style.marginLeft = '10px';
        printBtn.innerHTML = '🖨️ Cetak Halaman';
        
        printBtn.addEventListener('click', function() {
            window.print();
        });
        
        // Add print button next to back link
        const backLink = document.querySelector('.back-link');
        backLink.parentNode.insertBefore(printBtn, backLink.nextSibling);
    }

    /**
     * 12. Initialize Enhanced Features
     */
    function initializeEnhancedFeatures() {
        // Add print button
        addPrintButton();
        
        // Add subtle animation to content
        const primaryContent = document.querySelector('.primary-content');
        if (primaryContent) {
            primaryContent.style.opacity = '0';
            primaryContent.style.transform = 'translateY(20px)';
            primaryContent.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            
            setTimeout(() => {
                primaryContent.style.opacity = '1';
                primaryContent.style.transform = 'translateY(0)';
            }, 200);
        }
        
        // Add view count simulation (for demo purposes)
        simulateViewCount();
    }

    /**
     * 13. Simulate View Count
     */
    function simulateViewCount() {
        const viewCount = Math.floor(Math.random() * 100) + 50; // Random between 50-150
        const metaInfo = document.querySelector('.meta-info');
        
        if (metaInfo) {
            const viewSpan = document.createElement('span');
            viewSpan.innerHTML = `<strong>Dilihat:</strong> ${viewCount} kali`;
            metaInfo.appendChild(viewSpan);
        }
    }

    /**
     * 14. Keyboard Navigation
     */
    document.addEventListener('keydown', function(e) {
        // Escape key to close zoomed image
        if (e.key === 'Escape' && detailBanner && detailBanner.classList.contains('zoomed')) {
            detailBanner.classList.remove('zoomed');
            detailBanner.style.cursor = 'zoom-in';
            detailBanner.style.transform = 'scale(1)';
            detailBanner.style.zIndex = 'auto';
            detailBanner.style.position = 'static';
        }
        
        // 'B' key to go back
        if (e.key === 'b' && !e.ctrlKey && !e.altKey) {
            const backLink = document.querySelector('.back-link');
            if (backLink && document.activeElement !== backLink) {
                e.preventDefault();
                backLink.focus();
            }
        }
    });

    // Initialize enhanced features
    initializeEnhancedFeatures();

});