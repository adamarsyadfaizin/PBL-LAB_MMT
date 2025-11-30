<?php
session_start();
require_once '../config/db.php';

if (isset($_POST['save'])) {
    $id = $_POST['id'];
    $target_dir = "../assets/uploads/";
    
    // Ambil data lama
    $stmt = $pdo->prepare("SELECT * FROM lab_profile WHERE id = ?");
    $stmt->execute([$id]);
    $oldData = $stmt->fetch();

    // Daftar path gambar
    $paths = [
        'logo'    => $oldData['logo_path'],
        'hero'    => $oldData['hero_image_path'],
        'about'   => $oldData['about_hero_image'],
        'news'    => $oldData['news_hero_image'],
        'project' => $oldData['project_hero_image'],
        'gallery' => $oldData['gallery_hero_image'],
        'contact' => $oldData['contact_hero_image']
    ];

    // Fungsi Upload
    function handleUpload($inputName, $prefix, $currentPath, $dir) {
        if (!empty($_FILES[$inputName]['name'])) {
            $newName = $prefix . "_" . time() . "_" . basename($_FILES[$inputName]['name']);
            if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $dir . $newName)) {
                return "assets/uploads/" . $newName;
            }
        }
        return $currentPath;
    }

    // Eksekusi Upload
    $paths['logo']    = handleUpload('logo', 'logo', $paths['logo'], $target_dir);
    $paths['hero']    = handleUpload('hero_image', 'home', $paths['hero'], $target_dir);
    $paths['about']   = handleUpload('about_hero', 'about', $paths['about'], $target_dir);
    $paths['news']    = handleUpload('news_hero', 'news', $paths['news'], $target_dir);
    $paths['project'] = handleUpload('project_hero', 'proj', $paths['project'], $target_dir);
    $paths['gallery'] = handleUpload('gallery_hero', 'gal', $paths['gallery'], $target_dir);
    $paths['contact'] = handleUpload('contact_hero', 'cont', $paths['contact'], $target_dir);

    // UPDATE DATABASE
    $sql = "UPDATE lab_profile SET 
            logo_path = ?, 
            
            /* Gambar */
            hero_image_path = ?, 
            about_hero_image = ?,
            news_hero_image = ?,
            project_hero_image = ?,
            gallery_hero_image = ?,
            contact_hero_image = ?,
            
            /* Judul & Konten */
            hero_title = ?, 
            hero_description = ?,
            about_title = ?,
            news_title = ?,
            project_title = ?,
            gallery_title = ?,
            contact_title = ?,

            /* Footer */
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
            $paths['logo'],
            
            $paths['hero'],
            $paths['about'],
            $paths['news'],
            $paths['project'],
            $paths['gallery'],
            $paths['contact'],
            
            $_POST['hero_title'],
            $_POST['hero_description'],
            $_POST['about_title'],
            $_POST['news_title'],
            $_POST['project_title'],
            $_POST['gallery_title'],
            $_POST['contact_title'],

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
        
        echo "<script>alert('Pengaturan Berhasil Disimpan!'); window.location='pengaturan.php';</script>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>