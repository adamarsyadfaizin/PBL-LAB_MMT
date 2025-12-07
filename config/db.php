<?php
$host = 'localhost';
$port = '5432';
$dbname = 'db_pbl';
$user = 'postgres';
$password = 'raihan'; 

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Koneksi ke database gagal: " . $e->getMessage());
}

// --- FUNGSI LOG (DIBUNGKUS IF BIAR GAK ERROR REDECLARE) ---
if (!function_exists('writeLog')) {
    
    function writeLog($pdo, $username, $action) {
        // Ambil data user
        $ip = $_SERVER['REMOTE_ADDR'];
        $device = $_SERVER['HTTP_USER_AGENT'];

        try {
            // SQL Query (Syntax PostgreSQL)
            $sql = "INSERT INTO activity_logs (username, action, ip_address, device_info) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$username, $action, $ip, $device]);
        } catch (PDOException $e) {
            // Biarkan kosong agar error log tidak mengganggu user
        }
    }
    
}
?>