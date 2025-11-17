<?php
if (!isset($_SESSION)) session_start();
include 'menu/components/floating_profile.php'; 
renderFloatingProfile();
require_once 'config/db.php';
require_once 'menu/components/navbar.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - Laboratorium Mobile and Multimedia Tech POLINEMA</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    
    <!-- GUNAKAN CSS UTAMA YANG SAMA -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        /* HANYA STYLE TAMBAHAN KHUSUS UNTUK BERANDA */
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.5)), 
                        url('assets/images/hero-building.jpg') center center/cover no-repeat;
            height: 500px;
            display: flex;
            align-items: center;
            position: relative;
            color: var(--color-white);
        }
        .hero .container {
            position: relative;
            z-index: 2;
            text-align: center;
        }
        .hero h1 {
            font-size: 48px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.4);
            margin-bottom: 20px;
            line-height: 1.2;
        }
        .hero p {
            font-size: 18px;
            margin-bottom: 30px;
            line-height: 1.7;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        /* Styles untuk section beranda */
        .highlight-section {
            padding: 80px 0;
        }
        .bg-light {
            background-color: var(--color-bg-light);
        }
        .section-title {
            font-size: 28px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 40px;
            text-transform: uppercase;
        }
        .highlight-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-bottom: 40px;
        }
        .home-project-card, .home-news-card {
            text-decoration: none;
            color: inherit;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            background: var(--color-white);
            display: block;
        }
        .home-project-card:hover, .home-news-card:hover {
            transform: translateY(-5px);
        }
        .card-thumbnail {
            position: relative;
        }
        .card-thumbnail img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            display: block;
        }
        .card-category {
            position: absolute;
            top: 10px;
            left: 10px;
            background: var(--color-primary);
            color: var(--color-white);
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        .card-content {
            padding: 20px;
        }
        .card-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--color-text);
        }
        .card-year, .card-date {
            color: var(--color-text-secondary);
            font-size: 14px;
            display: block;
            margin-bottom: 8px;
        }
        .card-excerpt {
            color: var(--color-text-darker);
            font-size: 14px;
            line-height: 1.5;
        }
        .section-cta {
            text-align: center;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .highlight-grid {
                grid-template-columns: 1fr;
            }
            .hero h1 {
                font-size: 32px;
            }
            .hero {
                height: 400px;
            }
        }
    </style>
</head>
<body id="top">

    <?php
    // NAVBAR REUSABLE - SAMA PERSIS DENGAN HALAMAN LAIN
    renderNavbar('beranda');
    ?>

    <main>

        <section class="hero">
            <div class="container">
                <h1>LABORATORIUM MOBILE<br>AND MULTIMEDIA TECH</h1>
                <p>
                    Laboratorium Mobile & Multimedia Tech POLINEMA adalah pusat
                    pengembangan karya inovatif di bidang UI/UX, Game, AR/VR.
                </p>
                <a href="menu/profil.php" class="btn">Selengkapnya</a>
            </div>
        </section>
        
        <section class="highlight-section">
            <div class="container">
                <h2 class="section-title">Highlight Proyek</h2>
                
                <div class="highlight-grid">
                    <?php
                    $stmt = $pdo->query("SELECT * FROM projects WHERE status = 'published' ORDER BY created_at DESC LIMIT 3");
                    while ($project = $stmt->fetch(PDO::FETCH_ASSOC)):
                        // Ambil kategori
                        $cat_stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
                        $cat_stmt->execute([$project['category_id']]);
                        $category = $cat_stmt->fetchColumn() ?: 'Uncategorized';

                        // safe slug/url
                        $slugUrl = rawurlencode($project['slug']);
                    ?>
                    <a href="<?php echo 'menu/menu-proyek-detail/detail-proyek.php?slug=' . $slugUrl; ?>" class="home-project-card">
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
                        $slugNews = rawurlencode($item['slug']);
                    ?>
                    <a href="<?php echo 'menu/menu-detail-berita/detail-berita.php?slug=' . $slugNews; ?>" class="home-news-card">
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

    <?php
    // Include footer
    require_once 'menu/components/footer.php';
    renderFooter();
    ?>

    <a href="#top" id="scrollTopBtn" class="scroll-top-btn" aria-label="Kembali ke atas">
        &uarr;
    </a>

    <!-- Include JavaScript untuk navbar -->
    <script src="assets/js/navbar.js"></script>
</body>
</html>
