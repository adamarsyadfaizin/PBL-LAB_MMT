<?php
session_start();
require_once '../config/db.php';

if (isset($_POST['save'])) {
    $id = $_POST['id'];
    
    // Ambil data lama dulu (untuk jaga-jaga jika gambar tidak diganti)
    $stmt = $pdo->prepare("SELECT logo_path, hero_image_path FROM lab_profile WHERE id = ?");
    $stmt->execute([$id]);
    $oldData = $stmt->fetch();

    $logo_path = $oldData['logo_path'];
    $hero_path = $oldData['hero_image_path'];
    $target_dir = "../assets/uploads/";

    // 1. HANDLE UPLOAD LOGO
    if (!empty($_FILES['logo']['name'])) {
        $file_name = "logo_" . time() . "_" . basename($_FILES['logo']['name']);
        if (move_uploaded_file($_FILES['logo']['tmp_name'], $target_dir . $file_name)) {
            $logo_path = "assets/uploads/" . $file_name;
        }
    }

    // 2. HANDLE UPLOAD HERO BG
    if (!empty($_FILES['hero_image']['name'])) {
        $file_name = "hero_" . time() . "_" . basename($_FILES['hero_image']['name']);
        if (move_uploaded_file($_FILES['hero_image']['tmp_name'], $target_dir . $file_name)) {
            $hero_path = "assets/uploads/" . $file_name;
        }
    }

    // 3. UPDATE DATABASE (LENGKAP)
    $sql = "UPDATE lab_profile SET 
            logo_path = ?, 
            hero_image_path = ?, 
            hero_title = ?, 
            hero_description = ?,
            alamat_lab = ?,
            email_lab = ?,
            telepon_lab = ?,
            fb_link = ?,
            x_link = ?,
            ig_link = ?,
            yt_link = ?,
            linkedin = ?
            WHERE id = ?";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $logo_path,
            $hero_path,
            $_POST['hero_title'],
            $_POST['hero_description'],
            $_POST['alamat_lab'],
            $_POST['email_lab'],
            $_POST['telepon_lab'],
            $_POST['fb_link'],
            $_POST['x_link'],
            $_POST['ig_link'],
            $_POST['yt_link'],
            $_POST['linkedin'],
            $id
        ]);
        
        echo "<script>alert('Pengaturan berhasil disimpan!'); window.location='pengaturan.php';</script>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>