<?php
// Mulai session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah 'user_id' ada di session
if ( !isset($_SESSION['user_id']) ) {
    // Jika tidak ada, tendang ke halaman login
    header('Location: login.php');
    exit;
}
// Jika ada, kita aman.
// Variabel $_SESSION['user_id'] dan $_SESSION['user_name']
// bisa digunakan di halaman yang memanggil file ini.
?>