<?php
// admin/process_galeri.php
require_once __DIR__ . '/../config/auth.php';
startSecureSession();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';

requireAdmin($pdo);

function backWithMessage(string $message): void
{
    echo '<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"></head><body>'
        . '<script>alert(' . json_encode($message) . '); history.back();</script>'
        . '<noscript><p>' . htmlspecialchars($message) . '</p><a href="galeri.php">Kembali</a></noscript>'
        . '</body></html>';
    exit;
}

$target_dir = 'assets/uploads/';

// --- DELETE ---
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    if (!csrfValid($_GET['csrf'] ?? null)) {
        denyAccess('Token keamanan tidak valid. Muat ulang halaman galeri.');
    }

    $id = (int) ($_GET['id'] ?? 0);
    $stmt = $pdo->prepare("SELECT url FROM media_assets WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if ($row) {
        removeUpload($row['url']);
        $pdo->prepare("DELETE FROM media_assets WHERE id = ?")->execute([$id]);
        autoRefreshAfterCRUD($pdo, 'media');
    }

    header("Location: galeri.php?status=deleted");
    exit();
}

// --- SAVE (INSERT / UPDATE) ---
if (isset($_POST['save'])) {
    requireCsrf();

    $id         = (int) ($_POST['id'] ?? 0);
    $caption    = trim($_POST['caption'] ?? '');
    $event_name = trim($_POST['event_name'] ?? '');
    $type       = $_POST['type'] ?? 'foto';
    $deskripsi  = trim($_POST['deskripsi'] ?? '');

    // Gabung tanggal manual + jam sekarang
    $tanggal = $_POST['tanggal'] ?? '';
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
        $tanggal = date('Y-m-d');
    }
    $created_at = $tanggal . " " . date("H:i:s");

    $file_url = null;

    // Handle Upload File
    if (!empty($_FILES['media_file']['name'])) {
        $upload = storeUpload(
            $_FILES['media_file'],
            $target_dir,
            ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4'],
            25 * 1024 * 1024
        );

        if (!$upload['ok']) {
            backWithMessage($upload['error']);
        }

        $file_url = $upload['path'];
    }

    try {
        if ($id) {
            // --- UPDATE DATA LAMA ---
            $sql = "UPDATE media_assets SET caption=?, event_name=?, type=?, deskripsi=?, created_at=?";
            $params = [$caption, $event_name, $type, $deskripsi, $created_at];

            if ($file_url) {
                // Hapus file lama jika ada upload baru
                $stmt = $pdo->prepare("SELECT url FROM media_assets WHERE id = ?");
                $stmt->execute([$id]);
                removeUpload($stmt->fetchColumn());

                $sql .= ", url=?";
                $params[] = $file_url;
            }

            $sql .= " WHERE id=?";
            $params[] = $id;
            $pdo->prepare($sql)->execute($params);

        } else {
            // --- INSERT DATA BARU ---
            if (!$file_url) {
                backWithMessage('File wajib diunggah untuk data baru.');
            }
            $sql = "INSERT INTO media_assets (caption, event_name, type, deskripsi, url, created_at) VALUES (?, ?, ?, ?, ?, ?)";
            $pdo->prepare($sql)->execute([$caption, $event_name, $type, $deskripsi, $file_url, $created_at]);
        }

        autoRefreshAfterCRUD($pdo, 'media');

        header("Location: galeri.php?status=success");
        exit();

    } catch (PDOException $e) {
        error_log('[galeri] ' . $e->getMessage());
        backWithMessage('Gagal menyimpan media: ' . $e->getMessage());
    }
}
