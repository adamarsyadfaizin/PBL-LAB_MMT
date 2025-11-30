<?php
// menu/menu-detail-berita/detail-berita.php
if (!isset($_SESSION)) session_start();

// PATH: Naik dua tingkat (../../) untuk mencapai root /
$root_prefix = "../../"; 
// PATH: Naik satu tingkat (../) untuk mencapai /menu/
$menu_prefix = "../";
$cache_buster = time(); // Untuk refresh CSS/JS

// Config files are in root, so use $root_prefix
require_once $root_prefix . 'config/db.php'; 
require_once $root_prefix . 'config/settings.php'; // PENTING: Panggil Settings CMS

// Components are in /menu/, so use $menu_prefix
require_once $menu_prefix . 'components/navbar.php';
require_once $menu_prefix . 'components/footer.php';
include $menu_prefix . 'components/floating_profile.php'; 


// Ambil Slug
$slug = $_GET['slug'] ?? '';
if (!$slug) {
    // Kembali ke /menu/berita.php
    header("Location: ../berita.php");
    exit();
}

// Query Berita
$stmt = $pdo->prepare("SELECT * FROM news WHERE slug = ? AND status = 'published'");
$stmt->execute([$slug]);
$news = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$news) {
    echo "<h1>Berita tidak ditemukan!</h1><a href='../berita.php'>Kembali</a>";
    exit();
}

// --- PERBAIKAN PATH GAMBAR ---
// Hapus '../' dari DB path, lalu tambahkan $root_prefix (../../) untuk menuju ke aset di root
$clean_img = str_replace('../', '', $news['cover_image'] ?? 'assets/images/default.jpg');
$final_img = $root_prefix . $clean_img;

// Panggil Floating Profile sebelum HTML
renderFloatingProfile(); 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($news['title']) ?> - Laboratorium MMT</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <link rel="stylesheet" href="<?= $root_prefix ?>assets/css/style.css?v=<?= $cache_buster ?>">
    <link rel="stylesheet" href="<?= $menu_prefix ?>components/navbar.css?v=<?= $cache_buster ?>">
    
    <link rel="stylesheet" href="assets/det-berita/css/style-detail-berita.css?v=<?= $cache_buster ?>">
    
    <style>
        /* Background untuk seluruh halaman dengan wallpaper.jpg */
        body {
            background: url('<?= $root_prefix ?>assets/images/wallpaper.jpg') center center/cover fixed no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
        }
        
        /* Efek transparansi halus untuk area konten utama */
        .main-content-area {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            position: relative;
            padding: 60px 0;
        }
        
        /* Container untuk memastikan konten tetap readable */
        .container {
            position: relative;
            z-index: 1;
        }
        
        /* Hero section styling */
        .hero {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.8)), 
                        url('<?= $final_img ?>') center center/cover no-repeat;
            height: 400px;
        }
        
        /* Penyesuaian Judul di Hero */
        .hero h1 {
            font-size: 36px;
            max-width: 900px;
            margin-bottom: 15px;
            line-height: 1.3;
            text-shadow: 0 2px 10px rgba(0,0,0,0.5);
        }
        
        .news-meta-hero {
            font-size: 16px;
            color: rgba(255,255,255,0.9);
            font-weight: 500;
        }
        
        .news-meta-hero i { margin-right: 5px; }
        
        /* Primary content area */
        .primary-content {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        /* Back link button */
        .back-link {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #333;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 6px;
            display: inline-block;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        
        .back-link:hover {
            background: rgba(255, 255, 255, 0.95);
            transform: translateY(-2px);
        }
        
        /* Article content */
        .article-content {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(8px);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            padding: 30px;
        }
        
        /* Article banner image */
        .detail-article-banner {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        /* News body content */
        .news-body {
            line-height: 1.8;
            color: #333;
            font-size: 16px;
        }
        
        /* Article meta detail */
        .article-meta-detail {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(5px);
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 20px;
            margin-top: 40px;
        }
        
        /* Social share buttons */
        .social-share a {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-left: 10px;
            transition: all 0.3s ease;
        }
        
        .social-share a:hover {
            background: rgba(255, 255, 255, 0.95);
            transform: translateY(-2px);
        }
        
        /* Sidebar widgets */
        .sidebar .widget {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            padding: 25px;
            margin-bottom: 30px;
        }
        
        /* Related news list */
        .sidebar ul li {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(5px);
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            margin-bottom: 15px;
            padding: 15px;
            transition: all 0.3s ease;
        }
        
        .sidebar ul li:hover {
            background: rgba(255, 255, 255, 0.9);
            transform: translateX(5px);
        }
        
        @media (max-width: 768px) {
            .hero { height: 300px; }
            .hero h1 { font-size: 24px; }
            .detail-article-banner { height: 250px; }
        }
    </style>
</head>
<body id="top">

    <?php renderNavbar('berita', $root_prefix, $site_config); ?>

    <header class="hero">
        <div class="container" style="text-align: center; position: relative; z-index: 2;">
            <h1><?= htmlspecialchars($news['title']) ?></h1>
            <div class="news-meta-hero">
                <i class="fas fa-calendar-alt"></i> <?= date('d F Y', strtotime($news['created_at'])) ?> 
                &nbsp;&nbsp;|&nbsp;&nbsp; 
                <i class="fas fa-tag"></i> <?= htmlspecialchars($news['category'] ?? 'Umum') ?>
            </div>
        </div>
    </header>

    <main class="main-content-area">
        <div class="container">
            
            <div class="primary-content">
                <a href="../berita.php" class="btn-secondary back-link">&larr; Kembali ke Daftar Berita</a>
                
                <article class="article-content">
                    <img src="<?= $final_img ?>" alt="<?= htmlspecialchars($news['title']) ?>" class="detail-article-banner">

                    <div class="news-body">
                        <?= nl2br($news['content']) ?>
                    </div>
                    
                    <div class="article-meta-detail" style="margin-top: 40px;">
                        <div class="social-share">
                            <span>Bagikan:</span>
                            <?php 
                            $share_title = urlencode($news['title']);
                            $share_url = urlencode((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
                            ?>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $share_url ?>" target="_blank" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://twitter.com/intent/tweet?url=<?= $share_url ?>&text=<?= $share_title ?>" target="_blank" title="Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= $share_url ?>&title=<?= $share_title ?>" target="_blank" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </article>
            </div>

            <aside class="sidebar">
                <div class="widget">
                    <h3 class="widget-title">Berita Lainnya</h3>
                    <ul style="list-style: none; padding: 0;">
                        <?php
                        // Ambil 3 berita lain acak/terbaru
                        $stmt_other = $pdo->query("SELECT title, slug, created_at FROM news WHERE slug != '$slug' AND status = 'published' ORDER BY created_at DESC LIMIT 3");
                        
                        if ($stmt_other->rowCount() > 0):
                            while($other = $stmt_other->fetch()):
                        ?>
                        <li style="margin-bottom: 15px;">
                            <a href="detail-berita.php?slug=<?= $other['slug'] ?>" style="text-decoration: none; color: #333; font-weight: 600;">
                                <?= htmlspecialchars($other['title']) ?>
                            </a>
                            <span style="display: block; font-size: 12px; color: #888; margin-top: 5px;">
                                <?= date('d M Y', strtotime($other['created_at'])) ?>
                            </span>
                        </li>
                        <?php 
                            endwhile; 
                        else:
                            echo "<li>Tidak ada berita terkait lainnya.</li>";
                        endif;
                        ?>
                    </ul>
                </div>
            </aside>

        </div>
    </main>

    <?php 
    // Panggil Footer dengan prefix yang sesuai
    renderFooter($root_prefix, $site_config); 
    ?>

    <a href="#top" id="scrollTopBtn" class="scroll-top-btn" aria-label="Kembali ke atas">&uarr;</a>

    <script src="<?= $root_prefix ?>assets/js/navbar.js?v=<?= $cache_buster ?>"></script>
    <script src="<?= $root_prefix ?>assets/js/scrolltop.js?v=<?= $cache_buster ?>"></script>
    <script src="assets/det-berita/js/script-detail-berita.js?v=<?= $cache_buster ?>"></script>
</body>
</html>