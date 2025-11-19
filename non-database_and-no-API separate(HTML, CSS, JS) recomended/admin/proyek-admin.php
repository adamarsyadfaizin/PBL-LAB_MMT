<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Proyek - Admin Lab MMT</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link rel="stylesheet" href="assets/css/style-admin.css">
</head>
<body>

    <!-- Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <img src="../assets/images/LAB MUTIMEDIA V2_TSP.png" alt="Logo" style="height: 40px;">
            <h3>Admin Panel</h3>
        </div>
        
        <nav class="sidebar-nav">
            <ul>
                <li>
                    <a href="dashboard.php">
                        <span class="icon"><i class="bi bi-bar-chart-fill"></i></span>
                        <span class="text">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="berita-admin.php">
                        <span class="icon"><i class="bi bi-calendar-event-fill"></i></span>
                        <span class="text">Berita & Kegiatan</span>
                    </a>
                </li>
                <li class="active">
                    <a href="proyek-admin.php">
                        <span class="icon"><i class="bi bi-briefcase-fill"></i></span>
                        <span class="text">Proyek</span>
                    </a>
                </li>
                <li>
                    <a href="galeri-admin.php">
                        <span class="icon"><i class="bi bi-image-fill"></i></span>
                        <span class="text">Galeri</span>
                    </a>
                </li>
                <li>
                    <a href="profil-admin.php">
                        <span class="icon"><i class="bi bi-building-fill-gear"></i></span>
                        <span class="text">Profil Lab</span>
                    </a>
                </li>
                <li>
                    <a href="pengguna-admin.php">
                        <span class="icon"><i class="bi bi-people-fill"></i></span>
                        <span class="text">Pengguna</span>
                    </a>
                </li>
                <li>
                    <a href="pengaturan-admin.php">
                        <span class="icon"><i class="bi bi-gear-fill"></i></span>
                        <span class="text">Pengaturan</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <div class="sidebar-footer">
            <a href="../menu/login.php" class="logout-btn">
                <i class="bi bi-door-closed-fill"></i>
                <span class="text">Logout</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="admin-main">
        
        <!-- Top Bar -->
        <header class="admin-topbar">
            <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
                <span></span>
                <span></span>
                <span></span>
            </button>
            
            <div class="topbar-right">
                <div class="search-box">
                    <input type="search" placeholder="Cari proyek..." id="searchProyek">
                    <button type="button"><i class="bi bi-search"></i></button>
                </div>
                
                <div class="notifications">
                    <button class="notif-btn" aria-label="Notifikasi">
                        <i class="bi bi-bell-fill"></i>
                        <span class="badge">3</span>
                    </button>
                </div>
                
                <div class="user-menu">
                    <button class="user-btn" aria-label="Menu Pengguna">
                        <img src="../assets/images/avatar-1.jpg" alt="Avatar Admin">
                        <span>Admin</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <main class="admin-content">
            <div class="content-header">
                <div>
                    <h1>Manajemen Proyek</h1>
                    <p>Kelola semua proyek mahasiswa dan penelitian</p>
                </div>
                <button class="btn-primary" onclick="showAddModal()">
                    <span style="font-size: 18px; margin-right: 8px;">➕</span>
                    Tambah Proyek
                </button>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 32px;">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #e3f2fd;">💼</div>
                    <div class="stat-info">
                        <h3>Total Proyek</h3>
                        <p class="stat-number">32</p>
                        <span class="stat-change positive">+3 bulan ini</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #f3e5f5;">📱</div>
                    <div class="stat-info">
                        <h3>Proyek Mobile App</h3>
                        <p class="stat-number">12</p>
                        <span class="stat-change">37.5% dari total</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #fff3e0;">🎮</div>
                    <div class="stat-info">
                        <h3>Proyek AR/VR & Game</h3>
                        <p class="stat-number">8</p>
                        <span class="stat-change">25% dari total</span>
                    </div>
                </div>
            </div>

            <!-- Filter Bar -->
            <div class="content-box" style="margin-bottom: 24px;">
                <div class="filter-toolbar">
                    <div class="filter-group">
                        <label>Kategori:</label>
                        <select id="filterKategori" class="filter-select">
                            <option value="">Semua Kategori</option>
                            <option value="ui-ux">UI/UX</option>
                            <option value="mobile">Mobile App</option>
                            <option value="game">Game</option>
                            <option value="ar-vr">AR/VR</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Tahun:</label>
                        <select id="filterTahun" class="filter-select">
                            <option value="">Semua Tahun</option>
                            <option value="2025">2025</option>
                            <option value="2024">2024</option>
                            <option value="2023">2023</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Teknologi:</label>
                        <select id="filterTeknologi" class="filter-select">
                            <option value="">Semua Teknologi</option>
                            <option value="react">React Native</option>
                            <option value="unity">Unity</option>
                            <option value="figma">Figma</option>
                            <option value="flutter">Flutter</option>
                        </select>
                    </div>
                    <button class="btn-secondary" onclick="resetFilters()">Reset Filter</button>
                </div>
            </div>

            <!-- Grid View Proyek -->
            <div class="projects-grid">
                <div class="project-card-admin">
                    <div class="project-thumbnail">
                        <img src="../assets/images/project-1.jpg" alt="Proyek">
                        <div class="project-actions">
                            <button class="btn-icon btn-edit" onclick="editProyek(1)" title="Edit">✏️</button>
                            <button class="btn-icon btn-delete" onclick="deleteProyek(1)" title="Hapus">🗑️</button>
                        </div>
                    </div>
                    <div class="project-info">
                        <div class="project-category">
                            <span class="badge badge-info">Mobile App</span>
                            <span class="badge badge-secondary">2025</span>
                        </div>
                        <h3>Sistem Deteksi Kemacetan Real-time</h3>
                        <p class="project-tech">React Native • Firebase • Google Maps API</p>
                        <div class="project-stats">
                            <span>👁️ 1,234 views</span>
                            <span>⭐ 4.5/5</span>
                        </div>
                        <div class="project-team">
                            <div class="team-avatars">
                                <img src="../assets/images/avatar-1.jpg" alt="Tim 1">
                                <img src="../assets/images/avatar-2.jpg" alt="Tim 2">
                                <img src="../assets/images/avatar-3.jpg" alt="Tim 3">
                            </div>
                            <span class="team-count">3 anggota</span>
                        </div>
                    </div>
                </div>

                <div class="project-card-admin">
                    <div class="project-thumbnail">
                        <img src="../assets/images/project-2.jpg" alt="Proyek">
                        <div class="project-actions">
                            <button class="btn-icon btn-edit" onclick="editProyek(2)" title="Edit">✏️</button>
                            <button class="btn-icon btn-delete" onclick="deleteProyek(2)" title="Hapus">🗑️</button>
                        </div>
                    </div>
                    <div class="project-info">
                        <div class="project-category">
                            <span class="badge badge-warning">UI/UX</span>
                            <span class="badge badge-secondary">2024</span>
                        </div>
                        <h3>Dashboard Visualisasi Data COVID-19</h3>
                        <p class="project-tech">Figma • React • Chart.js</p>
                        <div class="project-stats">
                            <span>👁️ 987 views</span>
                            <span>⭐ 4.8/5</span>
                        </div>
                        <div class="project-team">
                            <div class="team-avatars">
                                <img src="../assets/images/avatar-2.jpg" alt="Tim 1">
                                <img src="../assets/images/avatar-3.jpg" alt="Tim 2">
                            </div>
                            <span class="team-count">2 anggota</span>
                        </div>
                    </div>
                </div>

                <div class="project-card-admin">
                    <div class="project-thumbnail">
                        <img src="../assets/images/project-4-game.jpg" alt="Proyek">
                        <div class="project-actions">
                            <button class="btn-icon btn-edit" onclick="editProyek(3)" title="Edit">✏️</button>
                            <button class="btn-icon btn-delete" onclick="deleteProyek(3)" title="Hapus">🗑️</button>
                        </div>
                    </div>
                    <div class="project-info">
                        <div class="project-category">
                            <span class="badge" style="background: rgba(156, 39, 176, 0.1); color: #9c27b0;">AR/VR & Game</span>
                            <span class="badge badge-secondary">2024</span>
                        </div>
                        <h3>Game Edukasi Sejarah (AR)</h3>
                        <p class="project-tech">Unity • ARCore • C#</p>
                        <div class="project-stats">
                            <span>👁️ 856 views</span>
                            <span>⭐ 4.6/5</span>
                        </div>
                        <div class="project-team">
                            <div class="team-avatars">
                                <img src="../assets/images/avatar-1.jpg" alt="Tim 1">
                                <img src="../assets/images/avatar-2.jpg" alt="Tim 2">
                                <img src="../assets/images/avatar-3.jpg" alt="Tim 3">
                                <span class="more-count">+2</span>
                            </div>
                            <span class="team-count">5 anggota</span>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- Modal Add/Edit Proyek -->
    <div class="modal" id="proyekModal">
        <div class="modal-content modal-large">
            <div class="modal-header">
                <h2 id="modalTitle">Tambah Proyek Baru</h2>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form id="proyekForm" data-autosave>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group" style="flex: 2;">
                            <label for="judulProyek">Judul Proyek *</label>
                            <input type="text" id="judulProyek" name="judul" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="tahunProyek">Tahun *</label>
                            <select id="tahunProyek" name="tahun" class="form-control" required>
                                <option value="">Pilih Tahun</option>
                                <option value="2025">2025</option>
                                <option value="2024">2024</option>
                                <option value="2023">2023</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="kategoriProyek">Kategori *</label>
                            <select id="kategoriProyek" name="kategori" class="form-control" required>
                                <option value="">Pilih Kategori</option>
                                <option value="ui-ux">UI/UX</option>
                                <option value="mobile">Mobile App</option>
                                <option value="game">Game</option>
                                <option value="ar-vr">AR/VR</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="statusProyek">Status *</label>
                            <select id="statusProyek" name="status" class="form-control" required>
                                <option value="ongoing">Sedang Berjalan</option>
                                <option value="completed">Selesai</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="deskripsiProyek">Deskripsi Singkat *</label>
                        <textarea id="deskripsiProyek" name="deskripsi" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="deskripsiLengkap">Deskripsi Lengkap</label>
                        <textarea id="deskripsiLengkap" name="deskripsi_lengkap" class="form-control" rows="6"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="teknologiProyek">Teknologi yang Digunakan *</label>
                        <input type="text" id="teknologiProyek" name="teknologi" class="form-control" required placeholder="Contoh: React Native, Firebase, Unity">
                        <small style="color: #666;">Pisahkan dengan koma</small>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="linkDemo">Link Demo</label>
                            <input type="url" id="linkDemo" name="link_demo" class="form-control" placeholder="https://">
                        </div>
                        <div class="form-group">
                            <label for="linkGithub">Link GitHub</label>
                            <input type="url" id="linkGithub" name="link_github" class="form-control" placeholder="https://github.com/">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="thumbnailProyek">Upload Thumbnail *</label>
                        <input type="file" id="thumbnailProyek" name="thumbnail" class="form-control" accept="image/*" required>
                    </div>

                    <div class="form-group">
                        <label for="anggotaTim">Anggota Tim (Email/Username) *</label>
                        <input type="text" id="anggotaTim" name="anggota" class="form-control" required placeholder="user1@email.com, user2@email.com">
                        <small style="color: #666;">Pisahkan dengan koma</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn-primary">Simpan Proyek</button>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/js/script-admin.js"></script>
    <script>
        function showAddModal() {
            document.getElementById('modalTitle').textContent = 'Tambah Proyek Baru';
            document.getElementById('proyekForm').reset();
            document.getElementById('proyekModal').classList.add('active');
        }

        function editProyek(id) {
            document.getElementById('modalTitle').textContent = 'Edit Proyek';
            document.getElementById('proyekModal').classList.add('active');
            console.log('Edit proyek ID:', id);
        }

        function deleteProyek(id) {
            if (confirm('Apakah Anda yakin ingin menghapus proyek ini?')) {
                console.log('Delete proyek ID:', id);
                alert('Proyek berhasil dihapus!');
            }
        }

        function closeModal() {
            document.getElementById('proyekModal').classList.remove('active');
        }

        function resetFilters() {
            document.getElementById('filterKategori').value = '';
            document.getElementById('filterTahun').value = '';
            document.getElementById('filterTeknologi').value = '';
        }

        document.getElementById('proyekForm').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Proyek berhasil disimpan!');
            closeModal();
        });

        document.getElementById('proyekModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
    </script>
</body>
</html>