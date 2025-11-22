<?php
if (!isset($_SESSION)) session_start();

// 1. Panggil koneksi database
require_once 'config/db.php';

// 2. Panggil settingan CMS (Logo, Hero, dll) yang sudah kita buat tadi
require_once 'config/settings.php'; 

// Include komponen lain
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
    
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        .hero {
            /* Kita gunakan PHP untuk mencetak URL gambar dari database */
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.7)), 
                        url('<?= htmlspecialchars($site_config['hero_image_path']) ?>') center center/cover no-repeat;
            
            /* Properti lain tetap sama seperti di style.css */
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
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 { font-size: 32px; }
            .hero { height: 400px; }
        }
    </style>
</head>
<body id="top">

    <?php
    // Render navbar (pastikan navbar.php juga sudah diupdate untuk logo dinamis)
    renderNavbar('beranda');
    ?>

    <main>

       <section class="hero">
            <div class="container">
                
                <?php
                $full_title = $site_config['hero_title']; // Ambil dari database
                $parts = explode(' ', $full_title, 2); // Pecah jadi 2 bagian berdasarkan spasi pertama
                
                $first_word = $parts[0] ?? ''; // "LABORATORIUM"
                $rest_words = $parts[1] ?? ''; // "MOBILE AND MULTIMEDIA TECH"
                ?>

                <h1>
                    <span class="hero-subtitle"><?= htmlspecialchars($first_word) ?></span>
                    <span class="hero-title-main"><?= htmlspecialchars($rest_words) ?></span>
                </h1>
                
                <p>
                    <?= htmlspecialchars($site_config['hero_description']) ?>
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
                        $cat_stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
                        $cat_stmt->execute([$project['category_id']]);
                        $category = $cat_stmt->fetchColumn() ?: 'Uncategorized';
                        $slugUrl = rawurlencode($project['slug']);
                        
                        // Perbaikan path gambar proyek (menghapus ../ jika ada)
                        $clean_img = str_replace('../', '', $project['cover_image']);
                    ?>
                    <a href="<?php echo 'menu/menu-proyek-detail/detail-proyek.php?slug=' . $slugUrl; ?>" class="home-project-card">
                        <div class="card-thumbnail">
                            <img src="<?php echo htmlspecialchars($clean_img); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>">
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
                        // Perbaikan path gambar berita
                        $clean_img_news = str_replace('../', '', $item['cover_image']);
                    ?>
                    <a href="<?php echo 'menu/menu-detail-berita/detail-berita.php?slug=' . $slugNews; ?>" class="home-news-card">
                        <div class="card-thumbnail">
                            <img src="<?php echo htmlspecialchars($clean_img_news); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
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
    require_once 'menu/components/footer.php';
    renderFooter();
    ?>

    <a href="#top" id="scrollTopBtn" class="scroll-top-btn" aria-label="Kembali ke atas">
        &uarr;
    </a>

    <script src="assets/js/navbar.js"></script>
</body>
</html>