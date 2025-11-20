<?php
session_start();
require_once '../config/db.php';

function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    return strtolower($text);
}

// DELETE
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    header("Location: berita.php");
    exit();
}

// SAVE
if (isset($_POST['save'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $slug = slugify($title);
    $summary = $_POST['summary'];
    $content = $_POST['content'];
    $status = $_POST['status'];

    // Upload Logic
    $cover_image = null;
    if (!empty($_FILES['cover_image']['name'])) {
        $target_dir = "../assets/uploads/"; 
        $file_name = time() . '_' . basename($_FILES["cover_image"]["name"]);
        $target_file = $target_dir . $file_name;
        
        if (move_uploaded_file($_FILES["cover_image"]["tmp_name"], $target_file)) {
            // Simpan path relatif untuk DB (misal: assets/uploads/foto.jpg)
            $cover_image = "assets/uploads/" . $file_name; 
        }
    }

    try {
        if ($id) {
            // UPDATE
            $sql = "UPDATE news SET title=?, slug=?, summary=?, content=?, status=?, updated_at=NOW()";
            $params = [$title, $slug, $summary, $content, $status];
            
            if ($cover_image) {
                $sql .= ", cover_image=?";
                $params[] = $cover_image;
            }
            $sql .= " WHERE id=?";
            $params[] = $id;
            
            $pdo->prepare($sql)->execute($params);
        } else {
            // INSERT
            $img_path = $cover_image ?? 'assets/images/placeholder.jpg';
            $sql = "INSERT INTO news (title, slug, summary, content, status, cover_image, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())";
            $pdo->prepare($sql)->execute([$title, $slug, $summary, $content, $status, $img_path]);
        }
        header("Location: berita.php");
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>