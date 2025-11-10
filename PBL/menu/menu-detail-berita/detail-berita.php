<?php
require_once '../../config/db.php';
if ( !isset($_GET['slug']) ) { header('Location: ../berita.php'); exit; }

$slug = $_GET['slug'];
$sql = "SELECT * FROM news WHERE slug = ? AND status = 'published' LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([$slug]);
$news = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$news) { die("Berita tidak ditemukan."); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Berita: Pelatihan Keamanan Siber - Sistem Informasi</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    
    <!-- CSS Utama (sama dengan halaman lain) -->
    <link rel="stylesheet" href="../../assets/css/style.css">
    
    <!-- CSS Khusus Halaman Detail Berita -->
    <link rel="stylesheet" href="assets/det-berita/css/style-detail-berita.css">
    
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
                        <a href="../berita.php" class="dropdown-toggle" aria-current="page">Berita & Kegiatan</a>
                        <ul class="dropdown-menu">
                            <li><a href="../berita.php">Berita Terbaru</a></li>
                            <li><a href="../berita.php">Agenda Kegiatan</a></li>
                        </ul>
                    </li>
                    
                    <li><a href="../proyek.php">Proyek</a></li>
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

        <div class="container">
            <h1><?php echo htmlspecialchars($news['title']); ?></h1>
            <img src="<?php echo htmlspecialchars($news['cover_image']); ?>" alt="">
            <p><?php echo nl2br(htmlspecialchars($news['content'])); ?></p>
        </div>
    </main>
    </body>
        
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
    
    <!-- JavaScript Khusus Halaman Detail Berita -->
    <script src="assets/det-berita/js/script-detail-berita.js"></script>

</body>
</html>