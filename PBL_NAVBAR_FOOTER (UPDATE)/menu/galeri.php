<?php
require_once '../config/db.php';
// Include navbar component
require_once 'components/navbar.php';
require_once 'components/footer.php';

// Ambil parameter pencarian dan filter dari URL
$search_term = $_GET['search'] ?? '';
$filter_jenis = $_GET['jenis'] ?? 'semua';
$filter_acara = $_GET['acara'] ?? 'semua';
$filter_tahun = $_GET['tahun'] ?? 'semua';

// Query dinamis berdasarkan filter
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

// Query untuk mengambil media dengan filter
$sql = "SELECT * FROM media_assets $sql_conditions ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$media_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil jenis media unik untuk filter
try {
    $types_sql = "SELECT DISTINCT type FROM media_assets WHERE type IS NOT NULL ORDER BY type";
    $types_stmt = $pdo->query($types_sql);
    $available_types = $types_stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $available_types = ['foto', 'video', 'animasi'];
}

// Ambil acara unik untuk filter
try {
    $events_sql = "SELECT DISTINCT event_name FROM media_assets WHERE event_name IS NOT NULL AND event_name != '' ORDER BY event_name";
    $events_stmt = $pdo->query($events_sql);
    $available_events = $events_stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $available_events = ['Dies Natalis', 'Wisuda', 'Seminar', 'Workshop'];
}

// Ambil tahun unik untuk filter
try {
    $years_sql = "SELECT DISTINCT EXTRACT(YEAR FROM created_at) as year FROM media_assets ORDER BY year DESC";
    $years_stmt = $pdo->query($years_sql);
    $available_years = $years_stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $available_years = [2025, 2024, 2023];
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Multimedia - Laboratorium Mobile and Multimedia Tech POLINEMA</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/galeri/css/style-galeri.css">
    
    <style>
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.5)), 
                        url('../assets/images/hero.jpg') center center/cover no-repeat;
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

        /* Style untuk form pencarian dan filter */
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

        .search-input-group {
            flex: 1;
        }

        .search-input-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            font-size: 14px;
            color: var(--color-text);
        }

        .search-input-group input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--color-border-light);
            border-radius: 4px;
            font-size: 14px;
            font-family: 'Open Sans', sans-serif;
        }

        .btn-search {
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

        .btn-search:hover {
            background: var(--color-accent);
            color: var(--color-text);
        }

        .filter-row {
            display: flex;
            gap: 15px;
            align-items: end;
        }

        .filter-group {
            flex: 1;
        }

        .filter-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            font-size: 14px;
            color: var(--color-text);
        }

        .filter-group select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--color-border-light);
            border-radius: 4px;
            font-size: 14px;
            font-family: 'Open Sans', sans-serif;
        }

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
            padding: 60px 20px;
            background: var(--color-bg-light);
            border-radius: 8px;
            margin: 40px 0;
        }

        .no-results h3 {
            color: var(--color-text-secondary);
            margin-bottom: 15px;
        }

        @media (max-width: 768px) {
            .search-row,
            .filter-row {
                flex-direction: column;
            }
            
            .search-input-group,
            .filter-group {
                width: 100%;
            }
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
                <h1>Galeri Multimedia</h1>
                <p style="margin-top: 10px; font-size: 18px;">Koleksi foto, video, dan animasi dari berbagai kegiatan laboratorium</p>
            </div>
        </section>

        <div class="main-content-area">
            <div class="container">

                <!-- FORM PENCARIAN & FILTER -->
                <div class="search-filter-container">
                    <form action="" method="get">
                        <!-- Baris Pencarian -->
                        <div class="search-row">
                            <div class="search-input-group">
                                <label for="search">Cari Media</label>
                                <input type="text" id="search" name="search" placeholder="Masukkan kata kunci..." value="<?php echo htmlspecialchars($search_term); ?>">
                            </div>
                            <button type="submit" class="btn-search">Cari</button>
                        </div>

                        <!-- Baris Filter -->
                        <div class="filter-row">
                            <div class="filter-group">
                                <label for="jenis">Jenis Media</label>
                                <select id="jenis" name="jenis">
                                    <option value="semua">Semua Media</option>
                                    <?php foreach ($available_types as $type): ?>
                                        <option value="<?php echo htmlspecialchars($type); ?>" <?php echo ($filter_jenis == $type) ? 'selected' : ''; ?>>
                                            <?php echo ucfirst(htmlspecialchars($type)); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="filter-group">
                                <label for="acara">Acara</label>
                                <select id="acara" name="acara">
                                    <option value="semua">Semua Acara</option>
                                    <?php foreach ($available_events as $event): ?>
                                        <option value="<?php echo htmlspecialchars($event); ?>" <?php echo ($filter_acara == $event) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($event); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="filter-group">
                                <label for="tahun">Tahun</label>
                                <select id="tahun" name="tahun">
                                    <option value="semua">Semua Tahun</option>
                                    <?php foreach ($available_years as $year): ?>
                                        <option value="<?php echo htmlspecialchars($year); ?>" <?php echo ($filter_tahun == $year) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($year); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn-filter">Terapkan Filter</button>
                        </div>
                    </form>
                </div>

                <!-- Info Hasil Pencarian -->
                <?php if (!empty($search_term) || $filter_jenis != 'semua' || $filter_acara != 'semua' || $filter_tahun != 'semua'): ?>
                <div class="search-results-info">
                    <strong>
                        <?php 
                        $filter_info = [];
                        if (!empty($search_term)) $filter_info[] = "kata kunci: \"$search_term\"";
                        if ($filter_jenis != 'semua') $filter_info[] = "jenis: " . ucfirst($filter_jenis);
                        if ($filter_acara != 'semua') $filter_info[] = "acara: $filter_acara";
                        if ($filter_tahun != 'semua') $filter_info[] = "tahun: $filter_tahun";
                        
                        echo "Menampilkan " . count($media_items) . " media dengan " . implode(', ', $filter_info);
                        ?>
                    </strong>
                    <a href="galeri.php" style="color: var(--color-accent); margin-left: 10px; font-weight: 600;">Tampilkan Semua</a>
                </div>
                <?php endif; ?>

                <!-- Grid Galeri -->
                <?php if (count($media_items) > 0): ?>
                <div class="gallery-grid-main">
                    <?php foreach ($media_items as $item): 
                        // Tentukan icon berdasarkan jenis media
                        $icon = '&#128247;'; // default icon untuk foto
                        $media_type = 'Foto';
                        
                        if ($item['type'] === 'video') {
                            $icon = '&#9658;';
                            $media_type = 'Video';
                        } elseif ($item['type'] === 'animasi') {
                            $icon = '&#10022;';
                            $media_type = 'Animasi';
                        }
                        
                        // Ekstrak judul dari caption
                        $caption_parts = explode(' ', $item['caption']);
                        $title = $item['caption'];
                        if (count($caption_parts) > 3) {
                            $title = implode(' ', array_slice($caption_parts, 0, 3)) . '...';
                        }
                    ?>
                    <a href="menu-detail-galeri/galeri-detail.php?id=<?php echo htmlspecialchars($item['id']); ?>" class="gallery-item" aria-label="Lihat <?php echo htmlspecialchars(strtolower($media_type)); ?> <?php echo htmlspecialchars($item['caption']); ?>">
                        <div class="image-container">
                            <img src="<?php echo htmlspecialchars($item['url']); ?>" alt="<?php echo htmlspecialchars($item['caption']); ?>">
                            <div class="overlay"><span class="icon"><?php echo $icon; ?></span></div>
                        </div>
                        <div class="item-info">
                            <h4><?php echo htmlspecialchars($title); ?></h4>
                            <span><?php echo htmlspecialchars($media_type); ?></span>
                            <?php if (!empty($item['event_name'])): ?>
                            <span class="event-tag"><?php echo htmlspecialchars($item['event_name']); ?></span>
                            <?php endif; ?>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="no-results">
                    <h3>😔 Tidak ada media yang ditemukan</h3>
                    <p>Silakan coba dengan kata kunci atau filter yang berbeda.</p>
                    <a href="galeri.php" class="btn">Tampilkan Semua Media</a>
                </div>
                <?php endif; ?>
                
            </div>
        </div>
        
    </main>

    <?php renderFooter(); ?>
    <a href="#top" id="scrollTopBtn" class="scroll-top-btn" aria-label="Kembali ke atas">
        &uarr;
    </a>

    <!-- Include JavaScript untuk navbar -->
    <script src="assets/js/navbar.js"></script>
    <script src="assets/galeri/js/script-galeri.js"></script>

</body>
</html>