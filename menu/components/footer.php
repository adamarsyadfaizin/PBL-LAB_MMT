<?php
// menu/components/footer.php

function renderFooter() {
    // Ambil variabel global $site_config dari settings.php
    global $site_config; 

    // Fallback data dummy jika config belum diload
    $config = [
        'alamat_lab' => $site_config['alamat_lab'] ?? 'Jl. Soekarno Hatta No.4, Jatimulyo, Kec. Lowokwaru, Kota Malang, Jawa Timur 65141',
        'email_lab'  => $site_config['email_lab'] ?? 'mobilemultimedia@polinema.ac.id',
        'telepon_lab'=> $site_config['telepon_lab'] ?? '(0341) 404424',
        // Pastikan nama key sesuai dengan yang digunakan di bawah
        'fb_link'    => $site_config['fb_link'] ?? '#',
        'x_link'     => $site_config['x_link'] ?? '#', 
        'ig_link'    => $site_config['ig_link'] ?? '#',
        'yt_link'    => $site_config['yt_link'] ?? '#', 
        'linkedin'   => $site_config['linkedin'] ?? '#'
    ];

    // --- LOGIKA PATH OTOMATIS ---
    $path_prefix = "";
    if (file_exists("assets/css/style.css")) {
        $path_prefix = ""; // Root (beranda.php)
    } elseif (file_exists("../assets/css/style.css")) {
        $path_prefix = "../"; // Level 1 (menu/proyek.php)
    } elseif (file_exists("../../assets/css/style.css")) {
        $path_prefix = "../../"; // Level 2 (detail)
    }
    ?>

    <style>
        /* ======================================= */
        /* CSS KHUSUS FOOTER (ORANYE GELAP + KUNING + BIRU) */
        /* ======================================= */
        :root {
            --color-primary: #E65100; /* Oranye gelap (lebih gelap dari sebelumnya) */
            --color-secondary: #1565C0; /* Biru */
            --color-accent: #FFB300; /* Kuning emas */
            --color-accent-light: #FFD54F; /* Kuning muda */
            --color-text-light: #F8F8F8; /* Putih Muda */
            --color-text-semi: rgba(255, 255, 255, 0.8); /* Putih semi-transparan */
        }

        .site-footer {
            background: linear-gradient(135deg, var(--color-primary) 0%, #BF360C 100%); /* Gradient oranye gelap */
            color: var(--color-text-light);
            padding: 50px 0;
            font-size: 0.95rem;
            position: relative;
            overflow: hidden;
        }

        /* Efek dekoratif dengan bentuk biru */
        .site-footer::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: var(--color-secondary);
            opacity: 0.1;
            border-radius: 50%;
            z-index: 1;
        }

        .site-footer::after {
            content: '';
            position: absolute;
            bottom: -30px;
            left: -30px;
            width: 150px;
            height: 150px;
            background: var(--color-accent);
            opacity: 0.1;
            border-radius: 50%;
            z-index: 1;
        }

        .footer-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            margin-bottom: 30px;
            position: relative;
            z-index: 2;
        }
        
        /* Pengaturan kolom untuk tampilan desktop */
        .footer-col {
            width: calc(33.333% - 20px);
            min-width: 250px;
        }
        @media (max-width: 992px) {
             .footer-col { width: calc(50% - 15px); }
        }
        @media (max-width: 576px) {
             .footer-col { width: 100%; }
        }

        .footer-col h4 {
            color: var(--color-accent); /* Judul kuning emas */
            border-bottom: 3px solid var(--color-accent-light); /* Garis bawah kuning muda */
            padding-bottom: 8px;
            margin-bottom: 15px;
            font-size: 1.25rem;
            font-weight: bold;
            position: relative;
        }

        /* Tambahkan aksen biru pada judul */
        .footer-col h4::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 30px;
            height: 3px;
            background: var(--color-secondary);
        }

        .footer-col p, .policy-links a, .footer-col li a {
            color: var(--color-text-semi);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .footer-col a:hover {
            color: var(--color-accent-light);
            transform: translateX(5px);
        }
        
        .footer-col li {
             list-style: none;
             padding: 6px 0;
             border-left: 2px solid transparent;
             transition: all 0.3s ease;
        }
        
        .footer-col li:hover {
            border-left: 2px solid var(--color-secondary);
            padding-left: 8px;
        }
        
        .footer-col ul {
             padding-left: 0;
        }

        /* Gaya Ikon Kontak */
        .footer-col p i {
            color: var(--color-accent-light); /* Ikon kuning muda */
            margin-right: 10px;
            width: 16px;
            text-align: center;
        }
        
        /* Gaya Ikon Sosial Media */
        .social-links-footer {
            margin-top: 20px;
            display: flex;
            gap: 8px;
        }
        
        .social-links-footer a {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            width: 38px;
            height: 38px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid var(--color-accent);
            border-radius: 8px;
            color: var(--color-accent);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .social-links-footer a::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .social-links-footer a:hover {
            background: var(--color-accent);
            color: var(--color-primary);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 179, 0, 0.3);
        }

        .social-links-footer a:hover::before {
            left: 100%;
        }

        /* Tambahkan warna biru pada hover untuk beberapa ikon */
        .social-links-footer a:nth-child(2):hover { /* Twitter/X */
            background: var(--color-secondary);
            border-color: var(--color-secondary);
        }
        
        .social-links-footer a:nth-child(5):hover { /* LinkedIn */
            background: var(--color-secondary);
            border-color: var(--color-secondary);
        }

        /* Copyright */
        .footer-copyright {
            border-top: 1px solid rgba(255, 179, 0, 0.3); /* Garis kuning transparan */
            padding-top: 20px;
            text-align: center;
            font-size: 0.8rem;
            color: var(--color-accent-light);
            position: relative;
            z-index: 2;
        }

        /* Efek tambahan untuk policy links */
        .policy-links ul {
            margin-top: 15px;
        }
        
        .policy-links li {
            display: inline-block;
            margin-right: 15px;
        }
        
        .policy-links a {
            padding: 5px 0;
            position: relative;
        }
        
        .policy-links a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 1px;
            background: var(--color-secondary);
            transition: width 0.3s ease;
        }
        
        .policy-links a:hover::after {
            width: 100%;
        }
    </style>

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
                        <ul>
                            <li><a href="<?= $path_prefix ?>kebijakan-privasi.php">Kebijakan Privasi</a></li>
                            <li><a href="<?= $path_prefix ?>syarat-ketentuan.php">Syarat & Ketentuan</a></li>
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
                        <i class="fas fa-map-marker-alt"></i> 
                        <?= nl2br(htmlspecialchars($config['alamat_lab'])) ?>
                    </p>
                    
                    <p style="margin-top: 10px;">
                        <i class="fas fa-envelope"></i> 
                        <a href="mailto:<?= htmlspecialchars($config['email_lab']) ?>">
                            <?= htmlspecialchars($config['email_lab']) ?>
                        </a>
                    </p>
                    
                    <p>
                        <i class="fas fa-phone"></i> 
                        <?= htmlspecialchars($config['telepon_lab']) ?>
                    </p>

                    <div class="social-links-footer">
                        <?php if (!empty($config['fb_link']) && $config['fb_link'] != '#'): ?>
                            <a href="<?= htmlspecialchars($config['fb_link']) ?>" target="_blank" aria-label="Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                        <?php endif; ?>

                        <?php if (!empty($config['x_link']) && $config['x_link'] != '#'): ?>
                            <a href="<?= htmlspecialchars($config['x_link']) ?>" target="_blank" aria-label="Twitter / X">
                                <i class="fab fa-x-twitter"></i>
                            </a>
                        <?php endif; ?>

                        <?php if (!empty($config['ig_link']) && $config['ig_link'] != '#'): ?>
                            <a href="<?= htmlspecialchars($config['ig_link']) ?>" target="_blank" aria-label="Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                        <?php endif; ?>

                        <?php if (!empty($config['yt_link']) && $config['yt_link'] != '#'): ?>
                            <a href="<?= htmlspecialchars($config['yt_link']) ?>" target="_blank" aria-label="YouTube">
                                <i class="fab fa-youtube"></i>
                            </a>
                        <?php endif; ?>

                        <?php if (!empty($config['linkedin']) && $config['linkedin'] != '#'): ?>
                            <a href="<?= htmlspecialchars($config['linkedin']) ?>" target="_blank" aria-label="LinkedIn">
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