<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - Laboratorium Mobile and Multimedia Tech POLINEMA</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body id="top">

    <header class="site-header" id="siteHeader">
        <div class="container">
            <div class="logo-area">
                <a href="beranda.php">
                    <img src="assets/images/logo-placeholder.png" alt="Logo Polinema" style="height: 50px; width: auto;">
                </a>
            </div>
            
            <div class="header-controls">
                <nav class="main-navigation" id="primary-menu">
                    <ul>
                        <li><a href="beranda.php" aria-current="page">Beranda</a></li>
                        
                        <li class="has-dropdown">
                            <a href="menu/profil.php" class="dropdown-toggle">Profil</a>
                            <ul class="dropdown-menu">
                                <li><a href="menu/profil.php#visi-misi">Visi & Misi</a></li>
                                <li><a href="menu/profil.php#sejarah">Sejarah</a></li>
                                <li><a href="menu/profil.php#struktur">Struktur</a></li>
                                <li><a href="menu/profil.php#tim">Tim Kami</a></li>
                            </ul>
                        </li>
                        
                        <li class="has-dropdown">
                            <a href="menu/berita.php" class="dropdown-toggle">Berita & Kegiatan</a>
                            <ul class="dropdown-menu">
                                <li><a href="menu/berita.php">Berita Terbaru</a></li>
                                <li><a href="menu/berita.php">Agenda Kegiatan</a></li>
                            </ul>
                        </li>
                        
                        <li><a href="menu/proyek.php">Proyek</a></li>
                        <li><a href="menu/galeri.php">Galeri</a></li>
                        <li><a href="menu/kontak.php">Kontak</a></li>
                        <li><a href="menu/login.php">Login</a></li>
                    </ul>
                </nav>

                <div class="global-search">
                    <form action="#" method="get" class="search-form" id="search-form">
                        <input type="search" placeholder="Cari..." name="s" id="search-input" aria-label="Kolom pencarian">
                        <button type="submit" aria-label="Kirim pencarian">&#128269;</button>
                    </form>
                    <button class="search-toggle" aria-controls="search-form" aria-expanded="false" aria-label="Buka pencarian">
                        &#128269;
                    </button>
                </div>
                
                <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false" aria-label="Buka menu navigasi">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </header>

    <main>

        <section class="hero-full" style="background-image: url('assets/images/hero-building.jpg');">
            <div class="hero-overlay">
                <div class="container">
                    <div class="hero-full-content">
                        <h1>LABORATORIUM MOBILE<br>AND MULTIMEDIA TECH</h1>
                        <p>
                            Laboratorium Mobile & Multimedia Tech POLINEMA adalah pusat
                            pengembangan karya inovatif di bidang UI/UX, Game, AR/VR.
                        </p>
                        <a href="menu/profil.php" class="btn">Selengkapnya</a>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="highlight-section">
            <div class="container">
                <h2 class="section-title">Highlight Proyek</h2>
                
                <div class="highlight-grid">
                    <?php
                    require_once 'config/db.php';
                    $stmt = $pdo->query("SELECT * FROM projects WHERE status = 'published' ORDER BY created_at DESC LIMIT 3");
                    while ($project = $stmt->fetch(PDO::FETCH_ASSOC)):
                        // Ambil kategori
                        $cat_stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
                        $cat_stmt->execute([$project['category_id']]);
                        $category = $cat_stmt->fetchColumn();
                    ?>
                    <a href="menu/proyek.php" class="home-project-card">
                        <div class="card-thumbnail">
                            <img src="<?php echo htmlspecialchars($project['cover_image']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>">
                            <span class="card-category"><?php echo htmlspecialchars($category); ?></span>
                        </div>
                        <div class="card-content">
                            <h4 class="card-title"><?php echo htmlspecialchars($project['title']); ?></h4>
                            <span class="card-year"><?php echo htmlspecialchars($project['year']); ?></span>
                        </div>
                    </a>
                    <?php endwhile; ?>
                </div>
                
                <div class="section-cta">
                    <a href="menu/proyek.php" class="btn">Lihat Semua Proyek</a>
                </div>
            </div>
        </section>

        <section class="highlight-section bg-light">
            <div class="container">
                <h2 class="section-title">Berita & Kegiatan Terbaru</h2>
                
                <div class="highlight-grid">
                    <?php
                    $stmt = $pdo->query("SELECT * FROM news WHERE status = 'published' ORDER BY created_at DESC LIMIT 3");
                    while ($item = $stmt->fetch(PDO::FETCH_ASSOC)):
                    ?>
                    <a href="menu/berita.php" class="home-news-card">
                        <div class="card-thumbnail">
                            <img src="<?php echo htmlspecialchars($item['cover_image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                        </div>
                        <div class="card-content">
                            <span class="card-date"><?php echo date('d F Y', strtotime($item['created_at'])); ?></span>
                            <h4 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h4>
                            <p class="card-excerpt"><?php echo htmlspecialchars($item['summary']); ?></p>
                        </div>
                    </a>
                    <?php endwhile; ?>
                </div>
                
                <div class="section-cta">
                    <a href="menu/berita.php" class="btn">Lihat Semua Berita & Kegiatan</a>
                </div>
            </div>
        </section>

    </main>

    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <img src="assets/images/logo-footer-placeholder.png" alt="Logo Lab Polinema Footer" style="height: 60px; margin-bottom: 20px;">
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
                        <li><a href="beranda.php">Beranda</a></li>
                        <li><a href="menu/profil.php">Profil</a></li>
                        <li><a href="menu/berita.php">Berita & Kegiatan</a></li>
                        <li><a href="menu/proyek.php">Proyek</a></li>
                        <li><a href="menu/galeri.php">Galeri</a></li>
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

    <script src="assets/js/script.js" defer></script>
</body>
</html>