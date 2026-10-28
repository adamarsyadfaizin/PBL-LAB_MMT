<?php
// FILE: kebijakan-privasi.php
require_once __DIR__ . '/config/auth.php';
startSecureSession();

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/settings.php';
require_once __DIR__ . '/menu/components/navbar.php';
require_once __DIR__ . '/menu/components/footer.php';

$path_prefix = "";
$cache_buster = time();
$page_title = "Kebijakan Privasi";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title) ?> - Laboratorium MMT POLINEMA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="menu/components/navbar.css?v=<?= $cache_buster ?>">
    <link rel="stylesheet" href="assets/css/style.css?v=<?= $cache_buster ?>">
    <style>
        body {
            background: url('assets/images/wallpaper.jpg') center center/cover fixed no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
        }
        .page-section { padding: 120px 0 80px; }
        .page-card {
            max-width: 860px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(10px);
            border-radius: 14px;
            padding: 40px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
        }
        .page-card h1 { color: #1b2a41; margin-bottom: 6px; }
        .page-card .updated { color: #777; font-size: 14px; margin-bottom: 28px; }
        .page-card h2 { color: #E65100; font-size: 20px; margin: 28px 0 10px; }
        .page-card p, .page-card li { color: #333; line-height: 1.8; }
        .page-card ul { padding-left: 22px; }
        @media (max-width: 600px) { .page-card { padding: 24px; } }
    </style>
</head>
<body id="top">
    <?php renderNavbar('', $path_prefix, $site_config); ?>

    <section class="page-section">
        <div class="container">
            <article class="page-card">
                <h1>Kebijakan Privasi</h1>
                <p class="updated">Terakhir diperbarui: <?= date('d F Y') ?></p>

                <p>
                    Laboratorium Mobile and Multimedia Technology (Lab MMT) Politeknik Negeri Malang
                    menghargai privasi setiap pengunjung situs ini. Halaman ini menjelaskan data apa
                    yang kami kumpulkan dan bagaimana data tersebut digunakan.
                </p>

                <h2>1. Data yang Dikumpulkan</h2>
                <ul>
                    <li>Nama dan alamat email yang Anda isi pada formulir kontak.</li>
                    <li>Isi pesan, komentar, dan penilaian yang Anda kirimkan melalui situs.</li>
                    <li>Informasi teknis sederhana seperti alamat IP dan jenis peramban untuk keperluan keamanan dan pencatatan aktivitas.</li>
                </ul>

                <h2>2. Penggunaan Data</h2>
                <ul>
                    <li>Menjawab pertanyaan, saran, atau permintaan kerja sama yang Anda kirimkan.</li>
                    <li>Menampilkan komentar pada halaman berita, proyek, atau galeri terkait.</li>
                    <li>Meningkatkan kualitas layanan dan konten situs.</li>
                </ul>

                <h2>3. Penyimpanan dan Keamanan</h2>
                <p>
                    Data disimpan pada basis data internal laboratorium dan hanya dapat diakses oleh
                    pengelola situs. Kami tidak menjual atau membagikan data pribadi Anda kepada pihak
                    ketiga untuk tujuan komersial.
                </p>

                <h2>4. Cookie dan Sesi</h2>
                <p>
                    Situs ini menggunakan cookie sesi untuk menjaga status masuk (login) pengguna.
                    Cookie ini tidak digunakan untuk pelacakan iklan dan akan berakhir saat Anda
                    keluar atau menutup peramban.
                </p>

                <h2>5. Hak Anda</h2>
                <p>
                    Anda dapat meminta penghapusan pesan atau komentar yang pernah Anda kirimkan dengan
                    menghubungi kami melalui email <a href="mailto:<?= e($site_config['email_lab'] ?? '') ?>"><?= e($site_config['email_lab'] ?? '') ?></a>.
                </p>

                <h2>6. Perubahan Kebijakan</h2>
                <p>
                    Kebijakan ini dapat diperbarui sewaktu-waktu. Perubahan akan ditampilkan pada
                    halaman ini beserta tanggal pembaruannya.
                </p>
            </article>
        </div>
    </section>

    <?php renderFooter($path_prefix, $site_config); ?>

    <a href="#top" id="scrollTopBtn" class="scroll-top-btn" aria-label="Kembali ke atas">&uarr;</a>
    <script src="assets/js/navbar.js?v=<?= $cache_buster ?>"></script>
    <script src="assets/js/scrolltop.js?v=<?= $cache_buster ?>"></script>
</body>
</html>
