<?php
if (!isset($_SESSION)) session_start();
require_once '../config/db.php';

// Include components
require_once 'components/navbar.php';
require_once 'components/footer.php';
include 'components/floating_profile.php';

// Variabel untuk feedback message
$success_message = '';
$error_message = '';

// Proses form ketika disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {
    $nama_lengkap = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subjek = trim($_POST['subject']);
    $pesan = trim($_POST['message']);
    
    // Validasi input
    if (empty($nama_lengkap) || empty($email) || empty($subjek) || empty($pesan)) {
        $error_message = "Semua field harus diisi!";
    } elseif (strlen($nama_lengkap) < 2) {
        $error_message = "Nama harus minimal 2 karakter!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Format email tidak valid!";
    } else {
        try {
            // Simpan ke database PostgreSQL
            $stmt = $pdo->prepare("INSERT INTO feedback (nama_lengkap, email, subjek, pesan, created_at, is_read) 
                                   VALUES (?, ?, ?, ?, NOW(), false)");
            $stmt->execute([$nama_lengkap, $email, $subjek, $pesan]);
            
            $success_message = "Pesan berhasil dikirim! Kami akan membalasnya segera.";
            
            // Clear form
            $_POST['name'] = $_POST['email'] = $_POST['subject'] = $_POST['message'] = '';
            
        } catch (PDOException $e) {
            $error_message = "Terjadi error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak - Laboratorium Mobile and Multimedia Tech POLINEMA</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    
    <style>
        /* ==== 1. Variabel Global & Reset ==== */
        :root {
            --color-primary: #003b8e;
            --color-accent: #ffc700;
            --color-accent-hover: #ffb700;
            --color-white: #ffffff;
            --color-bg-light: #f5f5f5;
            --color-text: #000000;
            --color-text-secondary: #666666;
            --color-text-darker: #444444;
            --color-border-light: #eeeeee;
            --color-dropdown-bg: #ffffff;
            --color-dropdown-border: #dddddd;
            --color-dropdown-hover: #f5f5f5;
            --color-mobile-submenu-bg: #002f80;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
            font-family: var(--font-body);
        }

        body {
            font-family: 'Open Sans', sans-serif;
            font-size: 16px;
            color: var(--color-text);
            line-height: 1.6;
            background: var(--color-white);
            text-rendering: optimizeLegibility;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* ==== 2. Aksesibilitas & Utilitas ==== */
        :focus-visible {
            outline: 3px solid rgba(255, 199, 0, 0.45);
            outline-offset: 2px;
            border-radius: 4px;
        }

        .container {
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            padding-left: 24px;
            padding-right: 24px;
        }

        img {
            max-width: 100%;
            height: auto;
            display: block;
        }

        a {
            color: var(--color-primary);
            text-decoration: none;
            transition: color 0.28s ease;
        }
        a:hover {
            color: var(--color-accent-hover);
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            line-height: 1.3;
        }

        .btn {
            display: inline-block;
            background: var(--color-accent);
            color: var(--color-text);
            padding: 10px 22px;
            border-radius: 6px;
            font-weight: 600;
            font-family: 'Open Sans', sans-serif;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: transform 0.18s ease, background-color 0.18s ease;
        }

        .btn:hover {
            background: var(--color-accent-hover);
            color: var(--color-text);
            transform: scale(1.03);
        }

        .btn-secondary {
            background: transparent;
            color: var(--color-primary);
            border: 2px solid var(--color-primary);
            padding: 8px 20px;
        }
        .btn-secondary:hover {
            background: var(--color-primary);
            color: var(--color-white);
            transform: scale(1.03);
        }

        .section-title {
            font-size: 28px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 40px;
            text-transform: uppercase;
        }

        /* ==== 3. Header & Navigasi ==== */
        .site-header {
            height: 60px;
            background: var(--color-primary);
            color: var(--color-white);
            position: sticky;
            top: 0;
            z-index: 999;
            width: 100%;
            transition: box-shadow 0.3s ease;
        }
        .site-header.header-scrolled { box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08); }
        .site-header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 100%;
        }
        .logo-area a { display: flex; align-items: center; color: var(--color-white); text-decoration: none; }
        .main-navigation ul { display: flex; list-style: none; gap: 24px; }
        .main-navigation li { margin: 0; position: relative; }
        .main-navigation a {
            color: var(--color-white);
            font-weight: 600;
            font-size: 15px;
            text-transform: uppercase;
            text-decoration: none;
            padding: 20px 0;
            position: relative;
            display: block;
        }
        .main-navigation a[aria-current="page"] { color: var(--color-accent); }
        .main-navigation a::after {
            content: '';
            display: block;
            width: 0;
            height: 3px;
            background: var(--color-accent);
            position: absolute;
            bottom: 10px;
            left: 0;
            transition: width 0.3s ease;
        }
        .main-navigation a:hover::after,
        .main-navigation a[aria-current="page"]::after {
            width: 100%;
        }

        /* MODIFIKASI: CSS untuk Search Bar */
        .nav-search {
            display: flex;
            align-items: center;
            margin-left: 16px;
        }
        .nav-search-form {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 999px;
            overflow: hidden;
            height: 36px;
            margin-top: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .nav-search-input {
            border: none;
            background: transparent;
            padding: 0 16px;
            font-size: 14px;
            font-family: 'Open Sans', sans-serif;
            color: var(--color-white);
            height: 100%;
            width: 160px;
            transition: width 0.3s ease;
        }
        .nav-search-input:focus {
            width: 200px;
            outline: none;
        }
        .nav-search-input::placeholder {
            color: rgba(255, 255, 255, 0.6);
            opacity: 1;
        }
        .nav-search-button {
            border: none;
            background: transparent;
            color: rgba(255, 255, 255, 0.7);
            cursor: pointer;
            height: 100%;
            padding: 0 16px;
            font-size: 16px;
            transition: color 0.2s ease;
        }
        .nav-search-button:hover {
            color: var(--color-white);
        }

        .main-navigation .has-dropdown > a { padding-right: 14px; }
        .main-navigation .has-dropdown > a::before {
            content: '\25BC';
            font-size: 10px;
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0.7;
            transition: transform 0.2s ease;
        }
        .main-navigation .has-dropdown:hover > a::before { transform: translateY(-50%) rotate(180deg); }

        /* ---- 3.1 Dropdown Menu (Desktop) ---- */
        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            min-width: 230px;
            background: var(--color-dropdown-bg);
            border: 1px solid var(--color-dropdown-border);
            box-shadow: 0 5px 10px rgba(0,0,0,0.08);
            z-index: 1000;
            border-radius: 0 0 6px 6px;
            border-top: 3px solid var(--color-accent);
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: opacity 0.25s ease, visibility 0.25s ease, transform 0.25s ease;
        }
        @media (min-width: 769px) {
            .main-navigation .has-dropdown:hover > .dropdown-menu {
                display: block;
                opacity: 1;
                visibility: visible;
                transform: translateY(0);
            }
        }
        .dropdown-menu li { width: 100%; gap: 0; }
        .dropdown-menu li a {
            color: var(--color-text);
            font-weight: 400;
            font-size: 14px;
            text-transform: none;
            padding: 12px 18px;
            white-space: nowrap;
            transition: background-color 0.2s ease, color 0.2s ease;
        }
        .dropdown-menu li a:hover { background: var(--color-dropdown-hover); color: var(--color-primary); }
        .dropdown-menu li a::after { display: none; }
        .dropdown-menu li a::before { display: none; }

        /* ---- 3.2 Navigasi Mobile ---- */
        .menu-toggle { display: none; background: none; border: none; color: var(--color-white); cursor: pointer; padding: 10px; z-index: 1001; width: 44px; height: 44px; }
        .menu-toggle span { display: block; width: 24px; height: 3px; background: var(--color-white); margin: 5px 0; transition: transform 0.3s ease, opacity 0.3s ease; }
        .menu-toggle[aria-expanded="true"] span:nth-child(1) { transform: translateY(8px) rotate(45deg); }
        .menu-toggle[aria-expanded="true"] span:nth-child(2) { opacity: 0; }
        .menu-toggle[aria-expanded="true"] span:nth-child(3) { transform: translateY(-8px) rotate(-45deg); }

        /* ==== 4. Hero Section ==== */
        .hero {
            height: 430px; 
            width: 100%;
            background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.5)), 
                        url('../assets/images/hero.jpg') center center/cover no-repeat;
            display: flex;
            align-items: center; 
            position: relative;
            color: var(--color-white);
        }
        .hero .container { position: relative; }
        .hero h1 {
            font-size: 48px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.4);
            max-width: 800px; 
        }

        /* ==== 5. Main Content Area (Layout 2 kolom) ==== */
        .main-content-area {
            padding-top: 80px;
            padding-bottom: 80px;
        }
        .main-content-area > .container {
            display: flex;
            flex-wrap: wrap; 
            gap: 48px; 
        }
        .primary-content {
            flex: 1; 
            min-width: 0; 
        }
        .sidebar {
            width: 360px; 
            flex-shrink: 0;
        }

        /* ==== 7. Style untuk Halaman Kontak ==== */
        .primary-content h2 {
            font-size: 28px;
            margin-bottom: 24px;
        }

        .contact-form .form-group {
            margin-bottom: 20px;
        }
        .contact-form label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .contact-form input[type="text"],
        .contact-form input[type="email"],
        .contact-form textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--color-border-light);
            border-radius: 6px;
            font-size: 16px;
            font-family: 'Open Sans', sans-serif;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .contact-form input[type="text"]:focus,
        .contact-form input[type="email"]:focus,
        .contact-form textarea:focus {
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(0, 59, 142, 0.15);
            outline: none;
        }
        .contact-form textarea {
            min-height: 150px;
            resize: vertical;
        }
        .contact-form button {
            font-size: 16px;
        }

        /* Sidebar Kontak */
        .widget {
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid var(--color-border-light);
            background: var(--color-white);
            margin-bottom: 32px;
        }
        .widget-title {
            background: var(--color-primary);
            color: var(--color-white);
            padding: 18px;
            font-size: 20px;
            margin: 0;
        }

        .widget-contact-info {
            padding: 24px;
            font-size: 15px;
            line-height: 1.7;
        }
        .widget-contact-info p {
            margin-bottom: 16px;
        }
        .widget-contact-info strong {
            color: var(--color-text-darker);
            display: block;
            margin-bottom: 4px;
        }
        .widget-contact-info a {
            color: var(--color-primary);
        }

        .widget-social {
            padding: 24px;
        }
        .widget-social-links {
            display: flex;
            gap: 12px;
        }
        .widget-social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border: 1px solid var(--color-border-light);
            background: var(--color-bg-light);
            color: var(--color-primary);
            border-radius: 50%;
            text-decoration: none;
            font-weight: bold;
            font-size: 18px;
            transition: background-color 0.2s, color 0.2s;
        }
        .widget-social-links a:hover {
            background-color: var(--color-primary);
            color: var(--color-white);
        }

        /* ==== 9. Footer ==== */
        .site-footer {
            background: var(--color-primary);
            color: var(--color-white);
            padding-top: 60px;
            padding-bottom: 30px;
            font-size: 14px;
        }
        .footer-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 40px; margin-bottom: 40px; }
        .footer-col { color: #dddddd; }
        .footer-col h4 { color: var(--color-accent); margin-bottom: 16px; font-size: 18px; }
        .footer-col p { margin-bottom: 16px; line-height: 1.7; }
        .footer-col ul { list-style: none; }
        .footer-col li { margin-bottom: 8px; }
        .footer-col a { color: #dddddd; }
        .footer-col a:hover { color: var(--color-white); text-decoration: underline; }
        .footer-copyright {
            text-align: center; font-size: 12px; color: #dddddd;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 30px; margin-top: 20px;
        }

        .social-links-footer {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }
        .social-links-footer a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: var(--color-white);
            border-radius: 50%;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.2s, border-color 0.2s;
        }
        .social-links-footer a:hover {
            background-color: var(--color-white);
            border-color: var(--color-white);
            color: var(--color-primary);
        }

        .policy-links {
            margin-top: 16px;
            padding: 0;
        }
        .policy-links li {
            margin-bottom: 5px;
        }
        .policy-links a {
            font-size: 13px;
            color: #bbbbbb;
        }
        .policy-links a:hover {
            color: var(--color-white);
        }

        /* ==== 10. Scroll to Top Button ==== */
        .scroll-top-btn {
            position: fixed; bottom: 20px; right: 20px;
            background: var(--color-accent); color: var(--color-text);
            width: 44px; height: 44px; border-radius: 50%;
            display: none; justify-content: center; align-items: center;
            font-size: 22px; font-weight: bold; text-decoration: none;
            z-index: 998; box-shadow: 0 2px 5px rgba(0,0,0,0.15);
            transition: background-color 0.2s ease, transform 0.2s ease, opacity 0.3s ease;
        }
        .scroll-top-btn.show { display: flex; opacity: 1; }
        .scroll-top-btn:hover { background: var(--color-accent-hover); color: var(--color-text); transform: scale(1.05); }

        /* ==== 11. Responsive Design ==== */
        @media (max-width: 992px) {
            .main-content-area > .container { flex-direction: column; gap: 60px; }
            .sidebar { width: 100%; }
        }

        @media (max-width: 768px) {
            body { font-size: 15px; }
            .container { padding-left: 16px; padding-right: 16px; }

            /* ---- 11.1 Navigasi Mobile (Overrides) ---- */
            .menu-toggle { display: block; }
            .main-navigation {
                display: none; position: absolute; top: 60px; left: 0; right: 0;
                width: 100%; background: var(--color-primary);
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                border-top: 1px solid rgba(255, 255, 255, 0.1);
            }
            .main-navigation.is-active { display: block; }
            .main-navigation ul { flex-direction: column; gap: 0; }
            .main-navigation li { border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
            .main-navigation li:last-child { border-bottom: none; }
            .main-navigation a { padding: 16px 24px; }
            .main-navigation a::after { display: none; }
            .main-navigation .has-dropdown > a::before { content: '\25B8'; transform: translateY(-50%) rotate(0deg); transition: transform 0.3s ease; }
            .main-navigation .has-dropdown.submenu-open > a::before { transform: translateY(-50%) rotate(90deg); }
            .dropdown-menu {
                display: none; position: static; width: 100%;
                background: var(--color-mobile-submenu-bg);
                border: none; box-shadow: none; border-radius: 0;
                opacity: 1; visibility: visible; transform: none; transition: none;
            }
            .main-navigation .has-dropdown.submenu-open > .dropdown-menu { display: block; }
            .dropdown-menu li { border-top: 1px solid rgba(255, 255, 255, 0.1); border-bottom: none; }
            .dropdown-menu li a { padding-left: 40px; color: #f0f0f0; font-size: 14px; }
            .dropdown-menu li a:hover { background: var(--color-primary); color: var(--color-white); }
            
            /* MODIFIKASI: CSS Search Bar (Mobile) */
            .main-navigation li.nav-search {
                padding: 16px 24px;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }
            .nav-search-form {
                width: 100%;
                margin-top: 0;
                background: rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }
            .nav-search-input {
                width: 100%;
                flex: 1;
            }
            .nav-search-input:focus {
                width: 100%;
            }

            /* ---- 11.2 Konten Mobile ---- */
            .hero { height: 320px; } 
            .hero h1 { font-size: 36px; }
            
            .footer-grid { grid-template-columns: 1fr; gap: 30px; }
        }

        /* ==== Additional Styles for Contact Page ==== */
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
            border-left: 4px solid;
        }

        .alert-success {
            background: #f0f9ff;
            color: #065f46;
            border-color: #10b981;
        }

        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border-color: #ef4444;
        }

        .form-group {
            position: relative;
        }

        .error-message {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }

        input:invalid, textarea:invalid {
            border-color: #dc3545;
        }

        input:valid, textarea:valid {
            border-color: #28a745;
        }

        /* Contact info items */
        .contact-info-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 20px;
        }
        .contact-info-icon {
            width: 44px;
            height: 44px;
            background: var(--color-primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .contact-info-content {
            flex: 1;
        }

        /* Peta styling */
        #map {
            width: 100%;
            height: 250px;
            border-radius: 0 0 8px 8px;
            overflow: hidden;
        }
        .map-msg {
            font-size: 13px;
            color: #666;
            margin-top: 8px;
            text-align: center;
            padding: 10px;
        }

        .btn-email {
            background: var(--color-accent);
            color: var(--color-text);
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
            transition: transform 0.18s ease, background-color 0.18s ease;
            text-align: center;
            width: 100%;
            margin-top: 16px;
        }
        .btn-email:hover {
            background: var(--color-accent-hover);
            color: var(--color-text);
            transform: scale(1.03);
        }
    </style>
</head>
<body id="top">

    <?php
    // Render navbar dengan halaman aktif 'kontak'
    renderNavbar('kontak');
    ?>

    <main>

        <section class="hero">
            <div class="container">
                <h1>Kontak Kami</h1>
            </div>
        </section>

        <div class="main-content-area">
            <div class="container">
                <div class="primary-content">
                    <h2>Kirim Pesan</h2>
                    <p style="margin-bottom: 30px; color: var(--color-text-secondary);">Punya pertanyaan atau ingin berkolaborasi? Silakan isi formulir di bawah ini dan kami akan segera menghubungi Anda.</p>
                    
                    <!-- Alert Messages -->
                    <?php if ($success_message): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success_message) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error_message): ?>
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error_message) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form class="contact-form" action="" method="post" id="contactForm">
                        <div class="form-group">
                            <label for="contact-name">Nama Lengkap *</label>
                            <input type="text" id="contact-name" name="name" 
                                   value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>" 
                                   required minlength="2" placeholder="Masukkan nama lengkap Anda">
                            <div class="error-message" id="nameError">Nama harus minimal 2 karakter</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="contact-email">Alamat Email *</label>
                            <input type="email" id="contact-email" name="email" 
                                   value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" 
                                   required placeholder="contoh@email.com">
                            <div class="error-message" id="emailError">Format email tidak valid</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="contact-subject">Subjek *</label>
                            <input type="text" id="contact-subject" name="subject" 
                                   value="<?= isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : '' ?>" 
                                   required placeholder="Subjek pesan Anda">
                            <div class="error-message" id="subjectError">Subjek harus diisi</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="contact-message">Pesan Anda *</label>
                            <textarea id="contact-message" name="message" rows="6" required placeholder="Tulis pesan Anda di sini..."><?= isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '' ?></textarea>
                            <div class="error-message" id="messageError">Pesan harus diisi</div>
                        </div>
                        
                        <button type="submit" class="btn">
                            <i class="fas fa-paper-plane"></i> Kirim Pesan
                        </button>
                    </form>
                </div>
                
                <aside class="sidebar">
                    
                    <div class="widget">
                        <h3 class="widget-title">Informasi Kontak</h3>
                        <div class="widget-contact-info">
                            <div class="contact-info-item">
                                <div class="contact-info-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="contact-info-content">
                                    <strong>Alamat</strong>
                                    <p>Jl. Soekarno Hatta No.9, Jatimulyo, Kec. Lowokwaru, Kota Malang, Jawa Timur 65141</p>
                                </div>
                            </div>
                            
                            <div class="contact-info-item">
                                <div class="contact-info-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="contact-info-content">
                                    <strong>Email</strong>
                                    <p><a href="mailto:info@polinema.ac.id">info@polinema.ac.id</a></p>
                                </div>
                            </div>
                            
                            <div class="contact-info-item">
                                <div class="contact-info-icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="contact-info-content">
                                    <strong>Telepon</strong>
                                    <p>(0341) 404 424</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="widget">
                        <h3 class="widget-title">Lokasi Kami</h3>
                        <div id="map"></div>
                        <div class="map-msg" id="mapMsg">Memuat peta lokasi POLINEMA...</div>
                    </div>

                    <div class="widget">
                        <h3 class="widget-title">Media Sosial</h3>
                        <div class="widget-social">
                            <div class="widget-social-links">
                                <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" aria-label="Twitter"><i class="fab fa-x-twitter"></i></a>
                                <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                                <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                                <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                            </div>

                            <a href="mailto:adamkian09@gmail.com?subject=Halo%20Lab%20POLINEMA" class="btn-email">
                                <i class="fas fa-envelope"></i> Kirim Email ke MMT
                            </a>
                        </div>
                    </div>

                </aside>
                
            </div>
        </div>
        
    </main>

    <?php 
    renderFloatingProfile();
    renderFooter(); 
    ?>

    <a href="#top" id="scrollTopBtn" class="scroll-top-btn" aria-label="Kembali ke atas">
        &uarr;
    </a>

    <!-- Include JavaScript untuk navbar -->
    <script src="assets/js/navbar.js"></script>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        // Validasi form real-time
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            let isValid = true;
            
            // Validasi nama
            const nameInput = document.getElementById('contact-name');
            const nameError = document.getElementById('nameError');
            if (nameInput.value.length < 2) {
                nameError.style.display = 'block';
                isValid = false;
            } else {
                nameError.style.display = 'none';
            }
            
            // Validasi email
            const emailInput = document.getElementById('contact-email');
            const emailError = document.getElementById('emailError');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailInput.value)) {
                emailError.style.display = 'block';
                isValid = false;
            } else {
                emailError.style.display = 'none';
            }
            
            // Validasi subjek
            const subjectInput = document.getElementById('contact-subject');
            const subjectError = document.getElementById('subjectError');
            if (subjectInput.value.trim() === '') {
                subjectError.style.display = 'block';
                isValid = false;
            } else {
                subjectError.style.display = 'none';
            }
            
            // Validasi pesan
            const messageInput = document.getElementById('contact-message');
            const messageError = document.getElementById('messageError');
            if (messageInput.value.trim() === '') {
                messageError.style.display = 'block';
                isValid = false;
            } else {
                messageError.style.display = 'none';
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });

        // Real-time validation
        document.getElementById('contact-name').addEventListener('input', function() {
            const error = document.getElementById('nameError');
            error.style.display = this.value.length < 2 ? 'block' : 'none';
        });

        document.getElementById('contact-email').addEventListener('input', function() {
            const error = document.getElementById('emailError');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            error.style.display = !emailRegex.test(this.value) ? 'block' : 'none';
        });

        // Map functionality
        (function(){
            const address = "Jl. Soekarno Hatta No.9, Jatimulyo, Lowokwaru, Malang";

            const map = L.map('map', { scrollWheelZoom: false }).setView([-7.9666, 112.6326], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap',
                maxZoom: 19
            }).addTo(map);

            const mapMsg = document.getElementById('mapMsg');

            const nominatimUrl = 'https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + encodeURIComponent(address);

            fetch(nominatimUrl)
                .then(res => res.json())
                .then(data => {
                    if (data.length > 0) {
                        const lat = parseFloat(data[0].lat);
                        const lon = parseFloat(data[0].lon);

                        map.setView([lat, lon], 16);

                        const marker = L.marker([lat, lon]).addTo(map);
                        marker.bindPopup(`<strong>Laboratorium Mobile & Multimedia Tech POLINEMA</strong><br>${address}`).openPopup();

                        mapMsg.textContent = "Lokasi POLINEMA di Malang";
                    } else {
                        mapMsg.textContent = "Lokasi tidak ditemukan — menampilkan area Malang.";
                    }
                })
                .catch(() => {
                    mapMsg.textContent = "Gagal memuat lokasi otomatis.";
                });

        })();

        // Scroll to top button functionality
        const scrollTopBtn = document.getElementById('scrollTopBtn');
        
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                scrollTopBtn.style.display = 'flex';
                scrollTopBtn.classList.add('show');
            } else {
                scrollTopBtn.style.display = 'none';
                scrollTopBtn.classList.remove('show');
            }
        });

        scrollTopBtn.addEventListener('click', (e) => {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>

</body>
</html>