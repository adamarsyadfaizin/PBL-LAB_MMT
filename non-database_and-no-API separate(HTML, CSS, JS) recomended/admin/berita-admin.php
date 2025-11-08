<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Berita - Admin Lab MMT</title>
    
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
                <li>
                    <a href="dashboard.php">
                        <span class="icon">📊</span>
                        <span class="text">Dashboard</span>
                    </a>
                </li>
                <li class="active">
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
                    <input type="search" placeholder="Cari berita..." id="searchBerita">
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
                <div>
                    <h1>Manajemen Berita & Kegiatan</h1>
                    <p>Kelola semua berita dan kegiatan laboratorium</p>
                </div>
                <button class="btn-primary" onclick="showAddModal()">
                    <span style="font-size: 18px; margin-right: 8px;">➕</span>
                    Tambah Berita
                </button>
            </div>

            <!-- Filter Bar -->
            <div class="content-box" style="margin-bottom: 24px;">
                <div class="filter-toolbar">
                    <div class="filter-group">
                        <label>Kategori:</label>
                        <select id="filterKategori" class="filter-select">
                            <option value="">Semua Kategori</option>
                            <option value="berita">Berita</option>
                            <option value="kegiatan">Kegiatan</option>
                            <option value="pengumuman">Pengumuman</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Status:</label>
                        <select id="filterStatus" class="filter-select">
                            <option value="">Semua Status</option>
                            <option value="publish">Publish</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Bulan:</label>
                        <select id="filterBulan" class="filter-select">
                            <option value="">Semua Bulan</option>
                            <option value="11">November 2025</option>
                            <option value="10">Oktober 2025</option>
                            <option value="09">September 2025</option>
                        </select>
                    </div>
                    <button class="btn-secondary" onclick="resetFilters()">Reset Filter</button>
                </div>
            </div>

            <!-- Table Berita -->
            <div class="content-box">
                <div class="table-header">
                    <div class="bulk-actions">
                        <input type="checkbox" id="selectAll">
                        <label for="selectAll">Pilih Semua</label>
                        <button class="btn-secondary" id="bulkActionBtn" disabled>Aksi</button>
                    </div>
                    <div class="table-info">
                        Total: <strong id="totalBerita">48</strong> berita
                    </div>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" id="selectAllTable">
                            </th>
                            <th>Thumbnail</th>
                            <th data-sort="title">Judul</th>
                            <th data-sort="category">Kategori</th>
                            <th data-sort="author">Penulis</th>
                            <th data-sort="date">Tanggal</th>
                            <th>Status</th>
                            <th>Views</th>
                            <th style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBerita">
                        <tr>
                            <td><input type="checkbox" class="item-checkbox"></td>
                            <td>
                                <img src="../assets/images/berita-1.jpg" alt="Thumbnail" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                            </td>
                            <td><strong>Pelatihan Keamanan Siber Internal 2025</strong></td>
                            <td><span class="badge badge-info">Berita</span></td>
                            <td>Admin</td>
                            <td>18 Okt 2025</td>
                            <td><span class="badge badge-success">Publish</span></td>
                            <td>1,234</td>
                            <td>
                                <div class="table-actions">
                                    <button class="btn-icon btn-edit" onclick="editBerita(1)" title="Edit">✏️</button>
                                    <button class="btn-icon btn-delete" onclick="deleteBerita(1)" title="Hapus">🗑️</button>
                                    <button class="btn-icon" onclick="viewBerita(1)" title="Lihat" style="background: #6c757d; color: white;">👁️</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" class="item-checkbox"></td>
                            <td>
                                <img src="../assets/images/berita-2.jpg" alt="Thumbnail" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                            </td>
                            <td><strong>Kolaborasi Riset Big Data dengan Industri</strong></td>
                            <td><span class="badge badge-info">Berita</span></td>
                            <td>Admin</td>
                            <td>15 Okt 2025</td>
                            <td><span class="badge badge-success">Publish</span></td>
                            <td>987</td>
                            <td>
                                <div class="table-actions">
                                    <button class="btn-icon btn-edit" onclick="editBerita(2)" title="Edit">✏️</button>
                                    <button class="btn-icon btn-delete" onclick="deleteBerita(2)" title="Hapus">🗑️</button>
                                    <button class="btn-icon" onclick="viewBerita(2)" title="Lihat" style="background: #6c757d; color: white;">👁️</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" class="item-checkbox"></td>
                            <td>
                                <img src="../assets/images/berita-3.jpg" alt="Thumbnail" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                            </td>
                            <td><strong>Workshop Pengembangan Aplikasi Mobile</strong></td>
                            <td><span class="badge badge-warning">Kegiatan</span></td>
                            <td>Kontributor</td>
                            <td>11 Okt 2025</td>
                            <td><span class="badge badge-success">Publish</span></td>
                            <td>743</td>
                            <td>
                                <div class="table-actions">
                                    <button class="btn-icon btn-edit" onclick="editBerita(3)" title="Edit">✏️</button>
                                    <button class="btn-icon btn-delete" onclick="deleteBerita(3)" title="Hapus">🗑️</button>
                                    <button class="btn-icon" onclick="viewBerita(3)" title="Lihat" style="background: #6c757d; color: white;">👁️</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" class="item-checkbox"></td>
                            <td>
                                <img src="../assets/images/event-highlight.jpg" alt="Thumbnail" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                            </td>
                            <td><strong>Seminar Nasional Implementasi AI</strong></td>
                            <td><span class="badge badge-warning">Kegiatan</span></td>
                            <td>Admin</td>
                            <td>05 Nov 2025</td>
                            <td><span class="badge badge-secondary">Draft</span></td>
                            <td>0</td>
                            <td>
                                <div class="table-actions">
                                    <button class="btn-icon btn-edit" onclick="editBerita(4)" title="Edit">✏️</button>
                                    <button class="btn-icon btn-delete" onclick="deleteBerita(4)" title="Hapus">🗑️</button>
                                    <button class="btn-icon" onclick="viewBerita(4)" title="Lihat" style="background: #6c757d; color: white;">👁️</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="table-pagination">
                    <div class="pagination-info">
                        Menampilkan 1-4 dari 48 berita
                    </div>
                    <div class="pagination-controls">
                        <button class="btn-secondary" disabled>« Prev</button>
                        <button class="btn-primary">1</button>
                        <button class="btn-secondary">2</button>
                        <button class="btn-secondary">3</button>
                        <button class="btn-secondary">Next »</button>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- Modal Add/Edit Berita -->
    <div class="modal" id="beritaModal">
        <div class="modal-content modal-large">
            <div class="modal-header">
                <h2 id="modalTitle">Tambah Berita Baru</h2>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form id="beritaForm" data-autosave>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group" style="flex: 2;">
                            <label for="judulBerita">Judul Berita *</label>
                            <input type="text" id="judulBerita" name="judul" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="kategoriBerita">Kategori *</label>
                            <select id="kategoriBerita" name="kategori" class="form-control" required>
                                <option value="">Pilih Kategori</option>
                                <option value="berita">Berita</option>
                                <option value="kegiatan">Kegiatan</option>
                                <option value="pengumuman">Pengumuman</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="tanggalBerita">Tanggal Publikasi *</label>
                            <input type="date" id="tanggalBerita" name="tanggal" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="penulisBerita">Penulis *</label>
                            <input type="text" id="penulisBerita" name="penulis" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="statusBerita">Status *</label>
                            <select id="statusBerita" name="status" class="form-control" required>
                                <option value="draft">Draft</option>
                                <option value="publish">Publish</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="excerptBerita">Ringkasan/Excerpt</label>
                        <textarea id="excerptBerita" name="excerpt" class="form-control" rows="3" placeholder="Ringkasan singkat untuk preview..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="kontenBerita">Konten Berita *</label>
                        <textarea id="kontenBerita" name="konten" class="form-control" rows="10" required placeholder="Tulis konten berita di sini..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="thumbnailBerita">Upload Thumbnail</label>
                        <input type="file" id="thumbnailBerita" name="thumbnail" class="form-control" accept="image/*">
                        <small style="color: #666;">Ukuran maksimal: 2MB. Format: JPG, PNG</small>
                    </div>

                    <div class="form-group">
                        <label for="tagsBerita">Tags (pisahkan dengan koma)</label>
                        <input type="text" id="tagsBerita" name="tags" class="form-control" placeholder="contoh: teknologi, inovasi, penelitian">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn-primary">Simpan Berita</button>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/js/script-admin.js"></script>
    <script>
        // Fungsi khusus untuk halaman berita-admin
        function showAddModal() {
            document.getElementById('modalTitle').textContent = 'Tambah Berita Baru';
            document.getElementById('beritaForm').reset();
            document.getElementById('beritaModal').classList.add('active');
        }

        function editBerita(id) {
            document.getElementById('modalTitle').textContent = 'Edit Berita';
            document.getElementById('beritaModal').classList.add('active');
            // Dalam implementasi nyata, load data dari server/storage
            console.log('Edit berita ID:', id);
        }

        function deleteBerita(id) {
            if (confirm('Apakah Anda yakin ingin menghapus berita ini?')) {
                // Dalam implementasi nyata, hapus dari server/storage
                console.log('Delete berita ID:', id);
                alert('Berita berhasil dihapus!');
            }
        }

        function viewBerita(id) {
            window.open('../menu/menu-detail-berita/detail-berita.php?id=' + id, '_blank');
        }

        function closeModal() {
            document.getElementById('beritaModal').classList.remove('active');
        }

        function resetFilters() {
            document.getElementById('filterKategori').value = '';
            document.getElementById('filterStatus').value = '';
            document.getElementById('filterBulan').value = '';
        }

        // Handle form submit
        document.getElementById('beritaForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Dalam implementasi nyata, kirim ke server
            alert('Berita berhasil disimpan!');
            closeModal();
        });

        // Close modal when clicking outside
        document.getElementById('beritaModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>
</html>