<?php
// FILE: beranda.php (root)

// ==================================================
// BOOTSTRAP SYSTEM
// ==================================================
if (!isset($_SESSION)) session_start();

require_once __DIR__ . "/config/db.php";        // Koneksi database
require_once __DIR__ . "/config/settings.php";  // Global site config

// Component
require_once __DIR__ . "/menu/components/navbar.php";
require_once __DIR__ . "/menu/components/footer.php";

// Utilities
$path_prefix = "";
$cache_buster = time();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - <?= htmlspecialchars($site_config['site_name']) ?></title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="menu/components/navbar.css?v=<?= $cache_buster ?>">
    <link rel="stylesheet" href="assets/css/style.css?v=<?= $cache_buster ?>">

    <style>
        /* Background untuk seluruh halaman dengan wallpaper.jpg */
        body {
            background: url('assets/images/wallpaper.jpg') center center/cover fixed no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
        }
        
        /* Hero section styling */
        .hero {
            background:
                linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.7)),
                url('<?= htmlspecialchars($site_config['hero_image_path']) ?>') 
                center/cover no-repeat;
            position: relative;
        }
        
        /* Highlight sections dengan efek transparansi */
        .highlight-section {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            position: relative;
            padding: 80px 0;
        }
        
        /* Section dengan background light */
        .highlight-section.bg-light {
            background: rgba(255, 255, 255, 0.05);
        }
        
        /* Container untuk memastikan konten tetap readable */
        .container {
            position: relative;
            z-index: 1;
        }
        
        /* Project dan News Cards */
        .home-project-card, .home-news-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .home-project-card:hover, .home-news-card:hover {
            background: rgba(255, 255, 255, 0.9);
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.15);
        }
        
        /* Card thumbnails */
        .card-thumbnail {
            position: relative;
            overflow: hidden;
        }
        
        .card-thumbnail img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .home-project-card:hover .card-thumbnail img,
        .home-news-card:hover .card-thumbnail img {
            transform: scale(1.05);
        }
        
        /* Category badges */
        .card-category {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            position: absolute;
            top: 10px;
            left: 10px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        /* Card content */
        .card-content {
            padding: 20px;
        }
        
        .card-title {
            color: #333;
            margin-bottom: 10px;
            line-height: 1.4;
        }
        
        .card-date, .card-year {
            color: #666;
            font-size: 14px;
            margin-bottom: 8px;
            display: block;
        }
        
        .card-excerpt {
            color: #666;
            line-height: 1.6;
            margin: 0;
        }
        
        /* Section titles - DIUBAH MENJADI PUTIH */
        .section-title {
            color: #ffffff;
            text-align: center;
            margin-bottom: 50px;
            font-size: 2.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        
        /* CTA buttons */
        .section-cta {
            text-align: center;
            margin-top: 40px;
        }
        
        .btn {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #333;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            background: rgba(255, 255, 255, 0.95);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body id="top">

<?php renderNavbar('beranda', $path_prefix, $site_config); ?>

<main>

    <!-- ===============================
         HERO SECTION
    ================================ -->
    <section class="hero" 
        style="background:
            linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.7)),
            url('<?= htmlspecialchars($site_config['hero_image_path']) ?>') 
            center/cover no-repeat;">
        
        <div class="container hero-inner">

            <?php 
            $title = explode(" ", $site_config['hero_title'], 2);
            ?>
            
            <h1>
                <span class="hero-subtitle"><?= htmlspecialchars($title[0] ?? '') ?></span>
                <span class="hero-title-main"><?= htmlspecialchars($title[1] ?? '') ?></span>
            </h1>

            <p><?= htmlspecialchars($site_config['hero_description']) ?></p>

            <a href="menu/profil.php" class="btn">Selengkapnya</a>
        </div>
    </section>


    <!-- ===============================
         HIGHLIGHT PROYEK
    ================================ -->
    <section class="highlight-section">
        <div class="container">
            <h2 class="section-title">Highlight Proyek</h2>

            <div class="highlight-grid">

                <?php
                $projects = $pdo->query("
                    SELECT p.*, c.name AS category_name 
                    FROM projects p
                    LEFT JOIN categories c ON c.id = p.category_id
                    WHERE p.status = 'published'
                    ORDER BY p.created_at DESC
                    LIMIT 3
                ")->fetchAll(PDO::FETCH_ASSOC);

                foreach ($projects as $p):
                    $img = str_replace('../', '', $p['cover_image']);
                ?>
                <a href="menu/menu-proyek-detail/detail-proyek.php?slug=<?= rawurlencode($p['slug']) ?>" class="home-project-card">
                    <div class="card-thumbnail">
                        <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($p['title']) ?>">
                        <span class="card-category"><?= htmlspecialchars($p['category_name']) ?></span>
                    </div>
                    <div class="card-content">
                        <h4 class="card-title"><?= htmlspecialchars($p['title']) ?></h4>
                        <span class="card-year"><?= htmlspecialchars($p['year']) ?></span>
                    </div>
                </a>
                <?php endforeach; ?>

            </div>

            <div class="section-cta">
                <a href="menu/proyek.php" class="btn">Lihat Semua Proyek</a>
            </div>
        </div>
    </section>


    <!-- ===============================
         BERITA & KEGIATAN
    ================================ -->
    <section class="highlight-section bg-light">
        <div class="container">
            <h2 class="section-title">Berita & Kegiatan Terbaru</h2>

            <div class="highlight-grid">

                <?php
                $news = $pdo->query("
                    SELECT * FROM news
                    WHERE status = 'published'
                    ORDER BY created_at DESC
                    LIMIT 3
                ")->fetchAll(PDO::FETCH_ASSOC);

                foreach ($news as $n):
                    $img = str_replace('../', '', $n['cover_image']);
                ?>
                <a href="menu/menu-detail-berita/detail-berita.php?slug=<?= rawurlencode($n['slug']) ?>" class="home-news-card">
                    <div class="card-thumbnail">
                        <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($n['title']) ?>">
                    </div>
                    <div class="card-content">
                        <span class="card-date"><?= date('d F Y', strtotime($n['created_at'])) ?></span>
                        <h4 class="card-title"><?= htmlspecialchars($n['title']) ?></h4>
                        <p class="card-excerpt"><?= htmlspecialchars($n['summary']) ?></p>
                    </div>
                </a>
                <?php endforeach; ?>

            </div>

            <div class="section-cta">
                <a href="menu/berita.php" class="btn">Lihat Semua Berita & Kegiatan</a>
            </div>
        </div>
    </section>

</main>

<?php renderFooter($path_prefix, $site_config); ?>

<a href="#top" id="scrollTopBtn" class="scroll-top-btn">&uarr;</a>

<script src="assets/js/navbar.js?v=<?= $cache_buster ?>"></script>
<script src="assets/js/scrolltop.js?v=<?= $cache_buster ?>"></script>

</body>
</html>