<?php
// admin/process_berita.php
session_start();
require_once '../config/db.php';

// Fungsi helper untuk membuat slug URL-friendly
function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    return strtolower($text);
}

// Konfigurasi Path
$target_dir = "../assets/images/news/";
$db_path_prefix = "assets/images/news/";

// Cek dan buat folder jika belum ada
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// ==========================================
// LOGIKA DELETE
// ==========================================
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = $_GET['id'];

    // Ambil info gambar lama dulu untuk dihapus
    $stmt = $pdo->prepare("SELECT cover_image FROM news WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if ($row && !empty($row['cover_image'])) {
        $file_path = "../" . $row['cover_image'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    $stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
    $stmt->execute([$id]);
    
    // Auto refresh materialized views setelah delete
    autoRefreshAfterCRUD($pdo, 'news');
    
    header("Location: berita.php");
    exit();
}

// ==========================================
// LOGIKA SAVE (INSERT / UPDATE)
// ==========================================
if (isset($_POST['save'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $slug = slugify($title);
    $category = $_POST['category'];
    $summary = $_POST['summary'];
    $content = $_POST['content'];
    $status = $_POST['status'];
    
    // PERBAIKAN: Tangkap tanggal dari form & gabungkan dengan jam sekarang
    // Menggabungkan jam sekarang penting agar tidak terset ke jam 00:00 (rawan bug timezone)
    $input_date = $_POST['created_at']; 
    $current_time = date('H:i:s'); 
    $final_created_at = $input_date . ' ' . $current_time;

    // --- PROSES UPLOAD GAMBAR ---
    $cover_image = null;
    
    if (!empty($_FILES['cover_image']['name'])) {
        $file_name_original = basename($_FILES["cover_image"]["name"]);
        $file_ext = strtolower(pathinfo($file_name_original, PATHINFO_EXTENSION));
        
        $new_name = time() . '_' . rand(100, 999) . '.' . $file_ext;
        $target_file = $target_dir . $new_name;
        
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($file_ext, $allowed)) {
            if (move_uploaded_file($_FILES["cover_image"]["tmp_name"], $target_file)) {
                $cover_image = $db_path_prefix . $new_name;
            } else {
                echo "<script>alert('Gagal upload gambar.'); window.history.back();</script>";
                exit;
            }
        } else {
            echo "<script>alert('Format file harus JPG, PNG, atau WEBP'); window.history.back();</script>";
            exit;
        }
    }

    try {
        if ($id) {
            // === UPDATE ===
            if ($cover_image) {
                // Hapus gambar lama jika ada upload baru
                $stmt = $pdo->prepare("SELECT cover_image FROM news WHERE id = ?");
                $stmt->execute([$id]);
                $old_img = $stmt->fetchColumn();
                if ($old_img && file_exists("../" . $old_img) && strpos($old_img, 'placeholder') === false) {
                    unlink("../" . $old_img);
                }

                // Update query DENGAN gambar, kategori, dan created_at
                $sql = "UPDATE news SET title=?, slug=?, category=?, summary=?, content=?, status=?, cover_image=?, created_at=?, updated_at=NOW() WHERE id=?";
                $pdo->prepare($sql)->execute([$title, $slug, $category, $summary, $content, $status, $cover_image, $final_created_at, $id]);
            } else {
                // Update query TANPA ganti gambar (tapi kategori & created_at update)
                $sql = "UPDATE news SET title=?, slug=?, category=?, summary=?, content=?, status=?, created_at=?, updated_at=NOW() WHERE id=?";
                $pdo->prepare($sql)->execute([$title, $slug, $category, $summary, $content, $status, $final_created_at, $id]);
            }

        } else {
            // === INSERT BARU ===
            $img_path = $cover_image ?? 'assets/images/logo-placeholder.png';
            
            // Insert query DENGAN kategori & created_at manual
            // Kita pakai $final_created_at menggantikan NOW() di kolom created_at
            $sql = "INSERT INTO news (title, slug, category, summary, content, status, cover_image, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            $pdo->prepare($sql)->execute([$title, $slug, $category, $summary, $content, $status, $img_path, $final_created_at]);
        }
        
        // Auto refresh materialized views setelah insert/update
        autoRefreshAfterCRUD($pdo, 'news');
        
        header("Location: berita.php");
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>