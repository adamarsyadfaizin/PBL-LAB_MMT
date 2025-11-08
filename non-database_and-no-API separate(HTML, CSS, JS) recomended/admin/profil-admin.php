<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Profil Lab - Admin Lab MMT</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/style-admin.css">
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
                <li>
                    <a href="galeri-admin.php">
                        <span class="icon">🖼️</span>
                        <span class="text">Galeri</span>
                    </a>
                </li>
                <li class="active">
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

        <main class="admin-content">
            <div class="content-header">
                <div>
                    <h1>Manajemen Profil Lab</h1>
                    <p>Kelola informasi Visi, Misi, Sejarah, dan Manajemen Lab</p>
                </div>
            </div>

            <div class="content-box">
                <form id="profilForm">
                    <div class="form-group">
                        <label for="sejarahLab">Sejarah Laboratorium</label>
                        <textarea id="sejarahLab" name="sejarah" class="form-control" rows="8" placeholder="Tuliskan sejarah singkat lab..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="visiLab">Visi</label>
                        <textarea id="visiLab" name="visi" class="form-control" rows="3" placeholder="Tuliskan visi lab..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="misiLab">Misi</label>
                        <textarea id="misiLab" name="misi" class="form-control" rows="5" placeholder="Tuliskan misi lab (pisahkan per poin jika perlu)..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="strukturOrganisasi">Upload Foto Struktur Organisasi</label>
                        <input type="file" id="strukturOrganisasi" name="struktur_img" class="form-control" accept="image/*">
                        <small>Kosongkan jika tidak ingin mengubah foto saat ini.</small>
                    </div>
                    
                    <hr style="margin: 24px 0; border: 0; border-top: 1px solid var(--color-border-light);">

                    <h3>Manajemen/Dosen</h3>
                    <div class="form-group">
                        <label for="dosenKonten">Konten Profil Dosen/Manajemen</label>
                        <textarea id="dosenKonten" name="dosen_konten" class="form-control" rows="10" placeholder="Tambahkan konten HTML untuk menampilkan profil dosen/manajemen..."></textarea>
                        <small>Anda bisa menambahkan list dosen atau anggota manajemen di sini.</small>
                    </div>

                    <div style="text-align: right; margin-top: 24px;">
                        <button type="submit" class="btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script src="assets/js/script-admin.js"></script>
    <script>
        document.getElementById('profilForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Di sini logika untuk menyimpan data form
            alert('Profil Lab berhasil diperbarui!');
        });
    </script>
</body>
</html>