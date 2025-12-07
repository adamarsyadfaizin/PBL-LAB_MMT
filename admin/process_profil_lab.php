<?php
session_start();
require_once '../config/db.php';

$target_dir = "../assets/uploads/";
if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

// --- 1. UPDATE PROFIL UTAMA ---
if (isset($_POST['update_profile'])) {
    $visi = $_POST['visi'];
    $misi = $_POST['misi'];
    $sejarah = $_POST['sejarah'];
    
    $stmt = $pdo->query("SELECT struktur_org_path FROM lab_profile LIMIT 1");
    $oldData = $stmt->fetch();
    $struktur_path = $oldData['struktur_org_path'] ?? '';

    if (!empty($_FILES['struktur_org']['name'])) {
        $ext = strtolower(pathinfo($_FILES['struktur_org']['name'], PATHINFO_EXTENSION));
        $newName = "struktur_" . time() . "." . $ext;
        if (move_uploaded_file($_FILES['struktur_org']['tmp_name'], $target_dir . $newName)) {
            $struktur_path = "assets/uploads/" . $newName;
        }
    }

    $check = $pdo->query("SELECT count(*) FROM lab_profile")->fetchColumn();
    if ($check > 0) {
        $sql = "UPDATE lab_profile SET visi=?, misi=?, sejarah=?, struktur_org_path=?";
        $pdo->prepare($sql)->execute([$visi, $misi, $sejarah, $struktur_path]);
    } else {
        $sql = "INSERT INTO lab_profile (visi, misi, sejarah, struktur_org_path) VALUES (?, ?, ?, ?)";
        $pdo->prepare($sql)->execute([$visi, $misi, $sejarah, $struktur_path]);
    }
    
    // Pastikan fungsi writeLog ada sebelum dipanggil
    if (function_exists('writeLog')) writeLog($pdo, $_SESSION['admin_user'] ?? 'Admin', "Update Profil Lab");
    header("Location: profil_lab.php?status=success&tab=profil");
    exit();
}

// --- 2. TAMBAH ANGGOTA TIM ---
if (isset($_POST['add_member'])) {
    $name = $_POST['name'];
    $role = $_POST['role'];
    $tags = $_POST['tags'];
    
    // Ambil 5 Input Sosmed
    $linkedin = $_POST['linkedin'];
    $scholar = $_POST['scholar'];
    $instagram = $_POST['instagram'];
    $youtube = $_POST['youtube'];
    $facebook = $_POST['facebook'];
    
    $avatar_url = '';
    if (!empty($_FILES['avatar']['name'])) {
        $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        $newName = "team_" . time() . uniqid() . "." . $ext;
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target_dir . $newName)) {
            $avatar_url = "assets/uploads/" . $newName;
        }
    }

    // Query Insert Lengkap
    $sql = "INSERT INTO members (name, role, tags, linkedin_url, scholar_url, instagram, youtube, facebook, avatar_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $pdo->prepare($sql)->execute([$name, $role, $tags, $linkedin, $scholar, $instagram, $youtube, $facebook, $avatar_url]);

    if (function_exists('writeLog')) writeLog($pdo, $_SESSION['admin_user'] ?? 'Admin', "Tambah Anggota: $name");
    header("Location: profil_lab.php?status=success&tab=tim");
    exit();
}

// --- 3. EDIT ANGGOTA TIM ---
if (isset($_POST['edit_member'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $role = $_POST['role'];
    $tags = $_POST['tags'];
    
    // Ambil 5 Input Sosmed
    $linkedin = $_POST['linkedin'];
    $scholar = $_POST['scholar'];
    $instagram = $_POST['instagram'];
    $youtube = $_POST['youtube'];
    $facebook = $_POST['facebook'];

    $stmt = $pdo->prepare("SELECT avatar_url FROM members WHERE id = ?");
    $stmt->execute([$id]);
    $oldMember = $stmt->fetch();
    $avatar_url = $oldMember['avatar_url'];

    if (!empty($_FILES['avatar']['name'])) {
        $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        $newName = "team_" . time() . uniqid() . "." . $ext;
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target_dir . $newName)) {
            if (!empty($avatar_url) && file_exists("../" . $avatar_url)) unlink("../" . $avatar_url);
            $avatar_url = "assets/uploads/" . $newName;
        }
    }

    // Query Update Lengkap
    $sql = "UPDATE members SET name=?, role=?, tags=?, linkedin_url=?, scholar_url=?, instagram=?, youtube=?, facebook=?, avatar_url=? WHERE id=?";
    $pdo->prepare($sql)->execute([$name, $role, $tags, $linkedin, $scholar, $instagram, $youtube, $facebook, $avatar_url, $id]);

    if (function_exists('writeLog')) writeLog($pdo, $_SESSION['admin_user'] ?? 'Admin', "Edit Anggota: $name");
    header("Location: profil_lab.php?status=success&tab=tim");
    exit();
}

// --- 4. HAPUS ANGGOTA TIM ---
if (isset($_GET['delete_member'])) {
    $id = $_GET['delete_member'];
    
    $stmt = $pdo->prepare("SELECT avatar_url, name FROM members WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    
    if ($row) {
        if (!empty($row['avatar_url']) && file_exists("../" . $row['avatar_url'])) unlink("../" . $row['avatar_url']);
        $pdo->prepare("DELETE FROM members WHERE id = ?")->execute([$id]);
        if (function_exists('writeLog')) writeLog($pdo, $_SESSION['admin_user'] ?? 'Admin', "Hapus Anggota: " . $row['name']);
    }

    header("Location: profil_lab.php?status=deleted&tab=tim");
    exit();
}
?>