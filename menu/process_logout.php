<?php
// menu/process_logout.php
session_start();

// 1. Hapus semua sesi PHP
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
        // 2. KIRIM SINYAL KE TAB LAIN
        // Kita set item di LocalStorage. Tab lain akan mendeteksi perubahan ini.
        // Kita pakai timestamp (Date.now()) agar nilainya selalu berubah unik.
        localStorage.setItem('login_status', 'logout_' + Date.now());

        // 3. Redirect user ke halaman login
        window.location.href = 'login.php';
    </script>
</body>
</html>