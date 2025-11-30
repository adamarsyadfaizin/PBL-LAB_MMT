<?php
session_start();
require_once '../config/db.php';

// --- HANDLE UPLOAD ---
if (isset($_POST['upload'])) {
    $caption = $_POST['caption'];
    $event_name = $_POST['event_name']; // Ambil input acara
    $type = $_POST['type'];
    $deskripsi = $_POST['deskripsi'];

    // Cek file upload
    if (!empty($_FILES['media_file']['name'])) {
        // Path tujuan: mundur dari admin -> masuk assets/uploads
        $target_dir = "../assets/uploads/";
        
        // Buat folder jika belum ada
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_ext = strtolower(pathinfo($_FILES["media_file"]["name"], PATHINFO_EXTENSION));
        // Nama file unik: time_uniqid.ext
        $file_name = time() . '_' . uniqid() . '.' . $file_ext;
        $target_file = $target_dir . $file_name;

        // Validasi ekstensi
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webp'];
        
        if (in_array($file_ext, $allowed_ext)) {
            if (move_uploaded_file($_FILES["media_file"]["tmp_name"], $target_file)) {
                
                // Simpan path relatif untuk database (tanpa ../)
                // Format: assets/uploads/namafile.jpg
                $db_url = "assets/uploads/" . $file_name;

                try {
                    $sql = "INSERT INTO media_assets (type, url, caption, deskripsi, event_name, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$type, $db_url, $caption, $deskripsi, $event_name]);
                    
                    header("Location: galeri.php?status=success");
                    exit();
                } catch (PDOException $e) {
                    echo "Database Error: " . $e->getMessage();
                }
            } else {
                echo "Gagal mengupload file ke server (Permission Denied).";
            }
        } else {
            echo "Format file tidak didukung. Hanya JPG, PNG, GIF, MP4.";
        }
    } else {
        echo "Harap pilih file terlebih dahulu.";
    }
}

// --- HANDLE DELETE ---
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = $_GET['id'];

    // 1. Ambil info file dulu untuk dihapus dari folder
    $stmt = $pdo->prepare("SELECT url FROM media_assets WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch();

    if ($data) {
        // Cek apakah ini file lokal (bukan link http)
        if (!filter_var($data['url'], FILTER_VALIDATE_URL)) {
            // Tambahkan ../ untuk menghapus dari posisi admin
            $file_path = "../" . $data['url'];
            if (file_exists($file_path)) {
                unlink($file_path); // Hapus file fisik
            }
        }

        // 2. Hapus dari database
        $delStmt = $pdo->prepare("DELETE FROM media_assets WHERE id = ?");
        $delStmt->execute([$id]);
    }

    header("Location: galeri.php?status=deleted");
    exit();
}
?>