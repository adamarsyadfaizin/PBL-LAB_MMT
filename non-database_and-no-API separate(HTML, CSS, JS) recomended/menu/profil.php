<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Lab - Laboratorium Mobile and Multimedia Tech POLINEMA</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/profil/css/style-profil.css">
    
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
                    <img src="../assets/images/logo-placeholder.png" alt="Logo Polinema" style="height: 50px; width: auto;">
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
                        <a href="profil.php" class="dropdown-toggle" aria-current="page">Profil</a>
                        <ul class="dropdown-menu">
                            <li><a href="#visi-misi">Visi & Misi</a></li>
                            <li><a href="#sejarah">Sejarah</a></li>
                            <li><a href="#struktur">Struktur Organisasi</a></li>
                            <li><a href="#tim">Tim Kami</a></li>
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
                    <li><a href="galeri.php">Galeri</a></li>
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
                <h1>Profil Laboratorium</h1>
            </div>
        </section>

        <section class="profile-section" id="visi-misi">
            <div class="container">
                <div class="visi-misi-grid">
                    <div class="visi-card">
                        <h3>Visi</h3>
                        <p>Menjadi pusat unggulan (center of excellence) dalam inovasi, riset, dan pengembangan teknologi mobile dan multimedia yang diakui secara nasional dan internasional, serta menghasilkan lulusan yang kompeten dan berdaya saing global.</p>
                    </div>
                    <div class="misi-card">
                        <h3>Misi</h3>
                        <ul>
                            <li>Menyelenggarakan pendidikan vokasi berkualitas tinggi di bidang teknologi mobile dan multimedia.</li>
                            <li>Melaksanakan penelitian terapan yang inovatif dan relevan dengan kebutuhan industri.</li>
                            <li>Melakukan pengabdian kepada masyarakat melalui penerapan teknologi mobile dan multimedia untuk solusi nyata.</li>
                            <li>Membangun kemitraan strategis dengan industri, pemerintah, dan institusi pendidikan lainnya.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="profile-section bg-light" id="sejarah">
            <div class="container">
                <h2 class="section-title">Sejarah Singkat</h2>
                <p class="sejarah-text">
                    Berdiri sejak tahun 2010, Laboratorium Mobile and Multimedia Tech (MMT) awalnya merupakan unit pendukung praktikum untuk mata kuliah pemrograman mobile dasar. Seiring dengan pesatnya perkembangan teknologi, lab ini bertransformasi menjadi pusat riset.
                </p>
                <p class="sejarah-text">
                    Pada tahun 2018, laboratorium diperluas dengan fokus baru pada Augmented Reality (AR) dan Virtual Reality (VR), serta UI/UX, untuk menjawab tantangan industri kreatif digital yang terus berkembang.
                </p>
            </div>
        </section>
        
        <section class="profile-section" id="struktur">
            <div class="container">
                <h2 class="section-title">Struktur Organisasi</h2>
                <div class="struktur-placeholder">
                    <img src="../assets/images/struktur-org-placeholder.png" alt="Bagan Struktur Organisasi Laboratorium Mobile and Multimedia Tech">
                </div>
            </div>
        </section>
        
        <section class="profile-section bg-light" id="tim">
            <div class="container">
                <h2 class="section-title">Tim Kami</h2>
                <div class="team-grid">
                    
                    <div class="team-card">
                        <img src="../assets/images/team-1.jpg" alt="Foto Dr. Ahmad Fadhil" class="team-photo">
                        <div class="team-info">
                            <h4>Dr. Ahmad Fadhil, M.Kom.</h4>
                            <span class="team-role">Kepala Laboratorium</span>
                            <div class="team-tags">
                                <span class="tag">AI</span>
                                <span class="tag">Data Science</span>
                            </div>
                            <div class="team-links">
                                <a href="#">LinkedIn</a> | <a href="#">Scholar</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="team-card">
                        <img src="../assets/images/team-2.jpg" alt="Foto Siti Nurhaliza" class="team-photo">
                        <div class="team-info">
                            <h4>Siti Nurhaliza, S.Tr.Kom.</h4>
                            <span class="team-role">Koordinator UI/UX</span>
                            <div class="team-tags">
                                <span class="tag">UI/UX</span>
                                <span class="tag">Figma</span>
                            </div>
                            <div class="team-links">
                                <a href="#">LinkedIn</a> | <a href="#">Scholar</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="team-card">
                        <img src="../assets/images/team-3.jpg" alt="Foto Bagus Wijaya" class="team-photo">
                        <div class="team-info">
                            <h4>Bagus Wijaya, M.Cs.</h4>
                            <span class="team-role">Koordinator Mobile Dev</span>
                            <div class="team-tags">
                                <span class="tag">React Native</span>
                                <span class="tag">Flutter</span>
                            </div>
                            <div class="team-links">
                                <a href="#">LinkedIn</a> | <a href="#">Scholar</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="team-card">
                        <img src="../assets/images/team-4.jpg" alt="Foto Rina Permatasari" class="team-photo">
                        <div class="team-info">
                            <h4>Rina Permatasari, M.T.</h4>
                            <span class="team-role">Koordinator AR/VR & Game</span>
                            <div class="team-tags">
                                <span class="tag">Unity</span>
                                <span class="tag">ARCore</span>
                            </div>
                            <div class="team-links">
                                <a href="#">LinkedIn</a> | <a href="#">Scholar</a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>
        
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

    <script src="assets/profil/js/script-profil.js"></script>

</body>
</html>