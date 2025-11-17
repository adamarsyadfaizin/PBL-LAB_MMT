/* ---
   File: script-kontak.js
   Deskripsi: Interaksi vanilla JS untuk Halaman Kontak
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
     * 5. Form Kontak Validation & Submission
     */
    const contactForm = document.querySelector('.contact-form');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Ambil nilai form
            const name = document.getElementById('contact-name').value.trim();
            const email = document.getElementById('contact-email').value.trim();
            const subject = document.getElementById('contact-subject').value.trim();
            const message = document.getElementById('contact-message').value.trim();
            
            // Validasi dasar
            if (!name || !email || !subject || !message) {
                showFormMessage('Harap isi semua field yang wajib diisi.', 'error');
                return;
            }
            
            if (!isValidEmail(email)) {
                showFormMessage('Format email tidak valid.', 'error');
                return;
            }
            
            // Simulasi pengiriman form
            const submitBtn = contactForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            submitBtn.textContent = 'Mengirim...';
            submitBtn.disabled = true;
            
            // Simulasi delay pengiriman
            setTimeout(() => {
                showFormMessage('Pesan Anda telah berhasil dikirim! Kami akan segera menghubungi Anda.', 'success');
                contactForm.reset();
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }, 2000);
        });
        
        // Real-time validation
        const formInputs = contactForm.querySelectorAll('input, textarea');
        formInputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', function() {
                clearFieldError(this);
            });
        });
    }

    /**
     * 6. Fungsi Validasi Email
     */
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    /**
     * 7. Fungsi Validasi Field
     */
    function validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let errorMessage = '';
        
        switch(field.type) {
            case 'text':
                if (field.id === 'contact-name' && value.length < 2) {
                    isValid = false;
                    errorMessage = 'Nama harus minimal 2 karakter.';
                } else if (field.id === 'contact-subject' && value.length < 5) {
                    isValid = false;
                    errorMessage = 'Subjek harus minimal 5 karakter.';
                }
                break;
                
            case 'email':
                if (!isValidEmail(value)) {
                    isValid = false;
                    errorMessage = 'Format email tidak valid.';
                }
                break;
                
            case 'textarea':
                if (value.length < 10) {
                    isValid = false;
                    errorMessage = 'Pesan harus minimal 10 karakter.';
                }
                break;
        }
        
        if (!isValid) {
            showFieldError(field, errorMessage);
        } else {
            clearFieldError(field);
        }
        
        return isValid;
    }

    /**
     * 8. Fungsi Menampilkan Error Field
     */
    function showFieldError(field, message) {
        clearFieldError(field);
        
        field.style.borderColor = '#dc3545';
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error';
        errorDiv.style.color = '#dc3545';
        errorDiv.style.fontSize = '14px';
        errorDiv.style.marginTop = '5px';
        errorDiv.textContent = message;
        
        field.parentNode.appendChild(errorDiv);
    }

    /**
     * 9. Fungsi Menghapus Error Field
     */
    function clearFieldError(field) {
        field.style.borderColor = '';
        
        const existingError = field.parentNode.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }
    }

    /**
     * 10. Fungsi Menampilkan Pesan Form
     */
    function showFormMessage(message, type) {
        // Hapus pesan sebelumnya
        const existingMessage = document.querySelector('.form-message');
        if (existingMessage) {
            existingMessage.remove();
        }
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `form-message form-message-${type}`;
        messageDiv.style.padding = '12px 16px';
        messageDiv.style.margin = '16px 0';
        messageDiv.style.borderRadius = '6px';
        messageDiv.style.fontWeight = '600';
        
        if (type === 'success') {
            messageDiv.style.backgroundColor = 'rgba(40, 167, 69, 0.1)';
            messageDiv.style.color = '#28a745';
            messageDiv.style.border = '1px solid #28a745';
        } else {
            messageDiv.style.backgroundColor = 'rgba(220, 53, 69, 0.1)';
            messageDiv.style.color = '#dc3545';
            messageDiv.style.border = '1px solid #dc3545';
        }
        
        messageDiv.textContent = message;
        
        // Sisipkan sebelum tombol submit
        const submitBtn = contactForm.querySelector('button[type="submit"]');
        contactForm.insertBefore(messageDiv, submitBtn);
        
        // Auto-hide pesan sukses setelah 5 detik
        if (type === 'success') {
            setTimeout(() => {
                messageDiv.remove();
            }, 5000);
        }
    }

    /**
     * 11. Enhanced Social Media Links
     */
    const socialLinks = document.querySelectorAll('.widget-social-links a');
    
    socialLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1) rotate(5deg)';
        });
        
        link.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1) rotate(0deg)';
        });
        
        // Tambah tooltip
        const platform = this.getAttribute('aria-label');
        if (platform) {
            this.setAttribute('title', `Kunjungi kami di ${platform}`);
        }
    });

    /**
     * 12. Copy Email Functionality
     */
    const emailLink = document.querySelector('a[href^="mailto:"]');
    if (emailLink) {
        emailLink.addEventListener('click', function(e) {
            e.preventDefault();
            
            const email = this.href.replace('mailto:', '');
            
            // Copy to clipboard
            navigator.clipboard.writeText(email).then(() => {
                // Show copy confirmation
                const originalText = this.textContent;
                this.textContent = 'Email disalin!';
                this.style.color = '#28a745';
                
                setTimeout(() => {
                    this.textContent = originalText;
                    this.style.color = '';
                }, 2000);
            }).catch(() => {
                // Fallback: open email client
                window.location.href = this.href;
            });
        });
    }

    /**
     * 13. Interactive Map Placeholder
     */
    const mapPlaceholder = document.querySelector('.map-placeholder');
    if (mapPlaceholder) {
        mapPlaceholder.addEventListener('click', function() {
            this.innerHTML = `
                <div style="text-align: center; padding: 20px;">
                    <h4 style="margin-bottom: 10px; color: var(--color-primary);">Google Maps Integration</h4>
                    <p style="color: var(--color-text-secondary); margin-bottom: 15px;">
                        Dalam implementasi nyata, di sini akan ditampilkan peta Google Maps yang interaktif.
                    </p>
                    <a href="https://maps.google.com/?q=Politeknik+Negeri+Malang" 
                       target="_blank" 
                       style="color: var(--color-primary); text-decoration: underline; font-weight: 600;">
                        Buka di Google Maps
                    </a>
                </div>
            `;
            
            // Reset setelah 5 detik
            setTimeout(() => {
                this.innerHTML = '[Tampilan Peta Google Maps]';
            }, 5000);
        });
        
        // Tambah style hover untuk map placeholder
        mapPlaceholder.style.cursor = 'pointer';
        mapPlaceholder.style.transition = 'background-color 0.2s ease';
        
        mapPlaceholder.addEventListener('mouseenter', function() {
            this.style.backgroundColor = 'var(--color-primary)';
            this.style.color = 'var(--color-white)';
        });
        
        mapPlaceholder.addEventListener('mouseleave', function() {
            this.style.backgroundColor = 'var(--color-bg-light)';
            this.style.color = 'var(--color-text-secondary)';
        });
    }

});