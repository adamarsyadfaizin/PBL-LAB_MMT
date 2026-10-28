<?php
/**
 * FILE: admin/components/auth_check.php
 * Dipanggil di awal setiap halaman admin.
 * Memastikan pengguna sudah login dan punya hak akses admin/editor.
 */
require_once __DIR__ . '/../../config/auth.php';
startSecureSession();
require_once __DIR__ . '/../../config/db.php';

requireAdmin($pdo);

// Data pengguna yang sedang aktif (dipakai di beberapa halaman)
$stmt = $pdo->prepare("SELECT id, name, email, role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$currentUser = $stmt->fetch();
