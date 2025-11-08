/* ==== ADMIN PANEL JAVASCRIPT ==== */

document.addEventListener("DOMContentLoaded", function() {

    /**
     * 1. Sidebar Toggle untuk Mobile
     */
    const sidebarToggle = document.querySelector('#sidebarToggle');
    const sidebar = document.querySelector('#adminSidebar');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('is-active');
            
            // Update toggle button animation
            this.classList.toggle('active');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    sidebar.classList.remove('is-active');
                    sidebarToggle.classList.remove('active');
                }
            }
        });
    }

    /**
     * 2. Highlight Active Menu Item
     */
    function highlightActiveMenu() {
        const currentPath = window.location.pathname;
        const menuItems = document.querySelectorAll('.sidebar-nav a');
        
        menuItems.forEach(item => {
            const href = item.getAttribute('href');
            if (currentPath.includes(href) && href !== '#') {
                item.parentElement.classList.add('active');
            }
        });
    }
    
    highlightActiveMenu();

    /**
     * 3. Konfirmasi Delete
     */
    const deleteButtons = document.querySelectorAll('.btn-delete');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const confirmed = confirm('Apakah Anda yakin ingin menghapus item ini?');
            if (!confirmed) {
                e.preventDefault();
            }
        });
    });

    /**
     * 4. Form Validation (Basic)
     */
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = 'var(--color-danger)';
                    
                    // Show error message
                    let errorMsg = field.nextElementSibling;
                    if (!errorMsg || !errorMsg.classList.contains('error-message')) {
                        errorMsg = document.createElement('span');
                        errorMsg.className = 'error-message';
                        errorMsg.style.color = 'var(--color-danger)';
                        errorMsg.style.fontSize = '12px';
                        errorMsg.style.marginTop = '4px';
                        errorMsg.textContent = 'Field ini wajib diisi';
                        field.parentNode.insertBefore(errorMsg, field.nextSibling);
                    }
                } else {
                    field.style.borderColor = '';
                    const errorMsg = field.nextElementSibling;
                    if (errorMsg && errorMsg.classList.contains('error-message')) {
                        errorMsg.remove();
                    }
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Mohon lengkapi semua field yang wajib diisi');
            }
        });
        
        // Clear error on input
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                this.style.borderColor = '';
                const errorMsg = this.nextElementSibling;
                if (errorMsg && errorMsg.classList.contains('error-message')) {
                    errorMsg.remove();
                }
            });
        });
    });

    /**
     * 5. File Upload Preview
     */
    const fileInputs = document.querySelectorAll('input[type="file"]');
    
    fileInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    // Find or create preview element
                    let preview = input.parentNode.querySelector('.file-preview');
                    if (!preview) {
                        preview = document.createElement('div');
                        preview.className = 'file-preview';
                        preview.style.marginTop = '10px';
                        input.parentNode.appendChild(preview);
                    }
                    
                    if (file.type.startsWith('image/')) {
                        preview.innerHTML = `<img src="${e.target.result}" style="max-width: 200px; border-radius: 6px;">`;
                    } else {
                        preview.innerHTML = `<p>File: ${file.name}</p>`;
                    }
                };
                
                reader.readAsDataURL(file);
            }
        });
    });

    /**
     * 6. Search Functionality
     */
    const searchInput = document.querySelector('.search-box input');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('.data-table tbody tr');
            
            if (tableRows.length > 0) {
                tableRows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
        });
    }

    /**
     * 7. Notification Badge Animation
     */
    const notifBtn = document.querySelector('.notif-btn');
    
    if (notifBtn) {
        notifBtn.addEventListener('click', function() {
            const badge = this.querySelector('.badge');
            if (badge) {
                // Animate badge
                badge.style.transform = 'scale(1.2)';
                setTimeout(() => {
                    badge.style.transform = 'scale(1)';
                }, 200);
            }
            
            // In real implementation, this would show notification dropdown
            alert('Notifikasi:\n\n1. Komentar baru pada berita\n2. Proyek baru ditambahkan\n3. Update sistem tersedia');
        });
    }

    /**
     * 8. Auto-save Draft (for forms)
     */
    const contentForms = document.querySelectorAll('form[data-autosave]');
    
    contentForms.forEach(form => {
        const formId = form.id || 'form-draft';
        
        // Load draft on page load
        const savedDraft = localStorage.getItem(formId);
        if (savedDraft) {
            const draftData = JSON.parse(savedDraft);
            Object.keys(draftData).forEach(key => {
                const field = form.querySelector(`[name="${key}"]`);
                if (field) {
                    field.value = draftData[key];
                }
            });
            
            // Show notification
            showNotification('Draft tersimpan dimuat', 'info');
        }
        
        // Save draft on input
        const formInputs = form.querySelectorAll('input, textarea, select');
        formInputs.forEach(input => {
            input.addEventListener('input', debounce(function() {
                saveDraft(form, formId);
            }, 1000));
        });
        
        // Clear draft on submit
        form.addEventListener('submit', function() {
            localStorage.removeItem(formId);
        });
    });

    function saveDraft(form, formId) {
        const formData = new FormData(form);
        const draftData = {};
        
        for (let [key, value] of formData.entries()) {
            draftData[key] = value;
        }
        
        localStorage.setItem(formId, JSON.stringify(draftData));
        showNotification('Draft tersimpan', 'success');
    }

    /**
     * 9. Show Notification Helper
     */
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = 'admin-notification';
        notification.style.cssText = `
            position: fixed;
            top: 80px;
            right: 20px;
            background: ${type === 'success' ? 'var(--color-success)' : 
                         type === 'danger' ? 'var(--color-danger)' : 
                         'var(--color-info)'};
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
        
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        setTimeout(() => {
            notification.style.transform = 'translateX(120%)';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 2000);
    }

    /**
     * 10. Debounce Helper
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    /**
     * 11. Table Sorting
     */
    const tableSortHeaders = document.querySelectorAll('.data-table th[data-sort]');
    
    tableSortHeaders.forEach(header => {
        header.style.cursor = 'pointer';
        header.style.userSelect = 'none';
        
        header.addEventListener('click', function() {
            const table = this.closest('table');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const columnIndex = Array.from(this.parentNode.children).indexOf(this);
            const sortOrder = this.dataset.sortOrder || 'asc';
            
            rows.sort((a, b) => {
                const aValue = a.children[columnIndex].textContent;
                const bValue = b.children[columnIndex].textContent;
                
                if (sortOrder === 'asc') {
                    return aValue.localeCompare(bValue);
                } else {
                    return bValue.localeCompare(aValue);
                }
            });
            
            rows.forEach(row => tbody.appendChild(row));
            
            // Toggle sort order
            this.dataset.sortOrder = sortOrder === 'asc' ? 'desc' : 'asc';
            
            // Update sort indicator
            tableSortHeaders.forEach(h => h.classList.remove('sorted-asc', 'sorted-desc'));
            this.classList.add(`sorted-${this.dataset.sortOrder}`);
        });
    });

    /**
     * 12. Bulk Actions
     */
    const selectAllCheckbox = document.querySelector('#selectAll');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    
    if (selectAllCheckbox && itemCheckboxes.length > 0) {
        selectAllCheckbox.addEventListener('change', function() {
            itemCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActionBtn();
        });
        
        itemCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateBulkActionBtn);
        });
    }
    
    function updateBulkActionBtn() {
        const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
        const bulkActionBtn = document.querySelector('#bulkActionBtn');
        
        if (bulkActionBtn) {
            if (checkedCount > 0) {
                bulkActionBtn.disabled = false;
                bulkActionBtn.textContent = `Aksi (${checkedCount} terpilih)`;
            } else {
                bulkActionBtn.disabled = true;
                bulkActionBtn.textContent = 'Aksi';
            }
        }
    }

    /**
     * 13. Initialize Tooltips
     */
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    
    tooltipElements.forEach(element => {
        element.style.position = 'relative';
        element.style.cursor = 'pointer';
        
        element.addEventListener('mouseenter', function() {
            const tooltipText = this.dataset.tooltip;
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = tooltipText;
            tooltip.style.cssText = `
                position: absolute;
                bottom: 100%;
                left: 50%;
                transform: translateX(-50%);
                background: rgba(0, 0, 0, 0.8);
                color: white;
                padding: 6px 12px;
                border-radius: 4px;
                font-size: 12px;
                white-space: nowrap;
                z-index: 1000;
                margin-bottom: 5px;
            `;
            
            this.appendChild(tooltip);
        });
        
        element.addEventListener('mouseleave', function() {
            const tooltip = this.querySelector('.tooltip');
            if (tooltip) {
                tooltip.remove();
            }
        });
    });

});