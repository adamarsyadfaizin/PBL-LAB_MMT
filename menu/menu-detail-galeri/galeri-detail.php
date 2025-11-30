<?php
// menu/menu-detail-galeri/galeri-detail.php
if (!isset($_SESSION)) session_start();

// PATH: Naik dua tingkat (../../) untuk mencapai root /
$root_prefix = "../../"; 
// PATH: Naik satu tingkat (../) untuk mencapai /menu/
$menu_prefix = "../";
$cache_buster = time(); // Untuk refresh CSS/JS

// Config files are in root, so use $root_prefix
require_once $root_prefix . 'config/db.php'; 
require_once $root_prefix . 'config/settings.php'; // CMS Setting

// Components are in /menu/, so use $menu_prefix
require_once $menu_prefix . 'components/navbar.php';
require_once $menu_prefix . 'components/footer.php';
include $menu_prefix . 'components/floating_profile.php'; 

// Ambil ID
$media_id = $_GET['id'] ?? '';
if (!$media_id || !is_numeric($media_id)) {
    // Kembali ke /menu/galeri.php
    header('Location: ../galeri.php');
    exit;
}

// Query Data
$sql = "SELECT * FROM media_assets WHERE id = ? LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([$media_id]);
$media = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$media) {
    die("Media tidak ditemukan atau telah dihapus.");
}

// Fix Path Gambar/Video
$is_link = filter_var($media['url'], FILTER_VALIDATE_URL);
// Tambahkan $root_prefix (../../) agar mundur 2 langkah ke root
$final_url = $is_link ? $media['url'] : $root_prefix . str_replace('../', '', $media['url']);

// Data Tampilan
$title = htmlspecialchars($media['caption'] ?? '(Tanpa Judul)');
$type = htmlspecialchars(ucfirst($media['type'] ?? '-'));
$created_at = !empty($media['created_at']) ? date('d F Y', strtotime($media['created_at'])) : '-';
$deskripsi = htmlspecialchars($media['deskripsi'] ?? '');
$event_name = htmlspecialchars($media['event_name'] ?? '');

// Hero Background (Menggunakan $root_prefix untuk default image)
$default_hero = $root_prefix . ($site_config['hero_image_path'] ?? 'assets/images/hero.jpg');
$hero_bg = ($media['type'] == 'foto' && !$is_link) ? $final_url : $default_hero;

// Panggil Floating Profile sebelum HTML
renderFloatingProfile();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Media: <?= $title; ?> - POLINEMA</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="stylesheet" href="<?= $root_prefix ?>assets/css/style.css?v=<?= $cache_buster ?>">
    <link rel="stylesheet" href="<?= $menu_prefix ?>components/navbar.css?v=<?= $cache_buster ?>">

    <link rel="stylesheet" href="assets/det-gel/css/style-galeri-detail.css?v=<?= $cache_buster ?>">

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
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.8)), 
                        url('<?= htmlspecialchars($hero_bg) ?>') center center/cover no-repeat;
            height: 400px !important;
        }
        
        .hero h1 { 
            margin-bottom: 10px; 
            text-shadow: 0 2px 10px rgba(0,0,0,0.5); 
        }
        
        /* Meta di Hero */
        .hero-meta {
            font-size: 16px; 
            color: rgba(255,255,255,0.9); 
            font-weight: 500;
        }
        
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
        
        /* Media content styling */
        .media-content {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(8px);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        /* Media elements */
        .media-content img,
        .media-content video,
        .media-content iframe {
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            max-width: 100%;
            height: auto;
        }
        
        .media-content iframe {
            min-height: 500px;
        }
        
        /* Article meta detail */
        .article-meta-detail {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(8px);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 25px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        /* Meta info */
        .meta-info {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .meta-info span {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(5px);
            border-radius: 6px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 8px 15px;
            font-size: 14px;
        }
        
        /* Social share buttons */
        .social-share {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .social-share span {
            font-weight: 600;
            color: #333;
        }
        
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
            transition: all 0.3s ease;
        }
        
        .social-share a:hover {
            background: rgba(255, 255, 255, 0.95);
            transform: translateY(-2px);
        }
        
        /* Media description */
        .media-description {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(8px);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .media-description h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 20px;
        }
        
        .media-description p {
            line-height: 1.7;
            color: #333;
            margin: 0;
        }
        
        /* Back link button */
        .back-link {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #333;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 6px;
            display: inline-block;
            transition: all 0.3s ease;
            font-weight: 600;
        }
        
        .back-link:hover {
            background: rgba(255, 255, 255, 0.95);
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .hero { height: 300px; }
            .hero h1 { font-size: 24px; }
            .media-content iframe { min-height: 300px; }
            .article-meta-detail { flex-direction: column; align-items: flex-start; }
            .social-share { margin-top: 15px; }
        }
    </style>
</head>

<body id="top">

    <?php renderNavbar('galeri', $root_prefix, $site_config); ?>

    <main>
        <section class="hero">
            <div class="container" style="text-align: center; z-index: 2;">
                <h1><?= $title; ?></h1>
                <div class="hero-meta">
                    <i class="fas fa-tag"></i> <?= $type ?> 
                    &nbsp;|&nbsp; 
                    <i class="fas fa-calendar-alt"></i> <?= $created_at ?>
                </div>
            </div>
        </section>

        <div class="main-content-area">
            <div class="container">
                <div class="primary-content">

                    <div class="media-content">
                        <?php if ($media['type'] === 'video'): ?>
                            <?php if (strpos($media['url'], 'youtube.com/embed') !== false): ?>
                                <iframe width="100%" height="500" src="<?= htmlspecialchars($media['url']); ?>" frameborder="0" allowfullscreen loading="lazy"></iframe>
                            <?php else: ?>
                                <video controls width="100%" style="background: #000;">
                                    <source src="<?= htmlspecialchars($final_url); ?>" type="video/mp4">
                                    Browser Anda tidak mendukung pemutaran video.
                                </video>
                            <?php endif; ?>
                        <?php else: ?>
                            <img src="<?= htmlspecialchars($final_url); ?>" alt="<?= htmlspecialchars($media['caption']); ?>" class="detail-article-banner" loading="lazy">
                        <?php endif; ?>
                    </div>

                    <div class="article-meta-detail">
                        <div class="meta-info">
                            <?php if (!empty($event_name)): ?>
                                <span><strong>Acara:</strong> <?= $event_name; ?></span>
                            <?php endif; ?>
                            <span><strong>Dilihat:</strong> <?= rand(50, 500) ?> kali</span>
                        </div>
                        
                        <div class="social-share">
                            <span>Bagikan:</span>
                            <?php 
                            $share_title = urlencode($title);
                            $share_url = urlencode((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
                            ?>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $share_url ?>" target="_blank" aria-label="Bagikan ke Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://twitter.com/intent/tweet?url=<?= $share_url ?>&text=<?= $share_title ?>" target="_blank" aria-label="Bagikan ke Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= $share_url ?>&title=<?= $share_title ?>" target="_blank" aria-label="Bagikan ke LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>

                    <?php if(!empty($deskripsi)): ?>
                    <div class="media-description">
                        <h3>Deskripsi</h3>
                        <p><?= nl2br($deskripsi); ?></p>
                    </div>
                    <?php endif; ?>

                    <a href="../galeri.php" class="btn-secondary back-link">&larr; Kembali ke Galeri</a>

                </div>
            </div>
        </div>
    </main>

    <?php 
    // Panggil Footer dengan prefix yang sesuai
    renderFooter($root_prefix, $site_config);
    ?>

    <a href="#top" id="scrollTopBtn" class="scroll-top-btn" aria-label="Kembali ke atas">&uarr;</a>

    <script src="<?= $root_prefix ?>assets/js/navbar.js?v=<?= $cache_buster ?>"></script>
    <script src="<?= $root_prefix ?>assets/js/scrolltop.js?v=<?= $cache_buster ?>"></script>
    <script src="assets/det-gel/js/script-galeri-detail.js?v=<?= $cache_buster ?>"></script>

</body>
</html>