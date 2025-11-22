<?php
// menu/components/footer.php

function renderFooter() {
    // Ambil variabel global $site_config dari settings.php
    global $site_config; 

    // Fallback data dummy jika config belum diload (untuk safety)
    if (!isset($site_config)) {
        $site_config = [
            'alamat_lab' => 'Alamat Lab belum diatur di Admin Panel.',
            'email_lab'  => 'info@polinema.ac.id',
            'telepon_lab'=> '(0341) 404424',
            'fb_link'    => '#',
            'ig_link'    => '#',
            'linkedin'   => '#',
            'yt_link'    => '#'
        ];
    }

    // --- LOGIKA PATH OTOMATIS (Sama seperti Navbar) ---
    $path_prefix = "";
    if (file_exists("assets/css/style.css")) {
        $path_prefix = ""; // Root (beranda.php)
    } elseif (file_exists("../assets/css/style.css")) {
        $path_prefix = "../"; // Level 1 (menu/proyek.php)
    } elseif (file_exists("../../assets/css/style.css")) {
        $path_prefix = "../../"; // Level 2 (detail)
    }
    ?>

    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                
                <div class="footer-col">
                    <h4>Tentang Lab</h4>
                    <p>
                        Laboratorium Mobile & Multimedia Tech POLINEMA adalah pusat pengembangan 
                        kreativitas dan inovasi mahasiswa di bidang teknologi digital.
                    </p>
                    <div class="policy-links">
                        <ul style="list-style:none; padding:0;">
                            <li><a href="#">Kebijakan Privasi</a></li>
                            <li><a href="#">Syarat & Ketentuan</a></li>
                        </ul>
                    </div>
                </div>

                <div class="footer-col">
                    <h4>Tautan Cepat</h4>
                    <ul>
                        <li><a href="<?= $path_prefix ?>beranda.php">Beranda</a></li>
                        <li><a href="<?= $path_prefix ?>menu/profil.php">Profil</a></li>
                        <li><a href="<?= $path_prefix ?>menu/berita.php">Berita & Kegiatan</a></li>
                        <li><a href="<?= $path_prefix ?>menu/proyek.php">Proyek</a></li>
                        <li><a href="<?= $path_prefix ?>menu/galeri.php">Galeri</a></li>
                        <li><a href="<?= $path_prefix ?>menu/kontak.php">Kontak</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Kontak Kami</h4>
                    <p>
                        <i class="fas fa-map-marker-alt" style="width:20px;"></i> 
                        <?= nl2br(htmlspecialchars($site_config['alamat_lab'] ?? '-')) ?>
                    </p>
                    
                    <p style="margin-top: 10px;">
                        <i class="fas fa-envelope" style="width:20px;"></i> 
                        <a href="mailto:<?= htmlspecialchars($site_config['email_lab'] ?? '') ?>">
                            <?= htmlspecialchars($site_config['email_lab'] ?? '-') ?>
                        </a>
                    </p>
                    
                    <p>
                        <i class="fas fa-phone" style="width:20px;"></i> 
                        <?= htmlspecialchars($site_config['telepon_lab'] ?? '-') ?>
                    </p>

                    <div class="social-links-footer">
                        <?php if (!empty($site_config['fb_link'])): ?>
                            <a href="<?= htmlspecialchars($site_config['fb_link']) ?>" target="_blank" aria-label="Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                        <?php endif; ?>

                        <?php if (!empty($site_config['x_link'])): ?>
                            <a href="<?= htmlspecialchars($site_config['x_link']) ?>" target="_blank" aria-label="Twitter / X">
                                <i class="fab fa-x-twitter"></i>
                            </a>
                        <?php endif; ?>

                        <?php if (!empty($site_config['ig_link'])): ?>
                            <a href="<?= htmlspecialchars($site_config['ig_link']) ?>" target="_blank" aria-label="Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                        <?php endif; ?>

                        <?php if (!empty($site_config['yt_link'])): ?>
                            <a href="<?= htmlspecialchars($site_config['yt_link']) ?>" target="_blank" aria-label="YouTube">
                                <i class="fab fa-youtube"></i>
                            </a>
                        <?php endif; ?>

                        <?php if (!empty($site_config['linkedin'])): ?>
                            <a href="<?= htmlspecialchars($site_config['linkedin']) ?>" target="_blank" aria-label="LinkedIn">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <div class="footer-copyright">
                <p>&copy; <?= date('Y') ?> Laboratorium Mobile & Multimedia Tech POLINEMA. All Rights Reserved.</p>
            </div>
        </div>
    </footer>
    <?php
}
?>