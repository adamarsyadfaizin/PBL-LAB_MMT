<?php include 'components/header.php'; ?>

<div class="header-flex">
    <h1>Riwayat Aktivitas & Login</h1>
    <a href="?clear=true" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus semua riwayat? Data tidak bisa dikembalikan.')">
        <i class="fas fa-trash"></i> Bersihkan Riwayat
    </a>
</div>

<?php
if (isset($_GET['clear'])) {
    require_once '../config/db.php';
    // TRUNCATE untuk mengosongkan tabel
    $pdo->exec("TRUNCATE TABLE activity_logs RESTART IDENTITY");
    echo "<script>alert('Riwayat berhasil dibersihkan!'); window.location='riwayat.php';</script>";
}
?>

<div class="table-card">
    <table class="table" style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="background:#f8f9fa; border-bottom:2px solid #ddd;">
                <th style="padding:12px;">Waktu</th>
                <th style="padding:12px;">User</th>
                <th style="padding:12px;">Aktivitas</th>
                <th style="padding:12px;">IP Address</th>
                <th style="padding:12px;">Perangkat</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Ambil 50 data terakhir
            $stmt = $pdo->query("SELECT * FROM activity_logs ORDER BY created_at DESC LIMIT 50");
            while ($row = $stmt->fetch()):
            ?>
            <tr style="border-bottom:1px solid #eee;">
                <td style="padding:10px; font-size:13px; color:#666;">
                    <?= date('d M Y H:i', strtotime($row['created_at'])) ?>
                </td>
                <td style="padding:10px;">
                    <span style="background:#e3f2fd; color:#0d47a1; padding:3px 8px; border-radius:10px; font-size:12px; font-weight:bold;">
                        <?= htmlspecialchars($row['username']) ?>
                    </span>
                </td>
                <td style="padding:10px; font-weight:600; color:#333;">
                    <?= htmlspecialchars($row['action']) ?>
                </td>
                <td style="padding:10px; font-family:monospace; color:#d63384;">
                    <?= htmlspecialchars($row['ip_address']) ?>
                </td>
                <td style="padding:10px; font-size:12px; color:#888;">
                    <?= htmlspecialchars(substr($row['device_info'], 0, 30)) ?>...
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    
    <?php if($stmt->rowCount() == 0): ?>
        <div style="text-align:center; padding:40px; color:#888;">
            Belum ada aktivitas tercatat.
        </div>
    <?php endif; ?>
</div>

<?php include 'components/footer.php'; ?>