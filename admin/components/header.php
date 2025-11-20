<?php require_once 'auth_check.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - PBL MMT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>

<nav class="sidebar">
    <h2>ADMIN PANEL</h2>
    <div class="nav-links">
        <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
            <i class="fas fa-chart-line"></i> Dashboard
        </a>
        <a href="berita.php" class="<?= strpos($_SERVER['PHP_SELF'], 'berita') !== false ? 'active' : '' ?>">
            <i class="fas fa-newspaper"></i> Berita & Artikel
        </a>
        <a href="proyek.php" class="<?= strpos($_SERVER['PHP_SELF'], 'proyek') !== false ? 'active' : '' ?>">
            <i class="fas fa-project-diagram"></i> Proyek
        </a>
        <a href="galeri.php" class="<?= strpos($_SERVER['PHP_SELF'], 'galeri') !== false ? 'active' : '' ?>">
            <i class="fas fa-images"></i> Galeri
        </a>
        <a href="pesan.php" class="<?= strpos($_SERVER['PHP_SELF'], 'pesan') !== false ? 'active' : '' ?>">
            <i class="fas fa-envelope"></i> Pesan Masuk
        </a>
        
        <a href="../menu/process_logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</nav>

<main class="main-content">