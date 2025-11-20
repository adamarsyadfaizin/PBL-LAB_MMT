<?php
if (!isset($_SESSION)) session_start();
require_once __DIR__ . '/../../config/db.php'; // Path ke config/db.php

// Cek apakah user login DAN role-nya bukan sekadar 'user' biasa (sesuaikan dengan tabel users)
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../menu/login.php");
    exit();
}

// Opsi: Ambil data user lagi untuk memastikan role (karena di session bisa saja dimanipulasi)
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$currentUser = $stmt->fetch();

if (!$currentUser || ($currentUser['role'] !== 'admin' && $currentUser['role'] !== 'editor')) {
    // Jika bukan admin/editor, tendang keluar
    header("Location: ../../menu/login.php");
    exit();
}
?>