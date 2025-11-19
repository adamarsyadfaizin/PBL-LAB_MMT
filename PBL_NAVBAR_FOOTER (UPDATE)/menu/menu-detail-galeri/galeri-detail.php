<?php
// ============================================================================
// File: galeri-detail.php
// Deskripsi: Halaman detail media (gambar / video / animasi) dari tabel media_assets
// ============================================================================
if (!isset($_SESSION)) session_start();
include '../components/floating_profile.php'; 
renderFloatingProfile();
require_once '../../config/db.php';
require_once '../components/navbar.php';

// Ambil ID media dari parameter URL
$media_id = $_GET['id'] ?? '';
if (!$media_id || !is_numeric($media_id)) {
    header('Location: ../galeri.php');
    exit;
}

// Ambil data media dari database
$sql = "SELECT * FROM media_assets WHERE id = ? LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([$media_id]);
$media = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$media) {
    die("Media tidak ditemukan atau telah dihapus.");
}

// Siapkan data aman untuk ditampilkan
$title = htmlspecialchars($media['caption'] ?? '(Tanpa Judul)');
$type = htmlspecialchars(ucfirst($media['type'] ?? '-'));
$url = htmlspecialchars($media['url'] ?? '');
$created_at = !empty($media['created_at']) ? date('d F Y', strtotime($media['created_at'])) : '-';
$deskripsi = htmlspecialchars($media['deskripsi'] ?? '');
$event_name = htmlspecialchars($media['event_name'] ?? '');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Media: <?= $title; ?> - POLINEMA</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap"
        rel="stylesheet">

    <!-- CSS Global -->
    <link rel="stylesheet" href="../../assets/css/style.css">

    <!-- CSS Khusus Halaman Galeri Detail -->
    <link rel="stylesheet" href="assets/det-gel/css/style-galeri-detail.css">

    <style>
        
        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.5)),
                url('../../assets/images/hero.jpg') center center/cover no-repeat;
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

        /* Media Display */
        .detail-article-banner {
            width: 100%;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
        }

        /* Deskripsi */
        .media-description {
            font-size: 16px;
            line-height: 1.6;
            color: #444;
            margin-top: 15px;
        }

        /* Meta Info */
        .article-meta-detail {
            margin-bottom: 20px;
        }

        .article-meta-detail .meta-info {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            font-size: 15px;
            color: #555;
        }

        .article-meta-detail strong {
            color: var(--color-primary);
        }

        /* Button Back */
        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
        }
    </style>
</head>

<body id="top">

    <?php renderNavbar('galeri'); ?>

    <main>

        <!-- Hero -->
        <section class="hero">
            <div class="container">
                <h1><?= $title; ?></h1>
            </div>
        </section>

        <!-- Konten Utama -->
        <div class="main-content-area">
            <div class="container">
                <div class="primary-content">

                    <!-- Media -->
                    <!-- Contoh di galeri-detail.php -->
                    <?php if ($media['type'] === 'video'): ?>
                        <?php if (strpos($media['url'], 'youtube.com/embed') !== false): ?>
                            <iframe width="100%" height="400" src="<?php echo htmlspecialchars($media['url']); ?>"
                                frameborder="0" allowfullscreen>
                            </iframe>
                        <?php else: ?>
                            <!-- Video lokal -->
                            <video controls width="100%" poster="assets/images/video-thumb.jpg">
                                <source src="<?php echo htmlspecialchars($media['url']); ?>" type="video/mp4">
                                Browser Anda tidak mendukung pemutaran video.
                            </video>
                        <?php endif; ?>
                    <?php else: ?>
                        <img src="<?php echo htmlspecialchars($media['url']); ?>"
                            alt="<?php echo htmlspecialchars($media['caption']); ?>">
                    <?php endif; ?>

                    <!-- Informasi Media -->
                    <div class="article-meta-detail">
                        <div class="meta-info">
                            <span><strong>Jenis:</strong> <?= $type; ?></span>
                            <?php if (!empty($event_name)): ?>
                                <span><strong>Acara:</strong> <?= $event_name; ?></span>
                            <?php endif; ?>
                            <span><strong>Tanggal:</strong> <?= $created_at; ?></span>
                        </div>
                    </div>

                    <!-- Tombol Kembali -->
                    <a href="../galeri.php" class="btn btn-secondary back-link">&larr; Kembali ke Galeri</a>

                </div>
            </div>
        </div>

    </main>

    <?php
    require_once '../components/footer.php';
    renderFooter();
    ?>

    <a href="#top" id="scrollTopBtn" class="scroll-top-btn" aria-label="Kembali ke atas">&uarr;</a>

    <!-- Script -->
    <script src="../../assets/js/navbar.js"></script>
    <script src="assets/det-gel/js/script-galeri-detail.js"></script>

</body>

</html>