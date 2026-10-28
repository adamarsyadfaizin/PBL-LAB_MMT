<?php
// FILE: errors/404.php
http_response_code(404);
$root_prefix = '../';
$page_title = 'Halaman tidak ditemukan';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman tidak ditemukan | Laboratorium MMT POLINEMA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            font-family: 'Poppins', system-ui, sans-serif;
            color: #fff;
            background: linear-gradient(135deg, #1b2a41 0%, #2f4f6f 100%);
        }
        .card {
            max-width: 520px;
            text-align: center;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 18px;
            padding: 48px 32px;
            backdrop-filter: blur(12px);
        }
        .code { font-size: 96px; font-weight: 700; line-height: 1; color: #FFB300; margin: 0; }
        h1 { font-size: 22px; margin: 16px 0 8px; }
        p { color: rgba(255, 255, 255, 0.75); line-height: 1.6; margin: 0 0 28px; }
        a.btn {
            display: inline-block;
            padding: 12px 28px;
            border-radius: 30px;
            background: #FFB300;
            color: #1b2a41;
            font-weight: 600;
            text-decoration: none;
        }
        a.btn:hover { background: #ffc233; }
    </style>
</head>
<body>
    <div class="card">
        <p class="code">404</p>
        <h1>Halaman tidak ditemukan</h1>
        <p>Alamat yang Anda buka tidak tersedia atau sudah dipindahkan.</p>
        <a class="btn" href="/">Kembali ke Beranda</a>
    </div>
</body>
</html>
