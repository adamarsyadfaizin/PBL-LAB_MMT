<?php
// 1. Mulai Session (HARUS PALING ATAS)
session_start();

// 2. Cek jika sudah login, lempar ke dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

// 3. Panggil koneksi database
// (Perhatikan path-nya, kita ada di dalam folder 'admin')
require_once '../config/db.php';

$error_message = '';

// 4. Cek jika form disubmit (method POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // 5. Ambil user berdasarkan email
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // 6. Verifikasi user DAN password
        // password_verify() membandingkan password 'admin123' dengan hash
        if ($user && password_verify($password, $user['password_hash'])) {
            
            // 7. Sukses! Simpan data user ke SESSION
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            
            // 8. Arahkan ke dashboard
            header('Location: dashboard.php');
            exit;

        } else {
            // Gagal
            $error_message = "Email atau password salah.";
        }

    } catch (PDOException $e) {
        $error_message = "Koneksi database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin</title>
    <style>
        body { font-family: Arial, sans-serif; display: grid; place-items: center; min-height: 100vh; background: #f4f4f4; }
        .login-box { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input { width: 300px; padding: 8px; }
        .error { color: red; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Admin Login</h2>
        <?php if ($error_message): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>
        
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
    </div>
</body>
</html>