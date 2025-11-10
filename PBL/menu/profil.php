<?php
require_once '../config/db.php';
// Ambil Visi, Misi, Sejarah
$profile = $pdo->query("SELECT * FROM lab_profile LIMIT 1")->fetch(PDO::FETCH_ASSOC);
// Ambil Anggota Tim
$members = $pdo->query("SELECT * FROM members ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>
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
                        <p><?php echo nl2br(htmlspecialchars($profile['visi'])); ?></p>
                    </div>
                    <div class="misi-card">
                        <h3>Misi</h3>
                        <?php 
                        // Memisahkan misi menjadi list items jika ada pemisah
                        $misi_items = explode("\n", $profile['misi']);
                        if (count($misi_items) > 1) {
                            echo '<ul>';
                            foreach ($misi_items as $item) {
                                if (trim($item) !== '') {
                                    echo '<li>' . htmlspecialchars(trim($item)) . '</li>';
                                }
                            }
                            echo '</ul>';
                        } else {
                            echo '<p>' . nl2br(htmlspecialchars($profile['misi'])) . '</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="profile-section bg-light" id="sejarah">
            <div class="container">
                <h2 class="section-title">Sejarah Singkat</h2>
                <p class="sejarah-text">
                    <?php echo nl2br(htmlspecialchars($profile['sejarah'])); ?>
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
                    <?php foreach ($members as $member): ?>
                    <div class="team-card">
                        <img src="<?php echo htmlspecialchars($member['avatar_url']); ?>" alt="Foto <?php echo htmlspecialchars($member['name']); ?>" class="team-photo">
                        <div class="team-info">
                            <h4><?php echo htmlspecialchars($member['name']); ?></h4>
                            <span class="team-role"><?php echo htmlspecialchars($member['role']); ?></span>
                            <?php if (!empty($member['tags'])): ?>
                            <div class="team-tags">
                                <?php 
                                $tags = explode(',', $member['tags']);
                                foreach ($tags as $tag):
                                ?>
                                <span class="tag"><?php echo htmlspecialchars(trim($tag)); ?></span>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                            <div class="team-links">
                                <?php if (!empty($member['linkedin_url'])): ?>
                                <a href="<?php echo htmlspecialchars($member['linkedin_url']); ?>">LinkedIn</a>
                                <?php endif; ?>
                                <?php if (!empty($member['linkedin_url']) && !empty($member['scholar_url'])): ?> | <?php endif; ?>
                                <?php if (!empty($member['scholar_url'])): ?>
                                <a href="<?php echo htmlspecialchars($member['scholar_url']); ?>">Scholar</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
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