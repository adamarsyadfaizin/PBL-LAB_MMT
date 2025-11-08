<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Galeri - Admin Lab MMT</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/style-admin.css">
    <style>
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 16px;
        }
        .gallery-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            aspect-ratio: 1 / 1;
        }
        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .gallery-item-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            color: white;
            opacity: 0;
            transition: opacity 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 12px;
        }
        .gallery-item:hover .gallery-item-overlay {
            opacity: 1;
        }
        .gallery-item-overlay p {
            font-size: 12px;
            margin-bottom: 8px;
        }
        .gallery-actions {
            display: flex;
            gap: 8px;
        }
    </style>
</head>
<body>

    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <img src="../assets/images/logo-placeholder.png" alt="Logo" style="height: 40px;">
            <h3>Admin Panel</h3>
        </div>
        
        <nav class="sidebar-nav">
            <ul>
                <li>
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
                <li class="active">
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

    <div class="admin-main">
        
        <header class="admin-topbar">
            <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
                <span></span>
                <span></span>
                <span></span>
            </button>
            
            <div class="topbar-right">
                <div class="search-box">
                    <input type="search" placeholder="Cari foto..." id="searchGaleri">
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

        <main class="admin-content">
            <div class="content-header">
                <div>
                    <h1>Manajemen Galeri</h1>
                    <p>Kelola semua foto dan video laboratorium</p>
                </div>
                <button class="btn-primary" onclick="showUploadModal()">
                    <span style="font-size: 18px; margin-right: 8px;">➕</span>
                    Upload Media
                </button>
            </div>

            <div class="content-box" style="margin-bottom: 24px;">
                <div class="filter-toolbar">
                    <div class="filter-group">
                        <label>Album:</label>
                        <select id="filterAlbum" class="filter-select">
                            <option value="">Semua Album</option>
                            <option value="workshop">Workshop</option>
                            <option value="penelitian">Penelitian</option>
                            <option value="lomba">Lomba</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Tipe Media:</label>
                        <select id="filterTipe" class="filter-select">
                            <option value="">Semua Tipe</option>
                            <option value="foto">Foto</option>
                            <option value="video">Video</option>
                        </select>
                    </div>
                    <button class="btn-secondary" onclick="resetFilters()">Reset Filter</button>
                </div>
            </div>

            <div class="content-box">
                <div class="gallery-grid">
                    <div class="gallery-item">
                        <img src="../assets/images/berita-1.jpg" alt="Galeri 1">
                        <div class="gallery-item-overlay">
                            <p>Workshop Keamanan Siber</p>
                            <div class="gallery-actions">
                                <button class="btn-icon btn-edit" onclick="editMedia(1)" title="Edit">✏️</button>
                                <button class="btn-icon btn-delete" onclick="deleteMedia(1)" title="Hapus">🗑️</button>
                            </div>
                        </div>
                    </div>
                    <div class="gallery-item">
                        <img src="../assets/images/berita-2.jpg" alt="Galeri 2">
                        <div class="gallery-item-overlay">
                            <p>Riset Big Data</p>
                            <div class="gallery-actions">
                                <button class="btn-icon btn-edit" onclick="editMedia(2)" title="Edit">✏️</button>
                                <button class="btn-icon btn-delete" onclick="deleteMedia(2)" title="Hapus">🗑️</button>
                            </div>
                        </div>
                    </div>
                    <div class="gallery-item">
                        <img src="../assets/images/berita-3.jpg" alt="Galeri 3">
                        <div class="gallery-item-overlay">
                            <p>Workshop Mobile Dev</p>
                            <div class="gallery-actions">
                                <button class="btn-icon btn-edit" onclick="editMedia(3)" title="Edit">✏️</button>
                                <button class="btn-icon btn-delete" onclick="deleteMedia(3)" title="Hapus">🗑️</button>
                            </div>
                        </div>
                    </div>
                    <div class="gallery-item">
                        <img src="../assets/images/project-1.jpg" alt="Galeri 4">
                        <div class="gallery-item-overlay">
                            <p>Demo Proyek</p>
                            <div class="gallery-actions">
                                <button class="btn-icon btn-edit" onclick="editMedia(4)" title="Edit">✏️</button>
                                <button class="btn-icon btn-delete" onclick="deleteMedia(4)" title="Hapus">🗑️</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div class="modal" id="galeriModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Upload Media Baru</h2>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form id="galeriForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="fileUpload">Upload File (Bisa lebih dari satu) *</label>
                        <input type="file" id="fileUpload" name="files" class="form-control" accept="image/*,video/*" multiple required>
                        <small>Max: 5MB per file. Format: JPG, PNG, MP4</small>
                    </div>

                    <div class="form-group">
                        <label for="albumMedia">Pilih Album *</label>
                        <select id="albumMedia" name="album" class="form-control" required>
                            <option value="">Pilih Album</option>
                            <option value="workshop">Workshop</option>
                            <option value="penelitian">Penelitian</option>
                            <option value="lomba">Lomba</option>
                            <option value="kegiatan-lain">Kegiatan Lain</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="captionMedia">Keterangan / Caption</label>
                        <textarea id="captionMedia" name="caption" class="form-control" rows="3" placeholder="Deskripsi singkat media..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn-primary">Simpan Media</button>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/js/script-admin.js"></script>
    <script>
        function showUploadModal() {
            document.getElementById('modalTitle').textContent = 'Upload Media Baru';
            document.getElementById('galeriForm').reset();
            document.getElementById('galeriModal').classList.add('active');
        }

        function editMedia(id) {
            document.getElementById('modalTitle').textContent = 'Edit Media';
            // Di sini nanti ada logika untuk load data media
            document.getElementById('galeriModal').classList.add('active');
            console.log('Edit media ID:', id);
        }

        function deleteMedia(id) {
            if (confirm('Apakah Anda yakin ingin menghapus media ini?')) {
                console.log('Delete media ID:', id);
                alert('Media berhasil dihapus!');
            }
        }

        function closeModal() {
            document.getElementById('galeriModal').classList.remove('active');
        }

        function resetFilters() {
            document.getElementById('filterAlbum').value = '';
            document.getElementById('filterTipe').value = '';
        }

        document.getElementById('galeriForm').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Media berhasil disimpan!');
            closeModal();
        });

        document.getElementById('galeriModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
    </script>
</body>
</html>