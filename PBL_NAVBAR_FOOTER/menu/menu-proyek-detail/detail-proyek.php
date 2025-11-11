<?php
require_once '../../config/db.php';
require_once '../components/navbar.php';

if (!isset($_GET['slug']) || empty($_GET['slug'])) {
    header('Location: ../proyek.php');
    exit;
}

$slug = $_GET['slug'];

try {
    // --- Ambil data proyek dan kategori ---
    $sql_project = "SELECT p.*, c.name AS category_name
                    FROM projects p
                    LEFT JOIN categories c ON p.category_id = c.id
                    WHERE p.slug = ? AND p.status = 'published'
                    LIMIT 1";
    $stmt = $pdo->prepare($sql_project);
    $stmt->execute([$slug]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$project) {
        die("Proyek tidak ditemukan!");
    }

    // --- Ambil tag ---
    $sql_tags = "SELECT t.name FROM tags t
                 JOIN project_tags pt ON t.id = pt.tag_id
                 WHERE pt.project_id = ?";
    $stmt_tags = $pdo->prepare($sql_tags);
    $stmt_tags->execute([$project['id']]);
    $tags = $stmt_tags->fetchAll(PDO::FETCH_ASSOC);

    // --- Ambil anggota tim proyek ---
    $sql_members = "SELECT m.name, m.role, m.avatar_url, m.linkedin_url
                    FROM members m
                    JOIN project_members pm ON m.id = pm.member_id
                    WHERE pm.project_id = ?";
    $stmt_members = $pdo->prepare($sql_members);
    $stmt_members->execute([$project['id']]);
    $team_members = $stmt_members->fetchAll(PDO::FETCH_ASSOC);

    // --- Ambil proyek terkait (kategori sama) ---
    $sql_related = "SELECT title, slug, summary
                    FROM projects
                    WHERE category_id = ? AND id != ? AND status = 'published'
                    LIMIT 3";
    $stmt_related = $pdo->prepare($sql_related);
    $stmt_related->execute([$project['category_id'], $project['id']]);
    $related_projects = $stmt_related->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Gagal mengambil data proyek: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($project['title']); ?> - Laboratorium Mobile & Multimedia Tech POLINEMA</title>

    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="assets/det-pro/css/style-detail-proyek.css">

    <style>
        .hero {
            background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.6)),
                        url('../../assets/images/hero.jpg') center/cover no-repeat;
            color: white;
            height: 300px;
            display: flex;
            align-items: center;
        }
        .hero h1 {
            font-size: 36px;
            font-weight: 700;
        }
    </style>
</head>
<body id="top">

<?php renderNavbar('proyek'); ?>

<main>
    <section class="hero">
        <div class="container">
            <h1><?php echo htmlspecialchars($project['title']); ?></h1>
        </div>
    </section>

    <div class="main-content-area">
        <div class="container">
            <div class="primary-content">
                <?php if (!empty($project['cover_image'])): ?>
                    <div class="project-cover">
                        <img src="<?php echo htmlspecialchars($project['cover_image']); ?>" 
                             alt="<?php echo htmlspecialchars($project['title']); ?>" 
                             style="width:100%; border-radius:10px;">
                    </div>
                <?php endif; ?>

                <p class="lead-paragraph"><?php echo nl2br(htmlspecialchars($project['summary'])); ?></p>

                <h3>Deskripsi Lengkap</h3>
                <p><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>

                <h3>Tautan Proyek</h3>
                <ul class="project-links-list">
                    <?php if ($project['demo_url']): ?>
                        <li><a href="<?php echo htmlspecialchars($project['demo_url']); ?>" target="_blank" class="btn">Lihat Demo</a></li>
                    <?php endif; ?>
                    <?php if ($project['repo_url']): ?>
                        <li><a href="<?php echo htmlspecialchars($project['repo_url']); ?>" target="_blank" class="btn btn-secondary">Repository</a></li>
                    <?php endif; ?>
                </ul>

                <a href="../proyek.php" class="btn btn-secondary">&larr; Kembali ke Katalog Proyek</a>
            </div>

            <aside class="sidebar">

                <!-- Anggota Tim -->
                <div class="widget">
                    <h4 class="widget-title">Anggota Tim</h4>
                    <?php if ($team_members): ?>
                        <ul style="list-style:none; padding:0;">
                            <?php foreach ($team_members as $member): ?>
                                <li style="display:flex; align-items:center; margin-bottom:10px;">
                                    <img src="<?php echo htmlspecialchars($member['avatar_url']); ?>" 
                                         alt="<?php echo htmlspecialchars($member['name']); ?>" 
                                         style="width:40px;height:40px;border-radius:50%;margin-right:10px;">
                                    <div>
                                        <strong><?php echo htmlspecialchars($member['name']); ?></strong><br>
                                        <small><?php echo htmlspecialchars($member['role']); ?></small>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>Belum ada data anggota tim.</p>
                    <?php endif; ?>
                </div>

                <!-- Detail -->
                <div class="widget">
                    <h4 class="widget-title">Detail</h4>
                    <ul style="list-style:none;padding:0;">
                        <li><strong>Tahun:</strong> <?php echo htmlspecialchars($project['year']); ?></li>
                        <li><strong>Kategori:</strong> <?php echo htmlspecialchars($project['category_name']); ?></li>
                        <li><strong>Tag:</strong>
                            <?php foreach ($tags as $tag): ?>
                                <span class="tag-badge"><?php echo htmlspecialchars($tag['name']); ?></span>
                            <?php endforeach; ?>
                        </li>
                    </ul>
                </div>

                <!-- Proyek Terkait -->
                <div class="widget">
                    <h4 class="widget-title">Proyek Terkait</h4>
                    <ul>
                        <?php if ($related_projects): ?>
                            <?php foreach ($related_projects as $rel): ?>
                                <li>
                                    <a href="detail-proyek.php?slug=<?php echo htmlspecialchars($rel['slug']); ?>">
                                        <strong><?php echo htmlspecialchars($rel['title']); ?></strong><br>
                                        <small><?php echo htmlspecialchars($rel['summary']); ?></small>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>Tidak ada proyek terkait.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</main>

<?php 
require_once '../components/footer.php';
renderFooter();
?>

<a href="#top" id="scrollTopBtn" class="scroll-top-btn">&uarr;</a>
<script src="../../assets/js/script.js"></script>
</body>
</html>
