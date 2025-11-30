<?php
// Pastikan koneksi db ($pdo) sudah tersedia sebelum file ini di-include
$stmtSettings = $pdo->query("SELECT * FROM lab_profile ORDER BY id ASC LIMIT 1");
$site_config = $stmtSettings->fetch(PDO::FETCH_ASSOC);

// Fallback jika data kosong (agar tidak error)
if (!$site_config) {
    $site_config = [
        'hero_title' => 'LABORATORIUM MMT',
        'hero_description' => 'Deskripsi default.',
        'logo_path' => 'assets/images/logo-placeholder.png',
        'hero_image_path' => 'assets/images/hero.jpg',
        'alamat_lab' => 'Alamat Default',
        'email_lab' => 'info@polinema.ac.id',
        'telepon_lab' => '-',
        'fb_link' => '#',
        'ig_link' => '#',
        'linkedin' => '#'
    ];
}
?>