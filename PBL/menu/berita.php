<?php
require_once '../config/db.php';

// Ambil data berita dari database
$stmt = $pdo->query("SELECT * FROM news WHERE status = 'published' ORDER BY created_at DESC");
$news_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil event highlight (ambil berita terbaru sebagai highlight)
$event_stmt = $pdo->query("SELECT * FROM news WHERE status = 'published' ORDER BY created_at DESC LIMIT 1");
$event_highlight = $event_stmt->fetch(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita & Kegiatan - Departemen Sistem Informasi</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/berita/css/style-berita.css">
    
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
                            <li><a href="profil.php#struktur">Struktur</a></li>
                            <li><a href="profil.php#tim">Tim Kami</a></li>
                        </ul>
                    </li>
                    
                    <li class="has-dropdown">
                        <a href="berita.php" class="dropdown-toggle" aria-current="page">Berita & Kegiatan</a>
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
                <h1>Berita & Kegiatan</h1>
            </div>
        </section>

        <div class="main-content-area">
            <div class="container">
                
                <div class="primary-content">

                    <?php if ($event_highlight): ?>
                    <section class="event-highlight">
                        <div class="event-highlight-img">
                            <img src="<?php echo htmlspecialchars($event_highlight['cover_image']); ?>" alt="Banner <?php echo htmlspecialchars($event_highlight['title']); ?>">
                        </div>
                        <div class="event-highlight-content">
                            <span class="event-tag">Berita Terbaru</span>
                            <h2><?php echo htmlspecialchars($event_highlight['title']); ?></h2>
                            <p class="event-date">
                                <?php echo date('d F Y', strtotime($event_highlight['created_at'])); ?>
                            </p>
                            <p><?php echo htmlspecialchars($event_highlight['summary']); ?></p>
                        </div>
                    </section>
                    <?php endif; ?>
                    
                    <form class="filter-bar" action="#" method="get">
                        <div class="filter-group">
                            <label for="filter-kategori">Kategori:</label>
                            <select id="filter-kategori" name="kategori">
                                <option value="semua">Semua</option>
                                <option value="berita">Berita</option>
                                <option value="kegiatan">Kegiatan</option>
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

                    <?php 
                    $image_position = 'left';
                    foreach ($news_items as $index => $item): 
                        // Alternating image position
                        $position_class = ($image_position == 'left') ? 'image-left' : 'image-right';
                        $image_position = ($image_position == 'left') ? 'right' : 'left';
                    ?>
                    <article class="facility-item <?php echo $position_class; ?>">
                        <div class="facility-image">
                            <img src="<?php echo htmlspecialchars($item['cover_image']); ?>" alt="Gambar <?php echo htmlspecialchars($item['title']); ?>">
                        </div>
                        <div class="facility-text">
                            <h2><?php echo htmlspecialchars($item['title']); ?></h2>
                            <span class="article-metadata">
                                Diposting pada: <?php echo date('d M Y', strtotime($item['created_at'])); ?>
                            </span>
                            <p><?php echo htmlspecialchars($item['summary']); ?></p>
                            <a href="menu-detail-berita/detail-berita.php?slug=<?php echo htmlspecialchars($item['slug']); ?>" class="btn">Lihat Detail</a>
                        </div>
                    </article>
                    <?php endforeach; ?>
                    
                </div>
                
                <aside class="sidebar">
                    
                    <div class="widget widget-calendar">
                        <h3 class="widget-title">Kalender Kegiatan</h3>
                        <table class="calendar-table">
                            <caption>November 2025</caption>
                            <thead>
                                <tr>
                                    <th>Sn</th> <th>Sl</th> <th>Rb</th> <th>Km</th> <th>Jm</th> <th>Sb</th> <th>Mg</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="other-month">27</td><td class="other-month">28</td><td class="other-month">29</td><td class="other-month">30</td><td class="other-month">31</td><td>1</td><td>2</td>
                                </tr>
                                <tr>
                                    <td>3</td><td>4</td><td class="today">5</td><td>6</td><td>7</td><td>8</td><td>9</td>
                                </tr>
                                <tr>
                                    <td>10</td><td>11</td><td>12</td><td class="event"><a href="#" title="Kuliah Tamu">13</a></td><td>14</td><td>15</td><td>16</td>
                                </tr>
                                <tr>
                                    <td>17</td><td>18</td><td>19</td><td>20</td><td>21</td><td>22</td><td>23</td>
                                </tr>
                                <tr>
                                    <td>24</td><td class="event"><a href="#" title="Seminar AI">25</a></td><td>26</td><td>27</td><td>28</td><td>29</td><td>30</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="widget widget-recent-news">
                        <h3 class="widget-title">Berita Terbaru</h3>
                        <ul>
                            <?php 
                            // Ambil 3 berita terbaru untuk sidebar
                            $recent_news = array_slice($news_items, 0, 3);
                            foreach ($recent_news as $recent): 
                            ?>
                            <li>
                                <h4><a href="menu-detail-berita/detail-berita.php?slug=<?php echo htmlspecialchars($recent['slug']); ?>">
                                    <?php echo htmlspecialchars($recent['title']); ?>
                                </a></h4>
                                <span class="pub-source"><?php echo date('d M Y', strtotime($recent['created_at'])); ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="widget widget-categories">
                        <h3 class="widget-title">Arsip</h3>
                        <ul>
                            <li><a href="?year=2025">Arsip 2025</a></li>
                            <li><a href="?year=2024">Arsip 2024</a></li>
                            <li><a href="?year=2023">Arsip 2023</a></li>
                            <li><a href="?year=2022">Arsip 2022</a></li>
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

    <script src="assets/berita/js/script-berita.js"></script>

</body>
</html>