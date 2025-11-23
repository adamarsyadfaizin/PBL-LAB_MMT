<?php
// menu/menu-detail-berita/detail-berita.php
if (!isset($_SESSION)) session_start();
require_once '../../config/db.php'; 
require_once '../../config/settings.php'; // <-- PENTING: Panggil Settings CMS
include '../components/floating_profile.php'; 
renderFloatingProfile();
require_once '../components/navbar.php';

// Ambil Slug
$slug = $_GET['slug'] ?? '';
if (!$slug) {
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
$clean_img = str_replace('../', '', $news['cover_image']);
$final_img = "../../" . $clean_img;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($news['title']) ?> - Laboratorium MMT</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <link rel="stylesheet" href="../../assets/css/style.css">
    
    <link rel="stylesheet" href="assets/det-berita/css/style-detail-berita.css">
    
    <style>
        .hero {
            /* Menggunakan gambar berita sebagai background hero */
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
        
        @media (max-width: 768px) {
            .hero { height: 300px; }
            .hero h1 { font-size: 24px; }
        }
    </style>
</head>
<body id="top">

    <?php renderNavbar('berita'); ?>

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
                            <a href="#" title="Facebook">f</a>
                            <a href="#" title="Twitter">t</a>
                            <a href="#" title="LinkedIn">l</a>
                        </div>
                    </div>
                </article>
            </div>

            <aside class="sidebar">
                <div class="widget">
                    <h3 class="widget-title">Berita Lainnya</h3>
                    <ul style="list-style: none; padding: 20px;">
                        <?php
                        // Ambil 3 berita lain acak/terbaru
                        $stmt_other = $pdo->query("SELECT title, slug, created_at FROM news WHERE slug != '$slug' AND status = 'published' ORDER BY created_at DESC LIMIT 3");
                        while($other = $stmt_other->fetch()):
                        ?>
                        <li style="margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 15px;">
                            <a href="detail-berita.php?slug=<?= $other['slug'] ?>" style="text-decoration: none; color: #333; font-weight: 600;">
                                <?= htmlspecialchars($other['title']) ?>
                            </a>
                            <span style="display: block; font-size: 12px; color: #888; margin-top: 5px;">
                                <?= date('d M Y', strtotime($other['created_at'])) ?>
                            </span>
                        </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </aside>

        </div>
    </main>

    <?php 
    require_once '../components/footer.php';
    renderFooter(); 
    ?>

    <a href="#top" id="scrollTopBtn" class="scroll-top-btn" aria-label="Kembali ke atas">&uarr;</a>

    <script src="../../assets/js/navbar.js"></script>
    <script src="assets/det-berita/js/script-detail-berita.js"></script>
</body>
</html>