<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Laboratorium MMT POLINEMA</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/login/css/style-login.css">
</head>
<body>

    <main class="login-card">
        
        <header class="login-header">
            <img src="../assets/images/logo-placeholder.png" alt="Logo Polinema">
            <h2>Selamat Datang</h2>
        </header>

        <form class="login-form" action="#" method="POST" id="loginForm">
            
            <div class="form-group">
                <label for="role">Pilih Peran Anda</label>
                <select id="role" name="role" required>
                    <option value="" disabled selected>-- Pilih Peran --</option>
                    <option value="admin">Admin</option>
                    <option value="anggota">Anggota</option>
                    <!--<option value="kontributor">Kontributor</option>-->
                </select>
            </div>
            
            <div class="form-group">
                <label for="username">Username atau Email</label>
                <input type="text" id="username" name="username" required aria-label="Username atau Email">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required aria-label="Password">
            </div>
            
            <div class="form-group">
                <label for="captcha">Verifikasi Keamanan</label>
                <div class="captcha-wrapper">
                    <div class="captcha-container">
                        <div class="captcha-box" id="captchaBox">
                            <span class="captcha-text" id="captchaText"></span>
                        </div>
                        <div class="captcha-controls">
                            <button type="button" class="refresh-captcha" id="refreshCaptcha" aria-label="Refresh CAPTCHA">
                                &#x21bb; Refresh
                            </button>
                            <span>Tidak terbaca?</span>
                        </div>
                    </div>
                    <input type="text" id="captcha" name="captcha" placeholder="Ketik kode di atas" required class="captcha-input" maxlength="6">
                </div>
                <div class="error-message" id="captchaError">Kode CAPTCHA tidak sesuai!</div>
            </div>
            
            <div class="form-options">
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Ingat saya</label>
                </div>
                <a href="#" class="forgot-password">Lupa password?</a>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn-login" id="loginButton">Login</button>
            </div>
            
        </form>

        <footer class="login-footer">
            <a href="../beranda.php" title="Kembali ke Halaman Utama">&larr; Kembali ke Beranda</a>
        </footer>

    </main>

    <script src="assets/login/js/script-login.js"></script>

</body>
</html>