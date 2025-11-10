<?php
require_once '../../config/db.php';

if ( !isset($_GET['slug']) || empty($_GET['slug']) ) {
    header('Location: ../proyek.php');
    exit;
}
$slug = $_GET['slug'];

try {
    // 1. Query Pertama: Ambil data proyek (Sekarang di-JOIN dengan kategori)
    $sql_project = "SELECT p.*, c.name AS category_name
                    FROM projects p
                    LEFT JOIN categories c ON p.category_id = c.id
                    WHERE p.slug = ? AND p.status = 'published'
                    LIMIT 1";
                    
    $stmt_project = $pdo->prepare($sql_project);
    $stmt_project->execute([$slug]);
    $project = $stmt_project->fetch(PDO::FETCH_ASSOC);

    if (!$project) {
        echo "Proyek tidak ditemukan!";
        exit;
    }

    // 2. Query Kedua: Ambil Tag (Ini sudah ada)
    $sql_tags = "SELECT t.name FROM tags t
                 JOIN project_tags pt ON t.id = pt.tag_id
                 WHERE pt.project_id = ?";
    $stmt_tags = $pdo->prepare($sql_tags);
    $stmt_tags->execute([ $project['id'] ]); 
    $tags = $stmt_tags->fetchAll(PDO::FETCH_ASSOC);

    // 3. Query Ketiga: Ambil Proyek Terkait (BARU)
    // (Proyek lain dalam kategori yang sama, KECUALI proyek ini)
    $sql_related = "SELECT p.title, p.summary, p.demo_url, c.name AS category_name
                    FROM projects p
                    LEFT JOIN categories c ON p.category_id = c.id
                    WHERE p.category_id = ? AND p.id != ? AND p.status = 'published'
                    LIMIT 3";
    
    $stmt_related = $pdo->prepare($sql_related);
    $stmt_related->execute([ $project['category_id'], $project['id'] ]);
    $related_projects = $stmt_related->fetchAll(PDO::FETCH_ASSOC);

    $stmt_tags->execute([ $project['id'] ]); 
    $tags = $stmt_tags->fetchAll(PDO::FETCH_ASSOC);

// ... (Kode untuk $related_projects sudah ada) ...
    $stmt_related->execute([ $project['category_id'], $project['id'] ]);
    $related_projects = $stmt_related->fetchAll(PDO::FETCH_ASSOC);

// -- TAMBAHKAN INI: Query untuk Anggota Tim Proyek --
    $sql_members = "SELECT m.name, m.role, m.avatar_url, m.linkedin_url
                    FROM members m
                    JOIN project_members pm ON m.id = pm.member_id
                    WHERE pm.project_id = ?";
    $stmt_members = $pdo->prepare($sql_members);
    $stmt_members->execute([ $project['id'] ]);
    $team_members = $stmt_members->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error: Gagal mengambil data proyek. " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($project['title']); ?> - Sistem Informasi</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    
    <!-- CSS Utama (sama dengan halaman lain) -->
    <link rel="stylesheet" href="../../assets/css/style.css">
    
    <!-- CSS Khusus Halaman Detail Proyek -->
    <link rel="stylesheet" href="assets/det-pro/css/style-detail-proyek.css">
    
</head>
<body id="top">

    <!-- ==== HEADER ==== -->
    <header class="site-header" id="siteHeader">
        <div class="container">
            <div class="logo-area">
                <a href="../../beranda.php">
                    <img src="../../assets/images/logo-placeholder.png" alt="Logo Departemen" style="height: 50px; width: auto;">
                </a>
            </div>
            
            <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false" aria-label="Buka menu navigasi">
                <span></span>
                <span></span>
                <span></span>
            </button>
            
            <nav class="main-navigation" id="primary-menu">
                <ul>
                    <li><a href="../../beranda.php">Beranda</a></li>
                    
                    <li class="has-dropdown">
                        <a href="../profil.php" class="dropdown-toggle">Profil</a>
                        <ul class="dropdown-menu">
                            <li><a href="../profil.php#visi-misi">Visi & Misi</a></li>
                            <li><a href="../profil.php#sejarah">Sejarah</a></li>
                            <li><a href="../profil.php#struktur">Struktur</a></li>
                            <li><a href="../profil.php#tim">Tim Kami</a></li>
                        </ul>
                    </li>
                    
                    <li class="has-dropdown">
                        <a href="../berita.php" class="dropdown-toggle">Berita & Kegiatan</a>
                        <ul class="dropdown-menu">
                            <li><a href="../berita.php">Berita Terbaru</a></li>
                            <li><a href="../berita.php">Agenda Kegiatan</a></li>
                        </ul>
                    </li>
                    
                    <li><a href="../proyek.php" aria-current="page">Proyek</a></li>
                    
                    <li><a href="../galeri.php">Galeri</a></li>
                    
                    <li><a href="../kontak.php">Kontak</a></li>
                    <li><a href="../../login.php">Login</a></li>

                    <li class="nav-search">
                        <form action="../../search.php" method="get" class="nav-search-form">
                            <input type="search" name="q" placeholder="Cari..." aria-label="Cari" class="nav-search-input">
                            <button type="submit" class="nav-search-button" aria-label="Cari">&#128269;</button>
                        </form>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <!-- ==== HERO ==== -->
        <section class="hero">
            <div class="container">
                <h1>Sistem Deteksi Kemacetan Real-time</h1>
            </div>
        </section>

        <!-- ==== MAIN CONTENT ==== -->
        <div class="main-content-area">
            <div class="container">
                
                <!-- Kolom Konten Utama -->
                <div class="primary-content">
                    
                    <!-- Media: Video Demo -->
                    <div class="project-video-embed">
                        <div class="mockup-video">
                            <span>&#9658;</span>
                        </div>
                    </div>

                    <!-- Ringkasan -->
                    <p class="lead-paragraph">
                        Proyek mahasiswa ini bertujuan membangun aplikasi mobile yang memanfaatkan data GPS crowdsourced dari pengguna untuk memetakan dan memprediksi titik kemacetan di area metropolitan, membantu pengguna menemukan rute tercepat.
                    </p>
                    
                    <!-- Deskripsi Lengkap -->
                    <h3>Deskripsi Proyek</h3>
                    <p>
                        Aplikasi ini dikembangkan sebagai solusi atas masalah kemacetan kronis di kota-kota besar. Dengan mengumpulkan data lokasi anonim dari pengguna secara real-time, sistem dapat menganalisis kecepatan rata-rata di berbagai ruas jalan.
                    </p>
                    <p>
                        Data yang terkumpul kemudian diproses menggunakan algoritma machine learning sederhana untuk memprediksi potensi kemacetan dalam 30 menit ke depan. Hasilnya ditampilkan dalam bentuk peta interaktif dengan visualisasi heatmap.
                    </p>

                    <!-- Teknologi -->
                    <h3>Teknologi yang Digunakan</h3>
                    <div class="tech-badge-container">
                        <span class="tech-badge">React Native</span>
                        <span class="tech-badge">Firebase</span>
                        <span class="tech-badge">Google Maps API</span>
                        <span class="tech-badge">Node.js</span>
                        <span class="tech-badge">Figma</span>
                    </div>

                    <!-- Tautan -->
                    <h3>Tautan Proyek</h3>
                    <ul class="project-links-list">
                        <li><a href="#" class="demo">Lihat Demo Interaktif</a></li>
                        <li><a href="#" class="github">Repository GitHub</a></li>
                        <li><a href="#" class="paper">Publikasi Ilmiah (PDF)</a></li>
                    </ul>

                    <!-- Rating & Feedback -->
                    <h3>Rating & Umpan Balik</h3>
                    <div class="star-rating" title="Rating: 4.5 dari 5 bintang">★★★★☆</div>
                    <div class="rating-summary">(4.5/5 berdasarkan 28 ulasan)</div>
                    
                    <form class="comment-form" action="#" method="post">
                        <h4>Beri Komentar</h4>
                        <div class="form-group">
                            <label for="comment-name">Nama Anda</label>
                            <input type="text" id="comment-name" name="comment_name" required>
                        </div>
                        <div class="form-group">
                            <label for="comment-text">Komentar Anda</label>
                            <textarea id="comment-text" name="comment_text" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn">Kirim Komentar</button>
                    </form>
                    
                    <div class="comment-list">
                        <h3>Komentar (2)</h3>
                        <div class="comment-item">
                            <p class="comment-author">Andi Budianto</p>
                            <p class="comment-date">2 November 2025</p>
                            <p>Aplikasi yang sangat inovatif dan bermanfaat untuk penggunaan sehari-hari. Sukses selalu untuk tim pengembang!</p>
                        </div>
                        <div class="comment-item">
                            <p class="comment-author">Citra Lestari</p>
                            <p class="comment-date">1 November 2025</p>
                            <p>Tampilannya bersih dan mudah digunakan. Prediksinya cukup akurat untuk area Surabaya pusat.</p>
                        </div>
                    </div>

                    <!-- Tombol Kembali -->
                    <a href="../proyek.php" class="btn btn-secondary back-link">&larr; Kembali ke Katalog Proyek</a>

                </div>
                
                <!-- Kolom Sidebar -->
                <aside class="sidebar">

                    <!-- Anggota Tim -->
                    <div class="widget" id="widget-team">
                        <h4 class="widget-title">Anggota Tim</h4>
                        <div style="padding: 18px;">
                            <?php if ($team_members): ?>
                                <ul style="list-style: none; padding: 0;">
                                    <?php foreach ($team_members as $member): ?>
                                    <li style="display: flex; align-items: center; margin-bottom: 10px;">
                                        <img src="<?php echo htmlspecialchars($member['avatar_url']); ?>" alt="" style="width: 40px; height: 40px; border-radius: 50%; margin-right: 10px;">
                                        <div>
                                            <strong><?php echo htmlspecialchars($member['name']); ?></strong>
                                            <br>
                                            <small><?php echo htmlspecialchars($member['role']); ?></small>
                                        </div>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p>Informasi tim tidak tersedia.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Proyek Terkait -->
                    <div class="widget widget-news">
                        <h3 class="widget-title">Proyek Terkait</h3>
                        <ul>
                            <li>
                                <a href="#">
                                    <h4>Dashboard Visualisasi Data COVID-19</h4>
                                    <span class="date">Kategori: Analitika Data</span>
                                    <p>Dashboard interaktif untuk memantau penyebaran kasus...</p>
                                    <span class="arrow-icon" aria-hidden="true">&rarr;</span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <h4>Aplikasi E-Commerce Terintegrasi UMKM</h4>
                                    <span class="date">Kategori: Aplikasi Mobile</span>
                                    <p>Pengembangan platform e-commerce untuk UMKM lokal...</p>
                                    <span class="arrow-icon" aria-hidden="true">&rarr;</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="widget" id="widget-details">
                            <h4 class="widget-title">Detail</h4>
                            
                            <div style="padding: 18px;">
                                <ul style="list-style: none; padding: 0;">
                                    
                                    <li style="margin-bottom: 15px;">
                                        <strong>Tahun:</strong> <?php echo htmlspecialchars($project['year']); ?>
                                    </li>
                                    
                                    <li style="margin-bottom: 15px;">
                                        <strong>Kategori:</strong> <?php echo htmlspecialchars($project['category_name']); ?>
                                    </li>

                                    <li style="margin-bottom: 20px;">
                                        <strong>Tag:</strong>
                                        <div class="detail-tags" style="margin-top: 5px;">
                                            <?php 
                                            if ($tags) {
                                                foreach ($tags as $tag) {
                                                    // Ganti 'tag-badge' dengan class yang benar dari CSS Anda jika ada
                                                    echo "<span class='tag-badge' style='margin-right: 5px; background: #eee; padding: 3px 8px; border-radius: 5px; font-size: 12px;'>" 
                                                        . htmlspecialchars($tag['name']) 
                                                        . "</span>";
                                                }
                                            }
                                            ?>
                                        </div>
                                    </li>
                                    
                                    <li style="margin-bottom: 10px;">
                                        <a href="<?php echo htmlspecialchars($project['demo_url']); ?>" class="btn">Lihat Demo</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo htmlspecialchars($project['repo_url']); ?>" class="btn btn-secondary">Lihat Kode</a>
                                    </li>
                                </ul>
                            </div>
                    </div>

                </aside>
                
            </div>
        </div>
        
    </main>

    <footer class="site-footer">
        <div class="container">
            <!-- Footer content sama seperti sebelumnya -->
        </div>
    </footer>

    <a href="#top" id="scrollTopBtn" class="scroll-top-btn" aria-label="Kembali ke atas">
        &uarr;
    </a>

    <!-- JavaScript Utama -->
    <script src="../../assets/js/script.js"></script>
    
    <!-- JavaScript Khusus Halaman Detail Proyek -->
    <script src="assets/det-pro/js/script-detail-proyek.js"></script>

</body>
</html>