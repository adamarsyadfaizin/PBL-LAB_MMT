<?php
require_once '../../config/db.php';
// Include navbar component
require_once '../components/navbar.php';

// Ambil data galeri dari database berdasarkan ID atau slug
$gallery_id = $_GET['id'] ?? '';
if (!$gallery_id) {
    header('Location: ../galeri.php');
    exit;
}

// Query untuk mengambil data galeri
$sql = "SELECT * FROM galleries WHERE id = ? AND status = 'published' LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([$gallery_id]);
$gallery = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$gallery) {
    die("Galeri tidak ditemukan.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Galeri: <?php echo htmlspecialchars($gallery['title']); ?> - POLINEMA</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    
    <!-- CSS Utama (sama dengan halaman lain) -->
    <link rel="stylesheet" href="../../assets/css/style.css">
    
    <!-- CSS Khusus Halaman Detail Galeri -->
    <link rel="stylesheet" href="assets/det-gel/css/style-galeri-detail.css">
    
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
    // Render navbar dengan halaman aktif 'galeri'
    renderNavbar('galeri');
    ?>

    <main>

        <section class="hero">
            <div class="container">
                <h1><?php echo htmlspecialchars($gallery['title']); ?></h1>
            </div>
        </section>

        <div class="main-content-area">
            <div class="container">
                
                <div class="primary-content">

                                    <img src="<?php echo htmlspecialchars($data['image_path'] ?? 'uploads/default.jpg'); ?>" 
                    alt="Pameran Teknologi 2025" 
                    class="detail-article-banner">

                    <div class="article-meta-detail">
                        <div class="meta-info">
                            <span><strong>Kategori:</strong> <?php echo htmlspecialchars($gallery['category']); ?></span>
                            <span><strong>Acara:</strong> <?php echo htmlspecialchars($gallery['event_name']); ?></span>
                            <span><strong>Tanggal:</strong> <?php echo date('d F Y', strtotime($gallery['created_at'])); ?></span>
                        </div>
                        <div class="social-share">
                            <span>Bagikan:</span>
                            <a href="#" aria-label="Bagikan ke Facebook">F</a>
                            <a href="#" aria-label="Bagikan ke Twitter">T</a>
                            <a href="#" aria-label="Bagikan ke LinkedIn">L</a>
                        </div>
                    </div>

                    <div class="article-content">
                        <?php echo nl2br(htmlspecialchars($gallery['description'])); ?>
                    </div>
                    
                    <a href="../galeri.php" class="btn btn-secondary back-link">&larr; Kembali ke Galeri</a>

                </div>
                
                <aside class="sidebar">
                    
                    <div class="widget widget-categories">
                        <h3 class="widget-title">Kategori Galeri</h3>
                        <ul>
                            <li><a href="../galeri.php">Semua Media</a></li>
                            <li><a href="../galeri.php?kategori=foto">Foto</a></li>
                            <li><a href="../galeri.php?kategori=video">Video</a></li>
                            <li><a href="../galeri.php?kategori=animasi">Animasi</a></li>
                        </ul>
                    </div>
                    
                    <div class="widget widget-news">
                        <h3 class="widget-title">Album Lainnya</h3>
                        <ul>
                            <?php
                            // Query untuk mengambil galeri lainnya
                            $sql_other = "SELECT * FROM galleries WHERE id != ? AND status = 'published' ORDER BY created_at DESC LIMIT 3";
                            $stmt_other = $pdo->prepare($sql_other);
                            $stmt_other->execute([$gallery_id]);
                            $other_galleries = $stmt_other->fetchAll(PDO::FETCH_ASSOC);
                            
                            foreach ($other_galleries as $other):
                            ?>
                            <li>
                                <a href="galeri-detail.php?id=<?php echo $other['id']; ?>">
                                    <h4><?php echo htmlspecialchars($other['title']); ?></h4>
                                    <span class="date"><?php echo htmlspecialchars($other['category']); ?> | <?php echo htmlspecialchars($other['event_name']); ?></span>
                                    <p><?php echo htmlspecialchars(substr($other['description'], 0, 100)); ?>...</p>
                                    <span class="arrow-icon" aria-hidden="true">&rarr;</span>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                </aside>
                
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
    
    <!-- JavaScript Khusus Halaman Detail Galeri -->
    <script src="assets/det-gel/js/script-galeri-detail.js"></script>

</body>
</html>