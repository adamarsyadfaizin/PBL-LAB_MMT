<?php
if (!isset($_SESSION)) session_start();
require_once '../config/db.php';
require_once '../config/settings.php'; // CMS Setting

// Include components
require_once 'components/navbar.php';
require_once 'components/footer.php';
include 'components/floating_profile.php';

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {
    $nama_lengkap = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subjek = trim($_POST['subject']);
    $pesan = trim($_POST['message']);
    
    if (empty($nama_lengkap) || empty($email) || empty($subjek) || empty($pesan)) {
        $error_message = "Semua field harus diisi!";
    } elseif (strlen($nama_lengkap) < 2) {
        $error_message = "Nama harus minimal 2 karakter!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Format email tidak valid!";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO feedback (nama_lengkap, email, subjek, pesan, created_at, is_read) VALUES (?, ?, ?, ?, NOW(), false)");
            $stmt->execute([$nama_lengkap, $email, $subjek, $pesan]);
            $success_message = "Pesan berhasil dikirim! Terima kasih.";
            $_POST = [];
        } catch (PDOException $e) {
            $error_message = "Terjadi error sistem.";
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
    
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="assets/kontak/css/style-kontak.css">
    
    <style>
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.8)), 
                        url('../<?= htmlspecialchars($site_config['hero_image_path'] ?? 'assets/images/hero.jpg') ?>') center center/cover no-repeat;
            height: 300px !important;
        }
        .hero h1 { margin-bottom: 0; }
    </style>
</head>
<body id="top">

    <?php renderNavbar('kontak'); ?>

    <main>
        <section class="hero"><div class="container"><h1>Kontak Kami</h1></div></section>

        <div class="main-content-area">
            <div class="container">
                
                <div class="primary-content">
                    <h2>Kirim Pesan</h2>
                    <p style="margin-bottom: 30px; color: #666;">Punya pertanyaan? Silakan isi formulir di bawah ini.</p>
                    
                    <?php if ($success_message): ?>
                        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success_message) ?></div>
                    <?php endif; ?>
                    <?php if ($error_message): ?>
                        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error_message) ?></div>
                    <?php endif; ?>
                    
                    <form class="contact-form" action="" method="post" id="contactForm">
                        <div class="form-group">
                            <label for="contact-name">Nama Lengkap *</label>
                            <input type="text" id="contact-name" name="name" value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>" required placeholder="Nama Anda">
                            <div class="error-message" id="nameError">Nama minimal 2 karakter</div>
                        </div>
                        <div class="form-group">
                            <label for="contact-email">Email *</label>
                            <input type="email" id="contact-email" name="email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required placeholder="email@contoh.com">
                            <div class="error-message" id="emailError">Format email salah</div>
                        </div>
                        <div class="form-group">
                            <label for="contact-subject">Subjek *</label>
                            <input type="text" id="contact-subject" name="subject" value="<?= isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : '' ?>" required placeholder="Judul pesan">
                        </div>
                        <div class="form-group">
                            <label for="contact-message">Pesan *</label>
                            <textarea id="contact-message" name="message" rows="6" required placeholder="Tulis pesan Anda..."><?= isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '' ?></textarea>
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
                                <div class="contact-info-content"><strong>Email</strong><p><a href="mailto:<?= htmlspecialchars($site_config['email_lab']) ?>"><?= htmlspecialchars($site_config['email_lab']) ?></a></p></div>
                            </div>
                            <div class="contact-info-item">
                                <div class="contact-info-icon"><i class="fas fa-phone"></i></div>
                                <div class="contact-info-content"><strong>Telepon</strong><p><?= htmlspecialchars($site_config['telepon_lab']) ?></p></div>
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
                                <?php if(!empty($site_config['fb_link'])): ?><a href="<?= $site_config['fb_link'] ?>" target="_blank"><i class="fab fa-facebook-f"></i></a><?php endif; ?>
                                <?php if(!empty($site_config['x_link'])): ?><a href="<?= $site_config['x_link'] ?>" target="_blank"><i class="fab fa-x-twitter"></i></a><?php endif; ?>
                                <?php if(!empty($site_config['ig_link'])): ?><a href="<?= $site_config['ig_link'] ?>" target="_blank"><i class="fab fa-instagram"></i></a><?php endif; ?>
                                <?php if(!empty($site_config['yt_link'])): ?><a href="<?= $site_config['yt_link'] ?>" target="_blank"><i class="fab fa-youtube"></i></a><?php endif; ?>
                                <?php if(!empty($site_config['linkedin'])): ?><a href="<?= $site_config['linkedin'] ?>" target="_blank"><i class="fab fa-linkedin-in"></i></a><?php endif; ?>
                            </div>
                        </div>
                    </div>
                </aside>
                
            </div>
        </div>
    </main>

    <?php renderFloatingProfile(); renderFooter(); ?>

    <a href="#top" id="scrollTopBtn" class="scroll-top-btn" aria-label="Kembali ke atas">&uarr;</a>

    <script src="../assets/js/navbar.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="assets/kontak/js/script-kontak.js"></script>

</body>
</html>