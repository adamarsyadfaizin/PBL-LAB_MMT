<?php
if (!isset($_SESSION)) session_start();
// PATH: Naik satu tingkat dari /menu/ ke root /config/
require_once '../config/db.php';
require_once '../config/settings.php'; // CMS Setting

// Include components
require_once 'components/navbar.php';
require_once 'components/footer.php';
include 'components/floating_profile.php';

// Utilities
$path_prefix = "../"; // Digunakan untuk navigasi dari /menu/ ke root /
$cache_buster = time(); // Untuk refresh CSS/JS

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {
    $nama_lengkap = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subjek = trim($_POST['subject']);
    $pesan = trim($_POST['message']);
    
    // PHP-side validation
    if (empty($nama_lengkap) || empty($email) || empty($subjek) || empty($pesan)) {
        $error_message = "Semua field harus diisi!";
    } elseif (strlen($nama_lengkap) < 2) {
        $error_message = "Nama harus minimal 2 karakter!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Format email tidak valid!";
    } else {
        try {
            // Menggunakan prepared statement untuk keamanan
            $stmt = $pdo->prepare("INSERT INTO feedback (nama_lengkap, email, subjek, pesan, created_at, is_read) VALUES (?, ?, ?, ?, NOW(), false)");
            $stmt->execute([$nama_lengkap, $email, $subjek, $pesan]);
            $success_message = "Pesan berhasil dikirim! Terima kasih.";
            // Reset POST data agar form kosong setelah sukses
            $_POST = []; 
        } catch (PDOException $e) {
            // Tambahkan logging error yang lebih detail jika diperlukan
            // error_log("Database Error: " . $e->getMessage()); 
            $error_message = "Terjadi error sistem. Mohon coba lagi nanti.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak - Laboratorium MMT</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    
    <link rel="stylesheet" href="components/navbar.css?v=<?= $cache_buster ?>"> 
    <link rel="stylesheet" href="<?= $path_prefix ?>assets/css/style.css?v=<?= $cache_buster ?>"> 
    <link rel="stylesheet" href="assets/kontak/css/style-kontak.css?v=<?= $cache_buster ?>"> 
    
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
                        url('<?= $path_prefix ?><?= htmlspecialchars($site_config['contact_hero_image'] ?? 'assets/images/hero.jpg') ?>') center center/cover no-repeat;
        }
        
        /* Tambahan styling untuk card/content agar lebih transparan */
        .primary-content, .sidebar {
            border-radius: 12px;
            padding: 15px;
        }
        
        .widget, .contact-form {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 15px;
            margin-bottom: 20px;
        }
        
        /* Contact form styling */
        .contact-form {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        
        /* Map container styling */
        #map {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(5px);
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.4);
        }
        
        /* Alert messages styling */
        .alert {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(8px);
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            padding: 15px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            font-weight: 600;
        }
        .alert i {
            margin-right: 10px;
        }
        .alert-success {
            background: rgba(212, 237, 218, 0.9);
            color: #155724;
            border: 1px solid rgba(195, 230, 203, 0.6);
        }
        .alert-error {
            background: rgba(248, 215, 218, 0.9);
            color: #721c24;
            border: 1px solid rgba(245, 198, 203, 0.6);
        }
        
        /* Contact info items */
        .contact-info-item {
            background: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(5px);
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 10px;
            margin-bottom: 10px;
        }
        
        /* Social media links */
        .widget-social-links a {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .widget-social-links a:hover {
            background: rgba(255, 255, 255, 0.9);
            transform: translateY(-2px);
        }
        
        /* Styling khusus untuk teks "Kirim Pesan" dengan kotak */
        .section-title-box {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.4);
            border-radius: 12px;
            padding: 20px 30px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        .section-title-box h2 {
            color: #333;
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .section-title-box p {
            color: #666;
            margin: 10px 0 0 0;
            font-size: 16px;
            line-height: 1.5;
        }
    </style>
</head>
<body id="top">
    <?php renderNavbar('kontak', $path_prefix, $site_config); ?>
    
    <main>
        <section class="hero">
            <div class="container">
                <div class="hero-breadcrumb">
                    <a href="<?= $path_prefix ?>index.php">Beranda</a> <i class="fas fa-chevron-right"></i> 
                    <span>Kontak Kami</span>
                </div>
                <h1><?= htmlspecialchars($site_config['contact_title'] ?? 'Kontak Kami') ?></h1>
            </div>
        </section>

        <div class="main-content-area">
            <div class="container contact-page-grid">
                
                <div class="primary-content">
                    <!-- Kotak untuk teks "Kirim Pesan" -->
                    <div class="section-title-box">
                        <h2>Kirim Pesan</h2>
                        <p>Punya pertanyaan? Silakan isi formulir di bawah ini.</p>
                    </div>
                    
                    <?php if ($success_message): ?>
                        <div class="alert alert-success"><i class="fas fa-check-circle"></i> **<?= htmlspecialchars($success_message) ?>**</div>
                    <?php endif; ?>
                    <?php if ($error_message): ?>
                        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> **<?= htmlspecialchars($error_message) ?>**</div>
                    <?php endif; ?>
                    
                    <form class="contact-form" action="" method="post" id="contactForm">
                        <div class="form-group">
                            <label for="contact-name">Nama Lengkap *</label>
                            <input type="text" id="contact-name" name="name" value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>" required placeholder="Nama Anda">
                            <div class="error-message" id="nameError"></div> 
                        </div>
                        <div class="form-group">
                            <label for="contact-email">Email *</label>
                            <input type="email" id="contact-email" name="email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required placeholder="email@contoh.com">
                            <div class="error-message" id="emailError"></div>
                        </div>
                        <div class="form-group">
                            <label for="contact-subject">Subjek *</label>
                            <input type="text" id="contact-subject" name="subject" value="<?= isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : '' ?>" required placeholder="Judul pesan">
                            <div class="error-message" id="subjectError"></div>
                        </div>
                        <div class="form-group">
                            <label for="contact-message">Pesan *</label>
                            <textarea id="contact-message" name="message" rows="6" required placeholder="Tulis pesan Anda..."><?= isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '' ?></textarea>
                            <div class="error-message" id="messageError"></div>
                        </div>
                        <button type="submit" class="btn" style="width:100%;"><i class="fas fa-paper-plane"></i> Kirim Pesan</button>
                    </form>
                </div>
                
                <aside class="sidebar">
                    <div class="widget">
                        <h3 class="widget-title">Informasi Kontak</h3>
                        <div class="widget-contact-info">
                            <div class="contact-info-item">
                                <div class="contact-info-icon"><i class="fas fa-map-marker-alt"></i></div>
                                <div class="contact-info-content"><strong>Alamat</strong><p><?= nl2br(htmlspecialchars($site_config['alamat_lab'] ?? '-')) ?></p></div>
                            </div>
                            <div class="contact-info-item">
                                <div class="contact-info-icon"><i class="fas fa-envelope"></i></div>
                                <div class="contact-info-content"><strong>Email</strong><p><a href="mailto:<?= htmlspecialchars($site_config['email_lab'] ?? '#') ?>"><?= htmlspecialchars($site_config['email_lab'] ?? '-') ?></a></p></div>
                            </div>
                            <div class="contact-info-item">
                                <div class="contact-info-icon"><i class="fas fa-phone"></i></div>
                                <div class="contact-info-content"><strong>Telepon</strong><p><?= htmlspecialchars($site_config['telepon_lab'] ?? '-') ?></p></div>
                            </div>
                        </div>
                    </div>

                    <div class="widget">
                        <h3 class="widget-title">Lokasi Kami</h3>
                        <div id="map"></div>
                        <div class="map-caption"><small><i class="fas fa-location-arrow"></i> Gedung Sipil Lt. 8, Polinema</small></div>
                    </div>

                    <div class="widget">
                        <h3 class="widget-title">Media Sosial</h3>
                        <div class="widget-social">
                            <div class="widget-social-links">
                                <?php if(!empty($site_config['fb_link'])): ?><a href="<?= htmlspecialchars($site_config['fb_link']) ?>" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a><?php endif; ?>
                                <?php if(!empty($site_config['x_link'])): ?><a href="<?= htmlspecialchars($site_config['x_link']) ?>" target="_blank" aria-label="X Twitter"><i class="fab fa-x-twitter"></i></a><?php endif; ?>
                                <?php if(!empty($site_config['ig_link'])): ?><a href="<?= htmlspecialchars($site_config['ig_link']) ?>" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a><?php endif; ?>
                                <?php if(!empty($site_config['yt_link'])): ?><a href="<?= htmlspecialchars($site_config['yt_link']) ?>" target="_blank" aria-label="Youtube"><i class="fab fa-youtube"></i></a><?php endif; ?>
                                <?php if(!empty($site_config['linkedin'])): ?><a href="<?= htmlspecialchars($site_config['linkedin']) ?>" target="_blank" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a><?php endif; ?>
                            </div>
                        </div>
                    </div>
                </aside>
                
            </div>
        </div>
    </main>

    <?php renderFloatingProfile(); renderFooter($path_prefix, $site_config); ?>

    <a href="#top" id="scrollTopBtn" class="scroll-top-btn" aria-label="Kembali ke atas">&uarr;</a>

    <script src="<?= $path_prefix ?>assets/js/navbar.js?v=<?= $cache_buster ?>"></script>
    <script src="<?= $path_prefix ?>assets/js/scrolltop.js?v=<?= $cache_buster ?>"></script>
    
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="assets/kontak/js/script-kontak.js?v=<?= $cache_buster ?>"></script>

</body>
</html>