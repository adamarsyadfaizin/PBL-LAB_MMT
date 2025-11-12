<?php
require_once '../../config/db.php';
// Include navbar component  
require_once '../components/navbar.php';

if ( !isset($_GET['slug']) ) { 
    header('Location: ../berita.php'); 
    exit; 
}

$slug = $_GET['slug'];
$sql = "SELECT * FROM news WHERE slug = ? AND status = 'published' LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([$slug]);
$news = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$news) { 
    die("Berita tidak ditemukan."); 
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Berita: <?php echo htmlspecialchars($news['title']); ?> - Laboratorium Mobile and Multimedia Tech POLINEMA</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    
    <!-- CSS Utama (sama dengan halaman lain) -->
    <link rel="stylesheet" href="../../assets/css/style.css">
    
    <!-- CSS Khusus Halaman Detail Berita -->
    <link rel="stylesheet" href="assets/det-berita/css/style-detail-berita.css">
    
    <style>
        /* Override background hero image dengan path yang benar */
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
    </style>
</head>
<body id="top">

    <?php
    // Render navbar dengan halaman aktif 'berita'
    renderNavbar('berita');
    ?>

    <main>

        <section class="hero">
            <div class="container">
                <h1>Detail Berita</h1>
            </div>
        </section>

        <div class="main-content-area">
            <div class="container">
                <article class="news-detail">
                    <header class="news-header">
                        <h1><?php echo htmlspecialchars($news['title']); ?></h1>
                        <div class="news-meta">
                            <span class="news-date"><?php echo date('d F Y', strtotime($news['created_at'])); ?></span>
                        </div>
                    </header>
                    
                    <div class="news-content">
                        <?php if (!empty($news['cover_image'])): ?>
                        <div class="news-image">
                            <img src="<?php echo htmlspecialchars($news['cover_image']); ?>" alt="<?php echo htmlspecialchars($news['title']); ?>">
                        </div>
                        <?php endif; ?>
                        
                        <div class="news-body">
                            <?php echo nl2br(htmlspecialchars($news['content'])); ?>
                        </div>
                    </div>
                </article>
            </div>
        </div>
        
    </main>

    <?php
    // Include footer
    require_once '../components/footer.php';
    renderFooter();
    ?>

    <a href="#top" id="scrollTopBtn" class="scroll-top-btn" aria-label="Kembali ke atas">
        &uarr;
    </a>

    <!-- JavaScript Utama -->
    <script src="../../assets/js/navbar.js"></script>
    
    <!-- JavaScript Khusus Halaman Detail Berita -->
    <script src="assets/det-berita/js/script-detail-berita.js"></script>

</body>
</html>