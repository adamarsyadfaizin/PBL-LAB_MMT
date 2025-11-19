<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan - Admin Lab MMT</title>
    
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
                <li>
                    <a href="pengguna-admin.php">
                        <span class="icon"><i class="bi bi-people-fill"></i></span>
                        <span class="text">Pengguna</span>
                    </a>
                </li>
                <li class="active">
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
                    <h1>Pengaturan Situs</h1>
                    <p>Kelola pengaturan umum, info kontak, dan media sosial</p>
                </div>
            </div>

            <form id="pengaturanForm">
                <div class="content-box" style="margin-bottom: 24px;">
                    <div class="box-header">
                        <h2>Pengaturan Umum</h2>
                    </div>
                    <div class="form-group">
                        <label for="namaSitus">Nama Situs/Lab *</label>
                        <input type="text" id="namaSitus" name="nama_situs" class="form-control" value="Laboratorium Mobile and Multimedia Tech" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="logoSitus">Upload Logo (Header)</label>
                            <input type="file" id="logoSitus" name="logo" class="form-control" accept="image/*">
                            <small>Kosongkan jika tidak ingin mengubah.</small>
                        </div>
                        <div class="form-group">
                            <label for="faviconSitus">Upload Favicon</label>
                            <input type="file" id="faviconSitus" name="favicon" class="form-control" accept="image/x-icon,image/png">
                            <small>Kosongkan jika tidak ingin mengubah.</small>
                        </div>
                    </div>
                </div>

                <div class="content-box" style="margin-bottom: 24px;">
                    <div class="box-header">
                        <h2>Informasi Kontak</h2>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="emailKontak">Email Kontak *</label>
                            <input type="email" id="emailKontak" name="email" class="form-control" value="info@polinema.ac.id" required>
                        </div>
                        <div class="form-group">
                            <label for="teleponKontak">Telepon *</label>
                            <input type="text" id="teleponKontak" name="telepon" class="form-control" value="(0341) 404 424" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="alamatLab">Alamat *</label>
                        <textarea id="alamatLab" name="alamat" class="form-control" rows="3" required>Jl. Soekarno Hatta No.9, Jatimulyo, Kec. Lowokwaru, Kota Malang, Jawa Timur 65141</textarea>
                    </div>
                </div>

                <div class="content-box">
                    <div class="box-header">
                        <h2>Media Sosial</h2>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="linkFacebook">Facebook URL</label>
                            <input type="url" id="linkFacebook" name="fb_url" class="form-control" placeholder="https://facebook.com/user">
                        </div>
                        <div class="form-group">
                            <label for="linkInstagram">Instagram URL</label>
                            <input type="url" id="linkInstagram" name="ig_url" class="form-control" placeholder="https://instagram.com/user">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="linkTwitter">Twitter/X URL</label>
                            <input type="url" id="linkTwitter" name="tw_url" class="form-control" placeholder="https://twitter.com/user">
                        </div>
                        <div class="form-group">
                            <label for="linkYoutube">YouTube URL</label>
                            <input type="url" id="linkYoutube" name="yt_url" class="form-control" placeholder="https://youtube.com/channel">
                        </div>
                    </div>
                </div>

                <div style="text-align: right; margin-top: 24px;">
                    <button type="submit" class="btn-primary">Simpan Pengaturan</button>
                </div>
            </form>

        </main>
    </div>

    <script src="assets/js/script-admin.js"></script>
    <script>
        document.getElementById('pengaturanForm').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Pengaturan berhasil disimpan!');
        });
    </script>
</body>
</html>