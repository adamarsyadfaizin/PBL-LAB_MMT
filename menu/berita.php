<?php
if (!isset($_SESSION)) session_start();
// PATH: Naik satu tingkat dari /menu/ ke root /config/
require_once '../config/db.php'; // KONEKSI DATABASE ANDA
require_once '../config/settings.php'; // CMS Setting

// Components
require_once 'components/navbar.php';
require_once 'components/footer.php';
require_once 'components/floating_profile.php';

// Utilities
$path_prefix = "../"; // Digunakan untuk navigasi ke root dari /menu/
$cache_buster = time(); // Untuk refresh CSS/JS

// ==========================================
// 1. LOGIKA UTAMA (Filter, Search, Paging)
// (Kode Logika Anda yang sudah ada)
// ==========================================

$search_term = $_GET['search'] ?? '';
$filter_kategori = $_GET['kategori'] ?? 'semua';
$filter_tahun = $_GET['tahun'] ?? 'semua';

$limit = 3; 
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
    // Menggunakan YEAR() untuk kompatibilitas yang lebih luas, tetapi EXTRACT(YEAR FROM created_at) juga benar
    $sql_conditions .= " AND YEAR(created_at) = ?";
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
    $years_stmt = $pdo->query("SELECT DISTINCT YEAR(created_at) as year FROM news WHERE status = 'published' ORDER BY year DESC");
    $available_years = $years_stmt->fetchAll(PDO::FETCH_COLUMN);

    $cat_stmt = $pdo->query("SELECT DISTINCT category FROM news WHERE status = 'published' AND category IS NOT NULL AND category != '' ORDER BY category ASC");
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
        // Pastikan link detail berita sudah benar
        $events_by_date[$d][] = ['title' => $r['title'], 'slug' => $r['slug'], 'summary' => $r['summary'], 'link' => "menu-detail-berita/detail-berita.php?slug={$r['slug']}"]; 
    }
} catch (PDOException $e) { $events_by_date = []; }

// Encode untuk JavaScript. Menggunakan JSON_UNESCAPED_UNICODE untuk kompatibilitas.
$events_json = json_encode($events_by_date, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);

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
    if ($total > 1 && $total != 1) $pages[] = $total;
    return array_unique($pages);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita & Kegiatan - Laboratorium MMT</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <link rel="stylesheet" href="components/navbar.css?v=<?= $cache_buster ?>"> 
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= $cache_buster ?>"> 
    <link rel="stylesheet" href="assets/berita/css/style-berita.css?v=<?= $cache_buster ?>">
    
    <style>
        /* Background untuk seluruh halaman dengan wallpaper.jpg */
        body {
            background: url('<?= $path_prefix ?>assets/images/wallpaper.jpg') center center/cover fixed no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
        }
        
        /* Efek transparansi halus untuk area konten utama - LEBIH TRANSPARAN */
        .main-content-area {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            position: relative;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        /* Container untuk memastikan konten tetap readable */
        .container {
            position: relative;
            z-index: 1;
        }
        
        /* Hero section styling */
        .hero {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.8)),
                        url('<?= $path_prefix ?><?= htmlspecialchars($site_config['news_hero_image'] ?? 'assets/images/hero.jpg') ?>') center center/cover no-repeat;
        }
        
        /* Tambahan styling untuk card/content agar lebih transparan */
        .primary-content, .sidebar {
            border-radius: 12px;
            padding: 15px;
        }
        
        .widget, .facility-item, .search-filter-container {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 15px;
            margin-bottom: 20px;
        }
        
        /* Event highlight lebih transparan */
        .event-highlight {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.4);
        }
        
        /* Pagination controls transparan */
        .pagination-controls {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(8px);
            border-radius: 8px;
            padding: 15px;
        }
        
        /* No results transparan */
        .no-results {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(8px);
            border-radius: 10px;
            padding: 30px;
        }
    </style>
</head>
<body id="top">
    <?php 
        // Memanggil renderNavbar dengan path prefix dan site config
        renderNavbar('berita', $path_prefix, $site_config); 
    ?>
    
    <main>
        <section class="hero">
            <div class="container">
                <div class="hero-breadcrumb">
                    <a href="<?= $path_prefix ?>index.php">Beranda</a> <i class="fas fa-chevron-right"></i> 
                    <span>Berita & Kegiatan</span>
                </div>
                <h1><?= htmlspecialchars($site_config['news_title'] ?? 'Berita & Kegiatan') ?></h1>
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
                            <img src="<?= $path_prefix ?><?= htmlspecialchars($hl_img) ?>" alt="<?= htmlspecialchars($event_highlight['title']) ?>">
                        </div>
                        <div class="event-highlight-content">
                            <span class="event-tag"><?= htmlspecialchars($event_highlight['category'] ?? 'Terbaru') ?></span>
                            <h2><?= htmlspecialchars($event_highlight['title']) ?></h2>
                            <p class="event-date"><i class="fas fa-calendar-alt"></i> <?= date('d F Y', strtotime($event_highlight['created_at'])) ?></p>
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
                        Ditemukan **<?= $total_news ?>** berita. 
                        <a href="berita.php">Reset Filter</a>
                    </div>
                    <?php endif; ?>
                    
                    <div class="section-separator">
                        <span class="separator-line"></span>
                        <i class="fas fa-newspaper separator-icon"></i> <span class="separator-line"></span>
                    </div>

                    <?php if (count($news_items) > 0): ?>
                        <?php 
                        $pos = 'left';
                        foreach ($news_items as $item): 
                            $class = ($pos == 'left') ? 'image-left' : 'image-right';
                            $pos = ($pos == 'left') ? 'right' : 'left';
                            // Menghapus '../' pada path cover_image yang disimpan di DB
                            $img_news = str_replace('../', '', $item['cover_image']);
                        ?>
                        <article class="facility-item <?= $class ?>">
                            <div class="facility-image">
                                <img src="<?= $path_prefix ?><?= htmlspecialchars($img_news) ?>" alt="<?= htmlspecialchars($item['title']) ?>">
                            </div>
                            <div class="facility-text">
                                <h2><?= htmlspecialchars($item['title']) ?></h2>
                                <span class="article-metadata">
                                    <i class="fas fa-calendar-alt"></i> <?= date('d M Y', strtotime($item['created_at'])) ?>
                                    <?php if (!empty($item['category'])): ?>
                                    <span class="metadata-separator">|</span>
                                    <i class="fas fa-tag"></i> <span class="article-category"><?= htmlspecialchars($item['category']) ?></span>
                                    <?php endif; ?>
                                </span>
                                <p><?= htmlspecialchars(substr($item['summary'], 0, 120)) ?>...</p>
                                <a href="menu-detail-berita/detail-berita.php?slug=<?= htmlspecialchars($item['slug']) ?>" class="btn">Lihat Detail</a>
                            </div>
                        </article>
                        <?php endforeach; ?>

                        <?php if ($total_pages > 1): ?>
                        <div class="pagination-controls">
                            <?php 
                            $q = $_GET; 
                            unset($q['page'], $q['cal_month'], $q['cal_year']); 
                            $pages = build_pagination($page, $total_pages);
                            
                            // Tombol Previous
                            if($page > 1) {
                                $q['page'] = $page - 1;
                                echo '<a class="btn-page" href="?'.http_build_query($q).'">&laquo;</a>';
                            }

                            // Angka Halaman
                            foreach($pages as $p) {
                                if($p === '...') {
                                    echo '<span class="page-ellipsis">...</span>';
                                } else {
                                    $q['page'] = $p;
                                    $act = ($p == $page) ? 'active' : '';
                                    echo '<a class="btn-page '.$act.'" href="?'.http_build_query($q).'">'.$p.'</a>';
                                }
                            }

                            // Tombol Next
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
                            <p>Coba reset filter pencarian atau cek kembali kata kunci Anda.</p>
                            <a href="berita.php" class="btn">Lihat Semua</a>
                        </div>
                    <?php endif; ?>
                </div>

                <aside class="sidebar">
                    <div class="widget widget-calendar">
                        <h3 class="widget-title">Kalender Kegiatan</h3>
                        <?php
                            $cMonth = isset($_GET['cal_month']) ? (int)$_GET['cal_month'] : date('n');
                            $cYear = isset($_GET['cal_year']) ? (int)$_GET['cal_year'] : date('Y');
                            $prevMonth = $cMonth - 1; $prevYear = $cYear; if ($prevMonth < 1) { $prevMonth = 12; $prevYear--; }
                            $nextMonth = $cMonth + 1; $nextYear = $cYear; if ($nextMonth > 12) { $nextMonth = 1; $nextYear++; }
                            
                            function getCalUrl($m, $y) { 
                                $p = $_GET; 
                                unset($p['page']); 
                                $p['cal_month'] = $m; 
                                $p['cal_year'] = $y; 
                                return '?' . http_build_query($p); 
                            }
                            
                            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $cMonth, $cYear);
                            $firstDay = date('N', strtotime("$cYear-$cMonth-01")); 
                            $mNames = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                            
                            echo "<div class='calendar-header'>
                                    <a href='".getCalUrl($prevMonth, $prevYear)."' class='cal-nav-prev' id='calNavPrev'>&laquo;</a>
                                    <span class='cal-current-month' id='calCurrentMonth'>{$mNames[$cMonth]} $cYear</span>
                                    <a href='".getCalUrl($nextMonth, $nextYear)."' class='cal-nav-next' id='calNavNext'>&raquo;</a></div>";
                            echo "<table class='calendar-table' id='calendarTable'><thead><tr><th>Sn</th><th>Sl</th><th>Rb</th><th>Km</th><th>Jm</th><th>Sb</th><th>Mg</th></tr></thead><tbody><tr>";
                            
                            for ($i = 1; $i < $firstDay; $i++) echo "<td></td>";
                            
                            for ($day = 1; $day <= $daysInMonth; $day++) {
                                $dStr = sprintf('%04d-%02d-%02d', $cYear, $cMonth, $day);
                                $cls = isset($events_by_date[$dStr]) ? 'event-day' : '';
                                $today_class = ($dStr == date('Y-m-d')) ? "today-day" : ""; // Class untuk hari ini
                                
                                // Tambahkan event listener inline atau biarkan JS dari script-berita.js menangani event-day
                                echo "<td class='$cls $today_class' data-date='$dStr'>$day</td>";
                                
                                if ((($day + $firstDay - 1) % 7) == 0) echo "</tr><tr>";
                            }
                            
                            if ((($daysInMonth + $firstDay - 1) % 7) != 0) {
                                while(((($daysInMonth + $firstDay - 1) % 7) != 0) && (((($daysInMonth + $firstDay - 1) % 7) < 7))) {
                                    echo "<td></td>";
                                    $daysInMonth++;
                                }
                                echo "</tr>";
                            }
                            echo "</tbody></table>";
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
    
    <a href="#top" id="scrollTopBtn" class="scroll-top-btn">&uarr;</a>

    <?php 
        renderFloatingProfile(); 
        // Memanggil renderFooter dengan path prefix dan site config
        renderFooter($path_prefix, $site_config); 
    ?>

    <div id="modalBackdrop" class="modal-backdrop">
        <div class="modal">
            <button class="close-btn" id="modalCloseBtn">&times;</button>
            <h3 id="modalDateTitle">Agenda <span id="modalDate"></span></h3>
            <div id="modalEventsList"></div> 
        </div>
    </div>

    <script>
        // DATA EVENT DIDEFINISIKAN DI SINI UNTUK DIAKSES OLEH script-berita.js
        // Menggunakan nama variabel eventsByDate agar konsisten dengan PHP Anda ($events_by_date)
        const eventsByDate = <?= $events_json ?>;
        
        // Kode inline untuk Modal Callbacks (Disederhanakan untuk memastikan tidak bentrok)
        const backdrop = document.getElementById('modalBackdrop');
        const modalDateEl = document.getElementById('modalDateTitle'); // ID diubah
        const modalContent = document.getElementById('modalEventsList'); // ID diubah
        const modalClose = document.getElementById('modalCloseBtn'); // ID diubah
        
        // Fungsi openModalForDate akan didefinisikan ulang di script-berita.js, 
        // tapi kita pastikan elemen HTML yang diakses sudah benar.
        
        if(modalClose) modalClose.addEventListener('click', () => { backdrop.style.display = 'none'; });
        document.addEventListener('keydown', function(e) { if (e.key === 'Escape') backdrop.style.display = 'none'; });
        if (backdrop) backdrop.addEventListener('click', function(e) {
             if (e.target === backdrop) backdrop.style.display = 'none';
        });
        
        // Event listener pada TD kalender akan diatur sepenuhnya oleh script-berita.js
        // Hapus kode DOMContentLoaded yang mengakses td.event-day di sini jika Anda menggunakan script-berita.js.
        // Jika Anda TIDAK menggunakan script-berita.js, biarkan kode ini ada.
        
        // Untuk saat ini, kita biarkan kode inline modal yang Anda buat, dan script-berita.js akan menimpanya/melengkapinya.
        
    </script>
    
    <script src="<?= $path_prefix ?>assets/js/navbar.js?v=<?= $cache_buster ?>"></script>
    <script src="<?= $path_prefix ?>assets/js/scrolltop.js?v=<?= $cache_buster ?>"></script>
    <script src="assets/berita/js/script-berita.js?v=<?= $cache_buster ?>"></script>
</body>
</html>