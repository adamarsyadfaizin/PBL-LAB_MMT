<?php
/**
 * FILE: menu/captcha.php
 * Menghasilkan gambar CAPTCHA dari kode yang tersimpan di sesi.
 * Dibuat sebagai gambar agar kode tidak bisa dibaca langsung dari HTML.
 */
require_once __DIR__ . '/../config/auth.php';
startSecureSession();

$code = $_SESSION['captcha'] ?? 'ABC123';

if (!function_exists('imagecreatetruecolor')) {
    // Kalau ekstensi GD tidak tersedia, kirim teks biasa sebagai cadangan.
    header('Content-Type: text/plain; charset=utf-8');
    echo $code;
    exit;
}

$width  = 160;
$height = 44;

$image = imagecreatetruecolor($width, $height);
imageantialias($image, true);

$background = imagecolorallocate($image, 244, 246, 248);
imagefilledrectangle($image, 0, 0, $width, $height, $background);

// Garis pengganggu
for ($i = 0; $i < 6; $i++) {
    $color = imagecolorallocate($image, rand(150, 210), rand(150, 210), rand(150, 210));
    imageline($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $color);
}

// Titik pengganggu
for ($i = 0; $i < 120; $i++) {
    $color = imagecolorallocate($image, rand(140, 220), rand(140, 220), rand(140, 220));
    imagesetpixel($image, rand(0, $width), rand(0, $height), $color);
}

// Karakter CAPTCHA
$font = 5;
$charWidth = imagefontwidth($font);
$totalWidth = $charWidth * strlen($code);
$x = (int) (($width - $totalWidth) / 2);

for ($i = 0; $i < strlen($code); $i++) {
    $color = imagecolorallocate($image, rand(20, 70), rand(40, 90), rand(80, 140));
    $y = rand(6, 12);
    imagestring($image, $font, $x, $y, $code[$i], $color);
    $x += $charWidth + 4;
}

header('Content-Type: image/png');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

imagepng($image);
imagedestroy($image);
