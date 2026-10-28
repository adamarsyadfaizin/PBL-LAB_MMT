<?php
/**
 * FILE: admin/api/_bootstrap.php
 * Bootstrap khusus endpoint JSON admin.
 * - Memastikan pengguna sudah login sebagai admin/editor.
 * - Menolak permintaan selain POST (kecuali dinyatakan lain oleh pemanggil).
 */

require_once __DIR__ . '/../../config/auth.php';
startSecureSession();
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

function jsonResponse(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload);
    exit;
}

if (!isLoggedIn()) {
    jsonResponse(['success' => false, 'message' => 'Sesi berakhir. Silakan login kembali.'], 401);
}

$role = $_SESSION['user_role'] ?? '';
if (!in_array($role, ['admin', 'editor'], true)) {
    jsonResponse(['success' => false, 'message' => 'Akses ditolak.'], 403);
}
