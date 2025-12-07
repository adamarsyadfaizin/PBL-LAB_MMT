<?php
if (!isset($_SESSION)) session_start();

// Perhatikan path include DB ini. 
// Jika file auth_check.php ada di dalam folder 'admin/components', maka:
// __DIR__ = admin/components
// /../..  = root project (PBL-LAB_MMT)
require_once __DIR__ . '/../../config/db.php'; 

// Cek apakah user login
if (!isset($_SESSION['user_id'])) {
    // PERBAIKAN: Cukup naik 1 tingkat dari folder 'admin' untuk ke folder 'menu'
    // Asumsi browser sedang membuka file di folder /admin/ (seperti index.php)
    header("Location: ../menu/login.php");
    exit();
}

// Cek Role
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$currentUser = $stmt->fetch();

if (!$currentUser || ($currentUser['role'] !== 'admin' && $currentUser['role'] !== 'editor')) {
    // PERBAIKAN: Sama, naik 1 tingkat saja
    header("Location: ../menu/login.php");
    exit();
}
?>