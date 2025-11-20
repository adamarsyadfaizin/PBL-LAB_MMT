<?php
session_start();
require_once '../config/db.php'; // Pastikan path ini benar

// HANDLE UPLOAD
if (isset($_POST['upload'])) {
    $caption = $_POST['caption'];
    $type = $_POST['type'];
    $deskripsi = $_POST['deskripsi'];

    // Cek apakah ada file yang dipilih
    if (!empty($_FILES['media_file']['name'])) {
        // Path folder tujuan (mundur 1 langkah dari admin ke root, lalu masuk assets/uploads)
        $target_dir = "../assets/uploads/";
        
        // Buat folder jika belum ada
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_ext = strtolower(pathinfo($_FILES["media_file"]["name"], PATHINFO_EXTENSION));
        // Generate nama unik agar tidak bentrok
        $file_name = time() . '_' . uniqid() . '.' . $file_ext;
        $target_file = $target_dir . $file_name;

        // Validasi ekstensi
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'mp4'];
        
        if (in_array($file_ext, $allowed_ext)) {
            if (move_uploaded_file($_FILES["media_file"]["tmp_name"], $target_file)) {
                
                // Simpan path untuk database (tanpa ../)
                $db_url = "assets/uploads/" . $file_name;

                try {
                    $sql = "INSERT INTO media_assets (type, url, caption, deskripsi, created_at) VALUES (?, ?, ?, ?, NOW())";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$type, $db_url, $caption, $deskripsi]);
                    
                    // Redirect kembali ke galeri dengan sukses
                    header("Location: galeri.php?status=success");
                    exit();
                } catch (PDOException $e) {
                    echo "Database Error: " . $e->getMessage();
                }
            } else {
                echo "Gagal mengupload file ke server.";
            }
        } else {
            echo "Format file tidak didukung. Hanya JPG, PNG, GIF, atau MP4.";
        }
    } else {
        echo "Harap pilih file terlebih dahulu.";
    }
}

// HANDLE DELETE
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = $_GET['id'];

    // Ambil info file dulu untuk dihapus dari folder
    $stmt = $pdo->prepare("SELECT url FROM media_assets WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch();

    if ($data) {
        // Hapus file fisik jika ada di folder lokal
        // Cek apakah url tidak mengandung http/https (berarti file lokal)
        if (!filter_var($data['url'], FILTER_VALIDATE_URL)) {
            $file_path = "../" . $data['url'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }

        // Hapus dari database
        $delStmt = $pdo->prepare("DELETE FROM media_assets WHERE id = ?");
        $delStmt->execute([$id]);
    }

    header("Location: galeri.php?status=deleted");
    exit();
}
?>