<?php
if (!isset($_SESSION)) session_start();
require_once '../config/db.php';

// Ambil parameter pencarian & filter dari URL
$search_term = $_GET['search'] ?? '';
$filter_kategori = $_GET['kategori'] ?? 'semua';
$filter_tahun = $_GET['tahun'] ?? 'semua';

// --- PAGINATION SETUP ---
$limit = 6; // jumlah berita per halaman
$page = isset($_GET['page']) && is_numeric($_GET['page']) && (int)$_GET['page'] > 0 ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// --- Query Dinamis (kondisi) ---
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

// --- Hitung total berita untuk pagination ---
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

// --- Ambil berita dengan LIMIT dan OFFSET ---
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

// --- Event Highlight ---
try {
    $event_sql = "SELECT * FROM news WHERE status = 'published' ORDER BY created_at DESC LIMIT 1";
    $event_stmt = $pdo->prepare($event_sql);
    $event_stmt->execute();
    $event_highlight = $event_stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $event_highlight = null;
}

// --- Tahun untuk filter ---
try {
    $years_sql = "SELECT DISTINCT EXTRACT(YEAR FROM created_at) as year 
                  FROM news 
                  WHERE status = 'published' 
                  ORDER BY year DESC";
    $years_stmt = $pdo->query($years_sql);
    $available_years = $years_stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $available_years = [];
}

// --- Kategori untuk filter ---
try {
    $categories_sql = "SELECT DISTINCT category 
                       FROM news 
                       WHERE status = 'published' 
                       AND category IS NOT NULL 
                       AND category != ''";
    $categories_stmt = $pdo->query($categories_sql);
    $available_categories = $categories_stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $available_categories = [];
}

// --- Events grouped by date (untuk kalender + popup) ---
$events_by_date = [];
try {
    $stmt_events = $pdo->query("SELECT id, title, slug, summary, DATE(created_at) as event_date FROM news WHERE status = 'published' ORDER BY created_at DESC");
    $rows = $stmt_events->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        $d = $r['event_date'];
        if (!isset($events_by_date[$d])) $events_by_date[$d] = [];
        $events_by_date[$d][] = [
            'id' => $r['id'],
            'title' => $r['title'],
            'slug' => $r['slug'],
            'summary' => $r['summary']
        ];
    }
} catch (PDOException $e) {
    $events_by_date = [];
}

// Helper: buat array pagination terbatas (1 2 ... 8 9 10)
function build_pagination($current, $total, $adj = 2) {
    // adj = jumlah halaman yang ditampilkan di kiri/kanan current
    $pages = [];

    if ($total <= 1) {
        $pages = [1];
        return $pages;
    }

    // selalu include 1
    $pages[] = 1;

    // if left block
    $start = max(2, $current - $adj);
    $end = min($total - 1, $current + $adj);

    if ($start > 2) {
        $pages[] = '...';
    }

    for ($i = $start; $i <= $end; $i++) {
        $pages[] = $i;
    }

    if ($end < $total - 1) {
        $pages[] = '...';
    }

    if ($total > 1) {
        $pages[] = $total;
    }

    // remove duplicates while preserving order
    $seen = [];
    $out = [];
    foreach ($pages as $p) {
        if (!isset($seen[$p])) {
            $seen[$p] = true;
            $out[] = $p;
        }
    }
    return $out;
}

// JSON for JS use (escape)
$events_json = json_encode($events_by_date, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita & Kegiatan - Laboratorium Mobile and Multimedia Tech POLINEMA</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/berita/css/style-berita.css">
    
    <style>
        .hero {
            background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.5)),
                        url('../assets/images/hero.jpg') center center/cover no-repeat;
            height: 300px;
            display:flex;
            align-items:center;
            color:var(--color-white);
        }
        .hero h1 { font-size:36px; font-weight:700; text-transform:uppercase; letter-spacing:1px; text-shadow:2px 2px 4px rgba(0,0,0,0.4); }

        .search-filter-container { background:var(--color-bg-light); padding:20px; border-radius:8px; margin-bottom:30px; }
        .search-row{ display:flex; gap:15px; align-items:end; margin-bottom:15px; }
        .search-input-group{ flex:1; }
        .search-input-group label{ display:block; margin-bottom:5px; font-weight:600; font-size:14px; }
        .search-input-group input, .filter-group select { width:100%; padding:8px 12px; border:1px solid var(--color-border-light); border-radius:4px; font-size:14px; }
        .btn-search, .btn-filter { background:var(--color-primary); color:var(--color-white); border:none; padding:8px 20px; border-radius:4px; font-weight:600; cursor:pointer; font-size:14px; height:35px; }
        .btn-search:hover, .btn-filter:hover { background:var(--color-accent); color:var(--color-text); }
        .filter-row{ display:flex; gap:15px; align-items:end; }
        .search-results-info { background:var(--color-primary); color:var(--color-white); padding:10px 15px; border-radius:4px; margin-bottom:20px; font-size:14px; }
        .no-results{ text-align:center; padding:40px 20px; background:var(--color-bg-light); border-radius:8px; margin:30px 0; }
        .calendar-table td.event-day { background-color: #ffd54f; border-radius:4px; font-weight:bold; color:#000; }
        .calendar-table td.event-day:hover { background-color:#ffca28; cursor:pointer; }
        .calendar-table { width:100%; border-collapse:collapse; text-align:center; }
        .calendar-table th, .calendar-table td { padding:8px; border-radius:4px; }

        /* Pagination */
        .pagination { display:flex; justify-content:center; gap:6px; margin-top:30px; flex-wrap:wrap; }
        .page-btn { display:inline-block; padding:6px 12px; background:var(--color-bg-light); border:1px solid var(--color-border-light); color:var(--color-text); border-radius:4px; text-decoration:none; font-size:14px; }
        .page-btn:hover { background:var(--color-primary); color:#fff; }
        .page-btn.active { background:var(--color-primary); color:#fff; font-weight:bold; }
        .page-ellipsis { display:inline-block; padding:6px 12px; color:var(--color-text); background:transparent; border:none; }

        /* Simple modal */
        .modal-backdrop { position:fixed; inset:0; background:rgba(0,0,0,0.5); display:none; align-items:center; justify-content:center; z-index:9999; }
        .modal { background:#fff; width:90%; max-width:600px; border-radius:8px; padding:18px; box-shadow:0 10px 30px rgba(0,0,0,0.2); max-height:80vh; overflow:auto; }
        .modal h3 { margin-top:0; }
        .modal .close-btn { float:right; background:transparent; border:none; font-size:18px; cursor:pointer; }
        .event-item { padding:10px 0; border-bottom:1px solid #eee; }
        .event-item:last-child { border-bottom:none; }
        .event-item h4 { margin:0 0 6px 0; font-size:16px; }
        .event-item p { margin:0; color:#555; font-size:14px; }
        .no-event { padding:12px; text-align:center; color:#666; }

        @media (max-width:768px) {
            .search-row, .filter-row { flex-direction:column; }
        }
    </style>
</head>
<body id="top">

    <?php
    // Include navbar
    require_once 'components/navbar.php';
    renderNavbar('berita');
    ?>

    <main>
        <section class="hero">
            <div class="container"><h1>Berita & Kegiatan</h1></div>
        </section>

        <div class="main-content-area">
            <div class="container">
                <div class="primary-content">

                    <?php if ($event_highlight && empty($search_term) && $filter_kategori == 'semua' && $filter_tahun == 'semua'): ?>
                    <section class="event-highlight">
                        <div class="event-highlight-img">
                            <?php if (!empty($event_highlight['cover_image'])): ?>
                                <img src="<?php echo htmlspecialchars($event_highlight['cover_image']); ?>" alt="Banner <?php echo htmlspecialchars($event_highlight['title']); ?>">
                            <?php endif; ?>
                        </div>
                        <div class="event-highlight-content">
                            <span class="event-tag">Berita Terbaru</span>
                            <h2><?php echo htmlspecialchars($event_highlight['title'] ?? ''); ?></h2>
                            <p class="event-date"><?php echo isset($event_highlight['created_at']) ? date('d F Y', strtotime($event_highlight['created_at'])) : ''; ?></p>
                            <p><?php echo htmlspecialchars($event_highlight['summary'] ?? ''); ?></p>
                            <a href="menu-detail-berita/detail-berita.php?slug=<?php echo htmlspecialchars($event_highlight['slug'] ?? ''); ?>" class="btn">Baca Selengkapnya</a>
                        </div>
                    </section>
                    <?php endif; ?>

                    <!-- Form Pencarian & Filter -->
                    <div class="search-filter-container">
                        <form action="" method="get">
                            <div class="search-row">
                                <div class="search-input-group">
                                    <label for="search">Cari Berita</label>
                                    <input type="text" id="search" name="search" placeholder="Masukkan kata kunci..." value="<?php echo htmlspecialchars($search_term); ?>">
                                </div>
                                <button type="submit" class="btn-search">Cari</button>
                            </div>

                            <div class="filter-row">
                                <div class="filter-group">
                                    <label for="kategori">Kategori</label>
                                    <select id="kategori" name="kategori">
                                        <option value="semua">Semua Kategori</option>
                                        <?php foreach ($available_categories as $category): ?>
                                            <option value="<?php echo htmlspecialchars($category); ?>" <?php echo ($filter_kategori == $category) ? 'selected' : ''; ?>><?php echo htmlspecialchars($category); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="filter-group">
                                    <label for="tahun">Tahun</label>
                                    <select id="tahun" name="tahun">
                                        <option value="semua">Semua Tahun</option>
                                        <?php foreach ($available_years as $y): ?>
                                            <option value="<?php echo htmlspecialchars($y); ?>" <?php echo ($filter_tahun == $y) ? 'selected' : ''; ?>><?php echo htmlspecialchars($y); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn-filter">Terapkan Filter</button>
                            </div>
                        </form>
                    </div>

                    <!-- Info hasil -->
                    <?php if (!empty($search_term) || $filter_kategori != 'semua' || $filter_tahun != 'semua'): ?>
                    <div class="search-results-info">
                        Menampilkan <?php echo count($news_items); ?> berita 
                        <?php 
                        if (!empty($search_term)) echo " dengan kata kunci: \"" . htmlspecialchars($search_term) . "\"";
                        if ($filter_kategori != 'semua') echo " dalam kategori: " . htmlspecialchars($filter_kategori);
                        if ($filter_tahun != 'semua') echo " tahun: " . htmlspecialchars($filter_tahun);
                        ?>
                        | <a href="berita.php" style="color: var(--color-accent); margin-left: 10px;">Tampilkan Semua</a>
                    </div>
                    <?php endif; ?>

                    <!-- Daftar berita -->
                    <?php if (count($news_items) > 0): ?>
                        <?php 
                        $image_position = 'left';
                        foreach ($news_items as $index => $item): 
                            $position_class = ($image_position == 'left') ? 'image-left' : 'image-right';
                            $image_position = ($image_position == 'left') ? 'right' : 'left';
                        ?>
                        <article class="facility-item <?php echo $position_class; ?>">
                            <div class="facility-image">
                                <?php if (!empty($item['cover_image'])): ?>
                                    <img src="<?php echo htmlspecialchars($item['cover_image']); ?>" alt="Gambar <?php echo htmlspecialchars($item['title']); ?>">
                                <?php endif; ?>
                            </div>
                            <div class="facility-text">
                                <h2><?php echo htmlspecialchars($item['title'] ?? ''); ?></h2>
                                <span class="article-metadata">
                                    Diposting pada: <?php echo isset($item['created_at']) ? date('d M Y', strtotime($item['created_at'])) : ''; ?>
                                </span>
                                <p><?php echo htmlspecialchars($item['summary'] ?? ''); ?></p>
                                <a href="menu-detail-berita/detail-berita.php?slug=<?php echo htmlspecialchars($item['slug'] ?? ''); ?>" class="btn">Lihat Detail</a>
                            </div>
                        </article>
                        <?php endforeach; ?>

                        <!-- Pagination (terbatas) -->
                        <?php if ($total_pages > 1 || $total_pages == 1): ?>
                            <div class="pagination">
                                <?php
                                $currentQuery = $_GET;
                                // ensure page param is present for consistency
                                if (!isset($currentQuery['page'])) $currentQuery['page'] = $page;
                                $pagesToShow = build_pagination($page, $total_pages, 2);

                                // Prev
                                if ($page > 1) {
                                    $currentQuery['page'] = $page - 1;
                                    echo '<a class="page-btn" href="?' . htmlspecialchars(http_build_query($currentQuery)) . '">&laquo; Prev</a>';
                                }

                                foreach ($pagesToShow as $p) {
                                    if ($p === '...') {
                                        echo '<span class="page-ellipsis">...</span>';
                                        continue;
                                    }
                                    $currentQuery['page'] = $p;
                                    $class = ($p == $page) ? 'page-btn active' : 'page-btn';
                                    echo '<a class="' . $class . '" href="?' . htmlspecialchars(http_build_query($currentQuery)) . '">' . $p . '</a>';
                                }

                                // Next
                                if ($page < $total_pages) {
                                    $currentQuery['page'] = $page + 1;
                                    echo '<a class="page-btn" href="?' . htmlspecialchars(http_build_query($currentQuery)) . '">Next &raquo;</a>';
                                }
                                ?>
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="no-results">
                            <h3>😔 Tidak ada berita yang ditemukan</h3>
                            <p>Silakan coba dengan kata kunci atau filter yang berbeda.</p>
                            <a href="berita.php" class="btn">Tampilkan Semua Berita</a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar -->
                <aside class="sidebar">
                    <div class="widget widget-calendar">
                        <h3 class="widget-title">Kalender Kegiatan</h3>
                        <?php
                            // Ambil bulan & tahun saat ini
                            $month = date('n');  // 1–12
                            $yearNow = date('Y');

                            // Hitung jumlah hari dalam bulan ini
                            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $yearNow);

                            // Cari hari pertama bulan ini jatuh di hari apa (1 = Senin)
                            $firstDayOfWeek = date('N', strtotime("$yearNow-$month-01")); // 1 (Mon) - 7 (Sun)

                            echo "<table class='calendar-table'>";
                            echo "<caption>" . date('F Y') . "</caption>";
                            echo "<thead><tr><th>Sn</th><th>Sl</th><th>Rb</th><th>Km</th><th>Jm</th><th>Sb</th><th>Mg</th></tr></thead><tbody><tr>";

                            // Kosongin sel sebelum tanggal 1
                            for ($i = 1; $i < $firstDayOfWeek; $i++) {
                                echo "<td class='other-month'></td>";
                            }

                            // Loop tanggal
                            for ($day = 1; $day <= $daysInMonth; $day++) {
                                $dateStr = sprintf('%04d-%02d-%02d', $yearNow, $month, $day);

                                // Cek apakah tanggal ini ada di event
                                $isEvent = array_key_exists($dateStr, $events_by_date);

                                // Tambahkan kelas warna kuning kalau ada event
                                $class = $isEvent ? 'event-day' : '';
                                $dataAttr = $isEvent ? " data-date=\"{$dateStr}\"" : "";
                                echo "<td class='{$class}'{$dataAttr}>{$day}</td>";

                                // Ganti baris tiap 7 kolom
                                if ((($day + $firstDayOfWeek - 1) % 7) == 0 && $day != $daysInMonth) {
                                    echo "</tr><tr>";
                                }
                            }

                            echo "</tr></tbody></table>";
                        ?>
                    </div>

                    <div class="widget widget-recent-news">
                        <h3 class="widget-title">Berita Terbaru</h3>
                        <ul>
                            <?php 
                            // Pastikan we show latest overall (not just page slice)
                            // ambil 3 berita terbaru global
                            try {
                                $stmt_latest = $pdo->prepare("SELECT title, slug, created_at FROM news WHERE status = 'published' ORDER BY created_at DESC LIMIT 3");
                                $stmt_latest->execute();
                                $latest3 = $stmt_latest->fetchAll(PDO::FETCH_ASSOC);
                            } catch (PDOException $e) {
                                $latest3 = [];
                            }

                            foreach ($latest3 as $recent): ?>
                            <li>
                                <h4><a href="menu-detail-berita/detail-berita.php?slug=<?php echo htmlspecialchars($recent['slug']); ?>">
                                    <?php echo htmlspecialchars($recent['title']); ?></a></h4>
                                <span class="pub-source"><?php echo isset($recent['created_at']) ? date('d M Y', strtotime($recent['created_at'])) : ''; ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </aside>
            </div>
        </div>
    </main>

    <?php
    include 'components/floating_profile.php'; 
    renderFloatingProfile(); 

    // Include footer
    require_once 'components/footer.php';
    renderFooter();
    ?>

    <!-- Modal for calendar events -->
    <div id="modalBackdrop" class="modal-backdrop" aria-hidden="true">
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
            <button class="close-btn" id="modalClose" aria-label="Tutup">&times;</button>
            <h3 id="modalTitle">Acara pada <span id="modalDate"></span></h3>
            <div id="modalContent"></div>
        </div>
    </div>

    <script>
        // events data from PHP
        const eventsByDate = <?php echo $events_json ?? '{}'; ?>;

        // modal elements
        const backdrop = document.getElementById('modalBackdrop');
        const modalDateEl = document.getElementById('modalDate');
        const modalContent = document.getElementById('modalContent');
        const modalClose = document.getElementById('modalClose');

        function openModalForDate(dateStr) {
            modalDateEl.textContent = dateStr;
            modalContent.innerHTML = '';

            const events = eventsByDate[dateStr] || [];
            if (events.length === 0) {
                modalContent.innerHTML = '<div class="no-event">Tidak ada acara untuk tanggal ini.</div>';
            } else {
                events.forEach(ev => {
                    const div = document.createElement('div');
                    div.className = 'event-item';
                    const title = document.createElement('h4');
                    const a = document.createElement('a');
                    a.href = 'menu-detail-berita/detail-berita.php?slug=' + encodeURIComponent(ev.slug);
                    a.textContent = ev.title;
                    title.appendChild(a);
                    const p = document.createElement('p');
                    p.textContent = ev.summary ? ev.summary : '';
                    div.appendChild(title);
                    div.appendChild(p);
                    modalContent.appendChild(div);
                });
            }

            backdrop.style.display = 'flex';
            backdrop.setAttribute('aria-hidden', 'false');
        }

        function closeModal() {
            backdrop.style.display = 'none';
            backdrop.setAttribute('aria-hidden', 'true');
        }

        // attach click handlers to calendar event cells
        document.addEventListener('click', function(ev) {
            const td = ev.target.closest('td[data-date]');
            if (td) {
                const dateStr = td.getAttribute('data-date');
                openModalForDate(dateStr);
            }

            // close when clicking backdrop outside modal
            if (ev.target === backdrop) {
                closeModal();
            }
        });

        modalClose.addEventListener('click', closeModal);
        // close on ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeModal();
        });
    </script>

    <script src="assets/js/navbar.js"></script>
    <script src="assets/berita/js/script-berita.js"></script>
</body>
</html>