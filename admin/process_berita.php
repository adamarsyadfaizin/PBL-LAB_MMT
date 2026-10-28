<?php
// admin/process_berita.php
require_once __DIR__ . '/../config/auth.php';
startSecureSession();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';

requireAdmin($pdo);

/**
 * Tampilkan pesan lalu kembali ke halaman sebelumnya.
 */
function backWithMessage(string $message): void
{
    echo '<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"></head><body>'
        . '<script>alert(' . json_encode($message) . '); history.back();</script>'
        . '<noscript><p>' . htmlspecialchars($message) . '</p><a href="berita.php">Kembali</a></noscript>'
        . '</body></html>';
    exit;
}

$target_dir = 'assets/images/news/';

// ==========================================
// LOGIKA DELETE
// ==========================================
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    if (!csrfValid($_GET['csrf'] ?? null)) {
        denyAccess('Token keamanan tidak valid. Muat ulang halaman daftar berita.');
    }

    $id = (int) ($_GET['id'] ?? 0);

    // Ambil info gambar lama dulu untuk dihapus
    $stmt = $pdo->prepare("SELECT cover_image FROM news WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if ($row) {
        removeUpload($row['cover_image']);

        $stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
        $stmt->execute([$id]);

        // Auto refresh materialized views setelah delete
        autoRefreshAfterCRUD($pdo, 'news');
    }

    header("Location: berita.php");
    exit();
}

// ==========================================
// LOGIKA SAVE (INSERT / UPDATE)
// ==========================================
if (isset($_POST['save'])) {
    requireCsrf();

    $id       = (int) ($_POST['id'] ?? 0);
    $title    = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $summary  = trim($_POST['summary'] ?? '');
    $content  = trim($_POST['content'] ?? '');
    $status   = $_POST['status'] ?? 'published';

    if ($title === '') {
        backWithMessage('Judul berita wajib diisi.');
    }

    $slug = uniqueSlug($pdo, 'news', slugify($title), $id ?: null);

    // Gabungkan tanggal dari form dengan jam sekarang agar tidak terset ke 00:00
    $input_date = $_POST['created_at'] ?? '';
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $input_date)) {
        $input_date = date('Y-m-d');
    }
    $final_created_at = $input_date . ' ' . date('H:i:s');

    // --- PROSES UPLOAD GAMBAR ---
    $cover_image = null;

    if (!empty($_FILES['cover_image']['name'])) {
        $upload = storeUpload($_FILES['cover_image'], $target_dir, ['jpg', 'jpeg', 'png', 'webp'], 4 * 1024 * 1024);

        if (!$upload['ok']) {
            backWithMessage($upload['error']);
        }

        $cover_image = $upload['path'];
    }

    try {
        if ($id) {
            // === UPDATE ===
            if ($cover_image) {
                // Hapus gambar lama jika ada upload baru
                $stmt = $pdo->prepare("SELECT cover_image FROM news WHERE id = ?");
                $stmt->execute([$id]);
                removeUpload($stmt->fetchColumn());

                $sql = "UPDATE news SET title=?, slug=?, category=?, summary=?, content=?, status=?, cover_image=?, created_at=?, updated_at=NOW() WHERE id=?";
                $pdo->prepare($sql)->execute([$title, $slug, $category, $summary, $content, $status, $cover_image, $final_created_at, $id]);
            } else {
                $sql = "UPDATE news SET title=?, slug=?, category=?, summary=?, content=?, status=?, created_at=?, updated_at=NOW() WHERE id=?";
                $pdo->prepare($sql)->execute([$title, $slug, $category, $summary, $content, $status, $final_created_at, $id]);
            }

        } else {
            // === INSERT BARU ===
            $img_path = $cover_image ?? 'assets/images/default.jpg';

            $sql = "INSERT INTO news (title, slug, category, summary, content, status, cover_image, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            $pdo->prepare($sql)->execute([$title, $slug, $category, $summary, $content, $status, $img_path, $final_created_at]);
        }

        // Auto refresh materialized views setelah insert/update
        autoRefreshAfterCRUD($pdo, 'news');

        header("Location: berita.php");
        exit();
    } catch (PDOException $e) {
        error_log('[berita] ' . $e->getMessage());
        backWithMessage('Gagal menyimpan berita: ' . $e->getMessage());
    }
}
