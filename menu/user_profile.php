<?php
if (!isset($_SESSION)) session_start();
require_once '../config/db.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include components
require_once 'components/navbar.php';
require_once 'components/footer.php';
include 'components/floating_profile.php';

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Ambil data user dari database - SESUAI STRUCTURE TABEL ANDA
try {
    $stmt = $pdo->prepare("SELECT id, name, email, role, created_at FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        $error = "User tidak ditemukan!";
    }
} catch (PDOException $e) {
    $error = "Error: " . $e->getMessage();
}

// Proses update password
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = "Semua field password harus diisi!";
    } elseif ($new_password !== $confirm_password) {
        $error = "Password baru dan konfirmasi password tidak cocok!";
    } elseif (strlen($new_password) < 6) {
        $error = "Password baru minimal 6 karakter!";
    } else {
        try {
            // Verifikasi password lama
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user_data && password_verify($current_password, $user_data['password'])) {
                // Update password baru
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$hashed_password, $user_id]);
                
                $success = "Password berhasil diubah!";
                // Clear form
                $_POST['current_password'] = $_POST['new_password'] = $_POST['confirm_password'] = '';
            } else {
                $error = "Password lama salah!";
            }
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

// Render floating profile
renderFloatingProfile();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Laboratorium Mobile and Multimedia Tech POLINEMA</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Open Sans', sans-serif;
        }

        body {
            background: #f5f5f5;
            min-height: 100vh;
        }

        .user-profile-container {
            max-width: 900px;
            margin: 40px auto 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .profile-header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .profile-header h1 {
            font-size: 28px;
            margin-bottom: 10px;
            font-family: 'Poppins', sans-serif;
        }

        .profile-header p {
            opacity: 0.9;
        }

        .profile-content {
            padding: 0;
        }

        .profile-section {
            padding: 30px;
            border-bottom: 1px solid #eee;
        }

        .profile-section:last-child {
            border-bottom: none;
        }

        .section-title {
            font-size: 20px;
            color: #333;
            margin-bottom: 20px;
            font-family: 'Poppins', sans-serif;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: #6a11cb;
        }

        /* Info Profile Styling */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
        }

        .info-group {
            margin-bottom: 15px;
        }

        .info-label {
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .info-value {
            color: #333;
            padding: 10px 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            font-size: 15px;
        }

        /* Form Styling */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #6a11cb;
            box-shadow: 0 0 0 3px rgba(106, 17, 203, 0.1);
        }

        /* Button Styling */
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            font-family: 'Poppins', sans-serif;
        }

        .btn-primary {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(106, 17, 203, 0.4);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        /* Alert Styling */
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
        }

        /* Created At Styling */
        .created-at-info {
            background: #e7f3ff;
            border-left: 4px solid #2575fc;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            grid-column: 1 / -1;
        }

        .created-at-info .info-label {
            color: #2575fc;
            font-weight: 700;
        }
    </style>
</head>
<body id="top">

    <!-- Simple Header tanpa navbar lengkap -->
    <header style="background: white; padding: 15px 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px; display: flex; justify-content: space-between; align-items: center;">
            <!-- Logo -->
            <div style="display: flex; align-items: center; gap: 15px;">
                <img src="../assets/images/logo.png" alt="Logo" style="height: 40px;">
                <span style="font-family: 'Poppins', sans-serif; font-weight: 700; color: #333;">LAB MMT</span>
            </div>
            
            <!-- Hanya tombol kembali ke beranda -->
            <a href="../beranda.php" class="btn btn-secondary" style="text-decoration: none;">
                <i class="fas fa-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>
    </header>

    <div class="user-profile-container">
        <!-- Header -->
        <div class="profile-header">
            <h1><i class="fas fa-user-circle"></i> User Profile</h1>
            <p>Kelola informasi profil dan akun Anda</p>
        </div>

        <div class="profile-content">
            <!-- Section: Info Profile -->
            <div class="profile-section">
                <h2 class="section-title">
                    <i class="fas fa-user"></i> Informasi Profil
                </h2>
                
                <?php if ($error && !isset($_POST['change_password'])): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <?php if ($user): ?>
                <div class="info-grid">
                    <div class="info-group">
                        <div class="info-label">ID User</div>
                        <div class="info-value"><?= htmlspecialchars($user['id']) ?></div>
                    </div>

                    <div class="info-group">
                        <div class="info-label">Nama Lengkap</div>
                        <div class="info-value"><?= htmlspecialchars($user['name']) ?></div>
                    </div>

                    <div class="info-group">
                        <div class="info-label">Email</div>
                        <div class="info-value"><?= htmlspecialchars($user['email']) ?></div>
                    </div>

                    <div class="info-group">
                        <div class="info-label">Role</div>
                        <div class="info-value"><?= htmlspecialchars($user['role']) ?></div>
                    </div>

                    <div class="created-at-info">
                        <div class="info-label">Akun Dibuat Pada</div>
                        <div class="info-value">
                            <i class="fas fa-calendar"></i> 
                            <?= date('d F Y H:i', strtotime($user['created_at'])) ?>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i> Data user tidak ditemukan
                </div>
                <?php endif; ?>
            </div>

            <!-- Section: Edit Password -->
            <div class="profile-section">
                <h2 class="section-title">
                    <i class="fas fa-lock"></i> Edit Password
                </h2>
                
                <?php if ($error && isset($_POST['change_password'])): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="current_password">
                                <i class="fas fa-key"></i> Password Lama
                            </label>
                            <input type="password" id="current_password" name="current_password" required 
                                   value="<?= isset($_POST['current_password']) ? htmlspecialchars($_POST['current_password']) : '' ?>"
                                   placeholder="Masukkan password lama">
                        </div>

                        <div class="form-group">
                            <label for="new_password">
                                <i class="fas fa-lock"></i> Password Baru
                            </label>
                            <input type="password" id="new_password" name="new_password" required 
                                   value="<?= isset($_POST['new_password']) ? htmlspecialchars($_POST['new_password']) : '' ?>"
                                   placeholder="Password baru (min. 6 karakter)">
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">
                                <i class="fas fa-lock"></i> Konfirmasi Password Baru
                            </label>
                            <input type="password" id="confirm_password" name="confirm_password" required 
                                   value="<?= isset($_POST['confirm_password']) ? htmlspecialchars($_POST['confirm_password']) : '' ?>"
                                   placeholder="Konfirmasi password baru">
                        </div>

                        <div class="form-group" style="display: flex; align-items: end;">
                            <button type="submit" name="change_password" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Password Baru
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

     

    <script>
        // Validasi form password
        document.querySelector('form')?.addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (newPassword.length < 6) {
                alert('minimal 6 karakter');
                e.preventDefault();
                return;
            }
            
            if (newPassword !== confirmPassword) {
                alert('Password baru dan konfirmasi password tidak cocok!');
                e.preventDefault();
                return;
            }
        });
    </script>

</body>
</html>