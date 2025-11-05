<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Laboratorium MMT POLINEMA</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    
    <style type="text/css">
        /* ==== 1. Variabel Global & Reset ==== */
        :root {
            --color-primary: #003b8e;
            --color-primary-darker: #002f80;
            --color-accent: #ffc700;
            --color-white: #ffffff;
            --color-bg-light: #f5f5f5;
            --color-text: #000000;
            --color-text-secondary: #666666;
            --color-border-light: #cccccc;
            --color-text-darker: #444444;
            --color-error: #dc3545;
            --color-success: #28a745;

            --font-heading: "Poppins", "Open Sans", sans-serif;
            --font-body: "Open Sans", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            font-family: var(--font-body);
        }

        body {
            font-family: var(--font-body);
            font-size: 16px;
            color: var(--color-text);
            line-height: 1.6;
            background-color: var(--color-bg-light); 
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px;
        }

        /* ==== 2. Kartu Login ==== */
        .login-card {
            max-width: 420px;
            width: 100%;
            background: var(--color-white);
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            overflow: hidden; 
            border: 1px solid #e0e0e0;
        }
        
        .login-header {
            text-align: center;
            padding: 32px 32px 24px 32px;
            border-bottom: 1px solid #f0f0f0;
        }

        .login-header img {
            height: 60px;
            width: auto;
            margin: 0 auto 16px auto;
        }

        .login-header h2 {
            font-family: var(--font-heading);
            font-size: 24px;
            color: var(--color-text);
            font-weight: 700;
        }

        /* ==== 3. Form Login ==== */
        .login-form {
            padding: 24px 32px 32px 32px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
            color: var(--color-text-secondary);
        }

        /* Style untuk input dan select */
        .form-group input[type="text"],
        .form-group input[type="password"],
        .form-group input[type="email"],
        .form-group select {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--color-border-light);
            border-radius: 6px;
            font-family: var(--font-body);
            font-size: 16px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            background-color: var(--color-white);
        }
        
        /* Style khusus dropdown (select) */
        .form-group select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2020%2020%22%20fill%3D%22%23666666%22%3E%3Cpath%20d%3D%22M5%208l5%205%205-5z%22%2F%3E%3C%2Fsvg%3E');
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 20px;
            padding-right: 40px;
        }
        
        .form-group select:invalid {
            color: var(--color-text-secondary);
        }
        
        .form-group input[type="text"]:focus,
        .form-group input[type="password"]:focus,
        .form-group input[type="email"]:focus,
        .form-group select:focus {
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(0, 59, 142, 0.15);
            outline: none;
        }

        :focus-visible {
             outline: 3px solid rgba(255, 199, 0, 0.45);
             outline-offset: 2px;
        }
        
        /* CAPTCHA Styles */
        .captcha-wrapper {
            display: flex;
            gap: 12px;
            align-items: stretch;
        }
        
        .captcha-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .captcha-box {
            flex-shrink: 0;
            width: 120px;
            height: 60px;
            background: linear-gradient(45deg, #f0f0f0, #e0e0e0);
            border: 1px solid var(--color-border-light);
            border-radius: 6px;
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
            font-size: 24px;
            letter-spacing: 2px;
            color: var(--color-text-darker);
            display: flex;
            align-items: center;
            justify-content: center;
            user-select: none;
            position: relative;
            overflow: hidden;
        }
        
        /* Efek distorsi untuk CAPTCHA */
        .captcha-text {
            transform: skew(-5deg, 2deg);
            background: linear-gradient(90deg, transparent 30%, rgba(255,255,255,0.3) 50%, transparent 70%);
            background-size: 200% 100%;
            animation: shimmer 3s infinite;
            padding: 5px 10px;
            border-radius: 3px;
        }
        
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        
        .captcha-controls {
            display: flex;
            gap: 8px;
            align-items: center;
            font-size: 12px;
        }
        
        .refresh-captcha {
            background: none;
            border: none;
            color: var(--color-primary);
            cursor: pointer;
            font-size: 12px;
            text-decoration: underline;
            padding: 0;
        }
        
        .refresh-captcha:hover {
            color: var(--color-primary-darker);
        }
        
        .captcha-input {
            flex-grow: 1;
        }
        
        .error-message {
            color: var(--color-error);
            font-size: 12px;
            margin-top: 4px;
            display: none;
        }

        /* ==== 4. Opsi Form (Ingat & Lupa) ==== */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            margin-bottom: 24px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            color: var(--color-text-secondary);
        }

        .remember-me input[type="checkbox"] {
            margin-right: 8px;
        }
        
        .remember-me label {
            margin-bottom: 0;
            font-weight: 400;
        }

        .forgot-password {
            color: var(--color-primary);
            text-decoration: none;
        }
        .forgot-password:hover {
            text-decoration: underline;
            color: var(--color-primary-darker);
        }

        /* ==== 5. Tombol & Footer ==== */
        .btn-login {
            display: block;
            width: 100%;
            padding: 14px;
            background: var(--color-primary);
            color: var(--color-white);
            border: none;
            border-radius: 6px;
            font-family: var(--font-heading);
            font-weight: 600;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .btn-login:hover {
            background: var(--color-primary-darker);
        }
        
        .btn-login:disabled {
            background: var(--color-text-secondary);
            cursor: not-allowed;
        }
        
        .login-footer {
            text-align: center;
            padding: 20px 32px;
            font-size: 14px;
            background: var(--color-bg-light);
        }
        
        .login-footer a {
            color: var(--color-text-secondary);
            text-decoration: none;
        }
        .login-footer a:hover {
            color: var(--color-primary);
            text-decoration: underline;
        }
        
        /* ==== 6. Responsive ==== */
        @media (max-width: 480px) {
            .captcha-wrapper {
                flex-direction: column;
            }
            .captcha-box {
                width: 100%;
                height: 50px;
            }
        }

    </style>
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
                    <option value="kontributor">Kontributor</option>
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

    <script>
        // Fungsi untuk generate CAPTCHA acak
        function generateCaptcha() {
            const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
            let captcha = '';
            const length = 5;
            
            for (let i = 0; i < length; i++) {
                captcha += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            
            return captcha;
        }

        // Fungsi untuk menampilkan CAPTCHA
        function displayCaptcha() {
            const captchaText = generateCaptcha();
            document.getElementById('captchaText').textContent = captchaText;
            // Simpan CAPTCHA yang benar untuk validasi
            document.getElementById('captchaBox').dataset.correctCaptcha = captchaText;
            
            // Reset error message
            document.getElementById('captchaError').style.display = 'none';
            document.getElementById('captcha').classList.remove('error');
        }

        // Fungsi validasi CAPTCHA
        function validateCaptcha() {
            const userInput = document.getElementById('captcha').value;
            const correctCaptcha = document.getElementById('captchaBox').dataset.correctCaptcha;
            const captchaError = document.getElementById('captchaError');
            
            if (userInput.toUpperCase() !== correctCaptcha.toUpperCase()) {
                captchaError.style.display = 'block';
                document.getElementById('captcha').classList.add('error');
                document.getElementById('captcha').style.borderColor = 'var(--color-error)';
                return false;
            }
            
            captchaError.style.display = 'none';
            document.getElementById('captcha').classList.remove('error');
            document.getElementById('captcha').style.borderColor = '';
            return true;
        }

        // Event listener untuk form submission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!validateCaptcha()) {
                // Regenerate CAPTCHA jika salah
                displayCaptcha();
                document.getElementById('captcha').value = '';
                document.getElementById('captcha').focus();
                return;
            }
            
            // Jika CAPTCHA benar, lanjutkan proses login
            const loginButton = document.getElementById('loginButton');
            loginButton.disabled = true;
            loginButton.textContent = 'Memproses...';
            
            // Simulasi proses login
            setTimeout(() => {
                alert('Login berhasil! (Ini hanya simulasi)');
                loginButton.disabled = false;
                loginButton.textContent = 'Login';
                displayCaptcha(); // Generate CAPTCHA baru
                document.getElementById('captcha').value = '';
            }, 1500);
        });

        // Event listener untuk refresh CAPTCHA
        document.getElementById('refreshCaptcha').addEventListener('click', function() {
            displayCaptcha();
            document.getElementById('captcha').value = '';
        });

        // Event listener untuk validasi real-time CAPTCHA
        document.getElementById('captcha').addEventListener('input', function() {
            if (this.value.length > 0) {
                document.getElementById('captchaError').style.display = 'none';
                this.classList.remove('error');
                this.style.borderColor = '';
            }
        });

        // Inisialisasi CAPTCHA saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            displayCaptcha();
            
            // Tambahkan style untuk error state
            const style = document.createElement('style');
            style.textContent = `
                .error {
                    border-color: var(--color-error) !important;
                    box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.15) !important;
                }
            `;
            document.head.appendChild(style);
        });
    </script>

</body>
</html>