<?php
// menu/login.php
require_once __DIR__ . '/../config/auth.php';
startSecureSession();
require_once __DIR__ . '/../config/db.php';

$error = "";

// -------------------------
// GENERATE / REFRESH CAPTCHA
// -------------------------
$captchaChars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

if (isset($_GET['refresh']) && $_GET['refresh'] == '1') {
    $_SESSION['captcha'] = substr(str_shuffle($captchaChars), 0, 6);

    $uri = strtok($_SERVER['REQUEST_URI'], '?');
    header("Location: $uri");
    exit;
}

if (empty($_SESSION['captcha'])) {
    $_SESSION['captcha'] = substr(str_shuffle($captchaChars), 0, 6);
}

// -------------------------
// PROSES LOGIN
// -------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $usernameOrEmail = trim($_POST['username'] ?? '');
    $password        = (string) ($_POST['password'] ?? '');
    $captchaInput    = trim($_POST['captcha'] ?? '');

    // Pembatasan percobaan login: maksimal 5 kali per 5 menit
    $attempts = $_SESSION['login_attempts'] ?? ['count' => 0, 'time' => 0];
    if ($attempts['count'] >= 5 && (time() - $attempts['time']) < 300) {
        $sisa = 300 - (time() - $attempts['time']);
        $error = "Terlalu banyak percobaan. Coba lagi dalam " . ceil($sisa / 60) . " menit.";
    } elseif (!csrfValid()) {
        $error = "Sesi formulir kedaluwarsa. Muat ulang halaman lalu coba lagi.";
    } elseif ($usernameOrEmail === '' || $password === '') {
        $error = "Isi username/email dan password.";
    } elseif (strtoupper($captchaInput) !== strtoupper($_SESSION['captcha'])) {
        $error = "Kode CAPTCHA salah!";
        $_SESSION['captcha'] = substr(str_shuffle($captchaChars), 0, 6);
    } else {
        $sql = "SELECT id, name, email, password, role 
                FROM users 
                WHERE name = :ue OR email = :ue 
                LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['ue' => $usernameOrEmail]);
        $user = $stmt->fetch();

        $loginOk = false;

        if ($user) {
            $stored = (string) $user['password'];

            if (password_verify($password, $stored)) {
                $loginOk = true;
            } elseif (hash_equals($stored, $password)) {
                // Data lama masih berupa teks biasa: diterima sekali,
                // lalu langsung diubah menjadi hash bcrypt.
                $loginOk = true;
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")
                    ->execute([$newHash, $user['id']]);
            }
        }

        if ($loginOk) {
            unset($_SESSION['login_attempts']);

            // Cegah session fixation
            session_regenerate_id(true);

            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];

            if (function_exists('writeLog')) {
                writeLog($pdo, $user['name'], "Login Berhasil");
            }

            $target = $_SESSION['redirect_after_login'] ?? null;
            unset($_SESSION['redirect_after_login']);

            if ($user['role'] === 'admin' || $user['role'] === 'editor') {
                header("Location: ../admin/index.php");
            } elseif ($target && strpos($target, '//') === false && strpos($target, 'login.php') === false) {
                header("Location: " . $target);
            } else {
                header("Location: ../beranda.php");
            }
            exit;

        } else {
            $_SESSION['login_attempts'] = [
                'count' => ($attempts['count'] ?? 0) + 1,
                'time'  => time(),
            ];
            $error = $user ? "Password salah!" : "Akun tidak ditemukan.";
            $_SESSION['captcha'] = substr(str_shuffle($captchaChars), 0, 6);
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
            padding:6px 8px;
            background:#fff;
            border-radius:6px;
            border:1px solid #ddd;
            line-height:0;
        }
        .captcha-box img { height: 44px; width: auto; display: block; }
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
            <?= csrfField() ?>

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
                    <div class="captcha-box">
                        <img src="captcha.php?t=<?= time() ?>" alt="Kode verifikasi" width="160" height="44">
                    </div>
                    <a href="?refresh=1" style="margin-left:10px;">&#x21bb; Ganti kode</a>
                </div>

                <input type="text" id="captcha" name="captcha" 
                    placeholder="Ketik kode di atas" maxlength="6" required
                    style="margin-top:8px;">
            </div>

            <button type="submit" class="btn-login">Login</button>
        </form>

        <footer class="login-footer">
            <a href="../beranda.php">&larr; Kembali ke Beranda</a>
        </footer>
    </main>
</body>
</html>
