<?php
if (!isset($_SESSION)) session_start();
// PATH: Naik satu tingkat dari /menu/ ke root /config/
require_once '../config/db.php';
require_once '../config/settings.php'; // Panggil Settings CMS

// Include components (Agar fungsi render Navbar/Footer/Profile tersedia)
require_once 'components/navbar.php';
require_once 'components/footer.php';
include 'components/floating_profile.php'; 

// Utilities
$path_prefix = "../"; // Digunakan untuk navigasi dari /menu/ ke root /
$cache_buster = time(); // Untuk refresh CSS/JS

// Ambil Visi, Misi, Sejarah
$profile = $pdo->query("SELECT * FROM lab_profile LIMIT 1")->fetch(PDO::FETCH_ASSOC);

// Ambil Anggota Tim
$members = $pdo->query("SELECT * FROM members ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Lab - Laboratorium Mobile and Multimedia Tech POLINEMA</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="components/navbar.css?v=<?= $cache_buster ?>">
    <link rel="stylesheet" href="<?= $path_prefix ?>assets/css/style.css?v=<?= $cache_buster ?>">
    <link rel="stylesheet" href="assets/profil/css/style-profil.css?v=<?= $cache_buster ?>">
    
    <style>
        /* Background untuk seluruh halaman dengan wallpaper.jpg */
        body {
            background: url('<?= $path_prefix ?>assets/images/wallpaper.jpg') center center/cover fixed no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
        }
        
        /* Efek transparansi halus untuk area konten utama */
        .profile-section {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            position: relative;
            padding: 60px 0;
        }
        
        /* Section dengan background light */
        .profile-section.bg-light {
            background: rgba(255, 255, 255, 0.05);
        }
        
        /* Container untuk memastikan konten tetap readable */
        .container {
            position: relative;
            z-index: 1;
        }
        
        /* Hero section styling */
        .hero {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.8)),
                        url('<?= $path_prefix ?><?= htmlspecialchars($site_config['about_hero_image'] ?? 'assets/images/hero.jpg') ?>') center center/cover no-repeat;
        }
        
        /* Visi Misi Cards */
        .visi-card, .misi-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            padding: 30px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        /* Sejarah text area */
        .sejarah-text {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 25px;
            line-height: 1.8;
        }
        
        /* Struktur placeholder */
        .struktur-placeholder {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            padding: 30px;
            text-align: center;
        }
        
        .struktur-placeholder img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }
        
        /* Team cards */
        .team-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            padding: 20px;
            transition: all 0.3s ease;
        }
        
        .team-card:hover {
            background: rgba(255, 255, 255, 0.9);
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.15);
        }
        
        /* Team tags */
        .team-tags .tag {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        /* Team links */
        .team-links a {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .team-links a:hover {
            background: rgba(255, 255, 255, 0.9);
        }
        
        /* Section titles - DIUBAH MENJADI PUTIH */
        .section-title {
            color: #ffffff;
            margin-bottom: 30px;
            text-align: center;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
    </style>
</head>
<body id="top">
    <?php renderNavbar('profil', $path_prefix, $site_config); ?>
    <main>
        <section class="hero">
            <div class="container">
                <div class="hero-breadcrumb">
                    <a href="<?= $path_prefix ?>index.php">Beranda</a> <i class="fas fa-chevron-right"></i> 
                    <span>Profil Laboratorium</span>
                </div>
                <h1><?= htmlspecialchars($site_config['about_title'] ?? 'Profil Laboratorium') ?></h1>
            </div>
        </section>

        <section class="profile-section" id="visi-misi">
            <div class="container">
                <div class="visi-misi-grid">
                    <div class="visi-card">
                        <h3><i class="fas fa-eye"></i> Visi</h3>
                        <p><?php echo nl2br(htmlspecialchars($profile['visi'] ?? 'Visi belum diatur.')); ?></p>
                    </div>
                    <div class="misi-card">
                        <h3><i class="fas fa-bullseye"></i> Misi</h3>
                        <?php 
                        // Pastikan ada data sebelum diolah
                        $misi_content = $profile['misi'] ?? '';
                        $misi_items = explode("\n", $misi_content);
                        
                        if (!empty(trim($misi_content))) {
                            echo '<ul>';
                            foreach ($misi_items as $item) {
                                if (trim($item) !== '') {
                                    echo '<li>' . htmlspecialchars(trim($item)) . '</li>';
                                }
                            }
                            echo '</ul>';
                        } else {
                            echo '<p>Misi belum diatur.</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="profile-section bg-light" id="sejarah">
            <div class="container">
                <h2 class="section-title">Sejarah Singkat</h2>
                <div class="sejarah-text">
                    <?php echo nl2br(htmlspecialchars($profile['sejarah'] ?? 'Sejarah belum diatur.')); ?>
                </div>
            </div>
        </section>
        
        <section class="profile-section" id="struktur">
            <div class="container">
                <h2 class="section-title">Struktur Organisasi</h2>
                <div class="struktur-placeholder">
                    <img src="<?= $path_prefix ?>assets/images/struktur-org-placeholder.png" alt="Bagan Struktur Organisasi">
                </div>
            </div>
        </section>
        
        <section class="profile-section bg-light" id="tim">
            <div class="container">
                <h2 class="section-title">Tim Kami</h2>
                <div class="team-grid">
                    <?php 
                    if (!empty($members)):
                        foreach ($members as $member): 
                            // Fix path gambar tim (hapus ../ dari database jika ada)
                            $avatar_path_raw = $member['avatar_url'] ?? 'assets/images/placeholder-team.jpg';
                            $avatar_path = str_replace('../', '', $avatar_path_raw);
                            
                            // Fallback jika avatar kosong (path relatif ke root, jadi tambahkan $path_prefix)
                            $final_avatar_path = $path_prefix . $avatar_path;
                            
                            // Jika avatar_url di database benar-benar kosong dan menggunakan fallback default:
                            if(empty($member['avatar_url'])) {
                                $final_avatar_path = $path_prefix . "assets/images/placeholder-team.jpg";
                            }
                        ?>
                        <div class="team-card">
                            <div class="team-photo-wrapper">
                                <img src="<?php echo htmlspecialchars($final_avatar_path); ?>" alt="Foto <?php echo htmlspecialchars($member['name']); ?>" class="team-photo">
                            </div>
                            <div class="team-info">
                                <h4><?php echo htmlspecialchars($member['name']); ?></h4>
                                <span class="team-role"><?php echo htmlspecialchars($member['role']); ?></span>
                                
                                <?php if (!empty($member['tags'])): ?>
                                <div class="team-tags">
                                    <?php 
                                    $tags = explode(',', $member['tags']);
                                    foreach ($tags as $tag):
                                    ?>
                                    <span class="tag"><?php echo htmlspecialchars(trim($tag)); ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>

                                <div class="team-links">
                                    <?php if (!empty($member['linkedin_url'])): ?>
                                    <a href="<?php echo htmlspecialchars($member['linkedin_url']); ?>" target="_blank"><i class="fab fa-linkedin"></i> LinkedIn</a>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($member['scholar_url'])): ?>
                                    <a href="<?php echo htmlspecialchars($member['scholar_url']); ?>" target="_blank"><i class="fas fa-graduation-cap"></i> Scholar</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach;
                    else:
                        echo "<div style='grid-column: 1/-1; text-align: center; background: rgba(255,255,255,0.7); backdrop-filter: blur(8px); padding: 30px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.3);'>
                                <p>Data anggota tim belum tersedia.</p>
                              </div>";
                    endif;
                    ?>
                </div>
            </div>
        </section>
        
    </main>

    <?php 
    renderFloatingProfile(); 
    // PEMANGGILAN FOOTER (Menggunakan $path_prefix dan $site_config)
    renderFooter($path_prefix, $site_config); 
    ?>

    <a href="#top" id="scrollTopBtn" class="scroll-top-btn" aria-label="Kembali ke atas">&uarr;</a>

    <script src="<?= $path_prefix ?>assets/js/navbar.js?v=<?= $cache_buster ?>"></script>
    <script src="<?= $path_prefix ?>assets/js/scrolltop.js?v=<?= $cache_buster ?>"></script>
    <script src="assets/profil/js/script-profil.js?v=<?= $cache_buster ?>"></script>

</body>
</html>