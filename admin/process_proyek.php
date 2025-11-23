<?php
session_start();
require_once '../config/db.php';

// Fungsi membuat slug URL (contoh: Judul Proyek -> judul-proyek)
function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    return strtolower($text);
}

// --- HANDLE DELETE (HAPUS) ---
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = $_GET['id'];
    
    // Opsional: Hapus gambar fisik dulu jika perlu
    // $stmt = $pdo->prepare("SELECT cover_image FROM projects WHERE id = ?");
    // $stmt->execute([$id]);
    // $img = $stmt->fetchColumn();
    // if ($img && file_exists("../" . $img)) unlink("../" . $img);

    $pdo->prepare("DELETE FROM projects WHERE id = ?")->execute([$id]);
    header("Location: proyek.php?status=deleted");
    exit();
}

// --- HANDLE SAVE (INSERT / UPDATE) ---
if (isset($_POST['save'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;
    $year = !empty($_POST['year']) ? $_POST['year'] : date('Y');
    $description = $_POST['description'];
    $demo_url = $_POST['demo_url'];
    $status = $_POST['status'];
    
    // Generate slug baru dari judul
    $slug = slugify($title);

    // Handle Upload Gambar
    $cover_image = null;
    if (!empty($_FILES['cover_image']['name'])) {
        $target_dir = "../assets/uploads/";
        
        // Buat folder jika belum ada
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        
        $file_name = time() . '_' . basename($_FILES["cover_image"]["name"]);
        $target_file = $target_dir . $file_name;
        
        if (move_uploaded_file($_FILES["cover_image"]["tmp_name"], $target_file)) {
            // Simpan path relatif (assets/uploads/...) tanpa ../
            $cover_image = "assets/uploads/" . $file_name; 
        }
    }

    try {
        if ($id) {
            // --- UPDATE DATA LAMA ---
            $sql = "UPDATE projects SET 
                    title = ?, 
                    slug = ?, 
                    category_id = ?, 
                    year = ?, 
                    description = ?, 
                    summary = ?, 
                    demo_url = ?, 
                    status = ?, 
                    updated_at = NOW()";
            
            $params = [
                $title, 
                $slug, 
                $category_id, 
                $year, 
                $description, 
                substr($description, 0, 150), // Summary otomatis dari 150 huruf pertama deskripsi
                $demo_url, 
                $status
            ];
            
            // Jika ada gambar baru, update kolom gambar
            if ($cover_image) {
                $sql .= ", cover_image = ?";
                $params[] = $cover_image;
            }
            
            $sql .= " WHERE id = ?";
            $params[] = $id;
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

        } else {
            // --- INSERT DATA BARU ---
            // Jika gambar tidak diupload, pakai placeholder default
            $img_path = $cover_image ?? 'assets/images/placeholder-project.jpg';
            $summary = substr($description, 0, 150); // Buat summary otomatis

            $sql = "INSERT INTO projects (title, slug, category_id, year, description, summary, demo_url, cover_image, status, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $title, 
                $slug, 
                $category_id, 
                $year, 
                $description, 
                $summary, 
                $demo_url, 
                $img_path, 
                $status
            ]);
        }
        
        header("Location: proyek.php?status=success");
        exit();

    } catch (PDOException $e) {
        die("Error Database: " . $e->getMessage());
    }
}
?>