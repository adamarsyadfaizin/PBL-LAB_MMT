<?php
require_once '../config/db.php';

// Ambil parameter pencarian dari URL
$search_term = $_GET['search'] ?? '';
$filter_kategori = $_GET['kategori'] ?? 'semua';
$filter_tahun = $_GET['tahun'] ?? 'semua';

// Query dinamis berdasarkan filter
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

// Query untuk mengambil berita dengan filter
$sql = "SELECT * FROM news $sql_conditions ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$news_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil event highlight (ambil berita terbaru sebagai highlight)
$event_sql = "SELECT * FROM news WHERE status = 'published' ORDER BY created_at DESC LIMIT 1";
$event_stmt = $pdo->prepare($event_sql);
$event_stmt->execute();
$event_highlight = $event_stmt->fetch(PDO::FETCH_ASSOC);

// Ambil tahun-tahun unik untuk filter tahun
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

// Ambil kategori unik untuk filter kategori
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
            background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.5)), 
                        url('../assets/images/hero.jpg') center center/cover no-repeat;
            height: 300px;
            display: flex;
            align-items: center;
            color: var(--color-white);
        }
        .hero h1 {
            font-size: 36px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.4);
        }
        /* Gaya form pencarian & filter */
        .search-filter-container {
            background: var(--color-bg-light);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .search-row {
            display: flex;
            gap: 15px;
            align-items: end;
            margin-bottom: 15px;
        }
        .search-input-group { flex: 1; }
        .search-input-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            font-size: 14px;
        }
        .search-input-group input,
        .filter-group select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--color-border-light);
            border-radius: 4px;
            font-size: 14px;
        }
        .btn-search,
        .btn-filter {
            background: var(--color-primary);
            color: var(--color-white);
            border: none;
            padding: 8px 20px;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            font-size: 14px;
            height: 35px;
        }
        .btn-search:hover,
        .btn-filter:hover {
            background: var(--color-accent);
            color: var(--color-text);
        }
        .filter-row {
            display: flex;
            gap: 15px;
            align-items: end;
        }
        .search-results-info {
            background: var(--color-primary);
            color: var(--color-white);
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .no-results {
            text-align: center;
            padding: 40px 20px;
            background: var(--color-bg-light);
            border-radius: 8px;
            margin: 30px 0;
        }
        @media (max-width: 768px) {
            .search-row,
            .filter-row { flex-direction: column; }
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
            <div class="container">
                <h1>Berita & Kegiatan</h1>
            </div>
        </section>

        <div class="main-content-area">
            <div class="container">
                <div class="primary-content">

                    <?php if ($event_highlight && empty($search_term) && $filter_kategori == 'semua' && $filter_tahun == 'semua'): ?>
                    <section class="event-highlight">
                        <div class="event-highlight-img">
                            <img src="<?php echo htmlspecialchars($event_highlight['cover_image']); ?>" alt="Banner <?php echo htmlspecialchars($event_highlight['title']); ?>">
                        </div>
                        <div class="event-highlight-content">
                            <span class="event-tag">Berita Terbaru</span>
                            <h2><?php echo htmlspecialchars($event_highlight['title']); ?></h2>
                            <p class="event-date"><?php echo date('d F Y', strtotime($event_highlight['created_at'])); ?></p>
                            <p><?php echo htmlspecialchars($event_highlight['summary']); ?></p>
                            <a href="menu-detail-berita/detail-berita.php?slug=<?php echo htmlspecialchars($event_highlight['slug']); ?>" class="btn">Baca Selengkapnya</a>
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
                                        <?php foreach ($available_years as $year): ?>
                                            <option value="<?php echo htmlspecialchars($year); ?>" <?php echo ($filter_tahun == $year) ? 'selected' : ''; ?>><?php echo htmlspecialchars($year); ?></option>
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
                        if (!empty($search_term)) echo " dengan kata kunci: \"$search_term\"";
                        if ($filter_kategori != 'semua') echo " dalam kategori: $filter_kategori";
                        if ($filter_tahun != 'semua') echo " tahun: $filter_tahun";
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
                                <img src="<?php echo htmlspecialchars($item['cover_image']); ?>" alt="Gambar <?php echo htmlspecialchars($item['title']); ?>">
                            </div>
                            <div class="facility-text">
                                <h2><?php echo htmlspecialchars($item['title']); ?></h2>
                                <span class="article-metadata">
                                    Diposting pada: <?php echo date('d M Y', strtotime($item['created_at'])); ?>
                                </span>
                                <p><?php echo htmlspecialchars($item['summary']); ?></p>
                                <a href="menu-detail-berita/detail-berita.php?slug=<?php echo htmlspecialchars($item['slug']); ?>" class="btn">Lihat Detail</a>
                            </div>
                        </article>
                        <?php endforeach; ?>
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
                        <table class="calendar-table">
                            <caption>November 2025</caption>
                            <thead>
                                <tr><th>Sn</th><th>Sl</th><th>Rb</th><th>Km</th><th>Jm</th><th>Sb</th><th>Mg</th></tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="other-month">27</td><td class="other-month">28</td><td class="other-month">29</td>
                                    <td class="other-month">30</td><td class="other-month">31</td><td>1</td><td>2</td>
                                </tr>
                                <tr><td>3</td><td>4</td><td class="today">5</td><td>6</td><td>7</td><td>8</td><td>9</td></tr>
                                <tr>
                                    <td>10</td><td>11</td><td>12</td><td class="event"><a href="#">13</a></td>
                                    <td>14</td><td>15</td><td>16</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="widget widget-recent-news">
                        <h3 class="widget-title">Berita Terbaru</h3>
                        <ul>
                            <?php 
                            $recent_news = array_slice($news_items, 0, 3);
                            foreach ($recent_news as $recent): ?>
                            <li>
                                <h4><a href="menu-detail-berita/detail-berita.php?slug=<?php echo htmlspecialchars($recent['slug']); ?>">
                                    <?php echo htmlspecialchars($recent['title']); ?></a></h4>
                                <span class="pub-source"><?php echo date('d M Y', strtotime($recent['created_at'])); ?></span>
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
    require_once 'components/footer.php';
    renderFooter();
    ?>

    <script src="assets/js/navbar.js"></script>
    <script src="assets/berita/js/script-berita.js"></script>

</body>
</html>
