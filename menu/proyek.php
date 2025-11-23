<?php
if (!isset($_SESSION)) session_start();
require_once '../config/db.php';
require_once '../config/settings.php'; // CMS Setting

// --- PENGATURAN HALAMAN ---
$limit = 6; // Tampilkan 6 proyek per halaman (agar pas 2 baris x 3 kolom)
$page = (int)($_GET['page'] ?? 1);
$offset = ($page - 1) * $limit;

// --- FILTER PARAMETER ---
$search_term = $_GET['s'] ?? '';
$category_slug = $_GET['kategori'] ?? 'semua';
$tech_slug = $_GET['teknologi'] ?? 'semua';
$year = $_GET['tahun'] ?? 'semua';
$sort = $_GET['sort'] ?? 'terbaru';

// --- QUERY BUILDER ---
$sql_base = "FROM projects p
             LEFT JOIN categories c ON p.category_id = c.id
             LEFT JOIN project_tags pt ON p.id = pt.project_id
             LEFT JOIN tags t ON pt.tag_id = t.id
             WHERE (p.status = 'published' OR p.status = '1')";

$params = [];

if (!empty($search_term)) {
    $sql_base .= " AND (p.title LIKE ? OR p.summary LIKE ?)";
    $params[] = "%$search_term%";
    $params[] = "%$search_term%";
}

if ($category_slug != 'semua') {
    $sql_base .= " AND c.slug = ?";
    $params[] = $category_slug;
}

if ($tech_slug != 'semua') {
    $sql_base .= " AND t.slug = ?";
    $params[] = $tech_slug;
}

if ($year != 'semua') {
    $sql_base .= " AND p.year = ?";
    $params[] = (int)$year;
}

$sql_base .= " GROUP BY p.id";

// --- HITUNG TOTAL DATA ---
$sql_count = "SELECT COUNT(DISTINCT p.id) " . $sql_base;
$stmt_count = $pdo->prepare($sql_count);
$stmt_count->execute($params);
$total_projects = $stmt_count->fetchColumn();
$total_pages = ceil($total_projects / $limit);

// Sorting
$order_by = " ORDER BY p.created_at DESC";
if ($sort == 'a-z') {
    $order_by = " ORDER BY p.title ASC";
}

// --- AMBIL DATA ---
$sql_final = "SELECT p.id, p.title, p.slug, p.summary, p.cover_image, p.demo_url
              " . $sql_base . 
              $order_by . 
              " LIMIT ? OFFSET ?";

$params_final = $params;
$params_final[] = $limit;
$params_final[] = $offset;

$stmt_projects = $pdo->prepare($sql_final);
$stmt_projects->execute($params_final);
$projects = $stmt_projects->fetchAll(PDO::FETCH_ASSOC);

// Data Dropdown
try {
    $categories = $pdo->query("SELECT name, slug FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    $tags = $pdo->query("SELECT name, slug FROM tags ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    $years = $pdo->query("SELECT DISTINCT year FROM projects WHERE year IS NOT NULL ORDER BY year DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categories = []; $tags = []; $years = [];
}

// Helper Pagination
function build_pagination($current, $total, $adj = 2) {
    $pages = [];
    if ($total <= 1) return [1];
    $pages[] = 1;
    $start = max(2, $current - $adj);
    $end = min($total - 1, $current + $adj);
    if ($start > 2) $pages[] = '...';
    for ($i = $start; $i <= $end; $i++) $pages[] = $i;
    if ($end < $total - 1) $pages[] = '...';
    if ($total > 1) $pages[] = $total;
    return array_unique($pages);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Proyek - Laboratorium MMT</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="assets/proyek/css/style-proyek.css">
    
    <style>
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.8)), 
                        url('../<?= htmlspecialchars($site_config['hero_image_path'] ?? 'assets/images/hero.jpg') ?>') center center/cover no-repeat;
            height: 300px !important; 
        }
        .hero h1 { margin-bottom: 0; }
    </style>
</head>
<body id="top">

    <?php
    require_once 'components/navbar.php';
    renderNavbar('proyek');
    ?>

    <main>
        <section class="hero">
            <div class="container">
                <h1>Katalog Proyek</h1>
            </div>
        </section>

        <div class="main-content-area">
            <div class="container">
                
                <form class="project-filter-bar" action="" method="get">
                    <div class="filter-group filter-group-search">
                        <label for="filter-search">Pencarian</label>
                        <input type="search" id="filter-search" name="s" placeholder="Cari proyek..." value="<?= htmlspecialchars($search_term) ?>">
                    </div>
                    <div class="filter-group">
                        <label for="filter-kategori">Kategori</label>
                        <select id="filter-kategori" name="kategori">
                            <option value="semua">Semua</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['slug'] ?>" <?= ($category_slug == $cat['slug']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="filter-teknologi">Teknologi</label>
                        <select id="filter-teknologi" name="teknologi">
                            <option value="semua">Semua</option>
                            <?php foreach ($tags as $tag): ?>
                                <option value="<?= $tag['slug'] ?>" <?= ($tech_slug == $tag['slug']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($tag['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="filter-tahun">Tahun</label>
                        <select id="filter-tahun" name="tahun">
                            <option value="semua">Semua</option>
                            <?php foreach ($years as $y): ?>
                                <option value="<?= $y['year'] ?>" <?= ($year == $y['year']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($y['year']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn" style="width:100%; height:42px; padding:0;">Filter</button>
                    </div>
                </form>

                <div class="project-grid">
                    <?php
                    if ($projects):
                        $sql_tags = "SELECT t.name FROM tags t JOIN project_tags pt ON t.id = pt.tag_id WHERE pt.project_id = ?";
                        $stmt_tags = $pdo->prepare($sql_tags);

                        foreach ($projects as $project):
                            $stmt_tags->execute([ $project['id'] ]);
                            $tags_list = $stmt_tags->fetchAll(PDO::FETCH_ASSOC);
                            $img_proyek = str_replace('../', '', $project['cover_image']);
                            ?>
                            
                            <a href="menu-proyek-detail/detail-proyek.php?slug=<?= htmlspecialchars($project['slug']) ?>" class="project-card">
                                <div class="project-card-thumbnail">
                                    <img src="../<?= htmlspecialchars($img_proyek) ?>" alt="<?= htmlspecialchars($project['title']) ?>">
                                </div>
                                <div class="project-card-content">
                                    <h4><?= htmlspecialchars($project['title']) ?></h4>
                                    <p><?= htmlspecialchars(substr($project['summary'], 0, 100)) ?>...</p>
                                    <div class="project-card-tags">
                                        <?php foreach($tags_list as $t): ?>
                                            <span class='tag-badge'><?= htmlspecialchars($t['name']) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </a>
                        
                        <?php 
                        endforeach;
                    else:
                        echo "<div class='no-results' style='grid-column: 1/-1; text-align:center; padding:40px;'>
                                <h3>Tidak ada proyek yang ditemukan.</h3>
                                <a href='proyek.php' class='btn' style='margin-top:10px;'>Lihat Semua</a>
                              </div>";
                    endif; 
                    ?>
                </div>

                <?php if ($total_pages > 1): ?>
                <div class="pagination-controls">
                    <?php 
                    $qs = $_GET; 
                    $pages_show = build_pagination($page, $total_pages);

                    // Prev
                    if ($page > 1) {
                        $qs['page'] = $page - 1;
                        echo '<a href="?' . http_build_query($qs) . '" class="btn-page">&laquo;</a>';
                    }

                    // Angka
                    foreach ($pages_show as $p):
                        if ($p === '...') {
                            echo '<span class="page-ellipsis">...</span>';
                        } else {
                            $qs['page'] = $p;
                            $activeClass = ($p == $page) ? 'active' : '';
                            echo '<a href="?' . http_build_query($qs) . '" class="btn-page ' . $activeClass . '">' . $p . '</a>';
                        }
                    endforeach;

                    // Next
                    if ($page < $total_pages) {
                        $qs['page'] = $page + 1;
                        echo '<a href="?' . http_build_query($qs) . '" class="btn-page">&raquo;</a>';
                    }
                    ?>
                </div>
                <?php endif; ?>
                
            </div>
        </div>
        
    </main>

    <?php
    include 'components/floating_profile.php'; 
    renderFloatingProfile();
    require_once 'components/footer.php';
    renderFooter();
    ?>

    <a href="#top" id="scrollTopBtn" class="scroll-top-btn" aria-label="Kembali ke atas">&uarr;</a>

    <script src="../assets/js/navbar.js"></script>
    <script src="assets/proyek/js/script-proyek.js"></script>

</body>
</html>