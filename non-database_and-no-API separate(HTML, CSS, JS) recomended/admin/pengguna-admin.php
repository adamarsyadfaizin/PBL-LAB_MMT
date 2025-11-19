<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna - Admin Lab MMT</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link rel="stylesheet" href="assets/css/style-admin.css">
</head>
<body>

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
                <li>
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
                <li class="active">
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
                <span class="icon"><i class="bi bi-door-closed-fill"></i></span>
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
                    <input type="search" placeholder="Cari pengguna..." id="searchPengguna">
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

        <main class="admin-content">
            <div class="content-header">
                <div>
                    <h1>Manajemen Pengguna</h1>
                    <p>Kelola akun admin dan kontributor</p>
                </div>
                <button class="btn-primary" onclick="showAddModal()">
                    <span style="font-size: 18px; margin-right: 8px;">➕</span>
                    Tambah Pengguna
                </button>
            </div>

            <div class="content-box">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" id="selectAllTable">
                            </th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Tanggal Bergabung</th>
                            <th>Status</th>
                            <th style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tablePengguna">
                        <tr>
                            <td><input type="checkbox" class="item-checkbox"></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <img src="../assets/images/avatar-1.jpg" alt="Avatar" style="width: 32px; height: 32px; border-radius: 50%;">
                                    <strong>Admin Utama</strong>
                                </div>
                            </td>
                            <td>admin@polinema.ac.id</td>
                            <td><span class="badge badge-danger">Admin</span></td>
                            <td>01 Jan 2024</td>
                            <td><span class="badge badge-success">Aktif</span></td>
                            <td>
                                <div class="table-actions">
                                    <button class="btn-icon btn-edit" onclick="editPengguna(1)" title="Edit">✏️</button>
                                    <button class="btn-icon btn-delete" onclick="deletePengguna(1)" title="Hapus">🗑️</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" class="item-checkbox"></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <img src="../assets/images/avatar-2.jpg" alt="Avatar" style="width: 32px; height: 32px; border-radius: 50%;">
                                    <strong>Anggota Satu</strong>
                                </div>
                            </td>
                            <td>kontributor@polinema.ac.id</td>
                            <td><span class="badge badge-info">Anggota</span></td>
                            <td>15 Mar 2024</td>
                            <td><span class="badge badge-success">Aktif</span></td>
                            <td>
                                <div class="table-actions">
                                    <button class="btn-icon btn-edit" onclick="editPengguna(2)" title="Edit">✏️</button>
                                    <button class="btn-icon btn-delete" onclick="deletePengguna(2)" title="Hapus">🗑️</button>
                                </div>
                            </td>
                        </tr>
                        <!--<tr>
                            <td><input type="checkbox" class="item-checkbox"></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <img src="../assets/images/avatar-3.jpg" alt="Avatar" style="width: 32px; height: 32px; border-radius: 50%;">
                                    <strong>User Nonaktif</strong>
                                </div>
                            </td>
                            <td>user@polinema.ac.id</td>
                            <td><span class="badge badge-info">Kontributor</span></td>
                            <td>20 Mei 2024</td>
                            <td><span class="badge badge-secondary">Nonaktif</span></td>
                            <td>
                                <div class="table-actions">
                                    <button class="btn-icon btn-edit" onclick="editPengguna(3)" title="Edit">✏️</button>
                                    <button class="btn-icon btn-delete" onclick="deletePengguna(3)" title="Hapus">🗑️</button>
                                </div>
                            </td>
                        </tr>-->
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <div class="modal" id="penggunaModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Tambah Pengguna Baru</h2>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form id="penggunaForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="namaPengguna">Nama Lengkap *</label>
                        <input type="text" id="namaPengguna" name="nama" class="form-control" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="emailPengguna">Email *</label>
                            <input type="email" id="emailPengguna" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="usernamePengguna">Username *</label>
                            <input type="text" id="usernamePengguna" name="username" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="passwordPengguna">Password *</label>
                            <input type="password" id="passwordPengguna" name="password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="konfirmasiPassword">Konfirmasi Password *</label>
                            <input type="password" id="konfirmasiPassword" name="konfirmasi_password" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="rolePengguna">Role *</label>
                            <select id="rolePengguna" name="role" class="form-control" required>
                                <option value="kontributor">Kontributor</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="statusPengguna">Status *</label>
                            <select id="statusPengguna" name="status" class="form-control" required>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="avatarPengguna">Upload Avatar</label>
                        <input type="file" id="avatarPengguna" name="avatar" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn-primary">Simpan Pengguna</button>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/js/script-admin.js"></script>
    <script>
        function showAddModal() {
            document.getElementById('modalTitle').textContent = 'Tambah Pengguna Baru';
            document.getElementById('penggunaForm').reset();
            document.getElementById('penggunaModal').classList.add('active');
        }

        function editPengguna(id) {
            document.getElementById('modalTitle').textContent = 'Edit Pengguna';
            document.getElementById('penggunaModal').classList.add('active');
            console.log('Edit pengguna ID:', id);
        }

        function deletePengguna(id) {
            if (confirm('Apakah Anda yakin ingin menghapus pengguna ini?')) {
                console.log('Delete pengguna ID:', id);
                alert('Pengguna berhasil dihapus!');
            }
        }

        function closeModal() {
            document.getElementById('penggunaModal').classList.remove('active');
        }

        document.getElementById('penggunaForm').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Pengguna berhasil disimpan!');
            closeModal();
        });

        document.getElementById('penggunaModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
    </script>
</body>
</html>