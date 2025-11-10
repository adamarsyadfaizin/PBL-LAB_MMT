<?php
require_once '../config/db.php';

// -- PENGATURAN PAGINASI --
$limit = 6; // Jumlah proyek per halaman (sesuai screenshot)
$page = (int)($_GET['page'] ?? 1); // Ambil halaman saat ini
$offset = ($page - 1) * $limit; // Hitung offset

// -- AMBIL DATA FILTER DARI URL --
$search_term = $_GET['s'] ?? '';
$category_slug = $_GET['kategori'] ?? 'semua';
$tech_slug = $_GET['teknologi'] ?? 'semua';
$year = $_GET['tahun'] ?? 'semua';
$sort = $_GET['sort'] ?? 'terbaru';

// -- SIAPKAN QUERY DINAMIS --
$sql_base = "FROM projects p
             LEFT JOIN categories c ON p.category_id = c.id
             LEFT JOIN project_tags pt ON p.id = pt.project_id
             LEFT JOIN tags t ON pt.tag_id = t.id
             WHERE p.status = 'published'";
$params = [];

// Tambahkan kondisi filter
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

// Group By untuk menghindari duplikat
$sql_base .= " GROUP BY p.id, c.name, p.created_at, p.title";

// -- HITUNG TOTAL PROYEK UNTUK PAGINASI --
$sql_count = "SELECT COUNT(DISTINCT p.id) " . $sql_base;
$stmt_count = $pdo->prepare($sql_count);
$stmt_count->execute($params);
$total_projects = $stmt_count->fetchColumn();
$total_pages = ceil($total_projects / $limit);

// -- ATUR PENGURUTAN (ORDER BY) --
$order_by = " ORDER BY p.created_at DESC";
if ($sort == 'a-z') {
    $order_by = " ORDER BY p.title ASC";
}

// -- QUERY AKHIR UNTUK MENGAMBIL PROYEK --
// SESUDAH
$sql_final = "SELECT p.id, p.title, p.summary, p.cover_image, p.demo_url " . $sql_base . $order_by . " LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

$stmt_projects = $pdo->prepare($sql_final);
$stmt_projects->execute($params);
$projects = $stmt_projects->fetchAll(PDO::FETCH_ASSOC);

// -- (Blok untuk mengambil data dropdown filter tetap sama seperti respons sebelumnya) --
// (Ambil $categories, $tags, $years)
try {
    $stmt_cats = $pdo->query("SELECT name, slug FROM categories ORDER BY name");
    $categories = $stmt_cats->fetchAll(PDO::FETCH_ASSOC);
    $stmt_tags = $pdo->query("SELECT name, slug FROM tags ORDER BY name");
    $tags = $stmt_tags->fetchAll(PDO::FETCH_ASSOC);
    $stmt_years = $pdo->query("SELECT DISTINCT year FROM projects WHERE year IS NOT NULL ORDER BY year DESC");
    $years = $stmt_years->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categories = []; $tags = []; $years = [];
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

    <header class="site-header" id="siteHeader">
        <div class="container">
            <div class="logo-area">
                <a href="../beranda.php">
                    <img src="../assets/images/logo-placeholder.png" alt="Logo Departemen" style="height: 50px; width: auto;">
                </a>
            </div>
            
            <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false" aria-label="Buka menu navigasi">
                <span></span>
                <span></span>
                <span></span>
            </button>
            
            <nav class="main-navigation" id="primary-menu">
                <ul>
                    <li><a href="../beranda.php">Beranda</a></li>
                    
                    <li class="has-dropdown">
                        <a href="profil.php" class="dropdown-toggle">Profil</a>
                        <ul class="dropdown-menu">
                            <li><a href="profil.php#visi-misi">Visi & Misi</a></li>
                            <li><a href="profil.php#sejarah">Sejarah</a></li>
                            <li><a href="profil.php#struktur">Struktur</a></li>
                            <li><a href="profil.php#tim">Tim Kami</a></li>
                        </ul>
                    </li>
                    
                    <li class="has-dropdown">
                        <a href="berita.php" class="dropdown-toggle">Berita & Kegiatan</a>
                        <ul class="dropdown-menu">
                            <li><a href="berita.php">Berita Terbaru</a></li>
                            <li><a href="berita.php">Agenda Kegiatan</a></li>
                        </ul>
                    </li>
                    
                    <li><a href="proyek.php" aria-current="page">Proyek</a></li>
                    
                    <li><a href="galeri.php">Galeri</a></li>
                    <li><a href="kontak.php">Kontak</a></li>
                    <li><a href="../login.php">Login</a></li>

                    <li class="nav-search">
                        <form action="../search.php" method="get" class="nav-search-form">
                            <input type="search" name="q" placeholder="Cari..." aria-label="Cari" class="nav-search-input">
                            <button type="submit" class="nav-search-button" aria-label="Cari">&#128269;</button>
                        </form>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

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
                        <input type="search" id="filter-search" name="s" placeholder="Cari proyek (misal: 'deteksi', 'game', 'ar')">
                    </div>
                    <div class="filter-group">
                        <label for="filter-kategori">Kategori</label>
                        <select id="filter-kategori" name="kategori">
                            <option value="semua">Semua Kategori</option>
                            <option value="ui-ux">UI/UX</option>
                            <option value="game">Game</option>
                            <option value="mobile">Mobile App</option>
                            <option value="ar-vr">AR/VR</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="filter-teknologi">Teknologi</label>
                        <select id="filter-teknologi" name="teknologi">
                            <option value="semua">Semua Teknologi</option>
                            <option value="react">React Native</option>
                            <option value="unity">Unity</option>
                            <option value="figma">Figma</option>
                            <option value="arcore">ARCore</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="filter-tahun">Tahun</label>
                        <select id="filter-tahun" name="tahun">
                            <option value="semua">Semua Tahun</option>
                            <option value="2025">2025</option>
                            <option value="2024">2024</option>
                            <option value="2023">2023</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="filter-sort">Urutkan</label>
                        <select id="filter-sort" name="sort">
                            <option value="terbaru">Terbaru</option>
                            <option value="terpopuler">Terpopuler</option>
                            <option value="a-z">A - Z</option>
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
                                $tags_html .= "<span class'tag-badge'>" . htmlspecialchars($tag['name']) . "</span>";
                            }
                            ?>
                            
                            <a href="<?php echo htmlspecialchars($project['demo_url']); ?>" class="project-card">
                                <div class="project-card-thumbnail">
                                    <img src="<?php echo htmlspecialchars($project['cover_image']); ?>" alt="">
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
                        echo "<p>Tidak ada proyek yang ditemukan.</p>";
                    endif; 
                    ?>
                </div>

                <div class="pagination-controls">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&s=<?php echo $search_term; ?>&kategori=<?php echo $category_slug; ?>&teknologi=<?php echo $tech_slug; ?>&tahun=<?php echo $year; ?>&sort=<?php echo $sort; ?>"
                        class="btn <?php if ($i == $page) echo 'active'; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
                
            </div>
        </div>
        
    </main>

    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                
                <div class="footer-col">
                    <img src="../assets/images/logo-footer-placeholder.png" alt="Logo Lab Polinema Footer" style="height: 60px; margin-bottom: 20px;">
                    <p>
                        <strong>Laboratorium Mobile and Multimedia Tech</strong><br>
                        Politeknik Negeri Malang<br>
                        Malang, Indonesia
                    </p>
                    <div class="social-links-footer">
                        <a href="#" aria-label="Facebook">F</a>
                        <a href="#" aria-label="Twitter">T</a>
                        <a href="#" aria-label="Instagram">I</a>
                        <a href="#" aria-label="YouTube">Y</a>
                    </div>
                </div>
                
                <div class="footer-col">
                    <h4>Tautan Cepat</h4>
                    <ul>
                        <li><a href="../beranda.php">Beranda</a></li>
                        <li><a href="profil.php">Profil</a></li>
                        <li><a href="berita.php">Berita & Kegiatan</a></li>
                        <li><a href="proyek.php">Proyek</a></li>
                        <li><a href="galeri.php">Galeri</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4>Kontak & Kebijakan</h4>
                    <p>
                        Jl. Soekarno Hatta No.9, Jatimulyo,<br>
                        Kec. Lowokwaru, Kota Malang,<br>
                        Jawa Timur 65141
                    </p>
                    <p>
                        Email: <a href="mailto:info@polinema.ac.id">info@polinema.ac.id</a><br>
                        Telepon: (0341) 404 424
                    </p>
                    <ul class="policy-links">
                        <li><a href="#">Kebijakan Privasi</a></li>
                        <li><a href="#">Ketentuan Layanan</a></li>
                    </ul>
                </div>
                
            </div>
            
            <div class="footer-copyright">
                <p>&copy; 2025 Laboratorium Mobile and Multimedia Tech POLINEMA. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>

    <a href="#top" id="scrollTopBtn" class="scroll-top-btn" aria-label="Kembali ke atas">
        &uarr;
    </a>

    <script src="assets/proyek/js/script-proyek.js"></script>

</body>
</html>