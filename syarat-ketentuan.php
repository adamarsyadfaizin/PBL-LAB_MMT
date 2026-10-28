<?php
// FILE: syarat-ketentuan.php
require_once __DIR__ . '/config/auth.php';
startSecureSession();

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/settings.php';
require_once __DIR__ . '/menu/components/navbar.php';
require_once __DIR__ . '/menu/components/footer.php';

$path_prefix = "";
$cache_buster = time();
$page_title = "Syarat dan Ketentuan";
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
                <h1>Syarat dan Ketentuan</h1>
                <p class="updated">Terakhir diperbarui: <?= date('d F Y') ?></p>

                <p>
                    Dengan mengakses dan menggunakan situs Laboratorium Mobile and Multimedia Technology
                    (Lab MMT) Politeknik Negeri Malang, Anda menyetujui syarat dan ketentuan berikut.
                </p>

                <h2>1. Penggunaan Konten</h2>
                <ul>
                    <li>Seluruh konten situs ini disediakan untuk keperluan informasi dan pendidikan.</li>
                    <li>Anda boleh mengutip konten dengan mencantumkan sumber Lab MMT POLINEMA.</li>
                    <li>Dilarang menggunakan konten untuk tujuan komersial tanpa izin tertulis.</li>
                </ul>

                <h2>2. Kontribusi Pengguna</h2>
                <ul>
                    <li>Komentar dan pesan yang dikirimkan harus bebas dari ujaran kebencian, spam, dan konten yang melanggar hukum.</li>
                    <li>Pengelola situs berhak menghapus konten yang tidak sesuai tanpa pemberitahuan sebelumnya.</li>
                    <li>Anda bertanggung jawab atas kebenaran informasi yang Anda kirimkan.</li>
                </ul>

                <h2>3. Akun Pengguna</h2>
                <p>
                    Beberapa fitur seperti pemberian komentar memerlukan akun. Jaga kerahasiaan kata sandi
                    Anda dan segera hubungi pengelola apabila menemukan aktivitas mencurigakan pada akun.
                </p>

                <h2>4. Batasan Tanggung Jawab</h2>
                <p>
                    Lab MMT berupaya menjaga ketepatan informasi, namun tidak bertanggung jawab atas
                    kerugian yang timbul dari penggunaan informasi pada situs ini.
                </p>

                <h2>5. Perubahan Ketentuan</h2>
                <p>
                    Ketentuan ini dapat berubah sewaktu-waktu. Versi terbaru selalu ditampilkan pada
                    halaman ini.
                </p>

                <h2>6. Kontak</h2>
                <p>
                    Pertanyaan mengenai ketentuan ini dapat dikirim ke
                    <a href="mailto:<?= e($site_config['email_lab'] ?? '') ?>"><?= e($site_config['email_lab'] ?? '') ?></a>.
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
