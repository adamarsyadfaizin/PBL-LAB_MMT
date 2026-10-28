<?php
/**
 * FILE: config/settings.php
 * Memuat konfigurasi tampilan situs dari tabel lab_profile.
 * Pastikan koneksi db ($pdo) sudah tersedia sebelum file ini di-include.
 */

// Nilai cadangan kalau tabel kosong atau database bermasalah.
$site_config = [
    'hero_title'       => 'LABORATORIUM MMT',
    'hero_description' => 'Laboratorium Mobile and Multimedia Technology Politeknik Negeri Malang.',
    'logo_path'        => 'assets/images/logo-placeholder.png',
    'hero_image_path'  => 'assets/images/hero.jpg',
    'alamat_lab'       => 'Jl. Soekarno Hatta No. 9, Malang',
    'email_lab'        => 'mobilemultimedia@polinema.ac.id',
    'telepon_lab'      => '(0341) 404424',
    'footer_desc'      => 'Laboratorium Mobile and Multimedia Technology, Jurusan Teknologi Informasi, Politeknik Negeri Malang.',
    'copyright_text'   => '© ' . date('Y') . ' Laboratorium MMT POLINEMA. All Rights Reserved.',
    'fb_link'          => '#',
    'x_link'           => '#',
    'ig_link'          => '#',
    'yt_link'          => '#',
    'linkedin'         => '#',
];

if (isset($pdo) && $pdo instanceof PDO) {
    try {
        $stmtSettings = $pdo->query("SELECT * FROM lab_profile ORDER BY id ASC LIMIT 1");
        $row = $stmtSettings ? $stmtSettings->fetch(PDO::FETCH_ASSOC) : null;

        if ($row) {
            // Gabungkan: nilai dari database menimpa nilai cadangan,
            // tapi kolom yang kosong tetap memakai nilai cadangan.
            foreach ($row as $key => $value) {
                if ($value !== null && $value !== '') {
                    $site_config[$key] = $value;
                }
            }
        }
    } catch (PDOException $e) {
        error_log('[settings] Gagal memuat lab_profile: ' . $e->getMessage());
    }
}

/**
 * Ambil satu nilai konfigurasi situs dengan nilai default.
 */
if (!function_exists('site')) {
    function site(string $key, string $default = '')
    {
        global $site_config;
        return $site_config[$key] ?? $default;
    }
}

/**
 * Prefix relatif menuju root project, dihitung dari URL file yang sedang dibuka.
 * Contoh: /menu/profil.php -> "../", /menu/detail/x.php -> "../../".
 */
if (!function_exists('pathPrefix')) {
    function pathPrefix(): string
    {
        $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
        $dir = trim(str_replace('\\', '/', dirname($script)), '/');

        if ($dir === '' || $dir === '.') {
            return '';
        }

        $depth = substr_count($dir, '/') + 1;
        return str_repeat('../', $depth);
    }
}
