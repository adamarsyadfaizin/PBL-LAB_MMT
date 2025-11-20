/* ---
   File: script-proyek.js
   Deskripsi: Interaksi vanilla JS untuk Halaman Katalog Proyek
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
     * 5. Filter Proyek - Real-time Filtering
     */
    const filterForm = document.querySelector('.project-filter-bar');
    const projectCards = document.querySelectorAll('.project-card');
    const loadMoreBtn = document.querySelector('.pagination-controls .btn');

    if (filterForm) {
        // Ambil semua elemen filter
        const searchInput = document.getElementById('filter-search');
        const categorySelect = document.getElementById('filter-kategori');
        const technologySelect = document.getElementById('filter-teknologi');
        const yearSelect = document.getElementById('filter-tahun');
        const sortSelect = document.getElementById('filter-sort');

        // Event listeners untuk semua filter
        const filterInputs = [searchInput, categorySelect, technologySelect, yearSelect, sortSelect];
        
        filterInputs.forEach(input => {
            if (input) {
                input.addEventListener('change', applyFilters);
                input.addEventListener('input', function() {
                    if (this.type === 'search') {
                        applyFilters();
                    }
                });
            }
        });

        // Fungsi untuk menerapkan filter
        function applyFilters() {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedCategory = categorySelect.value;
            const selectedTechnology = technologySelect.value;
            const selectedYear = yearSelect.value;
            const selectedSort = sortSelect.value;

            let visibleCount = 0;

            projectCards.forEach(card => {
                const title = card.querySelector('h4').textContent.toLowerCase();
                const description = card.querySelector('p').textContent.toLowerCase();
                const tags = Array.from(card.querySelectorAll('.tag-badge')).map(tag => tag.textContent.toLowerCase());
                
                let matchesSearch = searchTerm === '' || 
                    title.includes(searchTerm) || 
                    description.includes(searchTerm) ||
                    tags.some(tag => tag.includes(searchTerm));

                let matchesCategory = selectedCategory === 'semua' || 
                    tags.some(tag => tag.includes(selectedCategory));

                let matchesTechnology = selectedTechnology === 'semua' || 
                    tags.some(tag => tag.includes(selectedTechnology));

                let matchesYear = selectedYear === 'semua'; // Asumsi: data tahun belum tersedia

                if (matchesSearch && matchesCategory && matchesTechnology && matchesYear) {
                    card.style.display = 'block';
                    visibleCount++;
                    
                    // Animasi fade in
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                        card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                    }, 50);
                } else {
                    card.style.display = 'none';
                }
            });

            // Tampilkan pesan jika tidak ada hasil
            showNoResultsMessage(visibleCount === 0);
        }

        // Fungsi untuk menampilkan pesan tidak ada hasil
        function showNoResultsMessage(show) {
            let noResultsMsg = document.querySelector('.no-results-message');
            
            if (show && !noResultsMsg) {
                noResultsMsg = document.createElement('div');
                noResultsMsg.className = 'no-results-message';
                noResultsMsg.innerHTML = `
                    <div style="text-align: center; padding: 40px; color: var(--color-text-secondary);">
                        <h3>Tidak ada proyek yang sesuai dengan filter</h3>
                        <p>Coba ubah kriteria pencarian atau filter Anda</p>
                    </div>
                `;
                document.querySelector('.project-grid').appendChild(noResultsMsg);
            } else if (!show && noResultsMsg) {
                noResultsMsg.remove();
            }
        }
    }

    /**
     * 6. Load More Functionality
     */
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Simulasi loading
            this.textContent = 'Memuat...';
            this.disabled = true;
            
            setTimeout(() => {
                // Simulasi menambahkan proyek baru
                const newProjects = [
                    {
                        title: 'Aplikasi Monitoring Kesehatan Mental',
                        description: 'Platform mobile untuk melacak mood dan memberikan saran kesehatan mental berbasis AI.',
                        tags: ['Mobile App', 'AI']
                    },
                    {
                        title: 'Virtual Tour Museum Nasional',
                        description: 'Pengalaman virtual reality untuk menjelajahi museum secara online dengan panduan audio.',
                        tags: ['AR/VR', 'Unity']
                    },
                    {
                        title: 'Sistem Manajemen Inventori IoT',
                        description: 'Dashboard real-time untuk memantau stok barang menggunakan sensor IoT dan QR code.',
                        tags: ['UI/UX', 'IoT']
                    }
                ];
                
                // Tambahkan proyek baru ke grid
                newProjects.forEach(project => {
                    const projectCard = createProjectCard(project);
                    document.querySelector('.project-grid').appendChild(projectCard);
                });
                
                // Reset tombol
                this.textContent = 'Muat Lebih Banyak';
                this.disabled = false;
                
                // Scroll ke proyek baru
                const newCards = document.querySelectorAll('.project-card');
                if (newCards.length > 6) {
                    newCards[6].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
                
            }, 1000);
        });
    }

    /**
     * 7. Fungsi untuk membuat card proyek baru
     */
    function createProjectCard(projectData) {
        const card = document.createElement('a');
        card.className = 'project-card';
        card.href = 'menu-proyek-detail/detail-proyek.php';
        
        card.innerHTML = `
            <div class="project-card-thumbnail">
                <img src="../assets/images/project-placeholder.jpg" alt="${projectData.title}">
            </div>
            <div class="project-card-content">
                <h4>${projectData.title}</h4>
                <p>${projectData.description}</p>
                <div class="project-card-tags">
                    ${projectData.tags.map(tag => `<span class="tag-badge">${tag}</span>`).join('')}
                </div>
            </div>
        `;
        
        return card;
    }

    /**
     * 8. Enhanced Project Card Interactions
     */
    projectCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(-5px) scale(1)';
        });
        
        // Keyboard navigation
        card.addEventListener('focus', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });
        
        card.addEventListener('blur', function() {
            this.style.transform = 'translateY(-5px) scale(1)';
        });
    });

    /**
     * 9. Quick Filter Tags
     */
    const tagBadges = document.querySelectorAll('.tag-badge');
    
    tagBadges.forEach(tag => {
        tag.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const tagText = this.textContent.toLowerCase();
            const searchInput = document.getElementById('filter-search');
            
            if (searchInput) {
                searchInput.value = tagText;
                applyFilters();
            }
        });
        
        // Style untuk tag yang bisa diklik
        tag.style.cursor = 'pointer';
        tag.style.transition = 'background-color 0.2s ease';
        
        tag.addEventListener('mouseenter', function() {
            this.style.backgroundColor = 'var(--color-primary)';
            this.style.color = 'var(--color-white)';
        });
        
        tag.addEventListener('mouseleave', function() {
            this.style.backgroundColor = 'var(--color-bg-light)';
            this.style.color = 'var(--color-text-darker)';
        });
    });

});