<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Lab MMT POLINEMA</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/style-admin.css">
</head>
<body>

    <!-- Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <img src="../assets/images/logo-placeholder.png" alt="Logo" style="height: 40px;">
            <h3>Admin Panel</h3>
        </div>
        
        <nav class="sidebar-nav">
            <ul>
                <li class="active">
                    <a href="dashboard.php">
                        <span class="icon">📊</span>
                        <span class="text">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="berita-admin.php">
                        <span class="icon">📰</span>
                        <span class="text">Berita & Kegiatan</span>
                    </a>
                </li>
                <li>
                    <a href="proyek-admin.php">
                        <span class="icon">💼</span>
                        <span class="text">Proyek</span>
                    </a>
                </li>
                <li>
                    <a href="galeri-admin.php">
                        <span class="icon">🖼️</span>
                        <span class="text">Galeri</span>
                    </a>
                </li>
                <li>
                    <a href="profil-admin.php">
                        <span class="icon">👥</span>
                        <span class="text">Profil Lab</span>
                    </a>
                </li>
                <li>
                    <a href="pengguna-admin.php">
                        <span class="icon">👤</span>
                        <span class="text">Pengguna</span>
                    </a>
                </li>
                <li>
                    <a href="pengaturan-admin.php">
                        <span class="icon">⚙️</span>
                        <span class="text">Pengaturan</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <div class="sidebar-footer">
            <a href="../menu/login.php" class="logout-btn">
                <span class="icon">🚪</span>
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
                    <input type="search" placeholder="Cari...">
                    <button type="button">🔍</button>
                </div>
                
                <div class="notifications">
                    <button class="notif-btn" aria-label="Notifikasi">
                        🔔
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
                <h1>Dashboard</h1>
                <p>Selamat datang di panel admin Laboratorium Mobile and Multimedia Tech</p>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #e3f2fd;">📰</div>
                    <div class="stat-info">
                        <h3>Total Berita</h3>
                        <!-- Hanya tampilan, nanti tambahin di BE -->
                        <p class="stat-number">48</p>
                        <span class="stat-change positive">+5 bulan ini</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: #f3e5f5;">💼</div>
                    <div class="stat-info">
                        <h3>Total Proyek</h3>
                        <!-- Hanya tampilan, nanti tambahin di BE -->
                        <p class="stat-number">32</p>
                        <span class="stat-change positive">+3 bulan ini</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: #fff3e0;">🖼️</div>
                    <div class="stat-info">
                        <h3>Total Galeri</h3>
                        <!-- Hanya tampilan, nanti tambahin di BE -->
                        <p class="stat-number">156</p>
                        <span class="stat-change positive">+12 bulan ini</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: #e8f5e9;">👁️</div>
                    <div class="stat-info">
                        <h3>Pengunjung</h3>
                        <!-- Hanya tampilan, nanti tambahin di BE -->
                        <p class="stat-number">2,847</p>
                        <span class="stat-change negative">-3% dari bulan lalu</span>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <!-- Belum berfungsi -->
            <div class="content-grid">
                <div class="content-box">
                    <div class="box-header">
                        <h2>Aktivitas Terbaru</h2>
                        <a href="#" class="view-all">Lihat Semua</a> <!-- Belum berfungsi -->
                    </div>
                    <div class="activity-list">
                        <div class="activity-item">
                            <div class="activity-icon">📝</div>
                            <div class="activity-info">
                                <p><strong>Admin</strong> menambahkan berita baru</p>
                                <span class="activity-time">2 jam yang lalu</span>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon">💼</div>
                            <div class="activity-info">
                                <p><strong>Kontributor</strong> mengupdate proyek</p>
                                <span class="activity-time">5 jam yang lalu</span>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon">🖼️</div>
                            <div class="activity-info">
                                <p><strong>Admin</strong> menambahkan 8 foto galeri</p>
                                <span class="activity-time">1 hari yang lalu</span>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon">👤</div>
                            <div class="activity-info">
                                <p><strong>Admin</strong> menambahkan pengguna baru</p>
                                <span class="activity-time">2 hari yang lalu</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Belum berfungsi -->
                <div class="content-box">
                    <div class="box-header">
                        <h2>Konten Populer</h2>
                        <select class="filter-select">
                            <option>7 Hari Terakhir</option>
                            <option>30 Hari Terakhir</option>
                            <option>Semua Waktu</option>
                        </select>
                    </div>
                    <div class="popular-list">
                        <div class="popular-item">
                            <span class="rank">1</span>
                            <div class="popular-info">
                                <p>Pelatihan Keamanan Siber Internal 2025</p>
                                <span class="views">1,234 views</span>
                            </div>
                        </div>
                        <div class="popular-item">
                            <span class="rank">2</span>
                            <div class="popular-info">
                                <p>Sistem Deteksi Kemacetan Real-time</p>
                                <span class="views">987 views</span>
                            </div>
                        </div>
                        <div class="popular-item">
                            <span class="rank">3</span>
                            <div class="popular-info">
                                <p>Dies Natalis 2025</p>
                                <span class="views">856 views</span>
                            </div>
                        </div>
                        <div class="popular-item">
                            <span class="rank">4</span>
                            <div class="popular-info">
                                <p>Workshop Pengembangan Aplikasi Mobile</p>
                                <span class="views">743 views</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="content-box">
                <div class="box-header">
                    <h2>Aksi Cepat</h2>
                </div>
                <div class="quick-actions">
                    <a href="berita-admin.php?action=add" class="action-btn">
                        <span class="icon">➕</span>
                        <span>Tambah Berita</span>
                    </a>
                    <a href="proyek-admin.php?action=add" class="action-btn">
                        <span class="icon">➕</span>
                        <span>Tambah Proyek</span>
                    </a>
                    <a href="galeri-admin.php?action=add" class="action-btn">
                        <span class="icon">➕</span>
                        <span>Upload Galeri</span>
                    </a>
                    <a href="pengguna-admin.php?action=add" class="action-btn">
                        <span class="icon">➕</span>
                        <span>Tambah Pengguna</span>
                    </a>
                </div>
            </div>

        </main>
    </div>

    <script src="assets/js/script-admin.js"></script>
</body>
</html>