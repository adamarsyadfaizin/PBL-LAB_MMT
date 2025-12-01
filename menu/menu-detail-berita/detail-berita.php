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
        $rating = $_POST['rating'] ?? 0;
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
                    'news', // entity_type
                    $news['id'], // entity_id (news_id)
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
            .detail-article-banner { height: 250px; }
            .comments-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
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
                                    <textarea name="content" id="content" placeholder="Tulis komentar Anda di sini..." required><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
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