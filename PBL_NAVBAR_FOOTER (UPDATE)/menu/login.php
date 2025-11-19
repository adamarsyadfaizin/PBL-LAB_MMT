<?php
// menu/login.php
session_start();
require_once "../config/db.php"; // path sudah benar untuk file di folder menu/

$error = "";

// -------------------------
// GENERATE / REFRESH CAPTCHA
// -------------------------
if (isset($_GET['refresh']) && $_GET['refresh'] == '1') {
    $_SESSION['captcha'] = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 6);

    $uri = strtok($_SERVER['REQUEST_URI'], '?');
    header("Location: $uri");
    exit;
}

if (empty($_SESSION['captcha'])) {
    $_SESSION['captcha'] = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 6);
}

// -------------------------
// PROSES LOGIN
// -------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $usernameOrEmail = trim($_POST['username'] ?? '');
    $password        = trim($_POST['password'] ?? '');
    $captchaInput    = trim($_POST['captcha'] ?? '');

    if ($usernameOrEmail === '' || $password === '') {
        $error = "Isi username/email dan password.";
    } 
    elseif (strtoupper($captchaInput) !== strtoupper($_SESSION['captcha'])) {
        $error = "Kode CAPTCHA salah!";
        $_SESSION['captcha'] = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 6);
    } 
    else {
        // QUERY TANPA PASSWORD HASH
        $sql = "SELECT id, name, email, password, role 
                FROM users 
                WHERE name = :ue OR email = :ue 
                LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['ue' => $usernameOrEmail]);
        $user = $stmt->fetch();

        if ($user) {
            // VERIFIKASI PASSWORD PLAIN TEXT
            if ($password === $user['password']) {

                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];

                // Remember me (optional)
                if (!empty($_POST['remember'])) {
                    setcookie('remember_me', base64_encode($user['id']), time() + (30*24*60*60), "/");
                }

                header("Location: ../beranda.php");
                exit;

            } else {
                $error = "Password salah!";
                $_SESSION['captcha'] = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 6);
            }
        } else {
            $error = "Akun tidak ditemukan.";
            $_SESSION['captcha'] = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 6);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Laboratorium MMT POLINEMA</title>

    <link rel="stylesheet" href="assets/login/css/style-login.css">

    <style>
        .captcha-box {
            display:inline-block;
            padding:10px 14px;
            font-family: 'Courier New', monospace;
            font-weight:700;
            letter-spacing:3px;
            background:#f3f3f3;
            border-radius:6px;
            border:1px solid #ddd;
        }
        .error-message-inline { 
            color:#c00; 
            text-align:center; 
            margin-bottom:10px; 
            font-weight:600;
        }
    </style>
</head>
<body>
    <main class="login-card">
        <header class="login-header">
            <img src="../assets/images/logo-placeholder.png" alt="Logo Polinema">
            <h2>Selamat Datang</h2>
        </header>

        <?php if (!empty($error)): ?>
            <div class="error-message-inline"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form class="login-form" action="" method="POST" autocomplete="off">

            <div class="form-group">
                <label for="username">Username atau Email</label>
                <input type="text" id="username" name="username" required 
                    value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="captcha">Verifikasi Keamanan</label>

                <div class="captcha-wrapper">
                    <div class="captcha-box"><?= htmlspecialchars($_SESSION['captcha']) ?></div>
                    <a href="?refresh=1" style="margin-left:10px;">&#x21bb; Refresh</a>
                </div>

                <input type="text" id="captcha" name="captcha" 
                    placeholder="Ketik kode di atas" maxlength="6" required
                    style="margin-top:8px;">
            </div>

            <div class="form-options">
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Ingat saya</label>
                </div>
            </div>

            <button type="submit" class="btn-login">Login</button>
        </form>

        <footer class="login-footer">
            <a href="../beranda.php">&larr; Kembali ke Beranda</a>
        </footer>
    </main>
</body>
</html>
