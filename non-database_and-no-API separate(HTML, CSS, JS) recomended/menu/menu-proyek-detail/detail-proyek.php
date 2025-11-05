<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Proyek: Deteksi Kemacetan - Sistem Informasi</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    
    <!-- CSS Utama (sama dengan halaman lain) -->
    <link rel="stylesheet" href="../../assets/css/style.css">
    
    <!-- CSS Khusus Halaman Detail Proyek -->
    <link rel="stylesheet" href="assets/det-pro/css/style-detail-proyek.css">
    
</head>
<body id="top">

    <!-- ==== HEADER ==== -->
    <header class="site-header" id="siteHeader">
        <div class="container">
            <div class="logo-area">
                <a href="../../beranda.php">
                    <img src="../../assets/images/logo-placeholder.png" alt="Logo Departemen" style="height: 50px; width: auto;">
                </a>
            </div>
            
            <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false" aria-label="Buka menu navigasi">
                <span></span>
                <span></span>
                <span></span>
            </button>
            
            <nav class="main-navigation" id="primary-menu">
                <ul>
                    <li><a href="../../beranda.php">Beranda</a></li>
                    
                    <li class="has-dropdown">
                        <a href="../profil.php" class="dropdown-toggle">Profil</a>
                        <ul class="dropdown-menu">
                            <li><a href="../profil.php#visi-misi">Visi & Misi</a></li>
                            <li><a href="../profil.php#sejarah">Sejarah</a></li>
                            <li><a href="../profil.php#struktur">Struktur</a></li>
                            <li><a href="../profil.php#tim">Tim Kami</a></li>
                        </ul>
                    </li>
                    
                    <li class="has-dropdown">
                        <a href="../berita.php" class="dropdown-toggle">Berita & Kegiatan</a>
                        <ul class="dropdown-menu">
                            <li><a href="../berita.php">Berita Terbaru</a></li>
                            <li><a href="../berita.php">Agenda Kegiatan</a></li>
                        </ul>
                    </li>
                    
                    <li><a href="../proyek.php" aria-current="page">Proyek</a></li>
                    
                    <li><a href="../galeri.php">Galeri</a></li>
                    
                    <li><a href="../kontak.php">Kontak</a></li>
                    <li><a href="../../login.php">Login</a></li>

                    <li class="nav-search">
                        <form action="../../search.php" method="get" class="nav-search-form">
                            <input type="search" name="q" placeholder="Cari..." aria-label="Cari" class="nav-search-input">
                            <button type="submit" class="nav-search-button" aria-label="Cari">&#128269;</button>
                        </form>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <!-- ==== HERO ==== -->
        <section class="hero">
            <div class="container">
                <h1>Sistem Deteksi Kemacetan Real-time</h1>
            </div>
        </section>

        <!-- ==== MAIN CONTENT ==== -->
        <div class="main-content-area">
            <div class="container">
                
                <!-- Kolom Konten Utama -->
                <div class="primary-content">
                    
                    <!-- Media: Video Demo -->
                    <div class="project-video-embed">
                        <div class="mockup-video">
                            <span>&#9658;</span>
                        </div>
                    </div>

                    <!-- Ringkasan -->
                    <p class="lead-paragraph">
                        Proyek mahasiswa ini bertujuan membangun aplikasi mobile yang memanfaatkan data GPS crowdsourced dari pengguna untuk memetakan dan memprediksi titik kemacetan di area metropolitan, membantu pengguna menemukan rute tercepat.
                    </p>
                    
                    <!-- Deskripsi Lengkap -->
                    <h3>Deskripsi Proyek</h3>
                    <p>
                        Aplikasi ini dikembangkan sebagai solusi atas masalah kemacetan kronis di kota-kota besar. Dengan mengumpulkan data lokasi anonim dari pengguna secara real-time, sistem dapat menganalisis kecepatan rata-rata di berbagai ruas jalan.
                    </p>
                    <p>
                        Data yang terkumpul kemudian diproses menggunakan algoritma machine learning sederhana untuk memprediksi potensi kemacetan dalam 30 menit ke depan. Hasilnya ditampilkan dalam bentuk peta interaktif dengan visualisasi heatmap.
                    </p>

                    <!-- Teknologi -->
                    <h3>Teknologi yang Digunakan</h3>
                    <div class="tech-badge-container">
                        <span class="tech-badge">React Native</span>
                        <span class="tech-badge">Firebase</span>
                        <span class="tech-badge">Google Maps API</span>
                        <span class="tech-badge">Node.js</span>
                        <span class="tech-badge">Figma</span>
                    </div>

                    <!-- Tautan -->
                    <h3>Tautan Proyek</h3>
                    <ul class="project-links-list">
                        <li><a href="#" class="demo">Lihat Demo Interaktif</a></li>
                        <li><a href="#" class="github">Repository GitHub</a></li>
                        <li><a href="#" class="paper">Publikasi Ilmiah (PDF)</a></li>
                    </ul>

                    <!-- Rating & Feedback -->
                    <h3>Rating & Umpan Balik</h3>
                    <div class="star-rating" title="Rating: 4.5 dari 5 bintang">★★★★☆</div>
                    <div class="rating-summary">(4.5/5 berdasarkan 28 ulasan)</div>
                    
                    <form class="comment-form" action="#" method="post">
                        <h4>Beri Komentar</h4>
                        <div class="form-group">
                            <label for="comment-name">Nama Anda</label>
                            <input type="text" id="comment-name" name="comment_name" required>
                        </div>
                        <div class="form-group">
                            <label for="comment-text">Komentar Anda</label>
                            <textarea id="comment-text" name="comment_text" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn">Kirim Komentar</button>
                    </form>
                    
                    <div class="comment-list">
                        <h3>Komentar (2)</h3>
                        <div class="comment-item">
                            <p class="comment-author">Andi Budianto</p>
                            <p class="comment-date">2 November 2025</p>
                            <p>Aplikasi yang sangat inovatif dan bermanfaat untuk penggunaan sehari-hari. Sukses selalu untuk tim pengembang!</p>
                        </div>
                        <div class="comment-item">
                            <p class="comment-author">Citra Lestari</p>
                            <p class="comment-date">1 November 2025</p>
                            <p>Tampilannya bersih dan mudah digunakan. Prediksinya cukup akurat untuk area Surabaya pusat.</p>
                        </div>
                    </div>

                    <!-- Tombol Kembali -->
                    <a href="../proyek.php" class="btn btn-secondary back-link">&larr; Kembali ke Katalog Proyek</a>

                </div>
                
                <!-- Kolom Sidebar -->
                <aside class="sidebar">
                    
                    <!-- Anggota Tim -->
                    <div class="widget">
                        <h3 class="widget-title">Anggota Tim</h3>
                        <ul class="widget-team-list">
                            <li class="team-member">
                                <img src="../../assets/images/avatar-1.jpg" alt="Avatar Anggota 1" class="team-avatar">
                                <div class="team-info">
                                    <h4>Ahmad Fadhil</h4>
                                    <span class="role">Project Manager</span>
                                    <a href="#" class="contact-link">LinkedIn</a>
                                </div>
                            </li>
                            <li class="team-member">
                                <img src="../../assets/images/avatar-2.jpg" alt="Avatar Anggota 2" class="team-avatar">
                                <div class="team-info">
                                    <h4>Siti Nurhaliza</h4>
                                    <span class="role">UI/UX Designer</span>
                                    <a href="#" class="contact-link">LinkedIn</a>
                                </div>
                            </li>
                            <li class="team-member">
                                <img src="../../assets/images/avatar-3.jpg" alt="Avatar Anggota 3" class="team-avatar">
                                <div class="team-info">
                                    <h4>Bagus Wijaya</h4>
                                    <span class="role">Lead Developer</span>
                                    <a href="#" class="contact-link">LinkedIn</a>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Proyek Terkait -->
                    <div class="widget widget-news">
                        <h3 class="widget-title">Proyek Terkait</h3>
                        <ul>
                            <li>
                                <a href="#">
                                    <h4>Dashboard Visualisasi Data COVID-19</h4>
                                    <span class="date">Kategori: Analitika Data</span>
                                    <p>Dashboard interaktif untuk memantau penyebaran kasus...</p>
                                    <span class="arrow-icon" aria-hidden="true">&rarr;</span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <h4>Aplikasi E-Commerce Terintegrasi UMKM</h4>
                                    <span class="date">Kategori: Aplikasi Mobile</span>
                                    <p>Pengembangan platform e-commerce untuk UMKM lokal...</p>
                                    <span class="arrow-icon" aria-hidden="true">&rarr;</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </aside>
                
            </div>
        </div>
        
    </main>

    <footer class="site-footer">
        <div class="container">
            <!-- Footer content sama seperti sebelumnya -->
        </div>
    </footer>

    <a href="#top" id="scrollTopBtn" class="scroll-top-btn" aria-label="Kembali ke atas">
        &uarr;
    </a>

    <!-- JavaScript Utama -->
    <script src="../../assets/js/script.js"></script>
    
    <!-- JavaScript Khusus Halaman Detail Proyek -->
    <script src="assets/det-pro/js/script-detail-proyek.js"></script>

</body>
</html>