<?php
// menu/menu-proyek-detail/detail-proyek.php
if (!isset($_SESSION)) session_start();
require_once '../../config/db.php'; // Mundur 2 langkah
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
        WHERE p.slug = ? AND p.status = 'published'";
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
    
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .project-header {
            background: #f4f4f4;
            padding: 60px 0;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        .project-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 20px;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 40px;
        }
        .project-main img {
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .project-sidebar {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 10px;
            height: fit-content;
        }
        .info-item { margin-bottom: 20px; }
        .info-item label { font-weight: bold; display: block; color: #666; }
        .btn-demo {
            display: block;
            width: 100%;
            padding: 12px;
            background: var(--color-primary, #003b8e);
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
            font-weight: bold;
        }
        .btn-demo:hover { opacity: 0.9; }

        @media(max-width: 768px) {
            .project-container { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <?php renderNavbar('proyek'); ?>

    <div class="project-header">
        <div class="container">
            <h1><?= htmlspecialchars($project['title']) ?></h1>
            <p><?= htmlspecialchars($project['summary']) ?></p>
        </div>
    </div>

    <div class="project-container">
        <div class="project-main">
            <img src="<?= $final_img ?>" alt="<?= htmlspecialchars($project['title']) ?>">
            
            <h3>Deskripsi Proyek</h3>
            <p><?= nl2br(htmlspecialchars($project['description'])) ?></p>
        </div>

        <div class="project-sidebar">
            <div class="info-item">
                <label>Kategori</label>
                <span><?= htmlspecialchars($project['category_name'] ?? 'Uncategorized') ?></span>
            </div>
            <div class="info-item">
                <label>Tahun</label>
                <span><?= htmlspecialchars($project['year']) ?></span>
            </div>
            
            <?php if (!empty($project['demo_url'])): ?>
            <div class="info-item">
                <a href="<?= htmlspecialchars($project['demo_url']) ?>" target="_blank" class="btn-demo">
                    <i class="fas fa-external-link-alt"></i> Lihat Demo
                </a>
            </div>
            <?php endif; ?>

            <div class="info-item" style="margin-top: 30px; border-top: 1px solid #ddd; padding-top: 20px;">
                <a href="../proyek.php" style="text-decoration: none; color: #555;">&larr; Kembali ke Daftar Proyek</a>
            </div>
        </div>
    </div>

    <?php 
    require_once '../components/footer.php';
    renderFooter(); 
    ?>

    <script src="../../assets/js/navbar.js"></script>
</body>
</html>