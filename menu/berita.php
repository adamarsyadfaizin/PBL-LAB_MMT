<?php
if (!isset($_SESSION)) session_start();
require_once '../config/db.php';
require_once '../config/settings.php'; // CMS Setting

// ==========================================
// 1. LOGIKA UTAMA (Filter, Search, Paging)
// ==========================================

$search_term = $_GET['search'] ?? '';
$filter_kategori = $_GET['kategori'] ?? 'semua';
$filter_tahun = $_GET['tahun'] ?? 'semua';

// --- PERUBAHAN DISINI: LIMIT JADI 3 ---
$limit = 3; // Hanya tampilkan 3 berita di list bawah
$page = isset($_GET['page']) && is_numeric($_GET['page']) && (int)$_GET['page'] > 0 ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Query Kondisi
$sql_conditions = "WHERE status = 'published'";
$params = [];

if (!empty($search_term)) {
    $sql_conditions .= " AND (title LIKE ? OR summary LIKE ? OR content LIKE ?)";
    $params[] = "%$search_term%";
    $params[] = "%$search_term%";
    $params[] = "%$search_term%";
}

if ($filter_kategori != 'semua') {
    $sql_conditions .= " AND category = ?";
    $params[] = $filter_kategori;
}

if ($filter_tahun != 'semua') {
    $sql_conditions .= " AND EXTRACT(YEAR FROM created_at) = ?";
    $params[] = $filter_tahun;
}

// Hitung Total Data (Untuk Paging)
try {
    $count_sql = "SELECT COUNT(*) FROM news $sql_conditions";
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute($params);
    $total_news = (int) $count_stmt->fetchColumn();
} catch (PDOException $e) {
    $total_news = 0;
}

$total_pages = $total_news > 0 ? (int) ceil($total_news / $limit) : 1;
if ($page > $total_pages) {
    $page = $total_pages;
    $offset = ($page - 1) * $limit;
}

// Ambil Data Berita (List Bawah)
try {
    $params_fetch = $params;
    $params_fetch[] = $limit;
    $params_fetch[] = $offset;

    $sql = "SELECT * FROM news $sql_conditions ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params_fetch);
    $news_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $news_items = [];
}

// Event Highlight (Banner Atas - Tidak Kena Paging)
try {
    $event_sql = "SELECT * FROM news WHERE status = 'published' ORDER BY created_at DESC LIMIT 1";
    $event_stmt = $pdo->prepare($event_sql);
    $event_stmt->execute();
    $event_highlight = $event_stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $event_highlight = null;
}

// Data Dropdown
try {
    $years_stmt = $pdo->query("SELECT DISTINCT EXTRACT(YEAR FROM created_at) as year FROM news WHERE status = 'published' ORDER BY year DESC");
    $available_years = $years_stmt->fetchAll(PDO::FETCH_COLUMN);

    $cat_stmt = $pdo->query("SELECT DISTINCT category FROM news WHERE status = 'published' AND category IS NOT NULL AND category != ''");
    $available_categories = $cat_stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $available_years = [];
    $available_categories = [];
}

// Kalender Data
$events_by_date = [];
try {
    $stmt_events = $pdo->query("SELECT id, title, slug, summary, DATE(created_at) as event_date FROM news WHERE status = 'published' ORDER BY created_at DESC");
    $rows = $stmt_events->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        $d = $r['event_date'];
        if (!isset($events_by_date[$d])) $events_by_date[$d] = [];
        $events_by_date[$d][] = ['title' => $r['title'], 'slug' => $r['slug'], 'summary' => $r['summary']];
    }
} catch (PDOException $e) { $events_by_date = []; }

$events_json = json_encode($events_by_date, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);

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
    <title>Berita & Kegiatan - Laboratorium MMT</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="assets/berita/css/style-berita.css">
    
    <style>
        .hero {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.8)),
                        url('../<?= htmlspecialchars($site_config['hero_image_path'] ?? 'assets/images/hero.jpg') ?>') center center/cover no-repeat;
            height: 300px !important; 
        }
        .hero h1 { margin-bottom: 0; }
    </style>
</head>
<body id="top">

    <?php
    require_once 'components/navbar.php';
    renderNavbar('berita');
    ?>

    <main>
        <section class="hero">
            <div class="container">
                <h1>Berita & Kegiatan</h1>
            </div>
        </section>

        <div class="main-content-area">
            <div class="container">
                
                <div class="primary-content">

                    <?php if ($event_highlight && empty($search_term) && $filter_kategori == 'semua' && $filter_tahun == 'semua'): 
                        $hl_img = str_replace('../', '', $event_highlight['cover_image']);
                    ?>
                    <section class="event-highlight">
                        <div class="event-highlight-img">
                            <img src="../<?= htmlspecialchars($hl_img) ?>" alt="<?= htmlspecialchars($event_highlight['title']) ?>">
                        </div>
                        <div class="event-highlight-content">
                            <span class="event-tag">Terbaru</span>
                            <h2><?= htmlspecialchars($event_highlight['title']) ?></h2>
                            <p class="event-date"><?= date('d F Y', strtotime($event_highlight['created_at'])) ?></p>
                            <p><?= htmlspecialchars(substr($event_highlight['summary'], 0, 150)) ?>...</p>
                            <a href="menu-detail-berita/detail-berita.php?slug=<?= htmlspecialchars($event_highlight['slug']) ?>" class="btn">Baca Selengkapnya</a>
                        </div>
                    </section>
                    <?php endif; ?>

                    <div class="search-filter-container">
                        <form action="" method="get">
                            <div class="search-row">
                                <div class="search-input-group">
                                    <label>Cari Berita</label>
                                    <input type="text" name="search" placeholder="Kata kunci..." value="<?= htmlspecialchars($search_term) ?>">
                                </div>
                                <button type="submit" class="btn-search">Cari</button>
                            </div>

                            <div class="filter-row">
                                <div class="filter-group">
                                    <label>Kategori</label>
                                    <select name="kategori">
                                        <option value="semua">Semua</option>
                                        <?php foreach ($available_categories as $cat): ?>
                                            <option value="<?= $cat ?>" <?= ($filter_kategori == $cat) ? 'selected' : '' ?>><?= $cat ?></option>
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
                    </div>

                    <?php if (!empty($search_term) || $filter_kategori != 'semua' || $filter_tahun != 'semua'): ?>
                    <div class="search-results-info">
                        Ditemukan <?= count($news_items) ?> berita. 
                        <a href="berita.php" style="color:#fff; text-decoration:underline; margin-left:10px;">Reset Filter</a>
                    </div>
                    <?php endif; ?>

                    <?php if (count($news_items) > 0): ?>
                        <?php 
                        $pos = 'left';
                        foreach ($news_items as $item): 
                            $class = ($pos == 'left') ? 'image-left' : 'image-right';
                            $pos = ($pos == 'left') ? 'right' : 'left';
                            $img_news = str_replace('../', '', $item['cover_image']);
                        ?>
                        <article class="facility-item <?= $class ?>">
                            <div class="facility-image">
                                <img src="../<?= htmlspecialchars($img_news) ?>" alt="<?= htmlspecialchars($item['title']) ?>">
                            </div>
                            <div class="facility-text">
                                <h2><?= htmlspecialchars($item['title']) ?></h2>
                                <span class="article-metadata"><i class="fas fa-calendar-alt"></i> <?= date('d M Y', strtotime($item['created_at'])) ?></span>
                                <p><?= htmlspecialchars(substr($item['summary'], 0, 120)) ?>...</p>
                                <a href="menu-detail-berita/detail-berita.php?slug=<?= htmlspecialchars($item['slug']) ?>" class="btn">Lihat Detail</a>
                            </div>
                        </article>
                        <?php endforeach; ?>

                        <?php if ($total_pages > 1): ?>
                        <div class="pagination-controls">
                            <?php 
                            $q = $_GET; 
                            $pages = build_pagination($page, $total_pages);
                            
                            if($page > 1) {
                                $q['page'] = $page - 1;
                                echo '<a class="btn-page" href="?'.http_build_query($q).'">&laquo;</a>';
                            }

                            foreach($pages as $p) {
                                if($p === '...') {
                                    echo '<span class="page-ellipsis">...</span>';
                                } else {
                                    $q['page'] = $p;
                                    $act = ($p == $page) ? 'active' : '';
                                    echo '<a class="btn-page '.$act.'" href="?'.http_build_query($q).'">'.$p.'</a>';
                                }
                            }

                            if($page < $total_pages) {
                                $q['page'] = $page + 1;
                                echo '<a class="btn-page" href="?'.http_build_query($q).'">&raquo;</a>';
                            }
                            ?>
                        </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="no-results">
                            <h3>Tidak ada berita ditemukan.</h3>
                            <a href="berita.php" class="btn">Lihat Semua</a>
                        </div>
                    <?php endif; ?>
                </div>

                <aside class="sidebar">
                    <div class="widget widget-calendar">
                        <h3 class="widget-title">Kalender Kegiatan</h3>
                        <?php
                            $cMonth = isset($_GET['cal_month']) ? (int)$_GET['cal_month'] : date('n');
                            $cYear  = isset($_GET['cal_year']) ? (int)$_GET['cal_year'] : date('Y');
                            $prevMonth = $cMonth - 1; $prevYear = $cYear; if ($prevMonth < 1) { $prevMonth = 12; $prevYear--; }
                            $nextMonth = $cMonth + 1; $nextYear = $cYear; if ($nextMonth > 12) { $nextMonth = 1; $nextYear++; }
                            function getCalUrl($m, $y) { $p = $_GET; $p['cal_month'] = $m; $p['cal_year'] = $y; return '?' . http_build_query($p); }
                            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $cMonth, $cYear);
                            $firstDay = date('N', strtotime("$cYear-$cMonth-01"));
                            $mNames = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                            echo "<div style='display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; padding:0 10px;'>
                                  <a href='".getCalUrl($prevMonth, $prevYear)."' style='text-decoration:none; font-weight:bold; color:#003b8e;'>&laquo;</a>
                                  <span style='font-weight:bold; color:#003b8e;'>{$mNames[$cMonth]} $cYear</span>
                                  <a href='".getCalUrl($nextMonth, $nextYear)."' style='text-decoration:none; font-weight:bold; color:#003b8e;'>&raquo;</a></div>";
                            echo "<table class='calendar-table'><thead><tr><th>Sn</th><th>Sl</th><th>Rb</th><th>Km</th><th>Jm</th><th>Sb</th><th>Mg</th></tr></thead><tbody><tr>";
                            for ($i = 1; $i < $firstDay; $i++) echo "<td></td>";
                            for ($day = 1; $day <= $daysInMonth; $day++) {
                                $dStr = sprintf('%04d-%02d-%02d', $cYear, $cMonth, $day);
                                $cls = isset($events_by_date[$dStr]) ? 'event-day' : '';
                                $tdStyle = ($dStr == date('Y-m-d')) ? "style='border:2px solid #ffc700; border-radius:50%; font-weight:bold;'" : "";
                                echo "<td class='$cls' data-date='$dStr' $tdStyle>$day</td>";
                                if ((($day + $firstDay - 1) % 7) == 0) echo "</tr><tr>";
                            }
                            echo "</tr></tbody></table>";
                        ?>
                    </div>
                    <div class="widget widget-news">
                        <h3 class="widget-title">Terkini</h3>
                        <ul>
                            <?php 
                            $side_stmt = $pdo->query("SELECT title, slug, created_at FROM news WHERE status = 'published' ORDER BY created_at DESC LIMIT 4");
                            while($s = $side_stmt->fetch()): ?>
                            <li><a href="menu-detail-berita/detail-berita.php?slug=<?= $s['slug'] ?>"><h4><?= htmlspecialchars($s['title']) ?></h4><span class="date"><?= date('d M Y', strtotime($s['created_at'])) ?></span></a></li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </aside>

            </div>
        </div>
    </main>

    <?php include 'components/floating_profile.php'; renderFloatingProfile(); require_once 'components/footer.php'; renderFooter(); ?>

    <div id="modalBackdrop" class="modal-backdrop"><div class="modal"><button class="close-btn" id="modalClose">&times;</button><h3>Agenda <span id="modalDate"></span></h3><div id="modalContent"></div></div></div>
    <script>
        const eventsByDate = <?= $events_json ?>;
        const backdrop = document.getElementById('modalBackdrop');
        const modalDateEl = document.getElementById('modalDate');
        const modalContent = document.getElementById('modalContent');
        const modalClose = document.getElementById('modalClose');
        function openModalForDate(dateStr) {
            const dateObj = new Date(dateStr);
            modalDateEl.textContent = dateObj.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            modalContent.innerHTML = '';
            const events = eventsByDate[dateStr] || [];
            if (events.length === 0) modalContent.innerHTML = '<p style="text-align:center; color:#666;">Tidak ada kegiatan.</p>';
            else {
                events.forEach(ev => {
                    modalContent.innerHTML += `<div class="event-item" style="margin-bottom:10px; border-bottom:1px solid #eee; padding-bottom:5px;"><h4><a href="menu-detail-berita/detail-berita.php?slug=${ev.slug}" style="color:#003b8e;">${ev.title}</a></h4><p style="font-size:13px; color:#555;">${ev.summary || ''}</p></div>`;
                });
            }
            backdrop.style.display = 'flex';
        }
        function closeModal() { backdrop.style.display = 'none'; }
        document.addEventListener('click', function(ev) {
            const td = ev.target.closest('td[data-date]');
            if (td) openModalForDate(td.getAttribute('data-date'));
            if (ev.target === backdrop) closeModal();
        });
        if(modalClose) modalClose.addEventListener('click', closeModal);
        document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeModal(); });
    </script>
    <script src="../assets/js/navbar.js"></script>
    <script src="assets/berita/js/script-berita.js"></script>
</body>
</html>