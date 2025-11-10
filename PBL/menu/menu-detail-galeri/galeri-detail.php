<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Galeri: Dies Natalis 2025 - POLINEMA</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    
    <!-- CSS Utama (sama dengan halaman lain) -->
    <link rel="stylesheet" href="../../assets/css/style.css">
    
    <!-- CSS Khusus Halaman Detail Galeri -->
    <link rel="stylesheet" href="assets/det-gel/css/style-galeri-detail.css">
    
    <style>
        /* Override background hero image dengan path yang benar */
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.5)), 
                        url('../../assets/images/hero.jpg') center center/cover no-repeat;
            height: 300px;
            display: flex;
            align-items: center;
            position: relative;
            color: var(--color-white);
        }
        .hero .container {
            position: relative;
            z-index: 2;
        }
        .hero h1 {
            font-size: 36px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.4);
        }
    </style>
</head>
<body id="top">

    <header class="site-header" id="siteHeader">
        <div class="container">
            <div class="logo-area">
                <a href="../../beranda.php">
                    <img src="../../assets/images/logo-placeholder.png" alt="Logo Polinema" style="height: 50px; width: auto;">
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
                            <li><a href="profil.php#visi-misi">Visi & Misi</a></li>
                            <li><a href="profil.php#sejarah">Sejarah</a></li>
                            <li><a href="profil.php#struktur">Struktur</a></li>
                            <li><a href="profil.php#tim">Tim Kami</a></li>
                        </ul>
                    </li>
                    
                    <li class="has-dropdown">
                        <a href="../berita.php" class="dropdown-toggle">Berita & Kegiatan</a>
                        <ul class="dropdown-menu">
                            <li><a href="../berita.php">Berita Terbaru</a></li>
                            <li><a href="../berita.php">Agenda Kegiatan</a></li>
                        </ul>
                    </li>
                    
                    <li><a href="../proyek.php">Proyek</a></li>
                    
                    <li><a href="../galeri.php" aria-current="page">Galeri</a></li>
                    
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

        <section class="hero">
            <div class="container">
                <h1>Dies Natalis 2025</h1>
            </div>
        </section>

        <div class="main-content-area">
            <div class="container">
                
                <div class="primary-content">

                    <img src="../../assets/images/gallery-photo-1.jpg" alt="Foto Dies Natalis" class="detail-article-banner">

                    <div class="article-meta-detail">
                        <div class="meta-info">
                            <span><strong>Kategori:</strong> Foto</span>
                            <span><strong>Acara:</strong> Dies Natalis</span>
                            <span><strong>Tanggal:</strong> 15 Oktober 2025</span>
                        </div>
                        <div class="social-share">
                            <span>Bagikan:</span>
                            <a href="#" aria-label="Bagikan ke Facebook">F</a>
                            <a href="#" aria-label="Bagikan ke Twitter">T</a>
                            <a href="#" aria-label="Bagikan ke LinkedIn">L</a>
                        </div>
                    </div>

                    <div class="article-content">
                        <p>
                            Momen kebersamaan seluruh civitas akademika dalam perayaan Dies Natalis Politeknik Negeri Malang. Acara ini dimeriahkan dengan berbagai kegiatan, mulai dari jalan sehat, pameran karya, hingga malam penghargaan bagi dosen dan mahasiswa berprestasi.
                        </p>
                        <p>
                            Foto ini menangkap momen pemotongan tumpeng oleh Direktur, sebagai simbol rasa syukur atas pencapaian yang telah diraih dan harapan untuk masa depan yang lebih gemilang.
                        </p>
                    </div>
                    
                    <a href="../galeri.php" class="btn btn-secondary back-link">&larr; Kembali ke Galeri</a>

                </div>
                
                <aside class="sidebar">
                    
                    <div class="widget widget-categories">
                        <h3 class="widget-title">Kategori Galeri</h3>
                        <ul>
                            <li><a href="../galeri.php">Semua Media</a></li>
                            <li><a href="../galeri.php">Foto</a></li>
                            <li><a href="../galeri.php">Video</a></li>
                            <li><a href="../galeri.php">Animasi</a></li>
                        </ul>
                    </div>
                    
                    <div class="widget widget-news">
                        <h3 class="widget-title">Album Lainnya</h3>
                        <ul>
                            <li>
                                <a href="galeri-detail.php?id=4">
                                    <h4>Suasana Wisuda 2025</h4>
                                    <span class="date">Acara | Foto</span>
                                    <p>Momen haru dan bahagia para wisudawan...</p>
                                    <span class="arrow-icon" aria-hidden="true">&rarr;</span>
                                </a>
                            </li>
                            <li>
                                <a href="galeri-detail.php?id=2">
                                    <h4>Video Profil Departemen</h4>
                                    <span class="date">Profil | Video</span>
                                    <p>Tampilan baru laboratorium dan fasilitas kami...</p>
                                    <span class="arrow-icon" aria-hidden="true">&rarr;</span>
                                </a>
                            </li>
                            <li>
                                <a href="galeri-detail.php?id=5">
                                    <h4>Seminar Nasional AI</h4>
                                    <span class="date">Kegiatan | Foto</span>
                                    <p>Antusiasme peserta saat sesi tanya jawab...</p>
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
            <div class="footer-grid">
                
                <div class="footer-col">
                    <img src="../../assets/images/logo-footer-placeholder.png" alt="Logo Lab Polinema Footer" style="height: 60px; margin-bottom: 20px;">
                    <p>
                        <strong>Laboratorium Mobile and Multimedia Tech</strong><br>
                        Politeknik Negeri Malang<br>
                        Malang, Indonesia
                    </p>
                    <div class="social-links-footer">
                        <a href="#" aria-label="Facebook">F</a>
                        <a href="#" aria-label="Twitter">T</a>
                        <a href="#" aria-label="Instagram">I</a>
                        <a href="#" aria-label="YouTube">Y</a>
                    </div>
                </div>
                
                <div class="footer-col">
                    <h4>Tautan Cepat</h4>
                    <ul>
                        <li><a href="../../beranda.php">Beranda</a></li>
                        <li><a href="../profil.php">Profil</a></li>
                        <li><a href="../berita.php">Berita & Kegiatan</a></li>
                        <li><a href="../proyek.php">Proyek</a></li>
                        <li><a href="../galeri.php">Galeri</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4>Kontak & Kebijakan</h4>
                    <p>
                        Jl. Soekarno Hatta No.9, Jatimulyo,<br>
                        Kec. Lowokwaru, Kota Malang,<br>
                        Jawa Timur 65141
                    </p>
                    <p>
                        Email: <a href="mailto:info@polinema.ac.id">info@polinema.ac.id</a><br>
                        Telepon: (0341) 404 424
                    </p>
                    <ul class="policy-links">
                        <li><a href="#">Kebijakan Privasi</a></li>
                        <li><a href="#">Ketentuan Layanan</a></li>
                    </ul>
                </div>
                
            </div>
            
            <div class="footer-copyright">
                <p>&copy; 2025 Laboratorium Mobile and Multimedia Tech POLINEMA. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>

    <a href="#top" id="scrollTopBtn" class="scroll-top-btn" aria-label="Kembali ke atas">
        &uarr;
    </a>

    <!-- JavaScript Utama -->
    <script src="../../assets/js/script.js"></script>
    
    <!-- JavaScript Khusus Halaman Detail Galeri -->
    <script src="assets/det-gel/js/script-galeri-detail.js"></script>

</body>
</html>