<?php
if (!isset($_SESSION)) session_start();
require_once '../config/db.php';
require_once '../config/settings.php'; // CMS Setting

// --- LOGIKA FILTER & PAGINATION ---
$limit = 6; 
$page = (int)($_GET['page'] ?? 1);
$offset = ($page - 1) * $limit;

$search_term = $_GET['search'] ?? '';
$filter_jenis = $_GET['jenis'] ?? 'semua';
$filter_acara = $_GET['acara'] ?? 'semua';
$filter_tahun = $_GET['tahun'] ?? 'semua';

// Query Builder
$sql_conditions = "WHERE 1=1";
$params = [];

if (!empty($search_term)) {
    $sql_conditions .= " AND (caption LIKE ? OR event_name LIKE ?)";
    $params[] = "%$search_term%";
    $params[] = "%$search_term%";
}

if ($filter_jenis != 'semua') {
    $sql_conditions .= " AND type = ?";
    $params[] = $filter_jenis;
}

if ($filter_acara != 'semua') {
    $sql_conditions .= " AND event_name = ?";
    $params[] = $filter_acara;
}

if ($filter_tahun != 'semua') {
    $sql_conditions .= " AND EXTRACT(YEAR FROM created_at) = ?";
    $params[] = $filter_tahun;
}

// Hitung Total Data
$sql_count = "SELECT COUNT(*) FROM media_assets $sql_conditions";
$stmt_count = $pdo->prepare($sql_count);
$stmt_count->execute($params);
$total_items = $stmt_count->fetchColumn();
$total_pages = ceil($total_items / $limit);

// Query Final
$sql = "SELECT * FROM media_assets $sql_conditions ORDER BY created_at DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$media_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Data Dropdown
try {
    $types_stmt = $pdo->query("SELECT DISTINCT type FROM media_assets WHERE type IS NOT NULL ORDER BY type");
    $available_types = $types_stmt->fetchAll(PDO::FETCH_COLUMN);

    $events_stmt = $pdo->query("SELECT DISTINCT event_name FROM media_assets WHERE event_name IS NOT NULL AND event_name != '' ORDER BY event_name");
    $available_events = $events_stmt->fetchAll(PDO::FETCH_COLUMN);

    $years_stmt = $pdo->query("SELECT DISTINCT EXTRACT(YEAR FROM created_at) as year FROM media_assets ORDER BY year DESC");
    $available_years = $years_stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $available_types = []; $available_events = []; $available_years = [];
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
    <title>Galeri Multimedia - Laboratorium MMT</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <link rel="stylesheet" href="../assets/css/style.css">
    
    <link rel="stylesheet" href="assets/galeri/css/style-galeri.css?v=<?php echo time(); ?>">
    
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
    renderNavbar('galeri');
    ?>

    <main>
        <section class="hero">
            <div class="container">
                <h1>Galeri Multimedia</h1>
               
            </div>
        </section>

        <div class="main-content-area">
            <div class="container">

                <form class="search-filter-container" action="" method="get">
                    <div class="search-row">
                        <div class="search-input-group">
                            <label for="search">Cari Media</label>
                            <input type="text" id="search" name="search" placeholder="Kata kunci..." value="<?= htmlspecialchars($search_term) ?>">
                        </div>
                        <button type="submit" class="btn-search">Cari</button>
                    </div>

                    <div class="filter-row">
                        <div class="filter-group">
                            <label>Jenis</label>
                            <select name="jenis">
                                <option value="semua">Semua</option>
                                <?php foreach ($available_types as $type): ?>
                                    <option value="<?= $type ?>" <?= ($filter_jenis == $type) ? 'selected' : '' ?>><?= ucfirst($type) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label>Acara</label>
                            <select name="acara">
                                <option value="semua">Semua</option>
                                <?php foreach ($available_events as $event): ?>
                                    <option value="<?= $event ?>" <?= ($filter_acara == $event) ? 'selected' : '' ?>><?= $event ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label>Tahun</label>
                            <select name="tahun">
                                <option value="semua">Semua</option>
                                <?php foreach ($available_years as $y): ?>
                                    <option value="<?= $y ?>" <?= ($filter_tahun == $y) ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn-filter">Filter</button>
                    </div>
                </form>

                <?php if (!empty($search_term) || $filter_jenis != 'semua' || $filter_acara != 'semua' || $filter_tahun != 'semua'): ?>
                <div class="search-results-info">
                    Menampilkan <?= $total_items ?> media.
                    <a href="galeri.php" style="color:var(--color-accent); font-weight:bold; margin-left:10px;">Reset Filter</a>
                </div>
                <?php endif; ?>

                <?php if (count($media_items) > 0): ?>
                <div class="gallery-grid-main">
                    <?php foreach ($media_items as $item): 
                        $icon = ($item['type'] === 'video') ? 'fa-play' : (($item['type'] === 'animasi') ? 'fa-film' : 'fa-camera');
                        $media_type = ucfirst($item['type']);
                        
                        // Fix path gambar
                        $is_link = filter_var($item['url'], FILTER_VALIDATE_URL);
                        $img_src = $is_link ? $item['url'] : "../" . str_replace('../', '', $item['url']);
                    ?>
                    <a href="menu-detail-galeri/galeri-detail.php?id=<?= $item['id'] ?>" class="gallery-item">
                        <div class="image-container">
                            <img src="<?= htmlspecialchars($img_src) ?>" alt="<?= htmlspecialchars($item['caption']) ?>">
                            <div class="overlay"><i class="fas <?= $icon ?>"></i></div>
                            <span class="type-badge"><?= $media_type ?></span>
                        </div>
                        <div class="item-info">
                            <h4><?= htmlspecialchars($item['caption']) ?></h4>
                            <?php if (!empty($item['event_name'])): ?>
                            <span class="event-tag"><?= htmlspecialchars($item['event_name']) ?></span>
                            <?php endif; ?>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>

                <?php if ($total_pages > 1): ?>
                <div class="pagination-controls">
                    <?php 
                    $qs = $_GET;
                    $pages_show = build_pagination($page, $total_pages);

                    if ($page > 1) {
                        $qs['page'] = $page - 1;
                        echo '<a href="?' . http_build_query($qs) . '" class="btn-page">&laquo;</a>';
                    }

                    foreach ($pages_show as $p):
                        if ($p === '...') {
                            echo '<span class="page-ellipsis">...</span>';
                        } else {
                            $qs['page'] = $p;
                            $active = ($p == $page) ? 'active' : '';
                            echo '<a href="?' . http_build_query($qs) . '" class="btn-page ' . $active . '">' . $p . '</a>';
                        }
                    endforeach;

                    if ($page < $total_pages) {
                        $qs['page'] = $page + 1;
                        echo '<a href="?' . http_build_query($qs) . '" class="btn-page">&raquo;</a>';
                    }
                    ?>
                </div>
                <?php endif; ?>

                <?php else: ?>
                <div class="no-results" style="text-align:center; padding:50px; background:#f9f9f9; border-radius:8px;">
                    <h3>Tidak ada media ditemukan.</h3>
                    <a href="galeri.php" class="btn" style="margin-top:10px;">Lihat Semua</a>
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
    <script src="assets/galeri/js/script-galeri.js"></script>

</body>
</html>