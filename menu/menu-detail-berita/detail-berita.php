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
require_once $root_prefix . 'config/settings.php';

// Components are in /menu/, so use $menu_prefix
require_once $menu_prefix . 'components/navbar.php';
require_once $menu_prefix . 'components/footer.php';
include $menu_prefix . 'components/floating_profile.php'; 

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

// --- PERBAIKAN: URL ABSOLUT UNTUK SHARING ---
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
$current_url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$base_url = $protocol . $_SERVER['HTTP_HOST'] . dirname(dirname($_SERVER['REQUEST_URI'])) . '/';

// --- PERBAIKAN PATH GAMBAR ---
$clean_img = str_replace('../', '', $news['cover_image'] ?? 'assets/images/default.jpg');
$final_img = $root_prefix . $clean_img;

// --- PERBAIKAN: FULL URL UNTUK GAMBAR SHARING ---
$share_image_url = $protocol . $_SERVER['HTTP_HOST'] . '/' . $clean_img;

// --- PERBAIKAN: DATA UNTUK SOCIAL SHARE ---
$share_title = rawurlencode(htmlspecialchars_decode($news['title']));
$share_description = rawurlencode(strip_tags(substr($news['content'], 0, 200)) . '...');
$share_url = rawurlencode($current_url);
$share_hashtags = 'MMTLab,BeritaTeknologi';

// Membuat deskripsi singkat untuk Instagram
$instagram_caption = htmlspecialchars($news['title']) . "\n\n" . 
                    strip_tags(substr($news['content'], 0, 150)) . "...\n\n" .
                    "👉 Baca selengkapnya: " . $current_url . "\n" .
                    "#MMTLab #BeritaTeknologi #Teknologi";

// --- VARIABEL KOMENTAR ---
$comment_success = false;
$comment_error = '';
$comments = [];

// --- PROSES TAMBAH KOMENTAR ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    if (!isset($_SESSION['user_id'])) {
        $comment_error = "Anda harus login terlebih dahulu untuk memberikan komentar.";
    } else {
        $rating = $_POST['rating'] ?? 0;
        $content = trim($_POST['content'] ?? '');
        $author_name = $_SESSION['user_name'] ?? '';
        $author_email = $_SESSION['user_email'] ?? '';
        
        // VALIDASI: Minimal 10 karakter
        if (empty($content)) {
            $comment_error = "Komentar tidak boleh kosong.";
        } elseif (strlen($content) < 10) {
            $comment_error = "Komentar terlalu pendek. Minimal 10 karakter.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO comments 
                (entity_type, entity_id, author_name, author_email, rating, content, status, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, 'approved', NOW(), NOW())");
            
            try {
                $stmt->execute([
                    'news',
                    $news['id'],
                    $author_name,
                    $author_email,
                    $rating,
                    $content
                ]);
                
                $comment_success = true;
                $_POST = [];
            } catch (PDOException $e) {
                $comment_error = "Terjadi kesalahan saat menyimpan komentar: " . $e->getMessage();
            }
        }
    }
}

// --- AMBIL KOMENTAR YANG SUDAH ADA ---
$stmt = $pdo->prepare("SELECT * FROM comments 
    WHERE entity_type = 'news' 
    AND entity_id = ? 
    AND status = 'approved'
    ORDER BY created_at DESC");
$stmt->execute([$news['id']]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung rata-rata rating
$avg_rating = 0;
if (count($comments) > 0) {
    $total_rating = 0;
    $rated_comments = 0;
    foreach ($comments as $comment) {
        if ($comment['rating'] > 0) {
            $total_rating += $comment['rating'];
            $rated_comments++;
        }
    }
    if ($rated_comments > 0) {
        $avg_rating = round($total_rating / $rated_comments, 1);
    }
}

renderFloatingProfile(); 
?>

<!DOCTYPE html>
<html lang="id" prefix="og: https://ogp.me/ns#">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($news['title']) ?> - Laboratorium MMT</title>
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?= htmlspecialchars($news['title']) ?>">
    <meta property="og:description" content="<?= htmlspecialchars(strip_tags(substr($news['content'], 0, 200))) ?>...">
    <meta property="og:url" content="<?= $current_url ?>">
    <meta property="og:image" content="<?= $share_image_url ?>">
    <meta property="og:type" content="article">
    <meta property="og:site_name" content="Laboratorium MMT">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($news['title']) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars(strip_tags(substr($news['content'], 0, 200))) ?>...">
    <meta name="twitter:image" content="<?= $share_image_url ?>">
    <meta name="twitter:site" content="@mmt_lab">
    
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <link rel="stylesheet" href="<?= $root_prefix ?>assets/css/style.css?v=<?= $cache_buster ?>">
    <link rel="stylesheet" href="<?= $menu_prefix ?>components/navbar.css?v=<?= $cache_buster ?>">
    <link rel="stylesheet" href="assets/det-berita/css/style-detail-berita.css?v=<?= $cache_buster ?>">
    
    <style>
        /* Background untuk seluruh halaman */
        body {
            background: url('<?= $root_prefix ?>assets/images/wallpaper.jpg') center center/cover fixed no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
        }
        
        .main-content-area {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            position: relative;
            padding: 60px 0;
        }
        
        .container {
            position: relative;
            z-index: 1;
        }
        
        .hero {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.8)), 
                        url('<?= $final_img ?>') center center/cover no-repeat;
            height: 400px;
        }
        
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
        
        .primary-content {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            padding: 30px;
            margin-bottom: 30px;
        }
        
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
        
        .article-content {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(8px);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            padding: 30px;
        }
        
        .detail-article-banner {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .news-body {
            line-height: 1.8;
            color: #333;
            font-size: 16px;
        }
        
        .article-meta-detail {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(5px);
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 20px;
            margin-top: 40px;
        }
        
        /* ===== PERBAIKAN TOMBOL SHARE ===== */
        .share-section {
            margin: 30px 0;
        }
        
        .share-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .share-title i {
            color: #667eea;
        }
        
        .share-buttons-container {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
        }
        
        .share-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            border-radius: 12px;
            color: white;
            font-size: 20px;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            cursor: pointer;
            border: none;
            outline: none;
            box-shadow: 0 4px 6px rgba(50, 50, 93, 0.11), 0 1px 3px rgba(0, 0, 0, 0.08);
        }
        
        .share-btn:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1), 0 3px 6px rgba(0, 0, 0, 0.08);
        }
        
        .share-btn:active {
            transform: translateY(-1px);
        }
        
        .share-btn .btn-label {
            position: absolute;
            top: -30px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            pointer-events: none;
        }
        
        .share-btn:hover .btn-label {
            opacity: 1;
            visibility: visible;
            top: -35px;
        }
        
        /* Warna tombol sesuai platform */
        .share-btn.facebook {
            background: linear-gradient(135deg, #1877F2, #0D65D9);
        }
        
        .share-btn.twitter {
            background: linear-gradient(135deg, #1DA1F2, #0D8BD9);
        }
        
        .share-btn.instagram {
            background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
        }
        
        .share-btn.whatsapp {
            background: linear-gradient(135deg, #25D366, #128C7E);
        }
        
        .share-btn.linkedin {
            background: linear-gradient(135deg, #0077B5, #00669E);
        }
        
        .share-btn.telegram {
            background: linear-gradient(135deg, #0088cc, #0077B5);
        }
        
        .share-btn.copy {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }
        
        /* Container untuk link copy */
        .link-copy-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .link-copy-input {
            flex: 1;
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            background: white;
        }
        
        .link-copy-input:focus {
            border-color: #667eea;
            outline: none;
        }
        
        .btn-copy-link {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-copy-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .copy-success {
            color: #28a745;
            font-size: 14px;
            margin-top: 5px;
            display: none;
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
        
        /* STYLE KOMENTAR DENGAN VALIDASI */
        .comments-section {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            padding: 30px;
            margin-top: 40px;
        }
        
        .comments-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid rgba(0, 0, 0, 0.1);
        }
        
        .rating-summary {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .average-rating {
            font-size: 24px;
            font-weight: bold;
            color: #f39c12;
        }
        
        .star-rating {
            color: #f39c12;
            font-size: 18px;
        }
        
        .comment-form {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(5px);
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        .rating-input {
            display: flex;
            gap: 5px;
            margin-bottom: 15px;
        }
        
        .rating-input input[type="radio"] {
            display: none;
        }
        
        .rating-input label {
            font-size: 24px;
            color: #ddd;
            cursor: pointer;
            transition: color 0.3s;
        }
        
        .rating-input label:hover,
        .rating-input label:hover ~ label,
        .rating-input input:checked ~ label {
            color: #f39c12;
        }
        
        .rating-input input:checked + label {
            color: #f39c12;
        }
        
        /* STYLE UNTUK CHARACTER COUNTER */
        .character-counter {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
            font-size: 14px;
        }
        
        .char-info {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .char-count {
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .char-count.minimum {
            color: #dc3545;
        }
        
        .char-count.sufficient {
            color: #28a745;
        }
        
        .char-check {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #e9ecef;
            color: #6c757d;
            font-size: 12px;
            transition: all 0.3s ease;
        }
        
        .char-check.valid {
            background: #28a745;
            color: white;
        }
        
        .min-char-info {
            color: #6c757d;
            font-size: 13px;
        }
        
        textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            resize: vertical;
            min-height: 120px;
            font-family: inherit;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        textarea:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        textarea.error {
            border-color: #dc3545;
        }
        
        textarea.success {
            border-color: #28a745;
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-submit:disabled {
            background: #cccccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .btn-submit:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .login-prompt {
            background: rgba(255, 243, 205, 0.9);
            border-left: 4px solid #ffc107;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .login-prompt a {
            color: #007bff;
            text-decoration: none;
            font-weight: 600;
        }
        
        /* Footer background */
        .site-footer,
        body > footer,
        footer.site-footer {
            background: #FE7927 !important;
        }
        
        /* Social icons in footer hover */
        .site-footer .social-links-footer a:hover {
            background: #ffffff !important;
            border-color: #ffffff !important;
            color: #FE7927 !important;
        }
        
        .login-prompt a:hover {
            text-decoration: underline;
        }
        
        .comment-list {
            margin-top: 30px;
        }
        
        .comment-item {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(5px);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .comment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .comment-author {
            font-weight: 600;
            color: #333;
        }
        
        .comment-date {
            font-size: 12px;
            color: #888;
        }
        
        .comment-rating {
            color: #f39c12;
            margin-bottom: 10px;
        }
        
        .comment-content {
            line-height: 1.6;
            color: #555;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-success {
            background: rgba(212, 237, 218, 0.9);
            border-left: 4px solid #28a745;
            color: #155724;
        }
        
        .alert-error {
            background: rgba(248, 215, 218, 0.9);
            border-left: 4px solid #dc3545;
            color: #721c24;
        }
        
        /* Progress indicator */
        .progress-indicator {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }
        
        .progress-text {
            font-size: 13px;
            color: #6c757d;
        }
        
        .progress-bar {
            flex: 1;
            height: 4px;
            background: #e9ecef;
            border-radius: 2px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: #dc3545;
            border-radius: 2px;
            width: 0%;
            transition: width 0.3s ease, background-color 0.3s ease;
        }
        
        .progress-fill.sufficient {
            background: #28a745;
        }
        
        @media (max-width: 768px) {
            .hero { height: 300px; }
            .hero h1 { font-size: 24px; }
            .detail-article-banner { height: 250px; }
            .comments-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            .share-buttons-container {
                justify-content: center;
            }
            .share-btn {
                width: 45px;
                height: 45px;
                font-size: 18px;
            }
            .character-counter {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
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
                    
                    <!-- PERBAIKAN SECTION SHARE -->
                    <div class="article-meta-detail">
                        <div class="share-section">
                            <div class="share-title">
                                <i class="fas fa-share-alt"></i>
                                <span>Bagikan Berita Ini:</span>
                            </div>
                            
                            <div class="share-buttons-container">
                                <!-- Facebook -->
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $share_url ?>&quote=<?= $share_title ?>" 
                                   target="_blank" 
                                   class="share-btn facebook"
                                   onclick="window.open(this.href, 'fb-share', 'width=580,height=296'); return false;">
                                    <i class="fab fa-facebook-f"></i>
                                    <span class="btn-label">Facebook</span>
                                </a>
                                
                                <!-- Twitter -->
                                <a href="https://twitter.com/intent/tweet?url=<?= $share_url ?>&text=<?= $share_title ?>&hashtags=<?= $share_hashtags ?>" 
                                target="_blank" 
                                class="share-btn twitter"
                                onclick="window.open(this.href, 'tw-share', 'width=550,height=235'); return false;">
                                    <i class="fab fa-twitter"></i>
                                    <span class="btn-label">Twitter</span>
                                </a>

                                
                                <!-- Instagram - PERBAIKAN: Sekarang benar-benar ke Instagram -->
                                <a href="https://www.instagram.com/" 
                                   target="_blank" 
                                   class="share-btn instagram"
                                   onclick="shareToInstagram(event)">
                                    <i class="fab fa-instagram"></i>
                                    <span class="btn-label">Instagram</span>
                                </a>
                                
                                <!-- WhatsApp -->
                                <a href="https://api.whatsapp.com/send?text=<?= $share_title ?>%20-%20<?= $share_url ?>" 
                                   target="_blank" 
                                   class="share-btn whatsapp">
                                    <i class="fab fa-whatsapp"></i>
                                    <span class="btn-label">WhatsApp</span>
                                </a>
                                
                                <!-- LinkedIn -->
                                <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= $share_url ?>&title=<?= $share_title ?>&summary=<?= $share_description ?>" 
                                   target="_blank" 
                                   class="share-btn linkedin"
                                   onclick="window.open(this.href, 'li-share', 'width=600,height=500'); return false;">
                                    <i class="fab fa-linkedin-in"></i>
                                    <span class="btn-label">LinkedIn</span>
                                </a>
                                
                                <!-- Telegram -->
                                <a href="https://t.me/share/url?url=<?= $share_url ?>&text=<?= $share_title ?>" 
                                   target="_blank" 
                                   class="share-btn telegram">
                                    <i class="fab fa-telegram"></i>
                                    <span class="btn-label">Telegram</span>
                                </a>
                                
                                <!-- Copy Link -->
                                <button type="button" 
                                        class="share-btn copy"
                                        onclick="showCopySection()">
                                    <i class="fas fa-link"></i>
                                    <span class="btn-label">Salin Link</span>
                                </button>
                            </div>
                            
                            <!-- Section untuk Copy Link -->
                            <div id="copySection" class="link-copy-container" style="display: none;">
                                <input type="text" id="shareUrl" class="link-copy-input" value="<?= $current_url ?>" readonly>
                                <button onclick="copyShareUrl()" class="btn-copy-link">
                                    <i class="fas fa-copy"></i> Salin
                                </button>
                            </div>
                            <div id="copySuccess" class="copy-success">
                                <i class="fas fa-check-circle"></i> Link berhasil disalin ke clipboard!
                            </div>
                        </div>
                    </div>
                </article>
                
                <!-- SECTION KOMENTAR -->
                <section class="comments-section">
                    <div class="comments-header">
                        <h2>Komentar (<?= count($comments) ?>)</h2>
                        <?php if (count($comments) > 0 && $avg_rating > 0): ?>
                        <div class="rating-summary">
                            <span class="average-rating"><?= $avg_rating ?></span>
                            <div class="star-rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star<?= $i <= $avg_rating ? '' : '-o' ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <span>(<?= count($comments) ?> ulasan)</span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- FORM KOMENTAR DENGAN VALIDASI -->
                    <div class="comment-form">
                        <h3>Tambahkan Komentar</h3>
                        
                        <?php if ($comment_success): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> Komentar Anda berhasil dikirim dan akan ditampilkan setelah disetujui.
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($comment_error): ?>
                            <div class="alert alert-error">
                                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($comment_error) ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <div class="login-prompt">
                                <p><i class="fas fa-info-circle"></i> Anda harus <a href="<?= $root_prefix ?>menu/login.php">login</a> terlebih dahulu untuk memberikan komentar.</p>
                            </div>
                        <?php else: ?>
                            <form method="POST" action="" id="commentForm">
                                <div class="form-group">
                                    <label>Rating (opsional):</label>
                                    <div class="rating-input">
                                        <?php for ($i = 5; $i >= 1; $i--): ?>
                                            <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" <?= ($_POST['rating'] ?? 0) == $i ? 'checked' : '' ?>>
                                            <label for="star<?= $i ?>"><i class="fas fa-star"></i></label>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="content">
                                        Komentar Anda:
                                        <span style="font-size: 12px; color: #6c757d; font-weight: normal; margin-left: 5px;">
                                            (minimal 10 karakter)
                                        </span>
                                    </label>
                                    <textarea name="content" id="content" 
                                              placeholder="Tulis komentar Anda di sini..."
                                              required><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
                                    
                                    <!-- Progress Indicator -->
                                    <div class="progress-indicator">
                                        <div class="progress-text" id="progressText">0/10 karakter</div>
                                        <div class="progress-bar">
                                            <div class="progress-fill" id="progressFill"></div>
                                        </div>
                                    </div>
                                    
                                    <!-- Character Counter -->
                                    <div class="character-counter">
                                        <div class="char-info">
                                            <span class="min-char-info">Minimal 10 karakter</span>
                                            <span class="char-check" id="charCheck">
                                                <i class="fas fa-times"></i>
                                            </span>
                                        </div>
                                        <span id="charCount" class="char-count minimum">0 karakter</span>
                                    </div>
                                </div>
                                
                                <button type="submit" name="submit_comment" class="btn-submit" id="submitCommentBtn" disabled>
                                    <i class="fas fa-paper-plane"></i> Kirim Komentar
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                    
                    <!-- DAFTAR KOMENTAR -->
                    <div class="comment-list">
                        <?php if (count($comments) > 0): ?>
                            <?php foreach ($comments as $comment): ?>
                                <div class="comment-item">
                                    <div class="comment-header">
                                        <div class="comment-author">
                                            <i class="fas fa-user-circle"></i> <?= htmlspecialchars($comment['author_name']) ?>
                                        </div>
                                        <div class="comment-date">
                                            <?= date('d M Y H:i', strtotime($comment['created_at'])) ?>
                                        </div>
                                    </div>
                                    
                                    <?php if ($comment['rating'] > 0): ?>
                                        <div class="comment-rating">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star<?= $i <= $comment['rating'] ? '' : '-o' ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="comment-content">
                                        <?= nl2br(htmlspecialchars($comment['content'])) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div style="text-align: center; padding: 30px; color: #666;">
                                <i class="far fa-comment-alt" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
                                <p>Belum ada komentar. Jadilah yang pertama berkomentar!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
                
            </div>

            <aside class="sidebar">
                <div class="widget">
                    <h3 class="widget-title">Berita Lainnya</h3>
                    <ul style="list-style: none; padding: 0;">
                        <?php
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

    <?php renderFooter($root_prefix, $site_config); ?>

    <a href="#top" id="scrollTopBtn" class="scroll-top-btn" aria-label="Kembali ke atas">&uarr;</a>

    <script src="<?= $root_prefix ?>assets/js/navbar.js?v=<?= $cache_buster ?>"></script>
    <script src="<?= $root_prefix ?>assets/js/scrolltop.js?v=<?= $cache_buster ?>"></script>
    <script src="assets/det-berita/js/script-detail-berita.js?v=<?= $cache_buster ?>"></script>
    
    <script>
        // VALIDASI KOMENTAR REAL-TIME
        document.addEventListener('DOMContentLoaded', function() {
            const commentTextarea = document.getElementById('content');
            const charCountElement = document.getElementById('charCount');
            const charCheckElement = document.getElementById('charCheck');
            const progressTextElement = document.getElementById('progressText');
            const progressFillElement = document.getElementById('progressFill');
            const submitButton = document.getElementById('submitCommentBtn');
            
            if (commentTextarea && charCountElement && submitButton) {
                // Fungsi untuk update karakter
                function updateCharCount() {
                    const text = commentTextarea.value;
                    const charCount = text.length;
                    const minChars = 10;
                    const progressPercentage = Math.min((charCount / minChars) * 100, 100);
                    
                    // Update progress bar
                    progressFillElement.style.width = progressPercentage + '%';
                    progressTextElement.textContent = charCount + '/' + minChars + ' karakter';
                    
                    // Update counter
                    charCountElement.textContent = charCount + ' karakter';
                    
                    // Update styling berdasarkan jumlah karakter
                    if (charCount === 0) {
                        charCountElement.className = 'char-count minimum';
                        charCheckElement.className = 'char-check';
                        charCheckElement.innerHTML = '<i class="fas fa-times"></i>';
                        commentTextarea.className = '';
                        progressFillElement.className = 'progress-fill';
                    } else if (charCount < minChars) {
                        charCountElement.className = 'char-count minimum';
                        charCheckElement.className = 'char-check';
                        charCheckElement.innerHTML = '<i class="fas fa-times"></i>';
                        commentTextarea.className = 'error';
                        progressFillElement.className = 'progress-fill';
                    } else {
                        charCountElement.className = 'char-count sufficient';
                        charCheckElement.className = 'char-check valid';
                        charCheckElement.innerHTML = '<i class="fas fa-check"></i>';
                        commentTextarea.className = 'success';
                        progressFillElement.className = 'progress-fill sufficient';
                    }
                    
                    // Enable/disable tombol submit
                    submitButton.disabled = charCount < minChars;
                    
                    // Update tombol submit text
                    if (charCount >= minChars) {
                        submitButton.innerHTML = '<i class="fas fa-paper-plane"></i> Kirim Komentar ✓';
                    } else {
                        submitButton.innerHTML = '<i class="fas fa-paper-plane"></i> Kirim Komentar';
                    }
                }
                
                // Event listener untuk perubahan teks
                commentTextarea.addEventListener('input', updateCharCount);
                commentTextarea.addEventListener('keyup', updateCharCount);
                commentTextarea.addEventListener('change', updateCharCount);
                
                // Inisialisasi pertama kali
                updateCharCount();
                
                // Auto-resize textarea
                commentTextarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = (this.scrollHeight) + 'px';
                });
                
                // Validasi form submit
                document.getElementById('commentForm').addEventListener('submit', function(e) {
                    const text = commentTextarea.value;
                    if (text.length < 10) {
                        e.preventDefault();
                        commentTextarea.focus();
                        
                        // Animasi shake untuk indikasi error
                        commentTextarea.style.transform = 'translateX(-5px)';
                        setTimeout(() => {
                            commentTextarea.style.transform = 'translateX(5px)';
                            setTimeout(() => {
                                commentTextarea.style.transform = 'translateX(-5px)';
                                setTimeout(() => {
                                    commentTextarea.style.transform = 'translateX(0)';
                                }, 50);
                            }, 50);
                        }, 50);
                        
                        // Tampilkan pesan error
                        const errorMsg = document.createElement('div');
                        errorMsg.className = 'alert alert-error';
                        errorMsg.style.marginTop = '10px';
                        errorMsg.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Komentar harus minimal 10 karakter.';
                        
                        const formGroup = commentTextarea.closest('.form-group');
                        if (!document.querySelector('.alert-error')) {
                            formGroup.appendChild(errorMsg);
                            
                            // Hapus pesan error setelah 3 detik
                            setTimeout(() => {
                                if (errorMsg.parentNode) {
                                    errorMsg.remove();
                                }
                            }, 3000);
                        }
                    }
                });
            }
            
            // Rating stars script
            const ratingInputs = document.querySelectorAll('.rating-input input[type="radio"]');
            const ratingLabels = document.querySelectorAll('.rating-input label');
            
            ratingLabels.forEach(label => {
                label.addEventListener('mouseenter', function() {
                    const starValue = this.getAttribute('for').replace('star', '');
                    highlightStars(starValue);
                });
                
                label.addEventListener('mouseleave', function() {
                    const checkedInput = document.querySelector('.rating-input input[type="radio"]:checked');
                    if (checkedInput) {
                        highlightStars(checkedInput.value);
                    } else {
                        resetStars();
                    }
                });
            });
            
            ratingInputs.forEach(input => {
                input.addEventListener('change', function() {
                    highlightStars(this.value);
                });
            });
            
            function highlightStars(value) {
                resetStars();
                for (let i = 1; i <= value; i++) {
                    const label = document.querySelector(`.rating-input label[for="star${i}"]`);
                    if (label) {
                        label.style.color = '#f39c12';
                    }
                }
            }
            
            function resetStars() {
                ratingLabels.forEach(label => {
                    label.style.color = '#ddd';
                });
            }
        });
        
        // Fungsi untuk Share ke Instagram
        function shareToInstagram(event) {
            event.preventDefault();
            
            const caption = `<?= addslashes($instagram_caption) ?>`;
            const url = '<?= $current_url ?>';
            
            // Buat modal untuk instruksi
            const modalHtml = `
                <div style="
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0,0,0,0.8);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 10000;
                ">
                    <div style="
                        background: white;
                        padding: 30px;
                        border-radius: 15px;
                        max-width: 500px;
                        width: 90%;
                        text-align: center;
                    ">
                        <h3 style="color: #333; margin-bottom: 20px;">
                            <i class="fab fa-instagram" style="color: #E1306C;"></i> 
                            Bagikan ke Instagram
                        </h3>
                        
                        <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px; text-align: left;">
                            <p style="color: #666; margin-bottom: 10px;">
                                <strong>Instruksi:</strong>
                            </p>
                            <ol style="color: #666; padding-left: 20px; margin: 0;">
                                <li>Buka aplikasi Instagram di ponsel Anda</li>
                                <li>Pilih opsi "Buat Postingan" atau "Story"</li>
                                <li>Salin teks di bawah untuk caption:</li>
                            </ol>
                        </div>
                        
                        <textarea id="instagramCaption" readonly 
                            style="
                                width: 100%;
                                padding: 15px;
                                border: 2px solid #e0e0e0;
                                border-radius: 8px;
                                margin-bottom: 20px;
                                font-family: inherit;
                                min-height: 120px;
                                resize: vertical;
                            ">${caption}</textarea>
                        
                        <div style="display: flex; gap: 10px; justify-content: center;">
                            <button onclick="copyInstagramCaption()" 
                                style="
                                    background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);
                                    color: white;
                                    border: none;
                                    padding: 12px 25px;
                                    border-radius: 8px;
                                    font-weight: 600;
                                    cursor: pointer;
                                    transition: all 0.3s ease;
                                ">
                                <i class="fas fa-copy"></i> Salin Caption
                            </button>
                            <button onclick="copyInstagramLink()" 
                                style="
                                    background: #667eea;
                                    color: white;
                                    border: none;
                                    padding: 12px 25px;
                                    border-radius: 8px;
                                    font-weight: 600;
                                    cursor: pointer;
                                    transition: all 0.3s ease;
                                ">
                                <i class="fas fa-link"></i> Salin Link
                            </button>
                            <button onclick="closeInstagramModal()" 
                                style="
                                    background: #6c757d;
                                    color: white;
                                    border: none;
                                    padding: 12px 25px;
                                    border-radius: 8px;
                                    font-weight: 600;
                                    cursor: pointer;
                                ">
                                Tutup
                            </button>
                        </div>
                        
                        <p id="copyStatus" style="margin-top: 15px; color: #28a745; display: none;">
                            <i class="fas fa-check-circle"></i> Berhasil disalin!
                        </p>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', modalHtml);
        }
        
        function copyInstagramCaption() {
            const caption = document.getElementById('instagramCaption');
            caption.select();
            
            try {
                navigator.clipboard.writeText(caption.value).then(() => {
                    showCopyStatus('Caption berhasil disalin!');
                }).catch(err => {
                    document.execCommand('copy');
                    showCopyStatus('Caption berhasil disalin!');
                });
            } catch (err) {
                document.execCommand('copy');
                showCopyStatus('Caption berhasil disalin!');
            }
        }
        
        function copyInstagramLink() {
            const url = '<?= $current_url ?>';
            
            try {
                navigator.clipboard.writeText(url).then(() => {
                    showCopyStatus('Link berhasil disalin!');
                }).catch(err => {
                    const tempInput = document.createElement('input');
                    tempInput.value = url;
                    document.body.appendChild(tempInput);
                    tempInput.select();
                    document.execCommand('copy');
                    document.body.removeChild(tempInput);
                    showCopyStatus('Link berhasil disalin!');
                });
            } catch (err) {
                const tempInput = document.createElement('input');
                tempInput.value = url;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);
                showCopyStatus('Link berhasil disalin!');
            }
        }
        
        function showCopyStatus(message) {
            const status = document.getElementById('copyStatus');
            status.textContent = '✓ ' + message;
            status.style.display = 'block';
            
            setTimeout(() => {
                status.style.display = 'none';
            }, 2000);
        }
        
        function closeInstagramModal() {
            const modal = document.querySelector('div[style*="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8)"]');
            if (modal) {
                modal.remove();
            }
        }
        
        // Fungsi untuk Copy Link
        function showCopySection() {
            const copySection = document.getElementById('copySection');
            const copySuccess = document.getElementById('copySuccess');
            
            copySection.style.display = 'flex';
            copySuccess.style.display = 'none';
            
            // Scroll ke section copy
            copySection.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        
        function copyShareUrl() {
            const urlInput = document.getElementById('shareUrl');
            const copySuccess = document.getElementById('copySuccess');
            
            urlInput.select();
            urlInput.setSelectionRange(0, 99999);
            
            try {
                navigator.clipboard.writeText(urlInput.value).then(() => {
                    copySuccess.style.display = 'block';
                    setTimeout(() => {
                        copySuccess.style.display = 'none';
                    }, 3000);
                }).catch(err => {
                    document.execCommand('copy');
                    copySuccess.style.display = 'block';
                    setTimeout(() => {
                        copySuccess.style.display = 'none';
                    }, 3000);
                });
            } catch (err) {
                document.execCommand('copy');
                copySuccess.style.display = 'block';
                setTimeout(() => {
                    copySuccess.style.display = 'none';
                }, 3000);
            }
        }
        
        // Tutup modal Instagram dengan ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeInstagramModal();
            }
        });
    </script>
</body>
</html>