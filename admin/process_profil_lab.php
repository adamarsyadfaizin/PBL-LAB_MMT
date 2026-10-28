<?php
// admin/process_profil_lab.php
require_once __DIR__ . '/../config/auth.php';
startSecureSession();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';

requireAdmin($pdo);

function backWithMessage(string $message, string $fallback = 'profil_lab.php'): void
{
    echo '<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"></head><body>'
        . '<script>alert(' . json_encode($message) . '); history.back();</script>'
        . '<noscript><p>' . htmlspecialchars($message) . '</p><a href="' . $fallback . '">Kembali</a></noscript>'
        . '</body></html>';
    exit;
}

$target_dir = 'assets/uploads/';
$adminName = $_SESSION['user_name'] ?? 'Admin';

// --- 1. UPDATE PROFIL UTAMA ---
if (isset($_POST['update_profile'])) {
    requireCsrf();

    $visi     = trim($_POST['visi'] ?? '');
    $misi     = trim($_POST['misi'] ?? '');
    $sejarah  = trim($_POST['sejarah'] ?? '');

    // Selalu pakai baris profil pertama (id terkecil)
    $profileId = (int) $pdo->query("SELECT id FROM lab_profile ORDER BY id ASC LIMIT 1")->fetchColumn();
    $struktur_path = '';

    if ($profileId) {
        $stmt = $pdo->prepare("SELECT struktur_org_path FROM lab_profile WHERE id = ?");
        $stmt->execute([$profileId]);
        $struktur_path = $stmt->fetchColumn() ?: '';
    }

    if (!empty($_FILES['struktur_org']['name'])) {
        $upload = storeUpload($_FILES['struktur_org'], $target_dir, ['jpg', 'jpeg', 'png', 'webp'], 4 * 1024 * 1024);

        if (!$upload['ok']) {
            backWithMessage($upload['error']);
        }

        removeUpload($struktur_path);
        $struktur_path = $upload['path'];
    }

    if ($profileId) {
        $sql = "UPDATE lab_profile SET visi=?, misi=?, sejarah=?, struktur_org_path=? WHERE id=?";
        $pdo->prepare($sql)->execute([$visi, $misi, $sejarah, $struktur_path, $profileId]);
    } else {
        $sql = "INSERT INTO lab_profile (visi, misi, sejarah, struktur_org_path) VALUES (?, ?, ?, ?)";
        $pdo->prepare($sql)->execute([$visi, $misi, $sejarah, $struktur_path]);
    }

    if (function_exists('writeLog')) {
        writeLog($pdo, $adminName, "Update Profil Lab");
    }

    header("Location: profil_lab.php?status=success&tab=profil");
    exit();
}

// --- 2. TAMBAH ANGGOTA TIM ---
if (isset($_POST['add_member'])) {
    requireCsrf();

    $name = trim($_POST['name'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $tags = trim($_POST['tags'] ?? '');

    if ($name === '' || $role === '') {
        backWithMessage('Nama dan peran anggota wajib diisi.');
    }

    $avatar_url = '';
    if (!empty($_FILES['avatar']['name'])) {
        $upload = storeUpload($_FILES['avatar'], $target_dir, ['jpg', 'jpeg', 'png', 'webp'], 4 * 1024 * 1024);

        if (!$upload['ok']) {
            backWithMessage($upload['error']);
        }

        $avatar_url = $upload['path'];
    }

    $sql = "INSERT INTO members (name, role, tags, linkedin_url, scholar_url, instagram, youtube, facebook, avatar_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $pdo->prepare($sql)->execute([
        $name,
        $role,
        $tags,
        trim($_POST['linkedin'] ?? ''),
        trim($_POST['scholar'] ?? ''),
        trim($_POST['instagram'] ?? ''),
        trim($_POST['youtube'] ?? ''),
        trim($_POST['facebook'] ?? ''),
        $avatar_url
    ]);

    if (function_exists('writeLog')) {
        writeLog($pdo, $adminName, "Tambah Anggota: $name");
    }

    autoRefreshAfterCRUD($pdo, 'member');

    header("Location: profil_lab.php?status=success&tab=tim");
    exit();
}

// --- 3. EDIT ANGGOTA TIM ---
if (isset($_POST['edit_member'])) {
    requireCsrf();

    $id   = (int) ($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $tags = trim($_POST['tags'] ?? '');

    if ($id <= 0 || $name === '' || $role === '') {
        backWithMessage('Data anggota tidak lengkap.');
    }

    $stmt = $pdo->prepare("SELECT avatar_url FROM members WHERE id = ?");
    $stmt->execute([$id]);
    $avatar_url = $stmt->fetchColumn() ?: '';

    if (!empty($_FILES['avatar']['name'])) {
        $upload = storeUpload($_FILES['avatar'], $target_dir, ['jpg', 'jpeg', 'png', 'webp'], 4 * 1024 * 1024);

        if (!$upload['ok']) {
            backWithMessage($upload['error']);
        }

        removeUpload($avatar_url);
        $avatar_url = $upload['path'];
    }

    $sql = "UPDATE members SET name=?, role=?, tags=?, linkedin_url=?, scholar_url=?, instagram=?, youtube=?, facebook=?, avatar_url=? WHERE id=?";
    $pdo->prepare($sql)->execute([
        $name,
        $role,
        $tags,
        trim($_POST['linkedin'] ?? ''),
        trim($_POST['scholar'] ?? ''),
        trim($_POST['instagram'] ?? ''),
        trim($_POST['youtube'] ?? ''),
        trim($_POST['facebook'] ?? ''),
        $avatar_url,
        $id
    ]);

    if (function_exists('writeLog')) {
        writeLog($pdo, $adminName, "Edit Anggota: $name");
    }

    autoRefreshAfterCRUD($pdo, 'member');

    header("Location: profil_lab.php?status=success&tab=tim");
    exit();
}

// --- 4. HAPUS ANGGOTA TIM ---
if (isset($_GET['delete_member'])) {
    if (!csrfValid($_GET['csrf'] ?? null)) {
        denyAccess('Token keamanan tidak valid. Muat ulang halaman profil.');
    }

    $id = (int) $_GET['delete_member'];

    $stmt = $pdo->prepare("SELECT avatar_url, name FROM members WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if ($row) {
        removeUpload($row['avatar_url']);
        $pdo->prepare("DELETE FROM members WHERE id = ?")->execute([$id]);

        if (function_exists('writeLog')) {
            writeLog($pdo, $adminName, "Hapus Anggota: " . $row['name']);
        }

        autoRefreshAfterCRUD($pdo, 'member');
    }

    header("Location: profil_lab.php?status=deleted&tab=tim");
    exit();
}
