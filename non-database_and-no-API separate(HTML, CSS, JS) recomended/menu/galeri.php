<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Multimedia - Departemen Sistem Informasi</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/galeri/css/style-galeri.css">
    
    <style>
        /* Override background hero image dengan path yang benar */
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.5)), 
                        url('../assets/images/hero.jpg') center center/cover no-repeat;
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
                <a href="../beranda.php">
                    <img src="../assets/images/logo-placeholder.png" alt="Logo Departemen" style="height: 50px; width: auto;">
                </a>
            </div>
            
            <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false" aria-label="Buka menu navigasi">
                <span></span>
                <span></span>
                <span></span>
            </button>
            
            <nav class="main-navigation" id="primary-menu">
                <ul>
                    <li><a href="../beranda.php">Beranda</a></li>
                    
                    <li class="has-dropdown">
                        <a href="profil.php" class="dropdown-toggle">Profil</a>
                        <ul class="dropdown-menu">
                            <li><a href="profil.php#visi-misi">Visi & Misi</a></li>
                            <li><a href="profil.php#sejarah">Sejarah</a></li>
                            <li><a href="profil.php#manajemen">Manajemen</a></li>
                        </ul>
                    </li>
                    
                    <li class="has-dropdown">
                        <a href="berita.php" class="dropdown-toggle">Berita & Kegiatan</a>
                        <ul class="dropdown-menu">
                            <li><a href="berita.php">Berita Terbaru</a></li>
                            <li><a href="berita.php">Agenda Kegiatan</a></li>
                        </ul>
                    </li>
                    
                    <li><a href="proyek.php">Proyek</a></li>
                    
                    <li><a href="galeri.php" aria-current="page">Galeri</a></li>
                    
                    <li><a href="kontak.php">Kontak</a></li>
                    <li><a href="../login.php">Login</a></li>

                    <li class="nav-search">
                        <form action="../search.php" method="get" class="nav-search-form">
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
                <h1>Galeri Multimedia</h1>
            </div>
        </section>

        <div class="main-content-area">
            <div class="container">
                
                <form class="filter-bar" action="#" method="get">
                    <div class="filter-group">
                        <label for="filter-jenis">Jenis:</label>
                        <select id="filter-jenis" name="jenis">
                            <option value="semua">Semua Media</option>
                            <option value="foto">Foto</option>
                            <option value="video">Video</option>
                            <option value="animasi">Animasi</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="filter-acara">Acara:</label>
                        <select id="filter-acara" name="acara">
                            <option value="semua">Semua Acara</option>
                            <option value="dies-natalis">Dies Natalis</option>
                            <option value="wisuda">Wisuda</option>
                            <option value="seminar">Seminar</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="filter-tahun">Tahun:</label>
                        <select id="filter-tahun" name="tahun">
                            <option value="2025">2025</option>
                            <option value="2024">2024</option>
                            <option value="2023">2023</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-filter">Filter</button>
                </form>

                <div class="gallery-grid-main">
                    
                    <a href="menu-detail-galeri/galeri-detail.php?id=1" class="gallery-item" aria-label="Lihat foto Dies Natalis 2025">
                        <div class="image-container">
                            <img src="../assets/images/gallery-photo-1.jpg" alt="Foto Dies Natalis">
                            <div class="overlay"><span class="icon">&#128247;</span></div> </div>
                        <div class="item-info">
                            <h4>Dies Natalis 2025</h4>
                            <span>Foto</span>
                        </div>
                    </a>
                    
                    <a href="menu-detail-galeri/galeri-detail.php?id=2" class="gallery-item" aria-label="Lihat video Profil Departemen">
                        <div class="image-container">
                            <img src="../assets/images/gallery-video-1.jpg" alt="Thumbnail Video Profil">
                            <div class="overlay"><span class="icon">&#9658;</span></div> </div>
                        <div class="item-info">
                            <h4>Video Profil Departemen</h4>
                            <span>Video</span>
                        </div>
                    </a>

                    <a href="menu-detail-galeri/galeri-detail.php?id=3" class="gallery-item" aria-label="Lihat animasi Proyek UI/UX">
                        <div class="image-container">
                            <img src="../assets/images/gallery-anim-1.gif" alt="Animasi UI/UX">
                            <div class="overlay"><span class="icon">&#10022;</span></div> </div>
                        <div class="item-info">
                            <h4>Animasi Proyek UI/UX</h4>
                            <span>Animasi</span>
                        </div>
                    </a>

                    <a href="menu-detail-galeri/galeri-detail.php?id=4" class="gallery-item" aria-label="Lihat foto Suasana Wisuda 2025">
                        <div class="image-container">
                            <img src="../assets/images/gallery-photo-2.jpg" alt="Foto Wisuda">
                            <div class="overlay"><span class="icon">&#128247;</span></div>
                        </div>
                        <div class="item-info">
                            <h4>Suasana Wisuda 2025</h4>
                            <span>Foto</span>
                        </div>
                    </a>
                    
                    <a href="menu-detail-galeri/galeri-detail.php?id=5" class="gallery-item" aria-label="Lihat foto Seminar Nasional AI">
                        <div class="image-container">
                            <img src="../assets/images/event-highlight.jpg" alt="Foto Seminar AI">
                            <div class="overlay"><span class="icon">&#128247;</span></div>
                        </div>
                        <div class="item-info">
                            <h4>Seminar Nasional AI</h4>
                            <span>Foto</span>
                        </div>
                    </a>
                    
                    <a href="menu-detail-galeri/galeri-detail.php?id=6" class="gallery-item" aria-label="Lihat video Guest Lecture">
                        <div class="image-container">
                            <img src="../assets/images/gallery-video-2.jpg" alt="Thumbnail Guest Lecture">
                            <div class="overlay"><span class="icon">&#9658;</span></div>
                        </div>
                        <div class="item-info">
                            <h4>Guest Lecture: Big Data</h4>
                            <span>Video</span>
                        </div>
                    </a>
                    
                </div>
                
            </div>
        </div>
        
    </main>

    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                
                <div class="footer-col">
                    <img src="../assets/images/logo-footer-placeholder.png" alt="Logo Lab Polinema Footer" style="height: 60px; margin-bottom: 20px;">
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
                        <li><a href="../beranda.php">Beranda</a></li>
                        <li><a href="profil.php">Profil</a></li>
                        <li><a href="berita.php">Berita & Kegiatan</a></li>
                        <li><a href="proyek.php">Proyek</a></li>
                        <li><a href="galeri.php">Galeri</a></li>
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

    <script src="assets/galeri/js/script-galeri.js"></script>

</body>
</html>