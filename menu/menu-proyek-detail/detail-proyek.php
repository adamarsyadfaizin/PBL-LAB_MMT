<?php
// menu/menu-proyek-detail/detail-proyek.php
if (!isset($_SESSION)) session_start();
require_once '../../config/db.php'; 
require_once '../../config/settings.php'; // <-- PENTING: CMS Setting
include '../components/floating_profile.php'; 
renderFloatingProfile();
require_once '../components/navbar.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) {
    header("Location: ../proyek.php");
    exit();
}

// Query Proyek + Kategori
$sql = "SELECT p.*, c.name as category_name 
        FROM projects p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.slug = ? AND (p.status = 'published' OR p.status = '1')";
$stmt = $pdo->prepare($sql);
$stmt->execute([$slug]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    echo "<h1>Proyek tidak ditemukan!</h1><a href='../proyek.php'>Kembali</a>";
    exit();
}

// --- PERBAIKAN PATH GAMBAR ---
$clean_img = str_replace('../', '', $project['cover_image']);
$final_img = "../../" . $clean_img;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($project['title']) ?> - Proyek Lab MMT</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="stylesheet" href="../../assets/css/style.css">
    
    <link rel="stylesheet" href="assets/det-proyek/css/style-detail-proyek.css">

    <style>
        .hero {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.8)), 
                        url('<?= $final_img ?>') center center/cover no-repeat;
            height: 400px !important;
        }
        /* Penyesuaian Judul di Hero */
        .hero h1 {
            font-size: 36px;
            margin-bottom: 15px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.5);
        }
        .project-meta-hero {
            font-size: 16px;
            color: rgba(255,255,255,0.9);
            font-weight: 500;
        }
    </style>
</head>
<body id="top">

    <?php renderNavbar('proyek'); ?>

    <section class="hero">
        <div class="container">
            <h1><?= htmlspecialchars($project['title']) ?></h1>
            <div class="project-meta-hero">
                <i class="fas fa-folder"></i> <?= htmlspecialchars($project['category_name'] ?? 'Uncategorized') ?> 
                &nbsp;&nbsp;|&nbsp;&nbsp; 
                <i class="fas fa-calendar"></i> <?= htmlspecialchars($project['year']) ?>
            </div>
        </div>
    </section>

    <div class="main-content-area">
        <div class="container">
            
            <div class="primary-content">
                <a href="../proyek.php" class="btn-secondary back-link" style="margin-top:0; margin-bottom:30px;">&larr; Kembali ke Daftar Proyek</a>

                <img src="<?= $final_img ?>" alt="<?= htmlspecialchars($project['title']) ?>" style="width:100%; border-radius:10px; margin-bottom:30px; box-shadow:0 5px 15px rgba(0,0,0,0.1);">
                
                <h3>Deskripsi Proyek</h3>
                <div class="lead-paragraph">
                    <?= nl2br(htmlspecialchars($project['summary'])) ?>
                </div>
                
                <p><?= nl2br(htmlspecialchars($project['description'])) ?></p>

                <?php if (!empty($project['demo_url'])): ?>
                <div style="margin-top: 30px;">
                    <a href="<?= htmlspecialchars($project['demo_url']) ?>" target="_blank" class="btn">
                        <i class="fas fa-external-link-alt"></i> Lihat Demo Proyek
                    </a>
                </div>
                <?php endif; ?>
            </div>

            <aside class="sidebar">
                <div class="widget">
                    <h3 class="widget-title">Informasi Proyek</h3>
                    <ul style="list-style:none; padding:20px;">
                        <li style="margin-bottom:15px;">
                            <strong>Kategori:</strong><br>
                            <?= htmlspecialchars($project['category_name'] ?? '-') ?>
                        </li>
                        <li style="margin-bottom:15px;">
                            <strong>Tahun Pembuatan:</strong><br>
                            <?= htmlspecialchars($project['year']) ?>
                        </li>
                        <?php if (!empty($project['repo_url'])): ?>
                        <li style="margin-bottom:15px;">
                            <strong>Repository:</strong><br>
                            <a href="<?= htmlspecialchars($project['repo_url']) ?>" target="_blank">GitHub / GitLab</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="widget">
                    <h3 class="widget-title">Proyek Lainnya</h3>
                    <ul style="list-style:none; padding:20px;">
                        <?php
                       // Ganti RAND() menjadi RANDOM()
                        $stmt_other = $pdo->prepare("SELECT title, slug, year FROM projects WHERE slug != ? AND (status = 'published' OR status = '1') ORDER BY RANDOM() LIMIT 3");
                        $stmt_other->execute([$slug]);
                        while($other = $stmt_other->fetch()):
                        ?>
                        <li style="margin-bottom:15px; border-bottom:1px solid #eee; padding-bottom:10px;">
                            <a href="detail-proyek.php?slug=<?= $other['slug'] ?>" style="text-decoration:none; font-weight:600; color:#333;">
                                <?= htmlspecialchars($other['title']) ?>
                            </a>
                            <span style="display:block; font-size:12px; color:#888;"><?= $other['year'] ?></span>
                        </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </aside>

        </div>
    </div>

    <?php 
    require_once '../components/footer.php';
    renderFooter(); 
    ?>

    <a href="#top" id="scrollTopBtn" class="scroll-top-btn" aria-label="Kembali ke atas">&uarr;</a>

    <script src="../../assets/js/navbar.js"></script>
    <script src="assets/det-proyek/js/script-detail-proyek.js"></script>
</body>
</html>