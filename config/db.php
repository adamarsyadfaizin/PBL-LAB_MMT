<?php
$host = 'localhost';
$port = '5432';
$dbname = 'db_labmmt';
$user = 'postgres';
$password = 'sandy1'; // Pastikan password ini benar sesuai settingan pgAdmin kamu

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

    $pdo = new PDO($dsn, $user, $password);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Koneksi ke database gagal: " . $e->getMessage());
}
?>