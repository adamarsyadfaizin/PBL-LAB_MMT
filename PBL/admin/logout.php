<?php
session_start();

// Hancurkan semua data session
session_unset();
session_destroy();

// Arahkan kembali ke login.php
header('Location: login.php');
exit;
?>