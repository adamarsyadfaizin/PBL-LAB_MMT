<?php
// 1. Panggil Penjaga Gerbang
require_once 'auth_check.php';
// (Jika belum login, user sudah ditendang ke login.php)

// 2. Panggil Database
require_once '../config/db.php';

// 3. Ambil data untuk ringkasan
$total_projects = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
$total_news = $pdo->query("SELECT COUNT(*) FROM news")->fetchColumn();
$total_members = $pdo->query("SELECT COUNT(*) FROM members")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard Admin</title>
    </head>
<body>
    
    <header>
        <h1>Dashboard Admin</h1>
        <p>
            Selamat datang, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!
        </p>
        <nav>
            <a href="dashboard.php">Dashboard</a> |
            <a href="project_list.php">Manajemen Proyek</a> |
            <a href="news_list.php">Manajemen Berita</a> |
            <a href="member_list.php">Manajemen Tim</a> |
            <a href="media.php">Manajemen Media</a> |
            <a href="logout.php" style="color: red;">Logout</a>
        </nav>
    </header>

    <main>
        <h2>Ringkasan Konten</h2>
        <ul>
            <li>Total Proyek: <?php echo $total_projects; ?></li>
            <li>Total Berita: <?php echo $total_news; ?></li>
            <li>Total Anggota: <?php echo $total_members; ?></li>
        </ul>
    </main>

</body>
</html>