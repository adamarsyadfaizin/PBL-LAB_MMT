<?php
if (!isset($_SESSION)) session_start();
include 'components/floating_profile.php'; 
renderFloatingProfile();
require_once '../config/db.php';
require_once 'components/navbar.php';

// PAGINATION
$limit = 6;
$page = (int)($_GET['page'] ?? 1);
$offset = ($page - 1) * $limit;

// FILTER ON
$search_term = $_GET['s'] ?? '';
$category_slug = $_GET['kategori'] ?? 'semua';
$tech_slug = $_GET['teknologi'] ?? 'semua';
$year = $_GET['tahun'] ?? 'semua';
$sort = $_GET['sort'] ?? 'terbaru';

// BASE QUERY
$sql_base = "FROM projects p
             LEFT JOIN categories c ON p.category_id = c.id
             LEFT JOIN project_tags pt ON p.id = pt.project_id
             LEFT JOIN tags t ON pt.tag_id = t.id
             WHERE 1=1";

$params = [];

// Search
if (!empty($search_term)) {
    $sql_base .= " AND (p.title LIKE ? OR p.summary LIKE ?)";
    $params[] = "%$search_term%";
    $params[] = "%$search_term%";
}

// Category
if ($category_slug != 'semua') {
    $sql_base .= " AND c.slug = ?";
    $params[] = $category_slug;
}

// Tech
if ($tech_slug != 'semua') {
    $sql_base .= " AND t.slug = ?";
    $params[] = $tech_slug;
}

// Year
if ($year != 'semua') {
    $sql_base .= " AND p.year = ?";
    $params[] = (int)$year;
}

// Group (wajib biar SELECT aman)
$sql_base .= " GROUP BY p.id";

// Count total
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

// Query final
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

// Dropdown data
try {
    $categories = $pdo->query("SELECT name, slug FROM categories ORDER BY name")
                      ->fetchAll(PDO::FETCH_ASSOC);

    $tags = $pdo->query("SELECT name, slug FROM tags ORDER BY name")
                ->fetchAll(PDO::FETCH_ASSOC);

    $years = $pdo->query("SELECT DISTINCT year FROM projects WHERE year IS NOT NULL ORDER BY year DESC")
                 ->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categories = [];
    $tags = [];
    $years = [];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Proyek - Laboratorium Mobile and Multimedia Tech POLINEMA</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/proyek/css/style-proyek.css">
    
    <style>
        /* Override background hero image dengan path yang benar */
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
    </style>
</head>
<body id="top">

    <?php
    // Render navbar dengan halaman aktif 'proyek'
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
                
                <form class="project-filter-bar" action="#" method="get">
                    <div class="filter-group filter-group-search">
                        <label for="filter-search">Pencarian Teks</label>
                        <input type="search" id="filter-search" name="s" placeholder="Cari proyek (misal: 'deteksi', 'game', 'ar')" value="<?php echo htmlspecialchars($search_term); ?>">
                    </div>
                    <div class="filter-group">
                        <label for="filter-kategori">Kategori</label>
                        <select id="filter-kategori" name="kategori">
                            <option value="semua">Semua Kategori</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category['slug']); ?>" <?php echo ($category_slug == $category['slug']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="filter-teknologi">Teknologi</label>
                        <select id="filter-teknologi" name="teknologi">
                            <option value="semua">Semua Teknologi</option>
                            <?php foreach ($tags as $tag): ?>
                                <option value="<?php echo htmlspecialchars($tag['slug']); ?>" <?php echo ($tech_slug == $tag['slug']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($tag['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="filter-tahun">Tahun</label>
                        <select id="filter-tahun" name="tahun">
                            <option value="semua">Semua Tahun</option>
                            <?php foreach ($years as $year_item): ?>
                                <option value="<?php echo htmlspecialchars($year_item['year']); ?>" <?php echo ($year == $year_item['year']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($year_item['year']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="filter-sort">Urutkan</label>
                        <select id="filter-sort" name="sort">
                            <option value="terbaru" <?php echo ($sort == 'terbaru') ? 'selected' : ''; ?>>Terbaru</option>
                            <option value="a-z" <?php echo ($sort == 'a-z') ? 'selected' : ''; ?>>A - Z</option>
                        </select>
                    </div>
                </form>

                <div class="project-grid">
                    <?php
                    if ($projects):
                        // Siapkan query tag (untuk di dalam loop)
                        $sql_tags = "SELECT t.name FROM tags t
                                    JOIN project_tags pt ON t.id = pt.tag_id
                                    WHERE pt.project_id = ?";
                        $stmt_tags = $pdo->prepare($sql_tags);

                        foreach ($projects as $project):
                            // Ambil tag untuk proyek ini
                            $stmt_tags->execute([ $project['id'] ]);
                            $tags_list = $stmt_tags->fetchAll(PDO::FETCH_ASSOC);
                            $tags_html = "";
                            foreach ($tags_list as $tag) {
                                $tags_html .= "<span class='tag-badge'>" . htmlspecialchars($tag['name']) . "</span>";
                            }
                            ?>
                            
                <a href="menu-proyek-detail/detail-proyek.php?slug=<?php echo htmlspecialchars($project['slug'] ?? ''); ?>" class="project-card">
                                <div class="project-card-thumbnail">
                                    <img src="<?php echo htmlspecialchars($project['cover_image']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>">
                                </div>
                                <div class="project-card-content">
                                    <h4><?php echo htmlspecialchars($project['title']); ?></h4>
                                    <p><?php echo htmlspecialchars($project['summary']); ?></p>
                                    <div class="project-card-tags">
                                        <?php echo $tags_html; ?>
                                    </div>
                                </div>
                            </a>
                        
                        <?php 
                        endforeach;
                    else:
                        echo "<p class='no-projects'>Tidak ada proyek yang ditemukan.</p>";
                    endif; 
                    ?>
                </div>

                <?php if ($total_pages > 1): ?>
                <div class="pagination-controls">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&s=<?php echo htmlspecialchars($search_term); ?>&kategori=<?php echo htmlspecialchars($category_slug); ?>&teknologi=<?php echo htmlspecialchars($tech_slug); ?>&tahun=<?php echo htmlspecialchars($year); ?>&sort=<?php echo htmlspecialchars($sort); ?>"
                        class="btn <?php if ($i == $page) echo 'active'; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>
                
            </div>
        </div>
        
    </main>

    <?php
    // Include footer
    require_once 'components/footer.php';
    renderFooter();
    ?>

    <a href="#top" id="scrollTopBtn" class="scroll-top-btn" aria-label="Kembali ke atas">
        &uarr;
    </a>

    <!-- Include JavaScript untuk navbar -->
    <script src="assets/js/navbar.js"></script>
    <script src="assets/proyek/js/script-proyek.js"></script>

</body>
</html>