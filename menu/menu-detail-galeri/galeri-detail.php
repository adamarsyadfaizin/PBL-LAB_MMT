<?php
// menu/menu-detail-galeri/galeri-detail.php
if (!isset($_SESSION)) session_start();
require_once '../../config/db.php'; 
require_once '../../config/settings.php'; // CMS Setting
include '../components/floating_profile.php'; 
renderFloatingProfile();
require_once '../components/navbar.php';

// Ambil ID
$media_id = $_GET['id'] ?? '';
if (!$media_id || !is_numeric($media_id)) {
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
// Tambahkan ../../ agar mundur 2 langkah ke root
$final_url = $is_link ? $media['url'] : "../../" . str_replace('../', '', $media['url']);

// Data Tampilan
$title = htmlspecialchars($media['caption'] ?? '(Tanpa Judul)');
$type = htmlspecialchars(ucfirst($media['type'] ?? '-'));
$created_at = !empty($media['created_at']) ? date('d F Y', strtotime($media['created_at'])) : '-';
$deskripsi = htmlspecialchars($media['deskripsi'] ?? '');
$event_name = htmlspecialchars($media['event_name'] ?? '');

// Hero Background (Gunakan gambar galeri jika tipe foto, jika video pakai default)
$hero_bg = ($media['type'] == 'foto' && !$is_link) ? $final_url : '../../' . ($site_config['hero_image_path'] ?? 'assets/images/hero.jpg');
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

    <link rel="stylesheet" href="../../assets/css/style.css">

    <link rel="stylesheet" href="assets/det-gel/css/style-galeri-detail.css">

    <style>
        /* Hero Dinamis */
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.8)), 
                        url('<?= $hero_bg ?>') center center/cover no-repeat;
            height: 400px !important;
        }
        .hero h1 { margin-bottom: 10px; text-shadow: 0 2px 10px rgba(0,0,0,0.5); }
        
        /* Meta di Hero */
        .hero-meta {
            font-size: 16px; color: rgba(255,255,255,0.9); font-weight: 500;
        }
    </style>
</head>

<body id="top">

    <?php renderNavbar('galeri'); ?>

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
                                <iframe width="100%" height="500" src="<?= htmlspecialchars($media['url']); ?>" frameborder="0" allowfullscreen style="border-radius: 10px;"></iframe>
                            <?php else: ?>
                                <video controls width="100%" style="border-radius: 10px; background: #000;">
                                    <source src="<?= htmlspecialchars($final_url); ?>" type="video/mp4">
                                    Browser Anda tidak mendukung pemutaran video.
                                </video>
                            <?php endif; ?>
                        <?php else: ?>
                            <img src="<?= htmlspecialchars($final_url); ?>" alt="<?= htmlspecialchars($media['caption']); ?>" class="detail-article-banner">
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
                            <a href="#" aria-label="Bagikan ke Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" aria-label="Bagikan ke Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="#" aria-label="Bagikan ke LinkedIn"><i class="fab fa-linkedin-in"></i></a>
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
    require_once '../components/footer.php';
    renderFooter();
    ?>

    <a href="#top" id="scrollTopBtn" class="scroll-top-btn" aria-label="Kembali ke atas">&uarr;</a>

    <script src="../../assets/js/navbar.js"></script>
    <script src="assets/det-gel/js/script-galeri-detail.js"></script>

</body>
</html>