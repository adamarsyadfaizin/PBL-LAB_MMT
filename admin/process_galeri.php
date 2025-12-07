<?php
// admin/process_galeri.php
session_start();
require_once '../config/db.php';

$target_dir = "../assets/uploads/";
$db_path_prefix = "assets/uploads/"; 

if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// --- DELETE ---
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT url FROM media_assets WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    
    if ($row) {
        if (!filter_var($row['url'], FILTER_VALIDATE_URL)) {
            $file_path = "../" . $row['url'];
            if (file_exists($file_path)) unlink($file_path);
        }
        $pdo->prepare("DELETE FROM media_assets WHERE id = ?")->execute([$id]);
    }
    
    header("Location: galeri.php?status=deleted");
    exit();
}

// --- SAVE (INSERT / UPDATE) ---
if (isset($_POST['save'])) {
    $id = $_POST['id'];
    $caption = $_POST['caption'];
    $event_name = $_POST['event_name'];
    $type = $_POST['type'];
    $deskripsi = $_POST['deskripsi'];
    
    // Gabung tanggal manual + jam sekarang
    $tanggal = $_POST['tanggal'];
    $created_at = $tanggal . " " . date("H:i:s");

    $file_url = null;

    // Handle Upload File
    if (!empty($_FILES['media_file']['name'])) {
        $ext = strtolower(pathinfo($_FILES['media_file']['name'], PATHINFO_EXTENSION));
        $new_name = time() . '_' . uniqid() . '.' . $ext;
        $target_file = $target_dir . $new_name;
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webp'];
        
        if (in_array($ext, $allowed)) {
            if (move_uploaded_file($_FILES['media_file']['tmp_name'], $target_file)) {
                $file_url = $db_path_prefix . $new_name;
            } else {
                echo "<script>alert('Gagal upload.'); history.back();</script>"; exit;
            }
        } else {
            echo "<script>alert('Format file tidak didukung.'); history.back();</script>"; exit;
        }
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
                $old = $stmt->fetchColumn();
                if ($old && !filter_var($old, FILTER_VALIDATE_URL) && file_exists("../" . $old)) {
                    unlink("../" . $old);
                }

                $sql .= ", url=?";
                $params[] = $file_url;
            }

            $sql .= " WHERE id=?";
            $params[] = $id;
            $pdo->prepare($sql)->execute($params);

        } else {
            // --- INSERT DATA BARU ---
            if (!$file_url) {
                echo "<script>alert('File wajib diupload untuk data baru!'); history.back();</script>"; exit;
            }
            $sql = "INSERT INTO media_assets (caption, event_name, type, deskripsi, url, created_at) VALUES (?, ?, ?, ?, ?, ?)";
            $pdo->prepare($sql)->execute([$caption, $event_name, $type, $deskripsi, $file_url, $created_at]);
        }
        
        header("Location: galeri.php?status=success");
        exit();

    } catch (PDOException $e) {
        die("Error Database: " . $e->getMessage());
    }
}
?>