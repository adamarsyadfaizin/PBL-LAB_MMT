<?php
// menu/menu-detail-berita/detail-berita.php
if (!isset($_SESSION)) session_start();
require_once '../../config/db.php'; // Mundur 2 langkah untuk config
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
// Bersihkan path dari '../' bawaan database lama, lalu tambahkan '../../'
$clean_img = str_replace('../', '', $news['cover_image']);
$final_img = "../../" . $clean_img;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($news['title']) ?> - Laboratorium MMT</title>
    
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Style Sederhana untuk Detail Berita */
        .hero {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('<?= $final_img ?>');
            background-size: cover;
            background-position: center;
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            padding: 20px;
        }
        .hero h1 { font-size: 32px; max-width: 800px; margin-bottom: 10px; }
        .news-meta { font-size: 14px; opacity: 0.9; }
        
        .content-area { max-width: 800px; margin: 50px auto; padding: 0 20px; line-height: 1.8; color: #333; }
        .content-area img { max-width: 100%; height: auto; margin: 20px 0; border-radius: 8px; }
        
        .btn-back {
            display: inline-block;
            padding: 10px 20px;
            background: #eee;
            color: #333;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 30px;
            font-weight: bold;
        }
        .btn-back:hover { background: #ddd; }
    </style>
</head>
<body>

    <?php renderNavbar('berita'); ?>

    <header class="hero">
        <div>
            <h1><?= htmlspecialchars($news['title']) ?></h1>
            <div class="news-meta">
                <i class="fas fa-calendar"></i> <?= date('d F Y', strtotime($news['created_at'])) ?> 
                &nbsp;|&nbsp; 
                <i class="fas fa-tag"></i> <?= htmlspecialchars($news['category'] ?? 'Umum') ?>
            </div>
        </div>
    </header>

    <main class="content-area">
        <a href="../berita.php" class="btn-back">&larr; Kembali ke Berita</a>
        
        <img src="<?= $final_img ?>" alt="<?= htmlspecialchars($news['title']) ?>">

        <div class="news-body">
            <?= nl2br($news['content']) ?> </div>
    </main>

    <?php 
    require_once '../components/footer.php';
    renderFooter(); 
    ?>

    <script src="../../assets/js/navbar.js"></script>
</body>
</html>