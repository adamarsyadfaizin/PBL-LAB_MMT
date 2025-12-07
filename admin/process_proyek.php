<?php
// admin/process_proyek.php
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

$target_dir = "../assets/images/projects/"; 
$db_path_prefix = "assets/images/projects/"; 

if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// DELETE
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT cover_image FROM projects WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    
    if ($row && !empty($row['cover_image'])) {
        $file_path = "../" . $row['cover_image'];
        if (file_exists($file_path)) unlink($file_path);
    }
    
    $pdo->prepare("DELETE FROM projects WHERE id = ?")->execute([$id]);
    header("Location: proyek.php?status=deleted");
    exit();
}

// SAVE
if (isset($_POST['save'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $slug = slugify($title);
    $year = $_POST['year'];
    $category_id = $_POST['category_id'];
    $summary = $_POST['summary'];
    $description = $_POST['description'];
    
    // PERBAIKAN: Jika kosong, set NULL agar database bersih
    $repo_url = !empty($_POST['repo_url']) ? $_POST['repo_url'] : null;
    $demo_url = !empty($_POST['demo_url']) ? $_POST['demo_url'] : null;
    
    $status = $_POST['status'];

    if (empty($summary)) {
        $summary = substr(strip_tags($description), 0, 150);
    }

    $cover_image = null;
    if (!empty($_FILES['cover_image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION));
        $new_name = time() . '_' . rand(100, 999) . '.' . $ext;
        $target_file = $target_dir . $new_name;
        
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $target_file)) {
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
            // UPDATE
            $sql = "UPDATE projects SET title=?, slug=?, year=?, category_id=?, summary=?, description=?, repo_url=?, demo_url=?, status=?, updated_at=NOW()";
            $params = [$title, $slug, $year, $category_id, $summary, $description, $repo_url, $demo_url, $status];

            if ($cover_image) {
                $stmt = $pdo->prepare("SELECT cover_image FROM projects WHERE id = ?");
                $stmt->execute([$id]);
                $old = $stmt->fetchColumn();
                if ($old && file_exists("../" . $old) && strpos($old, 'placeholder') === false) {
                    unlink("../" . $old);
                }
                
                $sql .= ", cover_image=?";
                $params[] = $cover_image;
            }
            
            $sql .= " WHERE id=?";
            $params[] = $id;
            $pdo->prepare($sql)->execute($params);

        } else {
            // INSERT
            $img_path = $cover_image ?? 'assets/images/logo-placeholder.png';
            $sql = "INSERT INTO projects (title, slug, year, category_id, summary, description, repo_url, demo_url, status, cover_image, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            $pdo->prepare($sql)->execute([$title, $slug, $year, $category_id, $summary, $description, $repo_url, $demo_url, $status, $img_path]);
        }
        
        header("Location: proyek.php?status=success");
        exit();

    } catch (PDOException $e) {
        die("Error Database: " . $e->getMessage());
    }
}
?>