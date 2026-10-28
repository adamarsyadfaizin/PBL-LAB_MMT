<?php
// admin/process_pengaturan.php
require_once __DIR__ . '/../config/auth.php';
startSecureSession();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';

requireAdmin($pdo);

if (!isset($_POST['save'])) {
    header('Location: pengaturan.php');
    exit();
}

requireCsrf();

$id = (int) ($_POST['id'] ?? 0);
$target_dir = 'assets/uploads/';

// Ambil data lama
$stmt = $pdo->prepare("SELECT * FROM lab_profile WHERE id = ?");
$stmt->execute([$id]);
$oldData = $stmt->fetch();

if (!$oldData) {
    echo "<script>alert('Data profil lab tidak ditemukan.'); window.location='pengaturan.php';</script>";
    exit;
}

// Pasangan input file => kolom database
$uploads = [
    'logo'         => 'logo_path',
    'hero_image'   => 'hero_image_path',
    'about_hero'   => 'about_hero_image',
    'news_hero'    => 'news_hero_image',
    'project_hero' => 'project_hero_image',
    'gallery_hero' => 'gallery_hero_image',
    'contact_hero' => 'contact_hero_image',
];

$paths = [];
foreach ($uploads as $inputName => $column) {
    $paths[$column] = $oldData[$column] ?? null;

    if (!empty($_FILES[$inputName]['name'])) {
        $upload = storeUpload($_FILES[$inputName], $target_dir, ['jpg', 'jpeg', 'png', 'webp', 'svg'], 4 * 1024 * 1024);

        if (!$upload['ok']) {
            echo '<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"></head><body>'
                . '<script>alert(' . json_encode($upload['error']) . '); history.back();</script>'
                . '</body></html>';
            exit;
        }

        removeUpload($paths[$column]);
        $paths[$column] = $upload['path'];
    }
}

$sql = "UPDATE lab_profile SET 
        logo_path = ?, 
        hero_image_path = ?, about_hero_image = ?, news_hero_image = ?, 
        project_hero_image = ?, gallery_hero_image = ?, contact_hero_image = ?,

        hero_title = ?, hero_description = ?, about_title = ?, news_title = ?, 
        project_title = ?, gallery_title = ?, contact_title = ?,

        alamat_lab = ?,
        email_lab = ?,
        telepon_lab = ?,
        footer_desc = ?, 
        copyright_text = ?, 
        fb_link = ?, x_link = ?, ig_link = ?, yt_link = ?, linkedin = ?
        WHERE id = ?";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $paths['logo_path'],
        $paths['hero_image_path'], $paths['about_hero_image'], $paths['news_hero_image'], $paths['project_hero_image'], $paths['gallery_hero_image'], $paths['contact_hero_image'],

        $_POST['hero_title'] ?? '', $_POST['hero_description'] ?? '', $_POST['about_title'] ?? '', $_POST['news_title'] ?? '',
        $_POST['project_title'] ?? '', $_POST['gallery_title'] ?? '', $_POST['contact_title'] ?? '',

        $_POST['alamat_lab'] ?? '',
        $_POST['email_lab'] ?? '',
        $_POST['telepon_lab'] ?? '',
        $_POST['footer_desc'] ?? '',
        $_POST['copyright_text'] ?? '',
        $_POST['fb_link'] ?? '', $_POST['x_link'] ?? '', $_POST['ig_link'] ?? '', $_POST['yt_link'] ?? '', $_POST['linkedin'] ?? '',
        $id
    ]);

    if (function_exists('writeLog')) {
        writeLog($pdo, $_SESSION['user_name'] ?? 'Admin', 'Update Pengaturan Situs');
    }

    echo "<script>alert('Pengaturan berhasil disimpan!'); window.location='pengaturan.php';</script>";
} catch (PDOException $e) {
    error_log('[pengaturan] ' . $e->getMessage());
    echo "<script>alert('Gagal menyimpan pengaturan.'); history.back();</script>";
}
