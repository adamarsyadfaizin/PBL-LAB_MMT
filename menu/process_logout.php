<?php
// menu/process_logout.php
session_start();

// [PENTING] Panggil koneksi database dulu agar fungsi writeLog bisa dipakai
// Pastikan path-nya benar "../config/db.php"
require_once "../config/db.php"; 

// 1. Catat Log Logout (Jika user memang sedang login)
if (isset($_SESSION['user_name'])) {
    $nama_user = $_SESSION['user_name'];
    
    // Panggil fungsi pencatat (pastikan $pdo tersedia dari db.php)
    if (function_exists('writeLog') && isset($pdo)) {
        writeLog($pdo, $nama_user, "Logout Berhasil");
    }
}

// 2. Hapus semua sesi PHP
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Logging out...</title>
</head>
<body>
    <script>
        // 3. Hapus status login di LocalStorage (Opsional, untuk fitur multi-tab)
        localStorage.setItem('login_status', 'logout_' + Date.now());

        // 4. Redirect user ke halaman login
        window.location.href = 'login.php';
    </script>
</body>
</html>