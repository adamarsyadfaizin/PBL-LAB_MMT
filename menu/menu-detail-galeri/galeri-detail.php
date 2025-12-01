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

// --- VARIABEL KOMENTAR ---
$comment_success = false;
$comment_error = '';
$comments = [];

// --- PROSES TAMBAH KOMENTAR ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    // Cek apakah user sudah login
    if (!isset($_SESSION['user_id'])) {
        $comment_error = "Anda harus login terlebih dahulu untuk memberikan komentar.";
    } else {
        // Ambil data dari form
        $rating = !empty($_POST['rating']) ? (int)$_POST['rating'] : NULL;
        $content = trim($_POST['content'] ?? '');
        $author_name = $_SESSION['user_name'] ?? '';
        $author_email = $_SESSION['user_email'] ?? '';
        
        // Validasi
        if (empty($content)) {
            $comment_error = "Komentar tidak boleh kosong.";
        } elseif (strlen($content) < 5) {
            $comment_error = "Komentar terlalu pendek.";
        } else {
            // Simpan komentar ke database
            $stmt = $pdo->prepare("INSERT INTO comments 
                (entity_type, entity_id, author_name, author_email, rating, content, status, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, 'approved', NOW(), NOW())");
            
            try {
                $stmt->execute([
                    'media', // entity_type untuk media_assets
                    $media['id'], // entity_id (media_assets id)
                    $author_name,
                    $author_email,
                    $rating,
                    $content
                ]);
                
                $comment_success = true;
                $_POST = []; // Reset form
            } catch (PDOException $e) {
                $comment_error = "Terjadi kesalahan saat menyimpan komentar: " . $e->getMessage();
            }
        }
    }
}

// --- AMBIL KOMENTAR YANG SUDAH ADA ---
$stmt = $pdo->prepare("SELECT * FROM comments 
    WHERE entity_type = 'media' 
    AND entity_id = ? 
    AND status = 'approved'
    ORDER BY created_at DESC");
$stmt->execute([$media['id']]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung rata-rata rating
$avg_rating = 0;
if (count($comments) > 0) {
    $total_rating = 0;
    $rated_comments = 0;
    foreach ($comments as $comment) {
        if ($comment['rating'] !== NULL && $comment['rating'] > 0) {
            $total_rating += $comment['rating'];
            $rated_comments++;
        }
    }
    if ($rated_comments > 0) {
        $avg_rating = round($total_rating / $rated_comments, 1);
    }
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
        
        /* --- STYLE UNTUK KOMENTAR --- */
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
        
        textarea {
            width: 100%;
            padding: 15px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            resize: vertical;
            min-height: 120px;
            font-family: inherit;
            font-size: 14px;
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
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
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
        
        @media (max-width: 768px) {
            .hero { height: 300px; }
            .hero h1 { font-size: 24px; }
            .media-content iframe { min-height: 300px; }
            .article-meta-detail { flex-direction: column; align-items: flex-start; }
            .social-share { margin-top: 15px; }
            .comments-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
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
                        
                        <!-- FORM KOMENTAR -->
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
                                    <p><i class="fas fa-info-circle"></i> Anda harus <a href="<?= $root_prefix ?>login.php">login</a> terlebih dahulu untuk memberikan komentar.</p>
                                </div>
                            <?php else: ?>
                                <form method="POST" action="">
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
                                        <label for="content">Komentar Anda:</label>
                                        <textarea name="content" id="content" placeholder="Tulis komentar Anda tentang media ini..." required><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
                                    </div>
                                    
                                    <button type="submit" name="submit_comment" class="btn-submit">
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

    <script>
        // Script untuk rating stars
        document.addEventListener('DOMContentLoaded', function() {
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
            
            // Auto-resize textarea
            const textarea = document.getElementById('content');
            if (textarea) {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = (this.scrollHeight) + 'px';
                });
            }
        });
    </script>

</body>
</html>