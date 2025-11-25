<?php
if (!isset($_SESSION)) session_start();
require_once '../config/db.php';
require_once '../config/settings.php'; // Panggil Settings CMS

// Include navbar component
require_once 'components/navbar.php';
require_once 'components/footer.php';
include 'components/floating_profile.php'; 
renderFloatingProfile();

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
    
    <link rel="stylesheet" href="../assets/css/style.css">
    
    <link rel="stylesheet" href="assets/profil/css/style-profil.css">
    
    <style>
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.8)), 
                        url('../<?= htmlspecialchars($site_config['about_hero_image'] ?? 'assets/images/hero.jpg') ?>') center center/cover no-repeat;
            height: 350px !important; 
        }
        .hero h1 { margin-bottom: 0; }
    </style>
</head>
<body id="top">
    <?php renderNavbar('profil'); ?>
    <main>
        <section class="hero">
            <div class="container">
                <h1><?= htmlspecialchars($site_config['about_title'] ?? 'Profil Laboratorium') ?></h1>
            </div>
        </section>

        <section class="profile-section" id="visi-misi">
            <div class="container">
                <div class="visi-misi-grid">
                    <div class="visi-card">
                        <h3><i class="fas fa-eye"></i> Visi</h3>
                        <p><?php echo nl2br(htmlspecialchars($profile['visi'])); ?></p>
                    </div>
                    <div class="misi-card">
                        <h3><i class="fas fa-bullseye"></i> Misi</h3>
                        <?php 
                        $misi_items = explode("\n", $profile['misi']);
                        if (count($misi_items) > 1) {
                            echo '<ul>';
                            foreach ($misi_items as $item) {
                                if (trim($item) !== '') {
                                    echo '<li>' . htmlspecialchars(trim($item)) . '</li>';
                                }
                            }
                            echo '</ul>';
                        } else {
                            echo '<p>' . nl2br(htmlspecialchars($profile['misi'])) . '</p>';
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
                    <?php echo nl2br(htmlspecialchars($profile['sejarah'])); ?>
                </div>
            </div>
        </section>
        
        <section class="profile-section" id="struktur">
            <div class="container">
                <h2 class="section-title">Struktur Organisasi</h2>
                <div class="struktur-placeholder">
                    <img src="../assets/images/struktur-org-placeholder.png" alt="Bagan Struktur Organisasi">
                </div>
            </div>
        </section>
        
        <section class="profile-section bg-light" id="tim">
            <div class="container">
                <h2 class="section-title">Tim Kami</h2>
                <div class="team-grid">
                    <?php foreach ($members as $member): 
                        // Fix path gambar tim (hapus ../ dari database jika ada)
                        $avatar_path = str_replace('../', '', $member['avatar_url']);
                        
                        // Fallback jika avatar kosong
                        if(empty($avatar_path)) $avatar_path = "assets/images/placeholder-team.jpg";
                    ?>
                    <div class="team-card">
                        <div class="team-photo-wrapper">
                            <img src="../<?php echo htmlspecialchars($avatar_path); ?>" alt="Foto <?php echo htmlspecialchars($member['name']); ?>" class="team-photo">
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
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        
    </main>

    <?php renderFooter(); ?>

    <a href="#top" id="scrollTopBtn" class="scroll-top-btn" aria-label="Kembali ke atas">&uarr;</a>

    <script src="../assets/js/navbar.js"></script>
    <script src="assets/profil/js/script-profil.js"></script>

</body>
</html>