<?php
// menu/process_logout.php
require_once __DIR__ . '/../config/auth.php';
startSecureSession();

// [PENTING] Panggil koneksi database dulu agar fungsi writeLog bisa dipakai
require_once __DIR__ . '/../config/db.php';

// 1. Catat Log Logout (Jika user memang sedang login)
if (isset($_SESSION['user_name'])) {
    $nama_user = $_SESSION['user_name'];

    if (function_exists('writeLog') && isset($pdo)) {
        writeLog($pdo, $nama_user, "Logout Berhasil");
    }
}

// 2. Hapus semua sesi PHP
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
session_destroy();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keluar...</title>
</head>
<body>
    <script>
        // 3. Hapus status login di LocalStorage (fitur multi-tab)
        localStorage.setItem('login_status', 'logout_' + Date.now());

        // 4. Redirect user ke halaman login
        window.location.href = 'login.php';
    </script>
    <noscript>
        <p>Anda telah keluar. <a href="login.php">Masuk kembali</a>.</p>
    </noscript>
</body>
</html>
