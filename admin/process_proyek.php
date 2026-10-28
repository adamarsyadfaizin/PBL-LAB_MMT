<?php
// admin/process_proyek.php
require_once __DIR__ . '/../config/auth.php';
startSecureSession();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';

requireAdmin($pdo);

function backWithMessage(string $message): void
{
    echo '<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"></head><body>'
        . '<script>alert(' . json_encode($message) . '); history.back();</script>'
        . '<noscript><p>' . htmlspecialchars($message) . '</p><a href="proyek.php">Kembali</a></noscript>'
        . '</body></html>';
    exit;
}

$target_dir = 'assets/images/projects/';

// DELETE
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    if (!csrfValid($_GET['csrf'] ?? null)) {
        denyAccess('Token keamanan tidak valid. Muat ulang halaman daftar proyek.');
    }

    $id = (int) ($_GET['id'] ?? 0);

    $stmt = $pdo->prepare("SELECT cover_image FROM projects WHERE id = ?");
    $stmt->execute([$id]);
    removeUpload($stmt->fetchColumn());

    $pdo->prepare("DELETE FROM projects WHERE id = ?")->execute([$id]);
    autoRefreshAfterCRUD($pdo, 'project');

    header("Location: proyek.php?status=deleted");
    exit();
}

// SAVE
if (isset($_POST['save'])) {
    requireCsrf();

    $id          = (int) ($_POST['id'] ?? 0);
    $title       = trim($_POST['title'] ?? '');
    $year        = (int) ($_POST['year'] ?? date('Y'));
    $category_id = (int) ($_POST['category_id'] ?? 0) ?: null;
    $summary     = trim($_POST['summary'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status      = $_POST['status'] ?? 'published';

    // Jika kosong, set NULL agar database bersih
    $repo_url = !empty($_POST['repo_url']) ? trim($_POST['repo_url']) : null;
    $demo_url = !empty($_POST['demo_url']) ? trim($_POST['demo_url']) : null;

    if ($title === '') {
        backWithMessage('Judul proyek wajib diisi.');
    }

    if ($summary === '') {
        $summary = excerpt($description, 150);
    }

    $slug = uniqueSlug($pdo, 'projects', slugify($title), $id ?: null);

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
            // UPDATE
            $sql = "UPDATE projects SET title=?, slug=?, year=?, category_id=?, summary=?, description=?, repo_url=?, demo_url=?, status=?, updated_at=NOW()";
            $params = [$title, $slug, $year, $category_id, $summary, $description, $repo_url, $demo_url, $status];

            if ($cover_image) {
                $stmt = $pdo->prepare("SELECT cover_image FROM projects WHERE id = ?");
                $stmt->execute([$id]);
                removeUpload($stmt->fetchColumn());

                $sql .= ", cover_image=?";
                $params[] = $cover_image;
            }

            $sql .= " WHERE id=?";
            $params[] = $id;
            $pdo->prepare($sql)->execute($params);

        } else {
            // INSERT
            $img_path = $cover_image ?? 'assets/images/default.jpg';
            $sql = "INSERT INTO projects (title, slug, year, category_id, summary, description, repo_url, demo_url, status, cover_image, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            $pdo->prepare($sql)->execute([$title, $slug, $year, $category_id, $summary, $description, $repo_url, $demo_url, $status, $img_path]);
        }

        autoRefreshAfterCRUD($pdo, 'project');

        header("Location: proyek.php?status=success");
        exit();

    } catch (PDOException $e) {
        error_log('[proyek] ' . $e->getMessage());
        backWithMessage('Gagal menyimpan proyek: ' . $e->getMessage());
    }
}
